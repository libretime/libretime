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
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcDisplayAudioClipTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"

#include "RpcDisplayAudioClipTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcDisplayAudioClipTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcDisplayAudioClipTest :: setUp(void)                         throw ()
{
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
RpcDisplayAudioClipTest :: tearDown(void)                      throw ()
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient("localhost", 3344, "/RPC2", false);

    parameters["sessionId"] = sessionId->getId();
    CPPUNIT_ASSERT(xmlRpcClient.execute("logout", parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
RpcDisplayAudioClipTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;

    parameters["sessionId"]   = sessionId->getId();
    parameters["audioClipId"] = "0000000000010001";

    result.clear();
    xmlRpcClient.execute("displayAudioClip", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    CPPUNIT_ASSERT(result.hasMember("id"));
    CPPUNIT_ASSERT(result["id"].getType() == XmlRpcValue::TypeString);
    CPPUNIT_ASSERT(std::string(result["id"]) == "0000000000010001");

    CPPUNIT_ASSERT(result.hasMember("playlength"));
    CPPUNIT_ASSERT(result["playlength"].getType() == XmlRpcValue::TypeInt);
    CPPUNIT_ASSERT(int(result["playlength"]) == 60 * 60);

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  A very simple negative test
 *----------------------------------------------------------------------------*/
void
RpcDisplayAudioClipTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;

    parameters["sessionId"]   = sessionId->getId();
    parameters["audioClipId"] = "0000000000009999";

    result.clear();
    xmlRpcClient.execute("displayAudioClip", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("faultCode"));
    CPPUNIT_ASSERT(int(result["faultCode"]) == 603);    // audio clip not found

    xmlRpcClient.close();
}
