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
#ifndef LiveSupport_PlaylistExecutor_AudioPlayerEventListener_h
#define LiveSupport_PlaylistExecutor_AudioPlayerEventListener_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <exception>
#include <stdexcept>
#include <glibmm/ustring.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace boost;

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An event listener interface, for catching events of an audio player.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see AudioPlayerInterface
 */
class AudioPlayerEventListener
{
    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~AudioPlayerEventListener(void)                     throw ()
        {
        }

        /**
         *  Catch the event when playing has stopped.
         *  This will happen probably due to the fact that the
         *  audio clip has been played to its end.
         *
         *  @param errorMessage is a 0 pointer if the player stopped normally
         */
        virtual void
        onStop(Ptr<const Glib::ustring>::Ref  errorMessage)
                                                            throw () = 0;

        /**
         *  Catch the event when playback started.
         *  Every time player plays a new file, it sends this event at the beginning.
         *  App should use this to synchronize playback
         *
         *  @param id represents the file that just started
         */
        virtual void
        onStart(gint64 id)
                                                            throw () = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport


#endif // LiveSupport_PlaylistExecutor_AudioPlayerEventListener_h

