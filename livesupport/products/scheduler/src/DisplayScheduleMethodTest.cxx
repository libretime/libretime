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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.9 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/DisplayScheduleMethodTest.cxx,v $

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

#include "ScheduleFactory.h"
#include "UploadPlaylistMethod.h"
#include "DisplayScheduleMethod.h"
#include "DisplayScheduleMethodTest.h"

using namespace std;
using namespace XmlRpc;

using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(DisplayScheduleMethodTest);

/**
 *  The name of the configuration file for the storage client factory.
 */
const std::string DisplayScheduleMethodTest::storageClientConfig =
                                                    "etc/storageClient.xml";

/**
 *  The name of the configuration file for the connection manager factory.
 */
const std::string DisplayScheduleMethodTest::connectionManagerConfig =
                                          "etc/connectionManagerFactory.xml";

/**
 *  The name of the configuration file for the schedule factory.
 */
const std::string DisplayScheduleMethodTest::scheduleConfig =
                                            "etc/scheduleFactory.xml";

/**
 *  The name of the configuration file for the authentication client factory.
 */
const std::string DisplayScheduleMethodTest::authenticationClientConfig =
                                          "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethodTest :: configure(
            Ptr<Configurable>::Ref      configurable,
            const std::string           fileName)
                                                throw (std::invalid_argument,
                                                       xmlpp::exception)
{
    Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser(fileName, true));
    const xmlpp::Document * document = parser->get_document();
    const xmlpp::Element  * root     = document->get_root_node();

    configurable->configure(*root);
}

                                                        
/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethodTest :: setUp(void)                         throw ()
{
    Ptr<AuthenticationClientFactory>::Ref acf;
    try {
        Ptr<StorageClientFactory>::Ref scf
                                        = StorageClientFactory::getInstance();
        configure(scf, storageClientConfig);

        Ptr<ConnectionManagerFactory>::Ref
                    cmf = ConnectionManagerFactory::getInstance();
        configure(cmf, connectionManagerConfig);

        Ptr<ScheduleFactory>::Ref
                    sf = ScheduleFactory::getInstance();
        configure(sf, scheduleConfig);
        schedule = sf->getSchedule();
        schedule->install();

        acf = AuthenticationClientFactory::getInstance();
        configure(acf, authenticationClientConfig);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    authentication = acf->getAuthenticationClient();
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        std::string eMsg = "could not log in:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    insertEntries();    // this can only be called after sessionId is obtained
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethodTest :: tearDown(void)                      throw ()
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
DisplayScheduleMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<DisplayScheduleMethod>::Ref method(new DisplayScheduleMethod());
    XmlRpcValue     parameters;
    XmlRpcValue     rootParameter;
    rootParameter.setSize(1);
    XmlRpcValue     result;
    struct tm       time;

    // set up a structure for the parameters
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 18;
    time.tm_min  = 31;
    time.tm_sec  =  1;
    parameters["from"] = &time;
    time.tm_year = 2001;
    time.tm_mon  = 11;
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
 *  Insert some entries into the schedule
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethodTest :: insertEntries(void)
                                                            throw ()
{
    Ptr<UploadPlaylistMethod>::Ref  method(new UploadPlaylistMethod());
    XmlRpcValue     parameters;
    XmlRpcValue     rootParameter;
    rootParameter.setSize(1);
    XmlRpcValue     result;
    struct tm       time;

    // insert a playlist for 2004-07-31, at 10 o'clock
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour = 10;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;
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

    // insert a playlist for 2004-07-31, at 12 o'clock
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour = 12;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;
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

    // insert a playlist for 2004-07-31, at 14 o'clock
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour = 14;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;
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
}

 
/*------------------------------------------------------------------------------
 *  Look at some intervals and check against test data
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethodTest :: intervalTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<DisplayScheduleMethod>::Ref method(new DisplayScheduleMethod());
    XmlRpcValue     parameters;
    XmlRpcValue     rootParameter;
    rootParameter.setSize(1);
    XmlRpcValue     result;
    struct tm       time;

    // check for the interval 2004-07-31 between 9 and 11 o'clock
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour =  9;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour = 11;
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
    CPPUNIT_ASSERT(result[0].hasMember("playlistId"));
    CPPUNIT_ASSERT(result[0]["playlistId"].getType() 
                                                  == XmlRpcValue::TypeString);
    CPPUNIT_ASSERT(std::string(result[0]["playlistId"]) == "0000000000000001");
    time = result[0]["start"];
    CPPUNIT_ASSERT(time.tm_year == 2004);
    CPPUNIT_ASSERT(time.tm_mon == 7);
    CPPUNIT_ASSERT(time.tm_mday == 31);
    CPPUNIT_ASSERT(time.tm_hour == 10);
    CPPUNIT_ASSERT(time.tm_min == 0);
    CPPUNIT_ASSERT(time.tm_sec == 0);
    time = result[0]["end"];
    CPPUNIT_ASSERT(time.tm_year == 2004);
    CPPUNIT_ASSERT(time.tm_mon == 7);
    CPPUNIT_ASSERT(time.tm_mday == 31);
    CPPUNIT_ASSERT(time.tm_hour == 11);
    CPPUNIT_ASSERT(time.tm_min == 30);
    CPPUNIT_ASSERT(time.tm_sec == 0);

    // check for the interval 2004-07-31 between 9 and 13 o'clock
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour =  9;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour = 13;
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
    CPPUNIT_ASSERT(result[0].hasMember("playlistId"));
    CPPUNIT_ASSERT(result[0]["playlistId"].getType() 
                                                  == XmlRpcValue::TypeString);
    CPPUNIT_ASSERT(std::string(result[0]["playlistId"]) == "0000000000000001");
    time = result[0]["start"];
    CPPUNIT_ASSERT(time.tm_year == 2004);
    CPPUNIT_ASSERT(time.tm_mon == 7);
    CPPUNIT_ASSERT(time.tm_mday == 31);
    CPPUNIT_ASSERT(time.tm_hour == 10);
    CPPUNIT_ASSERT(time.tm_min == 0);
    CPPUNIT_ASSERT(time.tm_sec == 0);
    time = result[0]["end"];
    CPPUNIT_ASSERT(time.tm_year == 2004);
    CPPUNIT_ASSERT(time.tm_mon == 7);
    CPPUNIT_ASSERT(time.tm_mday == 31);
    CPPUNIT_ASSERT(time.tm_hour == 11);
    CPPUNIT_ASSERT(time.tm_min == 30);
    CPPUNIT_ASSERT(time.tm_sec == 0);

    CPPUNIT_ASSERT(result[1].hasMember("playlistId"));
    CPPUNIT_ASSERT(result[1]["playlistId"].getType() 
                                                  == XmlRpcValue::TypeString);
    CPPUNIT_ASSERT(std::string(result[1]["playlistId"]) == "0000000000000001");
    time = result[1]["start"];
    CPPUNIT_ASSERT(time.tm_year == 2004);
    CPPUNIT_ASSERT(time.tm_mon == 7);
    CPPUNIT_ASSERT(time.tm_mday == 31);
    CPPUNIT_ASSERT(time.tm_hour == 12);
    CPPUNIT_ASSERT(time.tm_min == 0);
    CPPUNIT_ASSERT(time.tm_sec == 0);
    time = result[1]["end"];
    CPPUNIT_ASSERT(time.tm_year == 2004);
    CPPUNIT_ASSERT(time.tm_mon == 7);
    CPPUNIT_ASSERT(time.tm_mday == 31);
    CPPUNIT_ASSERT(time.tm_hour == 13);
    CPPUNIT_ASSERT(time.tm_min == 30);
    CPPUNIT_ASSERT(time.tm_sec == 0);

    // check for the interval 2004-07-31 between 8 and 9 o'clock
    parameters["sessionId"]  = sessionId->getId();
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour =  8;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 2004;
    time.tm_mon  =  7;
    time.tm_mday = 31;
    time.tm_hour =  9;
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


