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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/Playable.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_Playable_h
#define LiveSupport_Core_Playable_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
#include <libxml++/libxml++.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Configurable.h"


namespace LiveSupport {
namespace Core {

using namespace std;
using namespace boost::posix_time;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A purely abstract class which is extended by AudioClip and Playlist.
 *  It contains the methods which are common to these classes.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class Playable
{
    public:

        /**
         *  Return the id of the audio clip or playlist.
         *
         *  @return the unique id of the audio clip or playlist.
         */
        virtual Ptr<UniqueId>::Ref
        getId(void) const                       throw () = 0;

        /**
         *  Return the total playing length for this audio clip or playlist.
         *
         *  @return the playing length in microseconds.
         */
        virtual Ptr<time_duration>::Ref
        getPlaylength(void) const               throw () = 0;

        /**
         *  Return the URI of the sound file of this audio clip or
         *  playlist, which can be played by the helix client.  This
         *  sound file can be an mp3 or a SMIL file.
         *
         *  @return the URI.
         */
        virtual Ptr<const string>::Ref
        getUri(void) const                      throw () = 0;

        /**
         *  Set the URI of the sound file of this audio clip or
         *  playlist, which can be played by the helix client.  This
         *  sound file can be an mp3 or a SMIL file.
         *
         *  @param uri the new URI.
         */
        virtual void
        setUri(Ptr<const string>::Ref uri)      throw () = 0;

        /**
         *  Return the token which is used to identify this audio clip
         *  or playlist to the storage server.
         *
         *  @return the token.
         */
        virtual Ptr<const string>::Ref
        getToken(void) const                    throw () = 0;

        /**
         *  Set the token which is used to identify this audio clip
         *  or playlist to the storage server.
         *
         *  @param token a new token.
         */
        virtual void
        setToken(Ptr<const string>::Ref token)  throw () = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Playable_h

