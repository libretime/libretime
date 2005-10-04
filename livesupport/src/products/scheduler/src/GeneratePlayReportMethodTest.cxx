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
#include <iostream>
#include <XmlRpcValue.h>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"

#include "SchedulerDaemon.h"
#include "PlayLogFactory.h"
#include "UploadPlaylistMethod.h"
#include "GeneratePlayReportMethod.h"
#include "GeneratePlayReportMethodTest.h"

using namespace std;

using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(GeneratePlayReportMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
GeneratePlayReportMethodTest :: setUp(void)                         throw ()
{
    Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();
    try {
        Ptr<StorageClientInterface>::Ref    storage = scheduler->getStorage();
        storage->reset();

        playLog = scheduler->getPlayLog();
        playLog->install();

        insertEntries();

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
GeneratePlayReportMethodTest :: tearDown(void)                      throw ()
{
    playLog->uninstall();

    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
GeneratePlayReportMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<GeneratePlayReportMethod>::Ref method(new GeneratePlayReportMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    // set up a structure for the parameters
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 101;     // 2001
    time.tm_mon  = 10;      // November
    time.tm_mday = 12;
    time.tm_hour = 18;
    time.tm_min  = 31;
    time.tm_sec  =  1;
    parameters["from"] = &time;
    time.tm_year = 101;     // 2001
    time.tm_mon  = 10;      // November
    time.tm_mday = 12;
    time.tm_hour = 19;
    time.tm_min  = 31;
    time.tm_sec  =  1;
    parameters["to"] = &time;
    rootParameter[0] = parameters;

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.size() == 0);
}


/*------------------------------------------------------------------------------
 *  Insert some entries into the play log
 *----------------------------------------------------------------------------*/
void
GeneratePlayReportMethodTest :: insertEntries(void)
                                                            throw ()
{
    Ptr<const UniqueId>::Ref    audioClipId(new UniqueId(10001));
    Ptr<const ptime>::Ref       timestamp(new ptime(time_from_string(
                                               "2004-10-26 14:00:00")));
    playLog->addPlayLogEntry(audioClipId, timestamp);

    audioClipId.reset(new UniqueId(10017));
    timestamp.reset(new ptime(time_from_string("2004-10-26 15:30:00")));
    playLog->addPlayLogEntry(audioClipId, timestamp);

    audioClipId.reset(new UniqueId(10003));
    timestamp.reset(new ptime(time_from_string("2004-10-27 10:01:00")));
    playLog->addPlayLogEntry(audioClipId, timestamp);
}


/*------------------------------------------------------------------------------
 *  Look at some intervals and check against test data
 *----------------------------------------------------------------------------*/
void
GeneratePlayReportMethodTest :: intervalTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<GeneratePlayReportMethod>::Ref method(new GeneratePlayReportMethod());
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    // check for the interval 2004-10-26 between 13 and 15 o'clock
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 104;     // 2004
    time.tm_mon  = 9;       // October
    time.tm_mday = 26;
    time.tm_hour = 13;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 104;     // 2004
    time.tm_mon  = 9;       // October
    time.tm_mday = 26;
    time.tm_hour = 15;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["to"] = &time;
    rootParameter[0] = parameters;

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }

    // check the returned values
    CPPUNIT_ASSERT(result.size() == 1);
    CPPUNIT_ASSERT(result[0].hasMember("audioClipId"));
    CPPUNIT_ASSERT(result[0]["audioClipId"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    UniqueId   newAudioClipId = UniqueId(std::string(result[0]["audioClipId"]));
    CPPUNIT_ASSERT(newAudioClipId.getId() == 10001);

    CPPUNIT_ASSERT(result[0].hasMember("timestamp"));
    CPPUNIT_ASSERT(result[0]["timestamp"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeDateTime);
    time = result[0]["timestamp"];
    CPPUNIT_ASSERT(time.tm_year == 104);    // 2004
    CPPUNIT_ASSERT(time.tm_mon  == 9);      // October
    CPPUNIT_ASSERT(time.tm_mday == 26);
    CPPUNIT_ASSERT(time.tm_hour == 14);
    CPPUNIT_ASSERT(time.tm_min  == 0);
    CPPUNIT_ASSERT(time.tm_sec  == 0);


    // check for the interval 2004-10-26 between 14 o'clock and 15:30
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 104;     // 2004
    time.tm_mon  = 9;       // October
    time.tm_mday = 26;
    time.tm_hour = 14;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 104;     // 2004
    time.tm_mon  = 9;       // October
    time.tm_mday = 26;
    time.tm_hour = 15;
    time.tm_min  = 30;
    time.tm_sec  =  0;
    parameters["to"] = &time;
    rootParameter[0] = parameters;

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }

    // check the returned values
    CPPUNIT_ASSERT(result.size() == 1);
    CPPUNIT_ASSERT(result[0].hasMember("audioClipId"));
    CPPUNIT_ASSERT(result[0]["audioClipId"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    newAudioClipId = UniqueId(std::string(result[0]["audioClipId"]));
    CPPUNIT_ASSERT(newAudioClipId.getId() == 10001);

    CPPUNIT_ASSERT(result[0].hasMember("timestamp"));
    CPPUNIT_ASSERT(result[0]["timestamp"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeDateTime);
    time = result[0]["timestamp"];
    CPPUNIT_ASSERT(time.tm_year == 104);    // 2004
    CPPUNIT_ASSERT(time.tm_mon  == 9);      // October
    CPPUNIT_ASSERT(time.tm_mday == 26);
    CPPUNIT_ASSERT(time.tm_hour == 14);
    CPPUNIT_ASSERT(time.tm_min  == 0);
    CPPUNIT_ASSERT(time.tm_sec  == 0);


    // check for the interval 2004-10-26 15:00 to 2012-08-01 midnight
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 104;     // 2004
    time.tm_mon  = 9;       // October
    time.tm_mday = 26;
    time.tm_hour = 15;
    time.tm_min  = 30;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 112;     // 2012
    time.tm_mon  = 7;       // August
    time.tm_mday =  1;
    time.tm_hour =  0;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["to"] = &time;
    rootParameter[0] = parameters;

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }

    // check the returned values
    CPPUNIT_ASSERT(result.size() == 2);
    CPPUNIT_ASSERT(result[0].hasMember("audioClipId"));
    CPPUNIT_ASSERT(result[0]["audioClipId"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    newAudioClipId = UniqueId(std::string(result[0]["audioClipId"]));
    CPPUNIT_ASSERT(newAudioClipId.getId() == 10017);

    CPPUNIT_ASSERT(result[0].hasMember("timestamp"));
    CPPUNIT_ASSERT(result[0]["timestamp"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeDateTime);
    time = result[0]["timestamp"];
    CPPUNIT_ASSERT(time.tm_year == 104);    // 2004
    CPPUNIT_ASSERT(time.tm_mon  == 9);      // October
    CPPUNIT_ASSERT(time.tm_mday == 26);
    CPPUNIT_ASSERT(time.tm_hour == 15);
    CPPUNIT_ASSERT(time.tm_min  == 30);
    CPPUNIT_ASSERT(time.tm_sec  == 0);

    CPPUNIT_ASSERT(result[1].hasMember("audioClipId"));
    CPPUNIT_ASSERT(result[1]["audioClipId"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    newAudioClipId = UniqueId(std::string(result[1]["audioClipId"]));
    CPPUNIT_ASSERT(newAudioClipId.getId() == 10003);

    CPPUNIT_ASSERT(result[1].hasMember("timestamp"));
    CPPUNIT_ASSERT(result[1]["timestamp"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeDateTime);
    time = result[1]["timestamp"];
    CPPUNIT_ASSERT(time.tm_year == 104);    // 2004
    CPPUNIT_ASSERT(time.tm_mon  == 9);      // October
    CPPUNIT_ASSERT(time.tm_mday == 27);
    CPPUNIT_ASSERT(time.tm_hour == 10);
    CPPUNIT_ASSERT(time.tm_min  == 01);
    CPPUNIT_ASSERT(time.tm_sec  == 0);


    // check for the interval 2004-10-26 16 o'clock to 2004-10-27 10 o'clock
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 104;     // 2004
    time.tm_mon  = 9;       // October
    time.tm_mday = 26;
    time.tm_hour = 16;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 104;     // 2004
    time.tm_mon  = 9;       // October
    time.tm_mday = 27;
    time.tm_hour = 10;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["to"] = &time;
    rootParameter[0] = parameters;

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }

    // check the returned values
    CPPUNIT_ASSERT(result.size() == 0);
}


