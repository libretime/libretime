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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PlaylistEventTest.cxx,v $

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
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"

#include "PlaylistEvent.h"
#include "PlaylistEventTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;
using namespace LiveSupport::PlaylistExecutor;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PlaylistEventTest);

/**
 *  The name of the configuration file for the audio player
 */
static const std::string audioPlayerConfigFileName = "etc/audioPlayer.xml";

/**
 *  The name of the configuration file for the storage client
 */
static const std::string storageClientConfigFileName = "etc/storageClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistEventTest :: setUp(void)                        throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref      parser;

        // configure the audio player factory
        Ptr<AudioPlayerFactory>::Ref    apf = AudioPlayerFactory::getInstance();
        parser.reset(new xmlpp::DomParser(audioPlayerConfigFileName, true));
        apf->configure(*(parser->get_document()->get_root_node()));

        audioPlayer = apf->getAudioPlayer();

        // configure the storage client factory
        Ptr<StorageClientFactory>::Ref  scf =
                                            StorageClientFactory::getInstance();
        parser.reset(new xmlpp::DomParser(storageClientConfigFileName, true));
        scf->configure(*(parser->get_document()->get_root_node()));

        storage = scf->getStorageClient();

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("error parsing configuration file");
    }

    audioPlayer->initialize();

    duration.reset(new time_duration(seconds(30)));
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistEventTest :: tearDown(void)                     throw ()
{
    audioPlayer->deInitialize();

    duration.reset();
    storage.reset();
    audioPlayer.reset();
}


/*------------------------------------------------------------------------------
 *  Create a sample playlist event
 *----------------------------------------------------------------------------*/
Ptr<PlaylistEvent>::Ref
PlaylistEventTest :: createTestEvent(void)              throw ()
{
    // create a fake schedule entry, with id 1 for playlist 1, starting
    // 10 seconds from now, and lasting 30 seconds
    Ptr<UniqueId>::Ref          entryId(new UniqueId(1));
    Ptr<UniqueId>::Ref          playlistId(new UniqueId(1));
    Ptr<ptime>::Ref             now = TimeConversion::now();
    Ptr<ptime>::Ref             startTime(new ptime(*now + seconds(10)));
    Ptr<ptime>::Ref             endTime(new ptime(*startTime + *duration));
    Ptr<ScheduleEntry>::Ref     scheduleEntry(new ScheduleEntry(entryId,
                                                                playlistId,
                                                                startTime,
                                                                endTime));

    Ptr<PlaylistEvent>::Ref     playlistEvent(new PlaylistEvent(audioPlayer,
                                                                storage,
                                                                scheduleEntry));

    return playlistEvent;
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
PlaylistEventTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<PlaylistEvent>::Ref     playlistEvent = createTestEvent();

    CPPUNIT_ASSERT(*playlistEvent->eventLength() == seconds(30));
}


/*------------------------------------------------------------------------------
 *  See if the playlist event can be initialized.
 *----------------------------------------------------------------------------*/
void
PlaylistEventTest :: initializeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<PlaylistEvent>::Ref     playlistEvent = createTestEvent();

    try {
        playlistEvent->initialize();
        playlistEvent->deInitialize();
    } catch (std::logic_error &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  See if the playlist can be played
 *----------------------------------------------------------------------------*/
void
PlaylistEventTest :: playTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<PlaylistEvent>::Ref     playlistEvent = createTestEvent();

    try {
        playlistEvent->initialize();
        playlistEvent->start();
        TimeConversion::sleep(duration);
        playlistEvent->stop();
        playlistEvent->deInitialize();
    } catch (std::logic_error &e) {
        CPPUNIT_FAIL(e.what());
    }
}

