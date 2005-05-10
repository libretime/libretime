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
    Version  : $Revision: 1.11 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/Attic/DeletePlaylistMethodTest.cxx,v $

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
#include "OpenPlaylistForEditingMethod.h"
#include "SavePlaylistMethod.h"

#include "DeletePlaylistMethod.h"
#include "DeletePlaylistMethodTest.h"


using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(DeletePlaylistMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
DeletePlaylistMethodTest :: setUp(void)                         throw ()
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
DeletePlaylistMethodTest :: tearDown(void)                      throw ()
{
    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  A very simple negative test
 *----------------------------------------------------------------------------*/
void
DeletePlaylistMethodTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<DeletePlaylistMethod>::Ref method(new DeletePlaylistMethod());
    XmlRpc::XmlRpcValue             parameter;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;

    parameter["sessionId"]  = sessionId->getId();
    parameter["playlistId"] = "0000000000009999";
    rootParameter[0] = parameter;

    result.clear();
    try {
        method->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed to delete non-existent playlist");
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 903);   // playlist not found
    }
    
    parameter.clear();
    rootParameter.clear();
    parameter["sessionId"]  = sessionId->getId();
    parameter["playlistId"] = "0000000000000001";
    rootParameter[0] = parameter;

    result.clear();
    try {
        method->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed disabled deletePlaylist method");
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 905);   // method disabled
    }
    
}

