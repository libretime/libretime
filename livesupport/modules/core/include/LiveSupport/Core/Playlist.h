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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/Playlist.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_Playlist_h
#define LiveSupport_Core_Playlist_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <map>
#include <stdexcept>
#include <libxml++/libxml++.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/PlaylistElement.h"


namespace LiveSupport {
namespace Core {

using namespace boost::posix_time;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing playlist.
 *  Playlist are containers for AudioClips, with the additional
 *  information of when and how each audio clip is played inside
 *  the playlist.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.4 $
 */
class Playlist : public Configurable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by Playlist.
         */
        static const std::string    configElementNameStr;

        /**
         *  The unique id of the playlist.
         */
        Ptr<UniqueId>::Ref          id;

        /**
         *  The playling length of the playlist.
         */
        Ptr<time_duration>::Ref     playlength;

        /**
         *  Flag set if playlist is currently playing.
         */
        bool                        isLockedForPlaying;

        /**
         *  Flag set if playlist is currently being edited.
         */
        bool                        isLockedForEditing;

        /**
         *  A map type for storing the playlist elements associated with 
         *  this playlist, indexed by their relative offsets.
         */
        typedef std::map<const time_duration, Ptr<PlaylistElement>::Ref>
                                                     PlaylistElementListType;

        /**
         *  The list of playlist elements for this playlist.
         */
        Ptr<PlaylistElementListType>::Ref  elementList;

        /**
         *  Add a new playlist element to the playlist.
         *
         *  @param playlistElement the new playlist element to be added
         *  @exception std::invalid_argument if the playlist already contains
         *             a playlist element with the same relative offset
         */
        void
        addPlaylistElement(Ptr<PlaylistElement>::Ref playlistElement)
                                                throw (std::invalid_argument);


    public:
        /**
         *  Default constructor.
         */
        Playlist(void)                          throw ()
        {
            this->isLockedForPlaying = false;
            this->isLockedForEditing = false;
        }

        /**
         *  Create a playlist by specifying all details.
         *  This is used for testing purposes.
         *
         *  @param id the id of the playlist.
         *  @param playlength the playing length of the playlist.
         */
        Playlist(Ptr<UniqueId>::Ref         id,
                 Ptr<time_duration>::Ref    playlength)     throw ()
        {
            this->id         = id;
            this->playlength = playlength;
            this->isLockedForPlaying = false;
            this->isLockedForEditing = false;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~Playlist(void)                         throw ()
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
         *  The supplied element is expected to be of the name
         *  returned by configElementName().
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument);

        /**
         *  Return the id of the playlist.
         *
         *  @return the unique id of the playlist.
         */
        Ptr<const UniqueId>::Ref
        getId(void) const                       throw ()
        {
            return id;
        }

        /**
         *  Return the total playing length for this playlist.
         *
         *  @return the playling length of this playlist, in milliseconds.
         */
        Ptr<const time_duration>::Ref
        getPlaylength(void) const               throw ()
        {
            return playlength;
        }

        /**
         *  Test whether the playlist is locked for editing.
         *
         *  @return true if playlist is locked, false if not
         */
        bool
        getIsLockedForEditing()                 throw ()
        {
            return isLockedForEditing;
        }

        /**
         *  Test whether the playlist is locked for playing.
         *
         *  @return true if playlist is locked, false if not
         */
        bool
        getIsLockedForPlaying()                 throw ()
        {
            return isLockedForPlaying;
        }

        /**
         *  Lock or unlock the playlist for editing.
         *
         *  @return true if successfully obtained or released lock;
         *          false otherwise.
         */
        bool
        setLockedForEditing(bool lockStatus)
                                                throw ();

        /**
         *  Lock or unlock the playlist for playing.
         *
         *  @return true if successfully obtained or released lock;
         *          false otherwise.
         */
        bool
        setLockedForPlaying(bool lockStatus)
                                                throw ();

        /**
         *  The iterator type for this class.
         *
         */
        typedef PlaylistElementListType::const_iterator  const_iterator;

        /**
         *  Get an iterator pointing to the first playlist element.
         *
         */
        const_iterator
        begin() const                           throw ()
        {
            return elementList->begin();
        }

        /**
         *  Get an iterator pointing to one after the last playlist element.
         *
         */
        const_iterator
        end() const                             throw ()
        {
            return elementList->end();
        }

        /**
         *  Add a new audio clip to the playlist.
         *
         *  @param relativeOffset the start of the audio clip, relative
         *             to the start of the playlist
         *  @param audioClip the new audio clip to be added
         *  @exception std::invalid_argument if the playlist already contains
         *             an audio clip with the same relative offset
         */
         void
         addAudioClip(Ptr<UniqueId>::Ref       audioClipId,
                      Ptr<time_duration>::Ref  relativeOffset)
                                                throw (std::invalid_argument);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Playlist_h

