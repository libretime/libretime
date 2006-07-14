/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
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

#include "SchedulerDaemon.h"
#include "PostgresqlBackup.h"
#include "PostgresqlBackupTest.h"


using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PostgresqlBackupTest);


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
        backup->install();
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
    CPPUNIT_ASSERT_NO_THROW(
        backup->uninstall();
    );
}


/*------------------------------------------------------------------------------
 *  Test to see if we can create backups
 *----------------------------------------------------------------------------*/
void
PostgresqlBackupTest :: createBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria);
    criteria->setLimit(10);
    Ptr<ptime>::Ref from(new ptime(time_from_string("2004-07-23 10:00:00")));
    Ptr<ptime>::Ref to(new ptime(time_from_string("2004-07-23 11:00:00")));

    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = backup->createBackupOpen(sessionId, criteria, from, to);
    );
    CPPUNIT_ASSERT(token);

    Ptr<const Glib::ustring>::Ref       url;
    Ptr<const Glib::ustring>::Ref       path;
    Ptr<const Glib::ustring>::Ref       errorMessage;
    AsyncState                          status;
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
    // TODO: test accessibility of the URL?
    
    CPPUNIT_ASSERT_NO_THROW(
        backup->createBackupClose(*token);
    );
    // TODO: test existence of schedule backup in tarball
}


