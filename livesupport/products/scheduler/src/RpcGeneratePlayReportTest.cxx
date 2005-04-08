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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcGeneratePlayReportTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "LiveSupport/Core/UniqueId.h"
#include "SchedulerDaemon.h"
#include "PlayLogFactory.h"

#include "RpcGeneratePlayReportTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcGeneratePlayReportTest);

/**
 *  The name of the configuration file for the scheduler daemon
 */
static const std::string configFileName = "etc/scheduler.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcGeneratePlayReportTest :: setUp(void)                         throw ()
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

    daemon->install();
    insertEntries();

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
RpcGeneratePlayReportTest :: tearDown(void)                      throw ()
{
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

    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
    daemon->uninstall();
}


/*------------------------------------------------------------------------------
 *  Insert some entries into the play log
 *----------------------------------------------------------------------------*/
void
RpcGeneratePlayReportTest :: insertEntries(void)
                                                            throw ()
{
    Ptr<PlayLogFactory>::Ref    plf = PlayLogFactory::getInstance();
    Ptr<PlayLogInterface>::Ref  playLog = plf->getPlayLog();

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
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
RpcGeneratePlayReportTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

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

    CPPUNIT_ASSERT(xmlRpcClient.execute("generatePlayReport", 
                                        parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.size() == 0);

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Look at some intervals and check against test data
 *----------------------------------------------------------------------------*/
void
RpcGeneratePlayReportTest :: intervalTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

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

    CPPUNIT_ASSERT(xmlRpcClient.execute("generatePlayReport", 
                                        parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

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

    result.clear();
    CPPUNIT_ASSERT(xmlRpcClient.execute("generatePlayReport", 
                                        parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

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

    result.clear();
    CPPUNIT_ASSERT(xmlRpcClient.execute("generatePlayReport", 
                                        parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

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

    result.clear();
    CPPUNIT_ASSERT(xmlRpcClient.execute("generatePlayReport", 
                                        parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    // check the returned values
    CPPUNIT_ASSERT(result.size() == 0);

    xmlRpcClient.close();
}

