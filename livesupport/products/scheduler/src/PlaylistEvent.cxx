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
    Version  : $Revision: 1.5 $
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
                        Ptr<AudioPlayerInterface>::Ref      audioPlayer,
                        Ptr<StorageClientInterface>::Ref    storage,
                        Ptr<ScheduleEntry>::Ref             scheduleEntry)
                                                                    throw ()
{
    this->audioPlayer   = audioPlayer;
    this->storage       = storage;
    this->scheduleEntry = scheduleEntry;
    this->sessionId.reset(new SessionId("dummy session ID"));
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: initialize(void)                  throw (std::exception)
{
    // some ugliness because getPlaylistId() returns a const pointer
    Ptr<UniqueId>::Ref    playlistId(new UniqueId(scheduleEntry->getPlaylistId()
                                                               ->getId()));
    playlist = storage->acquirePlaylist(sessionId, playlistId);
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: deInitialize(void)                throw ()
{
    storage->releasePlaylist(sessionId, playlist);
    playlist.reset();
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: start(void)                       throw ()
{
    try {
        audioPlayer->open(*playlist->getUri());
        audioPlayer->start();
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        // TODO: handle error?
    }
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
PlaylistEvent :: stop(void)                        throw ()
{
    audioPlayer->stop();
    audioPlayer->close();
}

