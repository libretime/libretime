/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund

    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org

    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/branches/scheduler_export/livesupport/src/products/scheduler/src/RpcStopCurrentlyPlayingTest.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"
#include "LiveSupport/Core/TimeConversion.h"

#include "RpcStopCurrentlyPlayingTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcStopCurrentlyPlayingTest);

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
RpcStopCurrentlyPlayingTest :: setUp(void)      throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    if (!daemon->isConfigured()) {
        try {
            Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser(
                                                        configFileName, true));
            const xmlpp::Document * document = parser->get_document();
            const xmlpp::Element  * root     = document->get_root_node();
            daemon->configure(*root);
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("semantic error in configuration file");
        } catch (xmlpp::exception &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("error parsing configuration file");
        }
    }

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
RpcStopCurrentlyPlayingTest :: tearDown(void)   throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    CPPUNIT_ASSERT(sessionId);

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
RpcStopCurrentlyPlayingTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    schedulePlaylistToPlayNow();

    Ptr<SchedulerDaemon>::Ref       daemon = SchedulerDaemon::getInstance();
    CPPUNIT_ASSERT(daemon);
    Ptr<AudioPlayerInterface>::Ref  audioPlayer = daemon->getAudioPlayer();
    CPPUNIT_ASSERT(audioPlayer);

    sleep(10);
    CPPUNIT_ASSERT(audioPlayer->isOpen());
    CPPUNIT_ASSERT(audioPlayer->isPlaying());

    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    // first schedule a playlist, so that there is something to reschedule
    CPPUNIT_ASSERT(sessionId);
    parameters["sessionId"]  = sessionId->getId();

    result.clear();
    xmlRpcClient.execute("stopCurrentlyPlaying", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();

    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
    CPPUNIT_ASSERT(!audioPlayer->isOpen());
}


/*------------------------------------------------------------------------------
 *  A simple negative test.
 *----------------------------------------------------------------------------*/
void
RpcStopCurrentlyPlayingTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT(sessionId);

    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    CPPUNIT_ASSERT(sessionId);
    parameters["sessionId"]       = sessionId->getId();

    result.clear();
    xmlRpcClient.execute("stopCurrentlyPlaying", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Schedule a playlist to play now.
 *----------------------------------------------------------------------------*/
void
RpcStopCurrentlyPlayingTest :: schedulePlaylistToPlayNow(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue     parameters;
    XmlRpc::XmlRpcValue     result;
    struct tm               time;

    XmlRpc::XmlRpcClient    xmlRpcClient(getXmlRpcHost().c_str(),
                                         getXmlRpcPort(),
                                         "/RPC2",
                                         false);

    // try to schedule playlist #1 for the time in 5 seconds
    parameters.clear();
    CPPUNIT_ASSERT(sessionId);
    parameters["sessionId"]     = sessionId->getId();
    parameters["playlistId"]    = "0000000000000001";
    Ptr<ptime>::Ref         now = TimeConversion::now();
    Ptr<ptime>::Ref         startTime(new ptime(*now + seconds(10)));
    TimeConversion::ptimeToTm(startTime, time);
    parameters["playtime"]      = &time;

    result.clear();
    xmlRpcClient.execute("uploadPlaylist", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());

    xmlRpcClient.close();
}

