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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcGetVersionTest.cxx,v $

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
#include "RpcGetVersionTest.h"

using namespace std;
using namespace XmlRpc;
using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcGetVersionTest);

/**
 *  The name of the configuration file for the scheduler daemon.
 */
static const std::string configFileName = "etc/scheduler.xml";

/**
 *  The prefix of the persumed version string.
 */
static const std::string versionPrefix = "LiveSupport Scheduler Daemon";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
RpcGetVersionTest :: configure(
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
RpcGetVersionTest :: setUp(void)                        throw ()
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
RpcGetVersionTest :: tearDown(void)                     throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

//    daemon->stop();
    daemon->uninstall();
}


/*------------------------------------------------------------------------------
 *  Test a simple upload.
 *----------------------------------------------------------------------------*/
void
RpcGetVersionTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;

    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);

    result.clear();
    xmlRpcClient.execute("getVersion", parameters, result);
    CPPUNIT_ASSERT(!result.hasMember("errorCode"));
    CPPUNIT_ASSERT(result.hasMember("version"));
    std::string versionStr = result["version"];
    CPPUNIT_ASSERT(versionStr.find(versionPrefix) == 0);
}

