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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PlaylistEvent.cxx,v $

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

    // this init time is a wild guess, say 5 seconds should be enough
    initTime.reset(new posix_time::time_duration(0, 0, 5, 0));

    state = created;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: initialize(void)                  throw (std::exception)
{
    if (state != created) {
        throw std::logic_error("PlaylistEvent in bad state");
    }

    state = initializing;
    // some ugliness because getPlaylistId() returns a const pointer
    Ptr<UniqueId>::Ref    playlistId(new UniqueId(scheduleEntry->getPlaylistId()
                                                               ->getId()));
    playlist = storage->acquirePlaylist(sessionId, playlistId);
    state = initialized;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: deInitialize(void)                throw ()
{
    if (state != stopped) {
        // TODO: handle error?
        return;
    }

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
PlaylistEvent :: start(void)                       throw ()
{
    if (state != initialized) {
        // TODO: handle error?
        return;
    }

    try {
        audioPlayer->open(*playlist->getUri());
        audioPlayer->start();

        playLog->addPlayLogEntry(playlist->getId(), TimeConversion::now());
    } catch (std::invalid_argument &e) {
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
    if (state != running) {
        // TODO: handle error?
        return;
    }

    try {
        audioPlayer->stop();
        audioPlayer->close();
    } catch (std::logic_error &e) {
        // TODO: handle error
        std::cerr << "PlaylistEvent::stop error: " << std::endl;
        std::cerr << e.what() << std::endl;
    }
    state = stopped;
}

