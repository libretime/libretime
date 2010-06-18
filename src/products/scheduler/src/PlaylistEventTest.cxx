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

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/StorageClient/StorageClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"

#include "SchedulerDaemon.h"
#include "PlayLogFactory.h"
#include "PlaylistEvent.h"
#include "PlaylistEventTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::PlaylistExecutor;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PlaylistEventTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistEventTest :: setUp(void)                throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();
    try {
        audioPlayer    = scheduler->getAudioPlayer();
        storage        = scheduler->getStorage();
        storage->reset();
        authentication = scheduler->getAuthentication();
        playLog        = scheduler->getPlayLog();

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("error parsing configuration file");
    }

    audioPlayer->initialize();

    duration.reset(new time_duration(seconds(30)));

    if (!(sessionId = authentication->login("root", "q"))) {
        CPPUNIT_FAIL("could not log in to authentication server");
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistEventTest :: tearDown(void)             throw (CPPUNIT_NS::Exception)
{
    audioPlayer->deInitialize();

    duration.reset();
    storage.reset();
    audioPlayer.reset();
    playLog.reset();

    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Create a sample playlist event
 *----------------------------------------------------------------------------*/
Ptr<PlaylistEvent>::Ref
PlaylistEventTest :: createTestEvent(void)      throw (CPPUNIT_NS::Exception)
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

    Ptr<PlaylistEvent>::Ref     playlistEvent(new PlaylistEvent(sessionId,
                                                                audioPlayer,
                                                                storage,
                                                                playLog,
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
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
}

