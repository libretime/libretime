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
    Version  : $Revision: 1.1 $
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


#include <pthread.h>

#include <dllacces.h>
#include <dllpath.h>

#include "LiveSupport/Core/Configurable.h"

#include "AdviseSink.h"
#include "ErrorSink.h"
#include "AuthenticationManager.h"
#include "ClientContext.h"


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class to play audio files and SMIL files through the Helix
 *  Community Library.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class HelixPlayer :
                    virtual public Configurable
{
    friend void * eventHandlerThread(void *)      throw();

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
         *  The event handling thread.
         */
        pthread_t               eventHandlingThread;

        /**
         *  Flag to mark if the event handling thread should be running
         *  and handling events.
         *  This is set by the HelixPlayer object, and read by the thread
         *  to determine when to stop.
         */
        bool                    handleEvents;

        /**
         *  Flag to indicate if the player is currently playing.
         *  Make sure no to rely on this flag, as it's not aware of
         *  the case that the playing has ended naturally. Always
         *  call isPlaying() instead.
         */
        bool                    playing;


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~HelixPlayer(void)                        throw ()
        {
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
        deInitialize(void);

        /**
         *  Specify which audio resource to play.
         *  The file may be a playlist, referencing other files, which
         *  will be accessed automatically.
         *  Note: this call will <b>not</b> start playing! You will
         *  have to call the start() function to begin playing.
         *
         *  @param fileUrl a URL to a file
         *  @exception std::invalid_argument if the supplied fileUrl
         *             seems to be invalid.
         *  @see #start
         */
        virtual void
        playThis(const std::string  fileUrl)    throw (std::invalid_argument);

        /**
         *  Start playing.
         *  This call will start playing the active playlist, which was
         *  set by a previous call to playThis().
         *  Playing can be stopped by calling stop().
         *
         *  @exception std::logic_error if there was no previous call to
         *             playThis().
         *  @see #playThis
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
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */

/**
 *  The main function of the thread that calls for handling of events
 *  in the createEngine all the time.
 *
 *  @param helixPlayer a pointer to the HelixPlayer object that started
 *         this thread.
 *  @return always 0
 */
void *
eventHandlerThread(void   * helixPlayer)                throw ();


} // namespace PlaylistExecutor
} // namespace LiveSupport

/**
 *  A global function returning the shared object access path to
 *  the Helix library.
 */
DLLAccessPath* GetDLLAccessPath(void);


#endif // HelixPlayer_h

