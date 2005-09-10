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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RemoveFromScheduleMethodTest.cxx,v $

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

#include "ScheduleFactory.h"
#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"

#include "SchedulerDaemon.h"
#include "UploadPlaylistMethod.h"
#include "RemoveFromScheduleMethod.h"
#include "RemoveFromScheduleMethodTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Authentication;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RemoveFromScheduleMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RemoveFromScheduleMethodTest :: setUp(void)                         throw ()
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
RemoveFromScheduleMethodTest :: tearDown(void)                      throw ()
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
RemoveFromScheduleMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  uploadMethod(
                                    new UploadPlaylistMethod());
    Ptr<RemoveFromScheduleMethod>::Ref  removeMethod(
                                    new RemoveFromScheduleMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;
    Ptr<UniqueId>::Ref              entryId;

    // first schedule (upload) a playlist
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 10;
    time.tm_min  =  0;
    time.tm_sec  =  0;
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

    parameters.clear();
    parameters["sessionId"]  = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    rootParameter[0]              = parameters;

    result.clear();
    try {
        removeMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  A test to try to remove a not-scheduled entry
 *----------------------------------------------------------------------------*/
void
RemoveFromScheduleMethodTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<RemoveFromScheduleMethod>::Ref  removeMethod(
                                        new RemoveFromScheduleMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    Ptr<UniqueId>::Ref              entryId(new UniqueId(9999));

    parameters["sessionId"]  = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    rootParameter[0]              = parameters;

    result.clear();
    try {
        removeMethod->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed to remove non-existent schedule entry");
    } catch (XmlRpc::XmlRpcException &e) {
    }
}


/*------------------------------------------------------------------------------
 *  A test to try to remove a currently playing entry.
 *----------------------------------------------------------------------------*/
void
RemoveFromScheduleMethodTest :: currentlyPlayingTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  uploadMethod(
                                    new UploadPlaylistMethod());
    Ptr<RemoveFromScheduleMethod>::Ref  removeMethod(
                                    new RemoveFromScheduleMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    Ptr<ptime>::Ref                 now;
    struct tm                       time;
    Ptr<time_duration>::Ref         duration;
    Ptr<UniqueId>::Ref              entryId;
    bool                            gotException;

    // first schedule (upload) a playlist, for 10 seconds from now
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

    // now try to remove what we've scheduled, this should fail
    parameters.clear();
    parameters["sessionId"]  = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    rootParameter[0]              = parameters;

    result.clear();
    gotException = false;
    try {
        removeMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);
}


