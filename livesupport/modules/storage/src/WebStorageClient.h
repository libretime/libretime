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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/WebStorageClient.h,v $

------------------------------------------------------------------------------*/
#ifndef WebStorageClient_h
#define WebStorageClient_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/StorageClientInterface.h"


namespace LiveSupport {
namespace Storage {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface to the (possibly remote) php storage server.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.2 $
 */
class WebStorageClient :
                    virtual public Configurable,
                    virtual public StorageClientInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by WebStorageClient
         */
        static const std::string    configElementNameStr;

        /**
         *  The path where the temporary SMIL files are strored.
         */
        std::string                 localTempStorage;

        /**
         *  The name of the storage server, e.g. "myserver.mycompany.com".
         */
        std::string                 storageServerName;

        /**
         *  The port wher the storage server is listening (default is 80).
         */
        int                         storageServerPort;

        /**
         *  The path to the storage server php page.
         */
        std::string                 storageServerPath;

        /**
         *  The login name to the storage server.
         */
        std::string                 storageServerLogin;

        /**
         *  The password to the storage server.
         */
        std::string                 storageServerPassword;

        /**
         *  Login to the storage server, using the data read from the
         *  configuration file.  If successful, a new session ID is returned.
         *
         *  @return the new session ID
         */
        std::string
        loginToStorageServer(void) const               throw ();

        /**
         *  Logout from the storage server.  The parameter is the ID of
         *  the session to end (returned previously by storageServerLogin()).
         *
         *  @param sessionId the ID of the session to end
         */
        void
        logoutFromStorageServer(std::string sessionId) const
                                                       throw ();


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~WebStorageClient(void)                        throw ()
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
         *  Tell if a playlist with a given id exists.
         *
         *  @param id the id of the playlist to check for.
         *  @return true if a playlist with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsPlaylist(Ptr<const UniqueId>::Ref id) const
                                                                throw ();

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
                                            throw (std::invalid_argument);

        /**
         *  Acquire the resources for the playlist.
         *
         *  The Playlist returned has a uri field (read using getUri())
         *  which points to a playable SMIL file.  This URI is a random string
         *  appended to the temp storage path read from the configuration file,
         *  plus a ".smil" extension.
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
                                            throw (std::logic_error);

        /**
         *  Release the resources (audio clips, other playlists) used 
         *  in a playlist.  The uri of the playlist is no longer valid, and 
         *  the uri field is deleted.
         *
         *  @param playlist the playlist to release.
         *  @exception std::logic_error if the playlist has no uri field,
         *             or the file does not exist, etc.
         */
        virtual void
        releasePlaylist(Ptr<Playlist>::Ref playlist) const
                                            throw (std::logic_error);

        /**
         *  Delete the playlist with the specified id.
         *
         *  @param id the id of the playlist to be deleted.
         *  @exception std::invalid_argument if no playlist with the specified
         *             id exists.
         */
        virtual void
        deletePlaylist(Ptr<const UniqueId>::Ref id)
                                            throw (std::invalid_argument);

        /**
         *  Return a list of all playlists in the playlist store.
         *
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(void) const         throw ();

        /**
         *  Create a new playlist.
         *
         *  @return the newly created playlist.
         */
        virtual Ptr<Playlist>::Ref
        createPlaylist()                    throw ();

        /**
         *  Tell if an audio clip with a given id exists.
         *
         *  @param id the id of the audio clip to check for.
         *  @return true if an audio clip with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsAudioClip(Ptr<const UniqueId>::Ref id) const
                                            throw ();

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
                                            throw (std::invalid_argument);

        /**
         *  Acquire the resources for the audio clip with the specified id.
         *
         *  Returns an AudioClip instance with a valid uri field, which points
         *  to the binary sound file.
         *
         *  @param id the id of the audio clip to acquire.
         *  @return a new AudioClip instance, containing a uri field which
         *          points to (a way of getting) the sound file.
         *  @exception std::invalid_argument if no audio clip with the 
         *             specified id exists. 
         */
        virtual Ptr<AudioClip>::Ref
        acquireAudioClip(Ptr<const UniqueId>::Ref id) const
                                            throw (std::logic_error);

        /**
         *  Release the resource (sound file) used by an audio clip.  The
         *  uri of the audio clip is no longer valid, and the uri field is
         *  deleted.
         *
         *  @param id the id of the audio clip to release.
         *  @exception std::logic_error if the audio clip has no uri field, 
         *             or the file does not exist, etc. 
         */
        virtual void
        releaseAudioClip(Ptr<AudioClip>::Ref audioClip) const
                                            throw (std::logic_error);

        /**
         *  Delete the audio clip with the specified id.
         *
         *  @param id the id of the audio clip to be deleted.
         *  @exception std::invalid_argument if no audio clip with the 
         *             specified id exists.
         */
        virtual void
        deleteAudioClip(Ptr<const UniqueId>::Ref id)
                                            throw (std::invalid_argument);

        /**
         *  Return a list of all audio clips in the playlist store.
         *
         *  @return a vector containing the audio clips.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(void) const         throw ();

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // WebStorageClient_h

