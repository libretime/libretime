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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PlaylistEventContainerTest.cxx,v $

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

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"

#include "PlaylistEventContainer.h"
#include "PlaylistEventContainerTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::PlaylistExecutor;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PlaylistEventContainerTest);

/**
 *  The name of the configuration file for the audio player
 */
static const std::string audioPlayerConfigFileName = "etc/audioPlayer.xml";

/**
 *  The name of the configuration file for the connection manager
 */
static const std::string connectionManagerConfigFileName =
                                        "etc/connectionManagerFactory.xml";

/**
 *  The name of the configuration file for the storage client
 */
static const std::string storageClientConfigFileName = "etc/storageClient.xml";

/**
 *  The name of the configuration file for the schedule factory
 */
static const std::string scheduleConfigFileName = "etc/scheduleFactory.xml";

/**
 *  The name of the configuration file for the authentication client factory.
 */
static const std::string authenticationClientConfigFileName =
                                          "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistEventContainerTest :: setUp(void)                        throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref      parser;

        // configure the audio player factory
        Ptr<AudioPlayerFactory>::Ref    apf = AudioPlayerFactory::getInstance();
        parser.reset(new xmlpp::DomParser(audioPlayerConfigFileName, true));
        apf->configure(*(parser->get_document()->get_root_node()));

        audioPlayer = apf->getAudioPlayer();

        // configure the connection manager factory
        Ptr<ConnectionManagerFactory>::Ref  cmf =
                                    ConnectionManagerFactory::getInstance();
        parser.reset(new xmlpp::DomParser(connectionManagerConfigFileName,
                                          true));
        cmf->configure(*(parser->get_document()->get_root_node()));

        // configure the storage client factory
        Ptr<StorageClientFactory>::Ref  scf =
                                            StorageClientFactory::getInstance();
        parser.reset(new xmlpp::DomParser(storageClientConfigFileName, true));
        scf->configure(*(parser->get_document()->get_root_node()));

        storage = scf->getStorageClient();
        storage->reset();

        // configure the schedule factory
        scheduleFactory = ScheduleFactory::getInstance();
        parser.reset(new xmlpp::DomParser(scheduleConfigFileName, true));
        scheduleFactory->configure(*(parser->get_document()->get_root_node()));

        schedule = scheduleFactory->getSchedule();

        // get an authentication client
        Ptr<AuthenticationClientFactory>::Ref acf;
        acf = AuthenticationClientFactory::getInstance();
        parser.reset(new xmlpp::DomParser(authenticationClientConfigFileName,
                                          true));
        acf->configure(*(parser->get_document()->get_root_node()));
        authentication = acf->getAuthenticationClient();

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("error parsing configuration file");
    }

    try {
        scheduleFactory->install();
    } catch (std::exception &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("can't install schedule factory");
    }
    audioPlayer->initialize();

    if (!(sessionId = authentication->login("root", "q"))) {
        CPPUNIT_FAIL("could not log in to authentication server");
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistEventContainerTest :: tearDown(void)                     throw ()
{
    audioPlayer->deInitialize();
    scheduleFactory->uninstall();

    schedule.reset();
    scheduleFactory.reset();
    storage.reset();
    audioPlayer.reset();

    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
PlaylistEventContainerTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<PlaylistEventContainer>::Ref    container;
    container.reset(new PlaylistEventContainer(sessionId,
                                               storage,
                                               schedule,
                                               audioPlayer));

    // see that there are no events scheduled
    Ptr<ScheduledEventInterface>::Ref   scheduledEvent;
    scheduledEvent = container->getNextEvent(TimeConversion::now());
    CPPUNIT_ASSERT(!scheduledEvent.get());
}


/*------------------------------------------------------------------------------
 *  Schedule something, and see if we can get it back.
 *----------------------------------------------------------------------------*/
void
PlaylistEventContainerTest :: scheduleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<PlaylistEventContainer>::Ref    container;
    container.reset(new PlaylistEventContainer(sessionId,
                                               storage,
                                               schedule,
                                               audioPlayer));

    // schedule playlist 1 at 10 seconds from now
    Ptr<UniqueId>::Ref      playlistId(new UniqueId(1));
    Ptr<Playlist>::Ref      playlist = storage->getPlaylist(sessionId, 
                                                            playlistId);
    CPPUNIT_ASSERT(playlist.get());
    Ptr<ptime>::Ref         now = TimeConversion::now();
    Ptr<ptime>::Ref         from(new ptime(*now + seconds(10)));

    try {
        schedule->schedulePlaylist(playlist, from);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<ScheduledEventInterface>::Ref   scheduledEvent;

    // see if we can get back the scheduled playlist
    scheduledEvent = container->getNextEvent(now);
    CPPUNIT_ASSERT(scheduledEvent.get());
    CPPUNIT_ASSERT(*scheduledEvent->eventLength()
                == *playlist->getPlaylength());

    // see that there are no events scheduled after from
    scheduledEvent = container->getNextEvent(from);
    CPPUNIT_ASSERT(!scheduledEvent.get());
}

