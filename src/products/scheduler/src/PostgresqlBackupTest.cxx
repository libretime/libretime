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

#include "LiveSupport/Core/FileTools.h"
#include "SchedulerDaemon.h"
#include "PostgresqlBackup.h"
#include "PostgresqlBackupTest.h"


using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PostgresqlBackupTest);

namespace {

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
PostgresqlBackupTest :: setUp(void)              throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
    try {
        Ptr<ConnectionManagerInterface>::Ref    connectionManager;
        Ptr<StorageClientInterface>::Ref        storage;
        Ptr<ScheduleInterface>::Ref             schedule;

        connectionManager = daemon->getConnectionManager();
        storage           = daemon->getStorage();
        schedule          = daemon->getSchedule();

        backup.reset(new PostgresqlBackup(connectionManager,
                                          storage,
                                          schedule));
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }

    authentication          = daemon->getAuthentication();
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        std::string eMsg = "could not log in:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
PostgresqlBackupTest :: tearDown(void)           throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        authentication->logout(sessionId);
    );

    remove(tempBackupTarFileName.c_str());
}


/*------------------------------------------------------------------------------
 *  Create the backup.
 *----------------------------------------------------------------------------*/
void
PostgresqlBackupTest :: createBackup(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SearchCriteria>::Ref        criteria(new SearchCriteria);
    criteria->setLimit(10);
    Ptr<ptime>::Ref from(new ptime(time_from_string("2004-07-23 10:00:00")));
    Ptr<ptime>::Ref to(new ptime(time_from_string("2004-07-23 11:00:00")));

    Ptr<const Glib::ustring>::Ref   token;
    CPPUNIT_ASSERT_NO_THROW(
        token = backup->createBackupOpen(sessionId, criteria, from, to);
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
            status = backup->createBackupCheck(*token, url, path, errorMessage);
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
        backup->createBackupClose(*token);
    );
}


/*------------------------------------------------------------------------------
 *  Test to see if we can create backups.
 *----------------------------------------------------------------------------*/
void
PostgresqlBackupTest :: createBackupTest(void)
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
PostgresqlBackupTest :: restoreBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        createBackup()
    );

    Ptr<const Glib::ustring>::Ref   backupFile(new const Glib::ustring(
                                                    tempBackupTarFileName));
    CPPUNIT_ASSERT_NO_THROW(
        backup->restoreBackup(sessionId, backupFile)
    );
    // TODO: try this with a non-empty backup file, too
}

