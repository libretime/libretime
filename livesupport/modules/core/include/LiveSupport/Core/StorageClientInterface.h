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
    Version  : $Revision: 1.11 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/Attic/StorageClientInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_StorageClientInterface_h
#define LiveSupport_Core_StorageClientInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Playlist.h"


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface for storage clients.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.11 $
 */
class StorageClientInterface
{
    public:
        /**
         *  Tell if a playlist with a given id exists.
         *
         *  @param id the id of the playlist to check for.
         *  @return true if a playlist with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsPlaylist(Ptr<const UniqueId>::Ref id) const       throw ()
                                                                        = 0;

        /**
         *  Return a playlist with the specified id.
         *
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception std::invalid_argument if no playlist with the specified
         *             id exists.
         */
        virtual Ptr<Playlist>::Ref
        getPlaylist(Ptr<const UniqueId>::Ref id) const
                                            throw (std::invalid_argument)
                                                                        = 0;

        /**
         *  Acquire the resources for the playlist.
         *
         *  @param id the id of the playlist to acquire.
         *  @return a new Playlist instance containing a uri field which
         *          points to an executable (playable) SMIL representation of
         *          the playlist (in the local storage).
         *  @exception std::invalid_argument if no playlist with the specified
         *             specified id exists. 
         */
        virtual Ptr<Playlist>::Ref
        acquirePlaylist(Ptr<const UniqueId>::Ref id) const
                                            throw (std::logic_error)
                                                                        = 0;

        /**
         *  Release the resources (audio clips, other playlists) used 
         *  in a playlist.
         *
         *  @param playlist the playlist to release.
         *  @exception std::logic_error if the playlist has no uri field,
         *             or the file does not exist, etc.
         */
        virtual void
        releasePlaylist(Ptr<const Playlist>::Ref playlist) const
                                            throw (std::logic_error)
                                                                        = 0;
        /**
         *  Delete a playlist with the specified id.
         *
         *  @param id the id of the playlist to be deleted.
         *  @exception std::invalid_argument if no playlist with the specified
         *             id exists.
         */
        virtual void
        deletePlaylist(Ptr<const UniqueId>::Ref id)
                                            throw (std::invalid_argument)
                                                                        = 0;

        /**
         *  Return a list of all playlists in the playlist store.
         *
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(void) const         throw ()                    = 0;


        /**
         *  Create a new playlist.
         *
         *  @return the newly created playlist.
         */
        virtual Ptr<Playlist>::Ref
        createPlaylist()                    throw ()                    = 0;

        /**
         *  Tell if an audio clip with a given id exists.
         *
         *  @param id the id of the audio clip to check for.
         *  @return true if an audio clip with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsAudioClip(Ptr<const UniqueId>::Ref id) const       throw ()
                                                                        = 0;
        /**
         *  Return an audio clip with the specified id.
         *
         *  @param id the id of the audio clip to return.
         *  @return the requested audio clip.
         *  @exception std::invalid_argument if no audio clip with the 
         *             specified id exists.
         */
        virtual Ptr<AudioClip>::Ref
        getAudioClip(Ptr<const UniqueId>::Ref id) const
                                            throw (std::invalid_argument)
                                                                        = 0;

        /**
         *  Acquire the resources for the audio clip with the specified id.
         *
         *  @param id the id of the audio clip to acquire.
         *  @return a new AudioClip instance, containing a uri field which
         *          points to (a way of getting) the sound file.
         *  @exception std::invalid_argument if no audio clip with the 
         *             specified id exists. 
         */
        virtual Ptr<AudioClip>::Ref
        acquireAudioClip(Ptr<const UniqueId>::Ref id) const
                                            throw (std::logic_error)
                                                                        = 0;

        /**
         *  Release the resource (sound file) used by an audio clip.
         *
         *  @param id the id of the audio clip to release.
         *  @exception std::logic_error if the audio clip has no uri field, 
         *             or the file does not exist, etc. 
         */
        virtual void
        releaseAudioClip(Ptr<const AudioClip>::Ref audioClip) const
                                            throw (std::logic_error)
                                                                        = 0;

        /**
         *  Delete an audio clip with the specified id.
         *
         *  @param id the id of the audio clip to be deleted.
         *  @exception std::invalid_argument if no audio clip with the
         *             specified id exists.
         */
        virtual void
        deleteAudioClip(Ptr<const UniqueId>::Ref id)
                                            throw (std::invalid_argument)
                                                                        = 0;

        /**
         *  Return a list of all audio clips in the playlist store.
         *
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(void) const         throw ()                    = 0;

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_StorageClientInterface_h

