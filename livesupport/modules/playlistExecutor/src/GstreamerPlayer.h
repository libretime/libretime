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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/GstreamerPlayer.h,v $

------------------------------------------------------------------------------*/
#ifndef GstreamerPlayer_h
#define GstreamerPlayer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <list>

#include <gst/gst.h>

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"

#include "LiveSupport/Core/Playlist.h"


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class to play audio files and some SMIL files through the Gstreamer
 *  library.
 *  This class can be configured with the following XML element.
 *
 *  <pre><code>
 *  <gstreamerPlayer    audioDevice = "plughw:0,0" />
 *  </code></pre>
 *
 *  where the optional audioDevice argument specifies the audio device
 *  (currently ALSA device) to use for playing.
 *
 *  The DTD for the above configuration is the following:
 *
 *  <pre><code>
 *  <!ELEMENT gstreamerPlayer   EMPTY >
 *  <!ATTLIST gstreamerPlayer   audioDevice  CDATA   #IMPLIED  >
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.5 $
 */
class GstreamerPlayer : virtual public Configurable,
                        virtual public AudioPlayerInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by GstreamerPlayer
         */
        static const std::string    configElementNameStr;

        /**
         *  The pipeline inside the player
         */
        GstElement            * pipeline;

        /**
         *  The file source element.
         */
        GstElement            * filesrc;

        /**
         *  The decoder element.
         */
        GstElement            * decoder;

        /**
         *  The audio sink
         */
        GstElement            * audiosink;

        /**
         *  The URL to play.
         */
        std::string             url;

        /**
         *  Flag to indicate if this object has been initialized.
         */
        bool                    initialized;

        /**
         *  The audio device to play on.
         */
        std::string             audioDevice;

        /**
         *  The type for the vector of listeners.
         *  Just a shorthand notation, to make reference to the type
         *  easier.
         */
        typedef std::vector<AudioPlayerEventListener*>
                                                        ListenerVector;

        /**
         *  A vector of event listeners, which are interested in events
         *  related to this player.
         */
        ListenerVector          listeners;

        /**
         *  Handler to recieve errors from gstreamer.
         *
         *  @param pipeline the pipeline generating the error
         *  @param source the source of the error
         *  @param error the error itself
         *  @param debug debug info
         *  @param self pointer to the associated GsreamerPlayer object.
         */
        static void
        errorHandler(GstElement   * pipeline,
                     GstElement   * source,
                     GError       * error,
                     gchar        * debug,
                     gpointer       self)                           throw ();

        /**
         *  Event handler for the state change event on the pipeline.
         *  Use this to catch events like playing has ended.
         *
         *  @param element the pipeline the event change has occured at
         *  @param oldState the old state
         *  @param newState the new state
         *  @param self a pointer to the associated GstreamerPlayer object.
         */
        static void
        stateChange(GstElement    * element,
                    gint            oldState,
                    gint            newState,
                    gpointer        self)
                                                                    throw ();

        /**
         *  Send the onStop event to all attached listeners.
         */
        virtual void
        fireOnStopEvent(void)                           throw ();

        /**
         *  Tell if the object is currently opened (has a file source to
         *  read.)
         *
         *  @return true if the object is currently opened, false otherwise.
         */
        bool
        isOpened(void)                                  throw ();


    public:
        /**
         *  Constructor.
         */
        GstreamerPlayer(void)                           throw ()
        {
            pipeline    = 0;
            filesrc     = 0;
            decoder     = 0;
            audiosink   = 0;
            initialized = false;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~GstreamerPlayer(void)                          throw ()
        {
            deInitialize();
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                      throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Configure the object based on the XML element supplied.
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the scheduler daemon has already
         *             been configured, and can not be reconfigured.
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  Initialize the Audio Player object, so that it is ready to
         *  play audio files.
         *
         *  @exception std::exception on initialization problems.
         */
        virtual void
        initialize(void)                        throw (std::exception);

        /**
         *  De-initialize the Audio Player object.
         */
        virtual void
        deInitialize(void)                      throw ();

        /**
         *  Attach an event listener for this audio player.
         *  After this call, the supplied event will recieve all events
         *  related to this audio player.
         *
         *  @param eventListener the event listener to register.
         *  @see #detach
         */
        virtual void
        attachListener(AudioPlayerEventListener*    eventListener)
                                                                    throw ();

        /**
         *  Detach an event listener for this audio player.
         *
         *  @param eventListener the event listener to unregister.
         *  @exception std::invalid_argument if the supplied event listener
         *             has not been previously registered.
         *  @see #attach
         */
        virtual void
        detachListener(AudioPlayerEventListener*    eventListener)
                                                throw (std::invalid_argument);

        /**
         *  Set the audio device used for playback.
         *
         *  @param deviceName the new device name, e.g., /dev/dsp or
         *         plughw:0,0
         *  @return true if successful, false if not
         */
        virtual bool
        setAudioDevice(const std::string &deviceName)       
                                                throw ();

        /**
         *  Specify which audio resource to play.
         *  The file may be a playlist, referencing other files, which
         *  will be accessed automatically.
         *  Note: this call will <b>not</b> start playing! You will
         *  have to call the start() function to begin playing.
         *  Always close any opened resource with a call to close().
         *
         *  @param fileUrl a URL to a file
         *  @exception std::invalid_argument if the supplied fileUrl
         *             seems to be invalid.
         *  @see #close
         *  @see #start
         */
        virtual void
        open(const std::string  fileUrl)        throw (std::invalid_argument);

        /**
         *  Close an audio source that was opened.
         *
         *  @see #open
         */
        virtual void
        close(void)                             throw ();

        /**
         *  Start playing.
         *  This call will start playing the active playlist, which was
         *  set by a previous call to open().
         *  Playing can be stopped by calling stop().
         *
         *  @exception std::logic_error if there was no previous call to
         *             open().
         *  @see #open
         *  @see #stop
         */
        virtual void
        start(void)                             throw (std::logic_error);

        /**
         *  Pause the player.
         *  Playing can be resumed by calling start().
         *
         *  @exception std::logic_error if there was no previous call to
         *             open().
         *  @see #open
         *  @see #start
         */
        virtual void
        pause(void)                             throw (std::logic_error);

        /**
         *  Tell if we're currently playing.
         *
         *  @return true of the player is currently playing, false
         *          otherwise.
         */
        virtual bool
        isPlaying(void)                         throw ();

        /**
         *  Stop playing.
         *
         *  @exception std::logic_error if there was no previous call to
         *             start()
         */
        virtual void
        stop(void)                              throw (std::logic_error);

        /**
         *  Get the length of the currently opened audio clip.
         *  This function waits as long as necessary to get the length.
         *
         *  @return the length of the currently playing audio clip, or 0,
         *          if nothing is openned.
         */
        virtual Ptr<posix_time::time_duration>::Ref
        getPlaylength(void)                                 throw ();

        /**
         *  Get the volume of the player.
         *
         *  @return the volume, from 1 to 100.
         */
        virtual unsigned int
        getVolume(void)                                     throw ();

        /**
         *  Set the volume of the player.
         *
         *  @param volume the new volume, from 1 to 100.
         */
        virtual void
        setVolume(unsigned int  volume)                     throw ();

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport


#endif // GstreamerPlayer_h

