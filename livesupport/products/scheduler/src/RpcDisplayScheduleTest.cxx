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
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcDisplayScheduleTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"

#include "RpcDisplayScheduleTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcDisplayScheduleTest);

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
RpcDisplayScheduleTest :: setUp(void)                        throw ()
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
RpcDisplayScheduleTest :: tearDown(void)                     throw ()
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
 *  Test a simple query, resulting in an empty result set.
 *----------------------------------------------------------------------------*/
void
RpcDisplayScheduleTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;
    struct tm                   time;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    // list the schedules for an interval (as the database is empty,
    // it's going to return an empty result set)
    parameters["sessionId"] = sessionId->getId();
    time.tm_year = 2044;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 10;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 2044;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 11;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["to"] = &time;

    result.clear();
    xmlRpcClient.execute("displaySchedule", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.size() == 0);

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Testing some error conditions.
 *----------------------------------------------------------------------------*/
void
RpcDisplayScheduleTest :: faultTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    result.clear();
    xmlRpcClient.execute("displaySchedule", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("faultCode"));
    CPPUNIT_ASSERT(int(result["faultCode"]) == 1101);

    parameters.clear();
    parameters["sessionId"] = sessionId->getId();
    parameters["from"]      = "the beginning";
    parameters["to"]        = "the end";

    result.clear();
    xmlRpcClient.execute("displaySchedule", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("faultCode"));
    CPPUNIT_ASSERT(int(result["faultCode"]) == 1102);
/*
    struct tm                   time;

    parameters.clear();
    parameters["sessionId"] = sessionId->getId();
    time.tm_year = 2044;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 10;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["from"] = &time;
    time.tm_year = 2044;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 11;
    time.tm_min  =  0;
    time.tm_sec  =  0;
    parameters["to"] = &time;

    result.clear();
    xmlRpcClient.execute("displaySchedule", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.size() == 0);
*/
    xmlRpcClient.close();
}

