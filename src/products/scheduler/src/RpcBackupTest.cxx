/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund

    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org

    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <fstream>

#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/XmlRpcTools.h"
#include "LiveSupport/Core/FileTools.h"
#include "SchedulerDaemon.h"
#include "PlayLogFactory.h"

#include "RpcBackupTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcBackupTest);

namespace {

/**
 *  The name of the configuration file for the scheduler daemon
 */
const std::string   configFileName = "etc/scheduler.xml";

/**
 *  The location of the temporary backup file
 */
const std::string   tempBackupTarFileName = "tmp/scheduleBackup.tar";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcBackupTest :: setUp(void)                    throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    if (!daemon->isConfigured()) {
        try {
            Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser(
                                                        configFileName, true));
            const xmlpp::Document * document = parser->get_document();
            const xmlpp::Element  * root     = document->get_root_node();
            daemon->configure(*root);
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("semantic error in configuration file");
        } catch (xmlpp::exception &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("error parsing configuration file");
        }
    }

    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    CPPUNIT_ASSERT(xmlRpcClient.execute("resetStorage", parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    parameters["login"]     = "root";
    parameters["password"]  = "q";
    CPPUNIT_ASSERT(xmlRpcClient.execute("login", parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("sessionId"));

    xmlRpcClient.close();

    sessionId.reset(new SessionId(std::string(result["sessionId"])));
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
RpcBackupTest :: tearDown(void)                 throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
    CPPUNIT_ASSERT(sessionId);

    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    parameters["sessionId"] = sessionId->getId();
    CPPUNIT_ASSERT(xmlRpcClient.execute("logout", parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();

    remove(tempBackupTarFileName.c_str());
}


/*------------------------------------------------------------------------------
 *  Create the backup.
 *----------------------------------------------------------------------------*/
void
RpcBackupTest :: createBackup(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT(sessionId);

    XmlRpc::XmlRpcValue         parameters;
    XmlRpc::XmlRpcValue         result;

    XmlRpc::XmlRpcClient        xmlRpcClient(getXmlRpcHost().c_str(),
                                             getXmlRpcPort(),
                                             "/RPC2",
                                             false);

    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria);
    criteria->setLimit(10);
    Ptr<ptime>::Ref from(new ptime(time_from_string("2004-07-23 10:00:00")));
    Ptr<ptime>::Ref to(new ptime(time_from_string("2004-07-23 11:00:00")));

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, parameters);
    XmlRpcTools::searchCriteriaToXmlRpcValue(criteria, parameters);
    XmlRpcTools::fromTimeToXmlRpcValue(from, parameters);
    XmlRpcTools::toTimeToXmlRpcValue(to, parameters);

    CPPUNIT_ASSERT(xmlRpcClient.execute("createBackupOpen",
                                        parameters,
                                        result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = XmlRpcTools::extractToken(result);
    );

    AsyncState                  status;
    parameters.clear();
    XmlRpcTools::tokenToXmlRpcValue(token, parameters);
    int     iterations = 20;
    do {
        std::cerr << "-/|\\"[iterations%4] << '\b';
        sleep(1);
        result.clear();
        CPPUNIT_ASSERT(xmlRpcClient.execute("createBackupCheck",
                                            parameters,
                                            result));
        CPPUNIT_ASSERT(!xmlRpcClient.isFault());

        CPPUNIT_ASSERT_NO_THROW(
            status = XmlRpcTools::extractBackupStatus(result);
        );
        CPPUNIT_ASSERT(status == AsyncState::pendingState
                         || status == AsyncState::finishedState
                         || status == AsyncState::failedState);
    } while (--iterations && status == AsyncState::pendingState);

    CPPUNIT_ASSERT_EQUAL(AsyncState::finishedState, status);
    // TODO: test accessibility of the URL?

    Ptr<const Glib::ustring>::Ref       url;
    Ptr<const Glib::ustring>::Ref       path;
    Ptr<const Glib::ustring>::Ref       errorMessage;

    CPPUNIT_ASSERT_NO_THROW(
        url = XmlRpcTools::extractUrl(result);
    );
    CPPUNIT_ASSERT_NO_THROW(
        path = XmlRpcTools::extractPath(result);
    );

    // copy the backup file
    CPPUNIT_ASSERT_NO_THROW(
        remove(tempBackupTarFileName.c_str());
        std::ifstream   ifs(path->c_str(),                  std::ios::binary);
        std::ofstream   ofs(tempBackupTarFileName.c_str(),  std::ios::binary);
        ofs << ifs.rdbuf();
    );

    parameters.clear();
    XmlRpcTools::tokenToXmlRpcValue(token, parameters);
    result.clear();
    CPPUNIT_ASSERT(xmlRpcClient.execute("createBackupClose",
                                        parameters,
                                        result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Test the createBackupXxxx methods.
 *----------------------------------------------------------------------------*/
void
RpcBackupTest :: createBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // Create the backup.
    CPPUNIT_ASSERT_NO_THROW(
        createBackup()
    );

    // Check the backup file.
    bool    exists;
    std::string     schedulerBackupInTarball = "meta-inf/scheduler.xml";
    CPPUNIT_ASSERT_NO_THROW(
        exists = FileTools::existsInTarball(tempBackupTarFileName,
                                            schedulerBackupInTarball)
    );
    CPPUNIT_ASSERT(exists);

    std::string     extractedTempFileName = "tmp/scheduler.tmp.xml";
    FILE *          file;

    remove(extractedTempFileName.c_str());
    file = fopen(extractedTempFileName.c_str(), "r");
    CPPUNIT_ASSERT(file == 0);

    CPPUNIT_ASSERT_NO_THROW(
        FileTools::extractFileFromTarball(tempBackupTarFileName,
                                          schedulerBackupInTarball,
                                          extractedTempFileName)
    );

    file = fopen(extractedTempFileName.c_str(), "r");
    CPPUNIT_ASSERT(file != 0);
    CPPUNIT_ASSERT(fclose(file) == 0);

    CPPUNIT_ASSERT(remove(extractedTempFileName.c_str()) == 0);
    file = fopen(extractedTempFileName.c_str(), "r");
    CPPUNIT_ASSERT(file == 0);
}


/*------------------------------------------------------------------------------
 *  Test the restoreBackup method.
 *----------------------------------------------------------------------------*/
void
RpcBackupTest :: restoreBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // Create the backup.
    CPPUNIT_ASSERT_NO_THROW(
        createBackup()
    );

    XmlRpc::XmlRpcValue         parameters;
    XmlRpc::XmlRpcValue         result;

    XmlRpc::XmlRpcClient        xmlRpcClient(getXmlRpcHost().c_str(),
                                             getXmlRpcPort(),
                                             "/RPC2",
                                             false);

    CPPUNIT_ASSERT(sessionId);
    Ptr<const Glib::ustring>::Ref   path(new const Glib::ustring(
                                                tempBackupTarFileName));

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, parameters);
    XmlRpcTools::pathToXmlRpcValue(path, parameters);

    CPPUNIT_ASSERT(xmlRpcClient.execute("restoreBackup",
                                        parameters,
                                        result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();
    // TODO: try this with a non-empty backup file, too
}

