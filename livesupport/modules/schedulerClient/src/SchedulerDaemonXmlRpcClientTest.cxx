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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/src/SchedulerDaemonXmlRpcClientTest.cxx,v $

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
#include <fstream>
#include <iostream>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "SchedulerDaemonXmlRpcClientTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::SchedulerClient;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SchedulerDaemonXmlRpcClientTest);

/**
 *  The name of the configuration file for the scheduler client.
 */
static const std::string configFileName = "etc/schedulerDaemonXmlRpcClient.xml";

/**
 *  The name of the configuration file for the authentication client factory.
 */
static const std::string authenticationClientConfigFileName =
                                          "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------ *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/void
SchedulerDaemonXmlRpcClientTest :: configure(
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
SchedulerDaemonXmlRpcClientTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        schedulerClient.reset(new SchedulerDaemonXmlRpcClient());
        schedulerClient->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }

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
SchedulerDaemonXmlRpcClientTest :: tearDown(void)                      throw ()
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
 *  Test to see if we can get the version string of the scheduler.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: getVersionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<const std::string>::Ref     version = schedulerClient->getVersion();

        CPPUNIT_ASSERT(version.get());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test to see if we can get the time of the scheduler.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: getSchedulerTimeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<const ptime>::Ref   time = schedulerClient->getSchedulerTime();
        Ptr<const ptime>::Ref   now  = TimeConversion::now();

        CPPUNIT_ASSERT(time.get());
        // assume that the scheduler and the client is in the same year
        // this can break at new year's eve - so don't run the test then :)
        CPPUNIT_ASSERT(time->date().year() == now->date().year());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test the displaySchedule XML-RPC method, when the schedule is empty
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: displayScheduleEmptyTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
        Ptr<ptime>::Ref                                     from;
        Ptr<ptime>::Ref                                     to;

        // check from now until 1 hour later
        from = TimeConversion::now();
        to.reset(new ptime(*from + hours(1)));

        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->empty());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test some simple playlist operations.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: displayPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<Playlist>::Ref      playlist;
    Ptr<UniqueId>::Ref      playlistId;

    // the test assumes that 
    //  * there is a playlist with the id of 1
    //  * there is no playlist with the id of 9999
    // in the storage accessed by the scheduler daemon

    playlistId.reset(new UniqueId(1));
    CPPUNIT_ASSERT_NO_THROW(
        playlist = schedulerClient->displayPlaylist(sessionId, playlistId)
    );
    CPPUNIT_ASSERT(playlist->getId()->getId() == 1);
    
    playlistId.reset(new UniqueId(9999));
    CPPUNIT_ASSERT_THROW(
        playlist = schedulerClient->displayPlaylist(sessionId, playlistId),
        Core::XmlRpcMethodFaultException
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        playlist = schedulerClient->createPlaylist(sessionId)
    );
    CPPUNIT_ASSERT(playlistId->getId() >= 0);

    playlistId = playlist->getId();
    CPPUNIT_ASSERT_NO_THROW(
        playlist = schedulerClient->displayPlaylist(sessionId, playlistId)
    );
    CPPUNIT_ASSERT(*playlist->getId() == *playlistId);
    CPPUNIT_ASSERT(playlist->getPlaylength()->total_seconds() == 0);

// This doesn't work yet: createPlaylist() opens the playlist for editing,
// and so far we have no way of saving it.
/*
    CPPUNIT_ASSERT_NO_THROW(
        schedulerClient->deletePlaylist(sessionId, playlistId)
    );

    CPPUNIT_ASSERT_THROW(
        playlist = schedulerClient->displayPlaylist(sessionId, playlistId),
        Core::XmlRpcMethodFaultException
    );
*/
}


/*------------------------------------------------------------------------------
 *  Test playlist management functions.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: playlistMgmtTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
        Ptr<ScheduleEntry>::Ref                             entry;
        Ptr<UniqueId>::Ref                                  entryId;
        Ptr<UniqueId>::Ref                                  playlistId;
        Ptr<ptime>::Ref                                     now;
        Ptr<ptime>::Ref                                     playtime;
        Ptr<ptime>::Ref                                     from;
        Ptr<ptime>::Ref                                     to;

        now = TimeConversion::now();
        // make sure now is only second resolution, not micro-second
        long    fsec = now->time_of_day().fractional_seconds();
        now.reset(new ptime(*now - microsec(fsec)));

        // the test assumes that there's a playlist with the id of 1 in
        // the storage accessed by the scheduler

        // schedule playlist #1 for one hour from now
        playlistId.reset(new UniqueId(1));
        playtime.reset(new ptime(*now + hours(1)));

        entryId = schedulerClient->uploadPlaylist(sessionId,
                                                  playlistId,
                                                  playtime);

        // now check if our playlist has indeed been scheduled
        from = now;
        to.reset(new ptime(*from + hours(2)));

        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->size() == 1);
        entry = (*entries)[0];
        CPPUNIT_ASSERT(*entry->getId() == *entryId);
        CPPUNIT_ASSERT(*entry->getPlaylistId() == *playlistId);
        CPPUNIT_ASSERT(*entry->getStartTime() == *playtime);


        // and now, remove the entry, and see that it's not there anymore
        schedulerClient->removeFromSchedule(sessionId, entryId);
        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->empty());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test for some XML-RPC error conditions
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: xmlRpcErrorTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
    Ptr<ScheduleEntry>::Ref                             entry;
    Ptr<UniqueId>::Ref                                  entryId;
    Ptr<UniqueId>::Ref                                  playlistId;
    Ptr<ptime>::Ref                                     now;
    Ptr<ptime>::Ref                                     playtime;
    bool                                                gotException;

    try {
        now = TimeConversion::now();
        // make sure now is only second resolution, not micro-second
        long    fsec = now->time_of_day().fractional_seconds();
        now.reset(new ptime(*now - microsec(fsec)));

        // the test assumes that there's a playlist with the id of 1 in
        // the storage accessed by the scheduler

        // schedule playlist #1 for one hour from now
        playlistId.reset(new UniqueId(1));
        playtime.reset(new ptime(*now + hours(1)));

        entryId = schedulerClient->uploadPlaylist(sessionId,
                                                  playlistId,
                                                  playtime);
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    gotException = false;
    try {
        // try to upload the same entry again, for the same time
        // this should result in an error
        schedulerClient->uploadPlaylist(sessionId, playlistId, playtime);
    } catch (Core::XmlRpcMethodFaultException &e) {
        gotException = true;
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(gotException);

    try {
        // and now, remove the entry, and see that it's not there anymore
        Ptr<ptime>::Ref     from = now;
        Ptr<ptime>::Ref     to(new ptime(*from + hours(2)));

        schedulerClient->removeFromSchedule(sessionId, entryId);
        entries = schedulerClient->displaySchedule(sessionId, from, to);
        CPPUNIT_ASSERT(entries->empty());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    gotException = false;
    try {
        // and now, try to remove it again, which should result in an
        // exception
        schedulerClient->removeFromSchedule(sessionId, entryId);
    } catch (Core::XmlRpcMethodFaultException &e) {
        gotException = true;
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(gotException);
}

