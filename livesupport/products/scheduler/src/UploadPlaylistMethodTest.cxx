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
#include <sstream>
#include <iostream>
#include <XmlRpcValue.h>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "ScheduleFactory.h"

#include "UploadPlaylistMethod.h"
#include "UploadPlaylistMethodTest.h"

using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(UploadPlaylistMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethodTest :: setUp(void)                         throw ()
{
    Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();
    try {
        Ptr<StorageClientInterface>::Ref    storage = scheduler->getStorage();
        storage->reset();

        schedule = scheduler->getSchedule();
        schedule->install();

    } catch (XmlRpcException &e) {
        std::cerr << "caught XmlRpcException durng setUp" << std::endl
                  << e.what() << std::endl;
    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in configuration file" << std::endl;
    } catch (xmlpp::exception &e) {
        std::cerr << "error parsing configuration file" << std::endl;
    } catch (std::exception &e) {
        std::cerr << e.what() << std::endl;
    }
    
    authentication = scheduler->getAuthentication();
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        std::cerr <<  "could not log in:" << std::endl
                  << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethodTest :: tearDown(void)                      throw ()
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
UploadPlaylistMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  method(new UploadPlaylistMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    // set up a structure for the parameters
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 18;
    time.tm_min  = 31;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0]        = parameters;    

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("scheduleEntryId"));
}


/*------------------------------------------------------------------------------
 *  Try to upload overlapping playlists, and see them fail.
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethodTest :: overlappingPlaylists(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  method(new UploadPlaylistMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    // load the first playlist, this will succeed
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 10;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;
    rootParameter[0]        = parameters;    

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("scheduleEntryId"));

    // try to load the same one, but in an overlapping time region
    // (we know that playlist with id 1 is 1 hour long)
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 10;
    time.tm_min  = 30;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;    

    result.clear();
    try {
        method->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed to schedule overlapping playlist");
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 1405);    // timeframe not available
    }

    // try to load the same one, but now in good timing
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 11;
    time.tm_min  = 30;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;    

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("scheduleEntryId"));

    // try to load the same one, this time overlapping both previos instances
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 10;
    time.tm_min  = 45;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;
    rootParameter[0]       = parameters;    

    result.clear();
    try {
        method->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed to schedule doubly overlapping playlist");
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 1405);    // timeframe not available
    }
}
