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
    Version  : $Revision: 1.6 $
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
#include <boost/enable_shared_from_this.hpp>
#include <glibmm/ustring.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Configurable.h"


namespace LiveSupport {
namespace Core {

class   AudioClip;      // forward declarations to avoid circularity
class   Playlist;

using namespace boost::posix_time;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An abstract class which is extended by AudioClip and Playlist.
 *  It contains the methods which are common to these classes.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.6 $
 */
class Playable : public boost::enable_shared_from_this<Playable>
{
    public:

        /**
         *  The sub-types a Playable object can belong to.
         */
        enum Type { AudioClipType, PlaylistType };
 
    private:
 
        /**
         *  The type of this playable object (audio clip or playlist).
         */
        Type        type;
 
    protected:

        /**
         *  Only my children are allowed to instantiate me.
         *
         *  @param typeParam  either AudioClipType or PlaylistType.
         */
        Playable(Type   typeParam)              throw ()
                    : type(typeParam)
        {
        }
 

    public:

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~Playable(void)                         throw ()
        {
        }

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
        virtual Ptr<const std::string>::Ref
        getUri(void) const                      throw () = 0;

        /**
         *  Set the URI of the sound file of this audio clip or
         *  playlist, which can be played by the helix client.  This
         *  sound file can be an mp3 or a SMIL file.
         *
         *  @param uri the new URI.
         */
        virtual void
        setUri(Ptr<const std::string>::Ref uri) throw () = 0;


        /**
         *  Return the token which is used to identify this audio clip
         *  or playlist to the storage server.
         *
         *  @return the token.
         */
        virtual Ptr<const std::string>::Ref
        getToken(void) const                    throw () = 0;

        /**
         *  Set the token which is used to identify this audio clip
         *  or playlist to the storage server.
         *
         *  @param token a new token.
         */
        virtual void
        setToken(Ptr<const std::string>::Ref token) 
                                                throw () = 0;


        /**
         *  Return the title of this audio clip or playlist.
         *
         *  @return the title.
         */
        virtual Ptr<const Glib::ustring>::Ref
        getTitle(void) const                    throw () = 0;

        /**
         *  Set the title of this audio clip or playlist.
         *
         *  @param title a new title.
         */
        virtual void
        setTitle(Ptr<const Glib::ustring>::Ref title)
                                                throw () = 0;


        /**
         *  Return the value of a metadata field in this audio clip or playlist.
         *
         *  @param  key  the name of the metadata field
         *  @param  ns   the namespace of the metadata field (optional)
         *  @return the value of the metadata field; 0 if there is 
         *          no such field;
         */
        virtual Ptr<Glib::ustring>::Ref
        getMetadata(const std::string &key, const std::string &ns = "") const
                                                throw () = 0;

        /**
         *  Set the value of a metadata field in this audio clip or playlist.
         *
         *  @param value the new value of the metadata field.
         *  @param  key  the name of the metadata field
         *  @param  ns   the namespace of the metadata field (optional)
         */
        virtual void
        setMetadata(Ptr<const Glib::ustring>::Ref value, 
                    const std::string &key, 
                    const std::string &ns = "")
                                                throw () = 0;

        /**
         *  Return an XML representation of this audio clip or playlist.
         *  This consists of minimal information only, without any metadata.
         *
         *  @return a string representation of the audio clip in XML
         */
       virtual Ptr<Glib::ustring>::Ref
       getXmlString(void)                       throw () = 0;


        /**
         *  Return the type of this object.
         *
         *  @return either AudioClipType or PlaylistType.
         */
        Type
        getType(void) const                                throw ()
        {
            return type;
        }
 
        /**
         *  Return an audio clip pointer to this object.  If the object's
         *  type is not AudioClipType, returns a zero pointer.
         *
         *  @see getType()
         *  @return an audio clip pointer to this object.
         */
        Ptr<AudioClip>::Ref
        getAudioClip(void)                                 throw ();
 
        /**
         *  Return a playlist pointer to this object.  If the object's
         *  type is not PlaylistType, returns a zero pointer.
         *
         *  @see getType()
         *  @return a playlist pointer to this object.
         */
        Ptr<Playlist>::Ref
        getPlaylist(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Playable_h

