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
    Version  : $Revision: 1.14 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/HelixPlayer.h,v $

------------------------------------------------------------------------------*/
#ifndef HelixPlayer_h
#define HelixPlayer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <list>
#include <boost/enable_shared_from_this.hpp>

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/Thread.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"

#include <dllacces.h>
#include <dllpath.h>

#include "ErrorSink.h"
#include "AuthenticationManager.h"
#include "ClientContext.h"
#include "LiveSupport/Core/Playlist.h"

#include <hxausvc.h>


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace boost;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class to play audio files and SMIL files through the Helix
 *  Community Library.
 *  This class can be configured with the following XML element.
 *
 *  <pre><code>
 *  <helixPlayer dllPath     = "../../usr/lib/helix"
 *               audioDevice = "/dev/sound/dsp"
 *  />
 *  <pre><code>
 *
 *  where the dllPath is the path to the directory containing the Helix
 *  library shared objects.  The optional audioDevice argument sets the
 *  AUDIO environment variable which is read by the Helix client.
 *
 *  There are two parameters which are only there because the current version
 *  of the Helix client does not handle animation tags in SMIL files properly.
 *  They will be removed from later versions.
 *  <ul>
 *      <li>audioStreamTimeOut (milliseconds) - the time to wait for each
 *          GetAudioStream() operation before a timeout occurs; 
 *          the default is 5;</li>
 *      <li>fadeLookAheadTime  (milliseconds) - each fade-in or fade-out is
 *          scheduled (using IHXAudioCrossFade::CrossFade()) this
 *          much time before it is to happen; the default is 2500. </li>
 *  </ul>
 *  
 *  The DTD for the above configuration is the following:
 *
 *  <pre><code>
 *  <!ELEMENT helixPlayer   EMPTY >
 *  <!ATTLIST helixPlayer   dllPath      CDATA   #REQUIRED >
 *  <!ATTLIST helixPlayer   audioDevice  CDATA   #IMPLIED  >
 *  <!ATTLIST helixPlayer   audioStreamTimeout   #IMPLIED >
 *  <!ATTLIST helixPlayer   fadeLookAheatTime    #IMPLIED >
 *  </pre></code>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.14 $
 */
class HelixPlayer : virtual public Configurable,
                    virtual public AudioPlayerInterface,
                    public boost::enable_shared_from_this<HelixPlayer>
{
    private:
        /**
         *  The name of the configuration XML elmenent used by HelixPlayer
         */
        static const std::string    configElementNameStr;

        /**
         *  The full path to the Helix library shared objects.
         */
        std::string             dllPath;

        /**
         *  Max time to wait for an audio stream, in milliseconds.
         */
        int                     audioStreamTimeout;

        /**
         *  Schedule fading this many milliseconds in advance.
         */
        int                     fadeLookAheadTime;

        /**
         *  The shared object access point.
         */
        DLLAccess               dllAccess;

        /**
         *  Function pointer to create the Helix engine.
         */
        FPRMCREATEENGINE        createEngine;

        /**
         *  Function pointer to close the Helix engine.
         */
        FPRMCLOSEENGINE         closeEngine;

        /**
         *  The Helix Client engine.
         */
        IHXClientEngine       * clientEngine;

        /**
         *  The Helix player.
         */
        IHXPlayer             * player;
        
        /**
         *  The example client context.
         */
        ClientContext         * clientContext;

        /**
         *  The URL to play.
         */
        std::string             url;

        /**
         *  The length of the currently playing audio clip,
         *  in milliseconds.
         */
        unsigned long           playlength;

        /**
         *  Flag to indicate if this object has been initialized.
         */
        bool                    initialized;

        /**
         *  Flag to indicate if the player is currently playing.
         *  Make sure no to rely on this flag, as it's not aware of
         *  the case that the playing has ended naturally. Always
         *  call isPlaying() instead.
         */
        bool                    playing;

        /**
         *  A thread for handling helix events, on a regular basis.
         *  Helix apperantly needs to be polled all the time to function.
         */
        Ptr<Thread>::Ref        eventHandlerThread;

        /**
         *  A type to contain the data about a single fade-in or fade-out
         *  event.
         */
        struct FadeData {
            IHXAudioStream* audioStreamFrom;
            IHXAudioStream* audioStreamTo;
            unsigned long   fadeAt;
            unsigned long   fadeLength;
        };

        /**
         *  A list of fade-in/fade-out events.
         *  This is set by the openAndStartPlaylist() method, and the
         *  actual fading is done by implementFading(), called from
         *  AdviseSink::OnPosLength().
         */
        Ptr<std::list<FadeData> >::Ref  fadeDataList;

        /**
         *  Declare AdviseSink::OnPosLength() to be a friend, so it can
         *  call the private method implementFading().
         */
        friend STDMETHODIMP
        AdviseSink::OnPosLength(UINT32      ulPosition,
                                UINT32      ulLength)
                                                throw ();

        /**
         *  Implement the actual fading scheduled by open(Ptr<Playlist>::Ref).
         *  This method is called from AdviceSink::OnPosLength().
         *  DO NOT call this method directly.
         *
         *  @param position the clip position
         */
        void
        implementFading(unsigned long position)
                                                throw(std::runtime_error);

    public:
        /**
         *  Constructor.
         */
        HelixPlayer(void)                           throw ()
        {
            playing     = false;
            initialized = false;
            playlength  = 0UL;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~HelixPlayer(void)                          throw ()
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
         *  Initialize the Helix Player object, so that it is ready to
         *  play audio files.
         *
         *  @exception std::exception on initialization problems.
         */
        virtual void
        initialize(void)                        throw (std::exception);

        /**
         *  De-initialize the Helix Player object.
         */
        virtual void
        deInitialize(void)                      throw ();

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
         *  Set the length of the currenlty playing audio clip.
         *  This is called by AdviseSink only!
         *
         *  @param playlength the length of the currently playing audio clip.
         *         in milliseconds
         *  @see AdviseSink#OnPosLength
         */
        void
        setPlaylength(unsigned long     playlength)
        {
            this->playlength = playlength;
        }

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

        /**
         *  Play a playlist, with simulated fading.
         *
         *  This is a stopgap method, and should be replaced as soon as
         *  the SMIL animation issues are fixed in the Helix client.
         *
         *  The playlist is assumed to contain a URI field, which points
         *  to a SMIL file containing the same audio clips, with the same
         *  offsets, as the playlist.  This can be ensured, for example, by 
         *  calling Storage::WebStorageClient::acquirePlaylist().
         *
         *  @param playlist the Playlist object to be played.
         *  @exception std::invalid_argument playlist is invalid (e.g.,
         *              does not have a URI field, or there is no valid
         *              SMIL file at the given URI).
         *  @exception std::logic_error thrown by start() if open() was
         *              unsuccessful, but returned normally (never happens)
         *  @exception std::runtime_error on errors thrown by the helix player
         */
        virtual void
        openAndStart(Ptr<Playlist>::Ref  playlist)       
                                                throw (std::invalid_argument,
                                                       std::logic_error,
                                                       std::runtime_error);

        /**
         *  Set the audio device used for playback.
         *
         *  Sets the environment variable AUDIO.  This should be replaced with
         *  a Helix function call as soon as this functionality is provided.
         *
         *  There are some strange issues with this: e.g., if you set the
         *  device to "/dev/null", this works fine for SMIL files, but gives
         *  Helix error 80040100 (HXR_AUDIO_DRIVER) when an mp3 file is played.
         *
         *  @param deviceName the new device name, e.g., /dev/dsp
         *  @return true if successful, false if not
         */
        virtual bool
        setAudioDevice(const std::string &deviceName)       
                                                throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport

/**
 *  A global function returning the shared object access path to
 *  the Helix library.
 */
DLLAccessPath* GetDLLAccessPath(void);


#endif // HelixPlayer_h

