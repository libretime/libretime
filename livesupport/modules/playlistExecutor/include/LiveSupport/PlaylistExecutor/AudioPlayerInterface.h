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
    Version  : $Revision: 1.13 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/include/LiveSupport/PlaylistExecutor/AudioPlayerInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_PlaylistExecutor_AudioPlayerInterface_h
#define LiveSupport_PlaylistExecutor_AudioPlayerInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <exception>
#include <stdexcept>

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Playlist.h"

#include "LiveSupport/PlaylistExecutor/AudioPlayerEventListener.h"

namespace LiveSupport {
namespace PlaylistExecutor {

using namespace boost;

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A generic interface for playing audio files.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.13 $
 */
class AudioPlayerInterface
{
    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~AudioPlayerInterface(void)                        throw ()
        {
        }

        /**
         *  Initialize the Player object, so that it is ready to
         *  play audio files.
         *
         *  @exception std::exception on initialization problems.
         */
        virtual void
        initialize(void)                        throw (std::exception)    = 0;

        /**
         *  De-initialize the Player object.
         */
        virtual void
        deInitialize(void)                      throw ()                  = 0;

        /**
         *  Attach an event listener for this audio player.
         *  After this call, the supplied event listener object will recieve
         *  all events related to this audio player.
         *
         *  Currently, there is only one event emitted by the audio player:
         *  if the audio clip or playlist has finished playing naturally, 
         *  the onStop() method of the listener is called.
         *  Note that this event is not emitted if playing was stopped by 
         *  a call to stop() or pause(); 
         *  and also that for a playlist, the event is only fired once, 
         *  at the end, and not for each item inside the playlist.
         *
         *  @param eventListener the event listener to register.
         *  @see #detachListener
         */
        virtual void
        attachListener(AudioPlayerEventListener*    eventListener)
                                                                throw ()  = 0;

        /**
         *  Detach an event listener for this audio player.
         *  It is not necessary to call detach if the player is destroyed.
         *  In that case, the listeners are detached automatically.
         *
         *  @param eventListener the event listener to unregister.
         *  @exception std::invalid_argument if the supplied event listener
         *             has not been previously registered.
         *  @see #attachListener
         */
        virtual void
        detachListener(AudioPlayerEventListener*    eventListener)
                                            throw (std::invalid_argument)  = 0;

        /**
         *  Specify which audio resource to play.
         *  The file may be a playlist, referencing other files, which
         *  will be accessed automatically.
         *  Note: this call will <b>not</b> start playing! You will
         *  have to call the start() function to begin playing.
         *  Always close any opened resources with a call to close().
         *
         *  @param fileUrl a URL to a file
         *  @exception std::invalid_argument if the supplied fileUrl
         *             seems to be invalid.
         *  @see #close
         *  @see #start
         */
        virtual void
        open(const std::string  fileUrl)        throw (std::invalid_argument)
                                                                        = 0;

        /**
         *  Close an audio source that was opened.
         *
         *  @see #open
         */
        virtual void
        close(void)                             throw ()                = 0;

        /**
         *  Get the length of the currently opened audio clip.
         *  This function waits as long as necessary to get the length.
         *
         *  @return the length of the currently playing audio clip, or 0,
         *          if nothing is openned.
         */
        virtual Ptr<posix_time::time_duration>::Ref
        getPlaylength(void)                                 throw () = 0;

        /**
         *  Start playing.
         *  This call will start playing the active playlist, which was
         *  set by a previous call to open().
         *  Playing can be stopped by calling stop().
         *
         *  @exception std::logic_error if there was no previous call to
         *             playThis().
         *  @see #open
         *  @see #stop
         */
        virtual void
        start(void)                             throw (std::logic_error)
                                                                      = 0;

        /**
         *  Pause the player. Playing can be resumed by calling start().
         *
         *  This will not trigger a call to onStop() of the attached listeners.
         *
         *  @exception std::logic_error if there was no previous call to
         *             open().
         *  @see #open
         *  @see #start
         */
        virtual void
        pause(void)                             throw (std::logic_error)
                                                                      = 0;

        /**
         *  Tell if we're currently playing.
         *
         *  @return true of the player is currently playing, false
         *          otherwise.
         */
        virtual bool
        isPlaying(void)                         throw ()              = 0;

        /**
         *  Stop playing.
         *  This will not trigger a call to onStop() of the attached listeners.
         *
         *  @exception std::logic_error if there was no previous call to
         *             start()
         */
        virtual void
        stop(void)                              throw (std::logic_error)
                                                                      = 0;

        /**
         *  Set the audio device used for playback.
         *
         *  @param deviceName the new device name, e.g., /dev/dsp
         *  @return true if successful, false if not
         */
        virtual bool
        setAudioDevice(const std::string &deviceName)       
                                                throw ()              = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport


#endif // LiveSupport_PlaylistExecutor_AudioPlayerInterface_h

