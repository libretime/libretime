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


#include "PlaylistEventContainer.h"
#include "PlaylistEvent.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::EventScheduler;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
PlaylistEventContainer :: PlaylistEventContainer(
                        Ptr<SessionId>::Ref                 sessionId,
                        Ptr<StorageClientInterface>::Ref    storage,
                        Ptr<ScheduleInterface>::Ref         schedule,
                        Ptr<AudioPlayerInterface>::Ref      audioPlayer,
                        Ptr<PlayLogInterface>::Ref          playLog)
                                                                    throw ()
{
    this->sessionId   = sessionId;
    this->storage     = storage;
    this->schedule    = schedule;
    this->audioPlayer = audioPlayer;
    this->playLog     = playLog;
}


/*------------------------------------------------------------------------------
 *  Return the first scheduled event after the specified timepoint
 *----------------------------------------------------------------------------*/
Ptr<ScheduledEventInterface>::Ref
PlaylistEventContainer :: getNextEvent(Ptr<ptime>::Ref  when)       throw ()
{
    Ptr<ScheduleEntry>::Ref     entry = schedule->getNextEntry(when);
    Ptr<PlaylistEvent>::Ref     event;

    if (entry.get()) {
        event.reset(new PlaylistEvent(sessionId,
                                      audioPlayer,
                                      storage,
                                      playLog,
                                      entry));
    }

    return event;
}

/*------------------------------------------------------------------------------
 *  Return the first scheduled event after the specified timepoint
 *----------------------------------------------------------------------------*/
Ptr<ScheduledEventInterface>::Ref
PlaylistEventContainer :: getCurrentEvent()       throw ()
{
    Ptr<ScheduleEntry>::Ref     entry = schedule->getCurrentEntry();
    Ptr<PlaylistEvent>::Ref     event;

    if (entry.get()) {
        event.reset(new PlaylistEvent(sessionId,
                                      audioPlayer,
                                      storage,
                                      playLog,
                                      entry));
    }

    return event;
}

