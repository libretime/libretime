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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/include/LiveSupport/PlaylistExecutor/AudioPlayerInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef AudioPlayerInterface_h
#define AudioPlayerInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <exception>
#include <stdexcept>


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace LiveSupport;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A generic interface for playing audio files.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.3 $
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
        playThis(const std::string  fileUrl)    throw (std::invalid_argument)
                                                                        = 0;

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
        start(void)                             throw (std::logic_error)
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
         *
         *  @exception std::logic_error if there was no previous call to
         *             start()
         */
        virtual void
        stop(void)                              throw (std::logic_error)
                                                                      = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport


#endif // AudioPlayerInterface_h

