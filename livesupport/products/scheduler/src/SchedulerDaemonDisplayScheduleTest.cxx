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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/Attic/SchedulerDaemonDisplayScheduleTest.cxx,v $

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
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "SchedulerDaemonDisplayScheduleTest.h"

using namespace std;
using namespace XmlRpc;
using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SchedulerDaemonDisplayScheduleTest);

/**
 *  The name of the configuration file for the scheduler daemon.
 */
static const std::string configFileName = "etc/scheduler.xml";

/**
 *  The name of the configuration file for the authentication client factory.
 */
static const std::string authenticationClientConfigFileName =
                                          "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonDisplayScheduleTest :: configure(
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
SchedulerDaemonDisplayScheduleTest :: setUp(void)                        throw ()
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

    try {
        Ptr<AuthenticationClientFactory>::Ref acf;
        acf = AuthenticationClientFactory::getInstance();
        configure(acf, authenticationClientConfigFileName);
        authentication = acf->getAuthenticationClient();
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("semantic error in authentication configuration file");
    } catch (xmlpp::exception &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("error parsing authentication configuration file");
    }
    
    if (!(sessionId = authentication->login("root", "q"))) {
        CPPUNIT_FAIL("could not log in to authentication server");
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonDisplayScheduleTest :: tearDown(void)                     throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

//    daemon->stop();
    daemon->uninstall();
    
    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Test a simple query, resulting in an empty result set.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonDisplayScheduleTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;
    struct tm                   time;

    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);

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
    CPPUNIT_ASSERT(!result.hasMember("errorCode"));
    CPPUNIT_ASSERT(result.size() == 0);
}

