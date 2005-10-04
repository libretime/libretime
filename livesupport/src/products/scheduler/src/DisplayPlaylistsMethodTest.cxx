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
    Location : $URL$

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
#include <vector>
#include <XmlRpcValue.h>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"

#include "SchedulerDaemon.h"
#include "DisplayPlaylistsMethod.h"
#include "DisplayPlaylistsMethodTest.h"


using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(DisplayPlaylistsMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
DisplayPlaylistsMethodTest :: setUp(void)                         throw ()
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
DisplayPlaylistsMethodTest :: tearDown(void)                      throw ()
{
    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
DisplayPlaylistsMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<DisplayPlaylistsMethod>::Ref method(new DisplayPlaylistsMethod());
    XmlRpc::XmlRpcValue       parameters;
    XmlRpc::XmlRpcValue       rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue       result;

    result.clear();
    parameters["sessionId"]  = sessionId->getId();
    rootParameter[0] = parameters;
    try {
        method->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.size() == 3);
    XmlRpc::XmlRpcValue     result0;
    Ptr<Playlist>::Ref      playlist;

    // check the first returned playlist
    result0 = result[0];
    CPPUNIT_ASSERT(result0.hasMember("playlist"));
    CPPUNIT_ASSERT(result0["playlist"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    CPPUNIT_ASSERT_NO_THROW(playlist.reset(new Playlist(result0)));
    CPPUNIT_ASSERT(playlist->getId()->getId() == 1);
    CPPUNIT_ASSERT(playlist->getPlaylength()->total_seconds() == 90 * 60);

    // check the second returned playlist
    result0 = result[1];
    CPPUNIT_ASSERT(result0.hasMember("playlist"));
    CPPUNIT_ASSERT(result0["playlist"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    CPPUNIT_ASSERT_NO_THROW(playlist.reset(new Playlist(result0)));
    CPPUNIT_ASSERT(playlist->getId()->getId() == 2);
    CPPUNIT_ASSERT(playlist->getPlaylength()->total_seconds() == 29);
}
