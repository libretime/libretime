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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcDisplayPlaylistTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"

#include "RpcDisplayPlaylistTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcDisplayPlaylistTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RpcDisplayPlaylistTest :: setUp(void)                        throw ()
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

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
RpcDisplayPlaylistTest :: tearDown(void)                     throw ()
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    parameters["sessionId"] = sessionId->getId();
    CPPUNIT_ASSERT(xmlRpcClient.execute("logout", parameters, result));
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
RpcDisplayPlaylistTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000000001";

    result.clear();
    xmlRpcClient.execute("displayPlaylist", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    CPPUNIT_ASSERT(result.hasMember("playlist"));
    CPPUNIT_ASSERT(result["playlist"].getType() 
                                        == XmlRpc::XmlRpcValue::TypeString);
    Ptr<Playlist>::Ref  playlist;
    CPPUNIT_ASSERT_NO_THROW(playlist.reset(new Playlist(result)));
    CPPUNIT_ASSERT(playlist->getId()->getId() == 1);
    CPPUNIT_ASSERT(playlist->getPlaylength()->total_seconds() == 90 * 60);

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  A simple negative test.
 *----------------------------------------------------------------------------*/
void
RpcDisplayPlaylistTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                 parameters;
    XmlRpcValue                 result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    parameters["sessionId"]  = sessionId->getId();
    parameters["playlistId"] = "0000000000009999";

    result.clear();
    xmlRpcClient.execute("displayPlaylist", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());

    xmlRpcClient.close();
}
