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

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif


#include <string>
#include <fstream>
#include <iostream>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Core/FileTools.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "SchedulerDaemonXmlRpcClientTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::SchedulerClient;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SchedulerDaemonXmlRpcClientTest);

namespace {

/**
 *  The name of the configuration file for the scheduler client.
 */
const std::string   configFileName = "schedulerDaemonXmlRpcClient.xml";

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
SchedulerDaemonXmlRpcClientTest :: setUp(void)                         throw ()
{
    try {
        xmlpp::DomParser        parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                             configFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        schedulerClient.reset(new SchedulerDaemonXmlRpcClient());
        schedulerClient->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }

    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(
                                 schedulerClient->getXmlRpcHost()->c_str(),
                                 schedulerClient->getXmlRpcPort(),
                                 schedulerClient->getXmlRpcUriPrefix()->c_str(),
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
SchedulerDaemonXmlRpcClientTest :: tearDown(void)                      throw ()
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(
                                schedulerClient->getXmlRpcHost()->c_str(),
                                schedulerClient->getXmlRpcPort(),
                                schedulerClient->getXmlRpcUriPrefix()->c_str(),
                                false);

    parameters["sessionId"] = sessionId->getId();
    CPPUNIT_ASSERT(xmlRpcClient.execute("logout", parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Test to see if we can get the version string of the scheduler.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: getVersionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<const std::string>::Ref     version = schedulerClient->getVersion();

        CPPUNIT_ASSERT(version.get());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test to see if we can get the time of the scheduler.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: getSchedulerTimeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<const ptime>::Ref   time = schedulerClient->getSchedulerTime();
        Ptr<const ptime>::Ref   now  = TimeConversion::now();

        CPPUNIT_ASSERT(time.get());
        // assume that the scheduler and the client is in the same year
        // this can break at new year's eve - so don't run the test then :)
        CPPUNIT_ASSERT(time->date().year() == now->date().year());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test the displaySchedule XML-RPC method, when the schedule is empty
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: displayScheduleEmptyTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
        Ptr<ptime>::Ref                                     from;
        Ptr<ptime>::Ref                                     to;

        // check from now until 1 hour later
        from = TimeConversion::now();
        to.reset(new ptime(*from + hours(1)));

        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->empty());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test playlist management functions.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: playlistMgmtTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
        Ptr<ScheduleEntry>::Ref                             entry;
        Ptr<UniqueId>::Ref                                  entryId;
        Ptr<UniqueId>::Ref                                  playlistId;
        Ptr<ptime>::Ref                                     now;
        Ptr<ptime>::Ref                                     playtime;
        Ptr<ptime>::Ref                                     from;
        Ptr<ptime>::Ref                                     to;

        now = TimeConversion::now();
        // make sure now is only second resolution, not micro-second
        long    fsec = now->time_of_day().fractional_seconds();
        now.reset(new ptime(*now - microsec(fsec)));

        // the test assumes that there's a playlist with the id of 1 in
        // the storage accessed by the scheduler

        // schedule playlist #1 for one hour from now
        playlistId.reset(new UniqueId(1));
        playtime.reset(new ptime(*now + hours(1)));

        entryId = schedulerClient->uploadPlaylist(sessionId,
                                                  playlistId,
                                                  playtime);

        // now check if our playlist has indeed been scheduled
        from = now;
        to.reset(new ptime(*from + hours(2)));

        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->size() == 1);
        entry = (*entries)[0];
        CPPUNIT_ASSERT(*entry->getId() == *entryId);
        CPPUNIT_ASSERT(*entry->getPlaylistId() == *playlistId);
        CPPUNIT_ASSERT(*entry->getStartTime() == *playtime);


        // and now, remove the entry, and see that it's not there anymore
        schedulerClient->removeFromSchedule(sessionId, entryId);
        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->empty());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test for some XML-RPC error conditions
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: xmlRpcErrorTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
    Ptr<ScheduleEntry>::Ref                             entry;
    Ptr<UniqueId>::Ref                                  entryId;
    Ptr<UniqueId>::Ref                                  playlistId;
    Ptr<ptime>::Ref                                     now;
    Ptr<ptime>::Ref                                     playtime;
    bool                                                gotException;

    try {
        now = TimeConversion::now();
        // make sure now is only second resolution, not micro-second
        long    fsec = now->time_of_day().fractional_seconds();
        now.reset(new ptime(*now - microsec(fsec)));

        // the test assumes that there's a playlist with the id of 1 in
        // the storage accessed by the scheduler

        // schedule playlist #1 for one hour from now
        playlistId.reset(new UniqueId(1));
        playtime.reset(new ptime(*now + hours(1)));

        entryId = schedulerClient->uploadPlaylist(sessionId,
                                                  playlistId,
                                                  playtime);
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    gotException = false;
    try {
        // try to upload the same entry again, for the same time
        // this should result in an error
        schedulerClient->uploadPlaylist(sessionId, playlistId, playtime);
    } catch (Core::XmlRpcMethodFaultException &e) {
        gotException = true;
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(gotException);

    try {
        // and now, remove the entry, and see that it's not there anymore
        Ptr<ptime>::Ref     from = now;
        Ptr<ptime>::Ref     to(new ptime(*from + hours(2)));

        schedulerClient->removeFromSchedule(sessionId, entryId);
        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->empty());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    gotException = false;
    try {
        // and now, try to remove it again, which should result in an
        // exception
        schedulerClient->removeFromSchedule(sessionId, entryId);
    } catch (Core::XmlRpcMethodFaultException &e) {
        gotException = true;
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(gotException);
}


/*------------------------------------------------------------------------------
 *  Create the backup.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: createBackup(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SearchCriteria>::Ref        criteria(new SearchCriteria);
    criteria->setLimit(10);
    Ptr<ptime>::Ref from(new ptime(time_from_string("2004-07-23 10:00:00")));
    Ptr<ptime>::Ref to(new ptime(time_from_string("2004-07-23 11:00:00")));

    Ptr<const Glib::ustring>::Ref   token;
    CPPUNIT_ASSERT_NO_THROW(
        token = schedulerClient->createBackupOpen(sessionId, 
                                                  criteria, 
                                                  from, 
                                                  to);
    );
    CPPUNIT_ASSERT(token);

    Ptr<const Glib::ustring>::Ref   url;
    Ptr<const Glib::ustring>::Ref   path;
    Ptr<const Glib::ustring>::Ref   errorMessage;
    AsyncState                      status;
    int     iterations = 20;
    do {
        std::cerr << "-/|\\"[iterations%4] << '\b';
        sleep(1);
        CPPUNIT_ASSERT_NO_THROW(
            status = schedulerClient->createBackupCheck(*token, 
                                                        url, 
                                                        path, 
                                                        errorMessage);
        );
        CPPUNIT_ASSERT(status == AsyncState::pendingState
                         || status == AsyncState::finishedState
                         || status == AsyncState::failedState);
    } while (--iterations && status == AsyncState::pendingState);
    
    CPPUNIT_ASSERT_EQUAL(AsyncState::finishedState, status);
    CPPUNIT_ASSERT(url);
    CPPUNIT_ASSERT(path);
    
    // copy the backup file
    CPPUNIT_ASSERT_NO_THROW(
        remove(tempBackupTarFileName.c_str());
        std::ifstream   ifs(path->c_str(),                  std::ios::binary);
        std::ofstream   ofs(tempBackupTarFileName.c_str(),  std::ios::binary);
        ofs << ifs.rdbuf();
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        schedulerClient->createBackupClose(*token);
    );
}


/*------------------------------------------------------------------------------
 *  Test the backup functions.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: createBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        createBackup()
    );
    
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
 *  Test to see if we can restore backups.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: restoreBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        createBackup()
    );
    
    Ptr<Glib::ustring>::Ref     backupFile(new Glib::ustring());
    char *                      currentDirName = get_current_dir_name();
    backupFile->append(currentDirName);
    backupFile->append("/");
    backupFile->append(tempBackupTarFileName);
    free(currentDirName);
    
    CPPUNIT_ASSERT_NO_THROW(
        schedulerClient->restoreBackup(sessionId, backupFile)
    );
    // TODO: try this with a non-empty backup file, too
}

