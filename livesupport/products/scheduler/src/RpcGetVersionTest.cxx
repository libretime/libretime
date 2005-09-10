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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcGetVersionTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"

#include "RpcGetVersionTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcGetVersionTest);

/**
 *  The prefix of the persumed version string.
 */
static const std::string versionPrefix = "LiveSupport Scheduler Daemon";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcGetVersionTest :: setUp(void)                        throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
    daemon->install();
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
RpcGetVersionTest :: tearDown(void)                     throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
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

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    result.clear();
    xmlRpcClient.execute("getVersion", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    CPPUNIT_ASSERT(result.hasMember("version"));
    std::string versionStr = result["version"];
    CPPUNIT_ASSERT(versionStr.find(versionPrefix) == 0);

    xmlRpcClient.close();
}

