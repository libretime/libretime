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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/Attic/SchedulerDaemonDisplayPlaylistTest.cxx,v $

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
#include "SchedulerDaemonDisplayPlaylistTest.h"


using namespace XmlRpc;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SchedulerDaemonDisplayPlaylistTest);

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
SchedulerDaemonDisplayPlaylistTest :: setUp(void)                        throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    if (!daemon->isConfigured()) {
        try {
            std::auto_ptr<xmlpp::DomParser> 
                             parser(new xmlpp::DomParser(configFileName, true));
            const xmlpp::Document * document = parser->get_document();
            daemon->configure(*(document->get_root_node()));
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
SchedulerDaemonDisplayPlaylistTest :: tearDown(void)                     throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

//    daemon->stop();
    daemon->uninstall();
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonDisplayPlaylistTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;

    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);

    parameters["playlistId"] = 1;

    xmlRpcClient.execute("displayPlaylist", parameters, result);
    CPPUNIT_ASSERT(result.valid());
    CPPUNIT_ASSERT(((int) result["id"]) == 1);
    CPPUNIT_ASSERT(((int) result["playlength"]) == (60 * 60));
}


/*------------------------------------------------------------------------------
 *  A simple negative test.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonDisplayPlaylistTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;

    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);

    parameters["playlistId"] = 9999;

    xmlRpcClient.execute("displayPlaylist", parameters, result);
    CPPUNIT_ASSERT(result.valid());
    CPPUNIT_ASSERT(((bool)result) == false);
}

