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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RescheduleMethodTest.cxx,v $

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
#include <iostream>
#include <XmlRpcValue.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"

#include "SchedulerDaemon.h"
#include "ScheduleFactory.h"
#include "UploadPlaylistMethod.h"
#include "RescheduleMethod.h"
#include "RescheduleMethodTest.h"

using namespace LiveSupport::Db;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RescheduleMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RescheduleMethodTest :: setUp(void)                         throw ()
{
    Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();
    try {
        Ptr<StorageClientInterface>::Ref    storage = scheduler->getStorage();
        storage->reset();

        schedule = scheduler->getSchedule();
        schedule->install();

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    authentication = scheduler->getAuthentication();
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
RescheduleMethodTest :: tearDown(void)                      throw ()
{
    schedule->uninstall();

    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
RescheduleMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  uploadMethod(new UploadPlaylistMethod());
    Ptr<RescheduleMethod>::Ref      rescheduleMethod(new RescheduleMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;
    Ptr<UniqueId>::Ref              entryId;

    // let's upload something so we can reschedule it
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 18;
    time.tm_min  = 31;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;

    result.clear();
    try {
        uploadMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("scheduleEntryId"));
    CPPUNIT_ASSERT(result["scheduleEntryId"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    entryId.reset(new UniqueId(std::string(result["scheduleEntryId"])));

    // now let's reschedule it
    parameters.clear();
    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 12;
    time.tm_min  = 31;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;

    result.clear();
    try {
        rescheduleMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }

    // now let's reschedule unto itself, should fail
    parameters.clear();
    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 12;
    time.tm_min  = 51;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;

    result.clear();
    try {
        rescheduleMethod->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed to schedule playlist onto itself");
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 1305);
    }
}


/*------------------------------------------------------------------------------
 *  Test to see if rescheduling the currently playing entry works (should not)
 *----------------------------------------------------------------------------*/
void
RescheduleMethodTest :: currentlyPlayingTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  uploadMethod(new UploadPlaylistMethod());
    Ptr<RescheduleMethod>::Ref      rescheduleMethod(new RescheduleMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;
    Ptr<ptime>::Ref                 now;
    Ptr<time_duration>::Ref         duration;
    bool                            gotException;
    Ptr<UniqueId>::Ref              entryId;

    // let's upload something so we can reschedule it
    now   = TimeConversion::now();
    *now += seconds(10);
    TimeConversion::ptimeToTm(now, time);
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;

    result.clear();
    try {
        uploadMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("scheduleEntryId"));
    CPPUNIT_ASSERT(result["scheduleEntryId"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    entryId.reset(new UniqueId(std::string(result["scheduleEntryId"])));

    // wait 10 seconds, so that what we've scheduled is the currently playing
    // entry
    duration.reset(new time_duration(seconds(10)));
    TimeConversion::sleep(duration);

    // now let's try reschedule it, which should fail
    parameters.clear();
    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 12;
    time.tm_min  = 31;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;

    result.clear();
    gotException = false;
    try {
        rescheduleMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);
}

