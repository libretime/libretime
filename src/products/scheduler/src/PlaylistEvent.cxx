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

#ifdef HAVE_UNISTD_H
#include <unistd.h>
#else
#error need unistd.h
#endif

#include <iostream>

#define DEBUG_PREFIX "Scheduler"
#include "LiveSupport/Core/Debug.h"

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/SessionId.h"

#include "PlaylistEvent.h"

using namespace boost;

using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
PlaylistEvent :: PlaylistEvent(
                        Ptr<SessionId>::Ref                 sessionId,
                        Ptr<AudioPlayerInterface>::Ref      audioPlayer,
                        Ptr<StorageClientInterface>::Ref    storage,
                        Ptr<PlayLogInterface>::Ref          playLog,
                        Ptr<ScheduleEntry>::Ref             scheduleEntry)
                                                                    throw ()
{
    this->sessionId     = sessionId;
    this->audioPlayer   = audioPlayer;
    this->storage       = storage;
    this->playLog       = playLog;
    this->scheduleEntry = scheduleEntry;

    // this init time is a wild guess, say 10 seconds should be enough
    initTime.reset(new posix_time::time_duration(0, 0, 10, 0));

    state = created;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: initialize(void)                  throw (std::exception)
{
    //DEBUG_BLOCK

    if (state != created) {
        throw std::logic_error("PlaylistEvent in bad state");
    }

    state = initializing;
    // some ugliness because getPlaylistId() returns a const pointer
    Ptr<UniqueId>::Ref    playlistId(new UniqueId(scheduleEntry->getPlaylistId()
                                                               ->getId()));
    try {
        playlist = storage->acquirePlaylist(sessionId, playlistId);
    } catch (Core::XmlRpcException &e) {
        std::string     errorMessage = "storage server error: ";
        errorMessage += e.what();
        throw std::logic_error(errorMessage);
    }

    audioPlayer->preload(*playlist->getUri());
    state = initialized;
}


/*------------------------------------------------------------------------------
 *  De-initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: deInitialize(void)                throw ()
{
    DEBUG_BLOCK

    try {
        storage->releasePlaylist(playlist);
    } catch (XmlRpcException &e) {
        std::cerr << e.what() << std::endl;
        // TODO: handle error?
    }
    playlist.reset();
    state = deInitialized;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: start(Ptr<time_duration>::Ref offset)                       throw ()
{
    DEBUG_BLOCK

    if (state != initialized) {
        initialize();
    }

    try {
        audioPlayer->open(*playlist->getUri(), (gint64)playlist->getId()->getId(), offset->total_nanoseconds());
        audioPlayer->start();

        playLog->addPlayLogEntry(playlist->getId(), TimeConversion::now());
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        // TODO: handle error?
    } catch (std::runtime_error &e) {
        std::cerr << e.what() << std::endl;
        // TODO: handle error?
    }
    state = running;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: stop(void)                        throw ()
{
    DEBUG_BLOCK

    try {
        audioPlayer->stop();
        audioPlayer->close();
    } catch (std::logic_error &e) {
        // TODO: handle error
        // NOTE: this may not be an error, because the user may have stopped
        // the playback manually (see Scheduler::StopCurrentlyPlayingMethod)
        std::cerr << "PlaylistEvent::stop error: " << std::endl;
        std::cerr << e.what() << std::endl;
    }
    state = stopped;
}

