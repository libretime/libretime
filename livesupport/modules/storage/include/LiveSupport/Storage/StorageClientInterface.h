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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/include/LiveSupport/Storage/StorageClientInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Storage_StorageClientInterface_h
#define LiveSupport_Storage_StorageClientInterface_h

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
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Core/XmlRpcException.h"


namespace LiveSupport {
namespace Storage {

using namespace Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface for storage clients.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.4 $
 */
class StorageClientInterface
{
    public:
        /**
         *  Tell if a playlist with a given id exists.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to check for.
         *  @return true if a playlist with the specified id exists,
         *          false otherwise.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual const bool
        existsPlaylist(Ptr<SessionId>::Ref  sessionId,
                       Ptr<UniqueId>::Ref   id) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return a playlist with the specified id, to be displayed.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
         *                             id exists.
         */
        virtual Ptr<Playlist>::Ref
        getPlaylist(Ptr<SessionId>::Ref sessionId,
                    Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return a playlist with the specified id, to be edited.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
         *                             id exists.
         */
        virtual Ptr<Playlist>::Ref
        editPlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Save the playlist after editing.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param playlist the playlist to save.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or the playlist has not been 
         *                             previously opened by getPlaylist() 
         */
        virtual void
        savePlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<Playlist>::Ref  playlist) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Acquire the resources for the playlist.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to acquire.
         *  @return a new Playlist instance containing a uri field which
         *          points to an executable (playable) SMIL representation of
         *          the playlist (in the local storage).
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
         *                             specified id exists. 
         */
        virtual Ptr<Playlist>::Ref
        acquirePlaylist(Ptr<SessionId>::Ref sessionId,
                        Ptr<UniqueId>::Ref  id) const
                                            throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Release the resources (audio clips, other playlists) used 
         *  in a playlist.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param playlist the playlist to release.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or the playlist has no uri field,
         *                             or the file does not exist, etc.
         */
        virtual void
        releasePlaylist(Ptr<SessionId>::Ref  sessionId,
                        Ptr<Playlist>::Ref   playlist) const
                                            throw (XmlRpcException)
                                                                        = 0;
        /**
         *  Delete a playlist with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to be deleted.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
         *                             id exists.
         */
        virtual void
        deletePlaylist(Ptr<SessionId>::Ref  sessionId,
                       Ptr<UniqueId>::Ref   id)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return a list of all playlists in the playlist store.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(Ptr<SessionId>::Ref sessionId) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Create a new playlist.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return the newly created playlist.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Playlist>::Ref
        createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Tell if an audio clip with a given id exists.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to check for.
         *  @return true if an audio clip with the specified id exists,
         *          false otherwise.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual const bool
        existsAudioClip(Ptr<SessionId>::Ref sessionId,
                        Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return an audio clip with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to return.
         *  @return the requested audio clip.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no audio clip with the 
         *                             specified id exists.
         */
        virtual Ptr<AudioClip>::Ref
        getAudioClip(Ptr<SessionId>::Ref    sessionId,
                     Ptr<UniqueId>::Ref     id) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Store an audio clip.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param audioClip the audio clip to store.
         *
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or we have not logged in yet.
         */
        virtual void
        storeAudioClip(Ptr<SessionId>::Ref sessionId,
                       Ptr<AudioClip>::Ref audioClip)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Acquire the resources for the audio clip with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to acquire.
         *  @return a new AudioClip instance, containing a uri field which
         *          points to (a way of getting) the sound file.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or if no audio clip with the 
         *                             specified id exists. 
         */
        virtual Ptr<AudioClip>::Ref
        acquireAudioClip(Ptr<SessionId>::Ref  sessionId,
                         Ptr<UniqueId>::Ref   id) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Release the resource (sound file) used by an audio clip.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param audioClip the id of the audio clip to release.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                 call or  the audio clip has no uri field, 
         *                 or the file does not exist, etc. 
         */
        virtual void
        releaseAudioClip(Ptr<SessionId>::Ref sessionId,
                         Ptr<AudioClip>::Ref audioClip) const
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Delete an audio clip with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to be deleted.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no audio clip with the
         *                             specified id exists.
         */
        virtual void
        deleteAudioClip(Ptr<SessionId>::Ref   sessionId,
                        Ptr<UniqueId>::Ref    id)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return a list of all audio clips in the playlist store.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(Ptr<SessionId>::Ref sessionId) const
                                                throw (XmlRpcException)
                                                                        = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Storage
} // namespace LiveSupport

#endif // LiveSupport_Storage_StorageClientInterface_h

