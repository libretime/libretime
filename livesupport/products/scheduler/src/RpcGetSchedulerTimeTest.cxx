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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcGetSchedulerTimeTest.cxx,v $

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
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"
#include "RpcGetSchedulerTimeTest.h"

using namespace std;
using namespace XmlRpc;
using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcGetSchedulerTimeTest);

/**
 *  The name of the configuration file for the scheduler daemon.
 */
static const std::string configFileName = "etc/scheduler.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
RpcGetSchedulerTimeTest :: configure(
            Ptr<Configurable>::Ref      configurable,
            const std::string         & fileName)
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
RpcGetSchedulerTimeTest :: setUp(void)                        throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    if (!daemon->isConfigured()) {
        try {
            configure(daemon, configFileName);
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("semantic error in configuration file");
        } catch (xmlpp::exception &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("error parsing configuration file");
        }
    }

    daemon->install();
//    daemon->start();
//    sleep(5);
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
RpcGetSchedulerTimeTest :: tearDown(void)                     throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

//    daemon->stop();
    daemon->uninstall();
}


/*------------------------------------------------------------------------------
 *  Test a simple query, resulting in an empty result set.
 *----------------------------------------------------------------------------*/
void
RpcGetSchedulerTimeTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue             parameters;
    XmlRpcValue             result;
    struct tm               time1,
                            time2;

    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);

    result.clear();
    xmlRpcClient.execute("getSchedulerTime", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("schedulerTime"));
    time1 = result["schedulerTime"];

    result.clear();
    xmlRpcClient.execute("getSchedulerTime", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("schedulerTime"));
    time2 = result["schedulerTime"];

    CPPUNIT_ASSERT(time1.tm_year == time2.tm_year);
    // could fail on New Year's Eve, but we don't work on New Year's Eve
    
    CPPUNIT_ASSERT(time1.tm_hour <= time2.tm_hour);
    CPPUNIT_ASSERT(time1.tm_min <= time2.tm_min);
    CPPUNIT_ASSERT(time1.tm_min + 1 >= time2.tm_min);

    xmlRpcClient.close();
}

