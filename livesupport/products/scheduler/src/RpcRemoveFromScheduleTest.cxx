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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcRemoveFromScheduleTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"

#include "RpcRemoveFromScheduleTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcRemoveFromScheduleTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcRemoveFromScheduleTest :: setUp(void)                        throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
    daemon->install();

    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient("localhost", 3344, "/RPC2", false);

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
RpcRemoveFromScheduleTest :: tearDown(void)                     throw ()
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient("localhost", 3344, "/RPC2", false);

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
RpcRemoveFromScheduleTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;
    struct tm                   time;

    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);

    // first schedule a playlist, so that there is something to remove
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
    parameters["scheduleEntryId"] = std::string(*entryId);

    result.clear();
    xmlRpcClient.execute("removeFromSchedule", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  A simple negative test.
 *----------------------------------------------------------------------------*/
void
RpcRemoveFromScheduleTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;

    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);

    parameters["sessionId"]       = sessionId->getId();
    parameters["scheduleEntryId"] = "0000000000009999";

    result.clear();
    xmlRpcClient.execute("removeFromSchedule", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());

    xmlRpcClient.close();
}

