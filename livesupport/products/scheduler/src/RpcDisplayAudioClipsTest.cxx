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
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcDisplayAudioClipsTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"

#include "RpcDisplayAudioClipsTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcDisplayAudioClipsTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */
                                                        
/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcDisplayAudioClipsTest :: setUp(void)                         throw ()
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
RpcDisplayAudioClipsTest :: tearDown(void)                      throw ()
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
RpcDisplayAudioClipsTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    result.clear();
    parameters["sessionId"]  = sessionId->getId();
    xmlRpcClient.execute("displayAudioClips", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.size() >= 2);

    XmlRpc::XmlRpcValue     result0 = result[0];
    CPPUNIT_ASSERT(result0.hasMember("audioClip"));
    CPPUNIT_ASSERT(result0["audioClip"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    Ptr<AudioClip>::Ref     audioClip;
    CPPUNIT_ASSERT_NO_THROW(audioClip.reset(new AudioClip(result0)));
    CPPUNIT_ASSERT(audioClip->getId()->getId() == 0x10001);
    CPPUNIT_ASSERT(audioClip->getPlaylength()->total_seconds() == 60 * 60);

    XmlRpc::XmlRpcValue     result1 = result[1];
    CPPUNIT_ASSERT(result1.hasMember("audioClip"));
    CPPUNIT_ASSERT(result1["audioClip"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    CPPUNIT_ASSERT_NO_THROW(audioClip.reset(new AudioClip(result1)));
    CPPUNIT_ASSERT(audioClip->getId()->getId() == 0x10002);
    CPPUNIT_ASSERT(audioClip->getPlaylength()->total_seconds() == 30 * 60);

    xmlRpcClient.close();
}

