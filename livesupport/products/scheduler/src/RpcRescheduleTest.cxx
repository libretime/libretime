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
    Version  : $Revision: 1.9 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcRescheduleTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"
#include "LiveSupport/Core/TimeConversion.h"

#include "RpcRescheduleTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcRescheduleTest);

/**
 *  The name of the configuration file for the scheduler daemon.
 */
static const std::string configFileName = "etc/scheduler.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcRescheduleTest :: setUp(void)                        throw ()
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
RpcRescheduleTest :: tearDown(void)                     throw ()
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
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
RpcRescheduleTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;
    struct tm               time;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    // first schedule a playlist, so that there is something to reschedule
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 10;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;

    result.clear();
    xmlRpcClient.execute("uploadPlaylist", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("scheduleEntryId"));
    CPPUNIT_ASSERT(result["scheduleEntryId"].getType() 
                                                == XmlRpcValue::TypeString);
    Ptr<UniqueId>::Ref  entryId(new UniqueId(std::string(
                                                result["scheduleEntryId"] )));

    // now reschedule it
    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour =  8;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;

    result.clear();
    xmlRpcClient.execute("reschedule", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    // now reschedule it unto itself, should fail
    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour =  8;
    time.tm_min  = 30;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;

    result.clear();
    xmlRpcClient.execute("reschedule", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  A simple negative test.
 *----------------------------------------------------------------------------*/
void
RpcRescheduleTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = "0000000000009999";

    result.clear();
    xmlRpcClient.execute("removeFromSchedule", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  A test to see if the currently playing entry can be rescheduled (should not)
 *----------------------------------------------------------------------------*/
void
RpcRescheduleTest :: currentlyPlayingTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue         parameters;
    XmlRpc::XmlRpcValue         result;
    Ptr<ptime>::Ref             now;
    struct tm                   time;
    Ptr<time_duration>::Ref     duration;
    Ptr<UniqueId>::Ref          entryId;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    // first schedule (upload) a playlist, for 10 seconds from now
    now   = TimeConversion::now();
    *now += seconds(10);
    TimeConversion::ptimeToTm(now, time);
    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";
    parameters["playtime"] = &time;

    result.clear();
    xmlRpcClient.execute("uploadPlaylist", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("scheduleEntryId"));
    CPPUNIT_ASSERT(result["scheduleEntryId"].getType() 
                                                == XmlRpcValue::TypeString);
    entryId.reset(new UniqueId(std::string(result["scheduleEntryId"] )));

    // wait 10 seconds, so that what we've scheduled is the currently playing
    // entry
    duration.reset(new time_duration(seconds(10)));
    TimeConversion::sleep(duration);

    // now try to reschedule it, should faile
    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = std::string(*entryId);
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour =  8;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["playtime"] = &time;

    result.clear();
    xmlRpcClient.execute("reschedule", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());

    xmlRpcClient.close();
}


