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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/DisplayAudioClipMethodTest.cxx,v $

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
#include <iostream>
#include <XmlRpcValue.h>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"

#include "SchedulerDaemon.h"
#include "DisplayAudioClipMethod.h"
#include "DisplayAudioClipMethodTest.h"


using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(DisplayAudioClipMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
DisplayAudioClipMethodTest :: setUp(void)                         throw ()
{
    Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();
    try {
        Ptr<StorageClientInterface>::Ref    storage = scheduler->getStorage();
        storage->reset();

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    authentication = scheduler->getAuthentication();
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        std::string eMsg = "could not log in:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
DisplayAudioClipMethodTest :: tearDown(void)                      throw ()
{
    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
DisplayAudioClipMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<DisplayAudioClipMethod>::Ref method(new DisplayAudioClipMethod());
    XmlRpc::XmlRpcValue             parameter;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;

    // set up a structure for the parameter
    parameter["sessionId"]   = sessionId->getId();
    parameter["audioClipId"] = "0000000000010001";
    rootParameter[0] = parameter;

    result.clear();
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("audioClip"));
    CPPUNIT_ASSERT(result["audioClip"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    Ptr<AudioClip>::Ref  audioClip;
    CPPUNIT_ASSERT_NO_THROW(audioClip.reset(new AudioClip(result)));
    CPPUNIT_ASSERT(audioClip->getId()->getId() == 0x10001);
    CPPUNIT_ASSERT(audioClip->getPlaylength()->total_seconds() == 11);
}


/*------------------------------------------------------------------------------
 *  A very simple negative test
 *----------------------------------------------------------------------------*/
void
DisplayAudioClipMethodTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<DisplayAudioClipMethod>::Ref method(new DisplayAudioClipMethod());
    XmlRpc::XmlRpcValue             parameter;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;

    // set up a structure for the parameter
    parameter["sessionId"]   = sessionId->getId();
    parameter["audioClipId"] = "0000000000009999";
    rootParameter[0] = parameter;

    result.clear();
    try {
        method->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed to display non-existent audio clip");
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 603);    // audio clip not found
    }
}
