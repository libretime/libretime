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

#include "SchedulerDaemon.h"
#include "SchedulerDaemonTest.h"


using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SchedulerDaemonTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonTest :: setUp(void)              throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
    try {
        Ptr<StorageClientInterface>::Ref    storage = daemon->getStorage();
        storage->reset();

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }

    authentication = daemon->getAuthentication();
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
SchedulerDaemonTest :: tearDown(void)           throw (CPPUNIT_NS::Exception)
{
    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonTest :: getSingleton(void)       throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    CPPUNIT_ASSERT( daemon.get() );
}


/*------------------------------------------------------------------------------
 *  Test to see if the scheduler starts and stops OK
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonTest :: testStartStop(void)      throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    CPPUNIT_ASSERT( daemon.get() );
    CPPUNIT_ASSERT( !(daemon->isRunning()) );
    daemon->start();
    sleep(3);
    CPPUNIT_ASSERT( daemon->isRunning() );
    daemon->stop();
    sleep(3);
    CPPUNIT_ASSERT( !(daemon->isRunning()) );
}

