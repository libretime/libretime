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
#ifndef TestEventListener_h
#define TestEventListener_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/PlaylistExecutor/AudioPlayerEventListener.h"


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace boost;

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A simple event listener, used for testing.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class TestEventListener : public AudioPlayerEventListener
{
    public:
        /**
         *  A flag set by the onStop() event to true.
         */
        bool        stopFlag;

        /**
         *  Constructor
         */
        TestEventListener(void)                             throw ()
        {
            stopFlag = false;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~TestEventListener(void)                            throw ()
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
                                                            throw ()
        {
            stopFlag = true;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport


#endif // TestEventListener_h

