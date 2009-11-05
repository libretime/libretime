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
#ifndef PlaylistEvent_h
#define PlaylistEvent_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Core/ScheduleEntry.h"
#include "LiveSupport/StorageClient/StorageClientInterface.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"
#include "LiveSupport/EventScheduler/ScheduledEventInterface.h"

#include "PlayLogInterface.h"

namespace LiveSupport {
namespace Scheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::PlaylistExecutor;
using namespace LiveSupport::EventScheduler;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::StorageClient;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A scheduled event for playing a playlist.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class PlaylistEvent : public virtual ScheduledEventInterface
{
    private:
        /**
         *  Enumeration describing the possible states of the event.
         */
        typedef enum {  created,
                        initializing,
                        initialized,
                        running,
                        stopped,
                        deInitialized } State;

    private:
        /**
         *  The audio player to play the playlist with.
         */
        Ptr<AudioPlayerInterface>::Ref      audioPlayer;

        /**
         *  The play log facility.
         */
        Ptr<PlayLogInterface>::Ref          playLog;

        /**
         *  The storage containing the playlist and all related audio clips.
         */
        Ptr<StorageClientInterface>::Ref    storage;

        /**
         *  The schedule entry this event is playing.
         */
        Ptr<ScheduleEntry>::Ref             scheduleEntry;

        /**
         *  The maximum time this event should get initialized in.
         */
        Ptr<time_duration>::Ref             initTime;

        /**
         *  The Playlist this event is playing.
         */
        Ptr<Playlist>::Ref                  playlist;

        /**
         *  The session ID used for authentication at the storage server.
         */
        Ptr<SessionId>::Ref                 sessionId;

        /**
         *  The current state of the event.
         */
        State                               state;
 

    public:
        /**
         *  Constructor.
         *
         *  @param sessionId the session id used to access the storage.
         *  @param audioPlayer the audio player to play the playlist with.
         *  @param storage the storage containing the playlist to play,
         *         and all the related audio clips.
         *  @param playLog the play log facility.
         *  @param scheduleEntry the schedule entry this event is
         *         playing.
         */
        PlaylistEvent(Ptr<SessionId>::Ref               sessionId,
                      Ptr<AudioPlayerInterface>::Ref    audioPlayer,
                      Ptr<StorageClientInterface>::Ref  storage,
                      Ptr<PlayLogInterface>::Ref        playLog,
                      Ptr<ScheduleEntry>::Ref           scheduleEntry)
                                                                    throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~PlaylistEvent(void)                           throw ()
        {
        }

        /**
         *  Tell the time this event is scheduled for.
         *
         *  @return the time this event is scheduled for.
         */
        virtual Ptr<const ptime>::Ref
        getScheduledTime(void)                              throw ()
        {
            return scheduleEntry->getStartTime();
        }

        /**
         *  Initialize the event object.
         *  This should finishin at most maxTimeToInitialize() time.
         *  Use this call to allocate any resources that will be needed
         *  by the event itself.
         *
         *  @exception std::exception on initialization problems.
         *             a raised exception will result in the cancellation
         *             of the event.
         *  @see #maxTimeToInitialize
         */
        virtual void
        initialize(void)                        throw (std::exception);

        /**
         *  The maximum time for the initalize() function to complete.
         *  It is the responsibility of the ScheduledEventInterface object to
         *  complete the initialization in that time.
         *
         *  @return the maximum time for the initialize() function to complete.
         *  @see #initialize
         */
        virtual Ptr<const time_duration>::Ref
        maxTimeToInitialize(void)                   throw ()
        {
            return initTime;
        }

        /**
         *  De-initialize the event object.
         */
        virtual void
        deInitialize(void)                          throw ();

        /**
         *  Start the event.
         *  This function call should start the execution of the event in
         *  a separate thread, and return immediately.
         */
        virtual void
        start(Ptr<time_duration>::Ref offset)                                 throw ();

        /**
         *  The length of the event.
         *  The scheduler will call stop() when this much time has passed
         *  after calling start().
         *
         *  @return the length of the event, in time.
         */
        virtual Ptr<const time_duration>::Ref
        eventLength(void)                           throw ()
        {
            return scheduleEntry->getPlaylength();
        }

        /**
         *  Stop the event.
         *  This function call should result in the event stopping, if
         *  this has not happened yet. The processing of this event should
         *  persue in a seperate thread, and the function itself should
         *  return immediately.
         */
        virtual void
        stop(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport


#endif // PlaylistEvent_h

