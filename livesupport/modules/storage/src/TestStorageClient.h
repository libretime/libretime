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
    Version  : $Revision: 1.22 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/TestStorageClient.h,v $

------------------------------------------------------------------------------*/
#ifndef TestStorageClient_h
#define TestStorageClient_h

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
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Storage/StorageClientInterface.h"


namespace LiveSupport {
namespace Storage {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A dummy storage client, only used for test purposes.
 *
 *  This object has to be configured with an XML configuration element
 *  called testStorage. This may look like the following:
 *
 *  <pre><code>
 *  &lt;testStorage tempFiles="file:///tmp/tempPlaylist" &gt;
 *      &lt;playlist&gt; ... &lt;/playlist&gt;
 *      ...
 *      &lt;audioClip&gt; ... &lt;/audioClip&gt;
 *      ...
 *  &lt;/testStorage&gt;
 *  </code></pre>
 *
 *  For detais of the playlist and audioClip elements, see the documentation 
 *  for the Core::Playlist and Core::AudioClip classes.
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT testStorage (playlist*, audioClip*) &gt;
 *  &lt;!ATTLIST testStorage tempFiles CDATA       #REQUIRED &gt;
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.22 $
 */
class TestStorageClient :
                    virtual public Configurable,
                    virtual public StorageClientInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by TestStorageClient
         */
        static const std::string    configElementNameStr;

        /**
         *  The map type containing the playlists by their ids.
         */
        typedef std::map<const UniqueId::IdType, Ptr<Playlist>::Ref>
                                                                PlaylistMap;

        /**
         *  The map holding all contained playlists, by ids.
         */
        PlaylistMap                 playlistMap;

        /**
         *  The map type containing the audio clips by their ids.
         */
        typedef std::map<const UniqueId::IdType, Ptr<AudioClip>::Ref>
                                                                AudioClipMap;

        /**
         *  The map holding all contained audio clips, by ids.
         */
        AudioClipMap                audioClipMap;

        /**
         *  The path where the temporary SMIL files are strored.
         */
        std::string                 localTempStorage;


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~TestStorageClient(void)                        throw ()
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
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument);


        /**
         *  Tell if a playlist with a given id exists.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to check for.
         *  @return true if a playlist with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsPlaylist(Ptr<SessionId>::Ref  sessionId,
                       Ptr<UniqueId>::Ref   id) const
                                                throw ();


        /**
         *  Return a playlist with the specified id, to be displayed.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception XmlRpcException if no playlist with the specified
         *                             id exists.
         */
        virtual Ptr<Playlist>::Ref
        getPlaylist(Ptr<SessionId>::Ref sessionId,
                    Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException);


        /**
         *  Return a playlist with the specified id, to be edited.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception XmlRpcException if no playlist with the specified
         *                             id exists.
         */
        virtual Ptr<Playlist>::Ref
        editPlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException);


        /**
         *  Save the playlist after editing.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param playlist the playlist to save.
         */
        virtual void
        savePlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<Playlist>::Ref  playlist) const
                                                throw ();


        /**
         *  Acquire the resources for the playlist.
         *
         *  The Playlist returned has a uri field (read using getUri())
         *  which points to a playable SMIL file.  This URI is a random string
         *  appended to the temp storage path read from the configuration file,
         *  plus a ".smil" extension.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to acquire.
         *  @return a new Playlist instance containing a uri field which
         *          points to an executable (playable) SMIL representation of
         *          the playlist (in the local storage).
         *  @exception XmlRpcException if no playlist with the specified
         *                             specified id exists. 
         */
        virtual Ptr<Playlist>::Ref
        acquirePlaylist(Ptr<SessionId>::Ref sessionId,
                        Ptr<UniqueId>::Ref  id) const
                                            throw (XmlRpcException);


        /**
         *  Release the resources (audio clips, other playlists) used 
         *  in a playlist.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param playlist the playlist to release.
         *  @exception XmlRpcException if the playlist has no uri field,
         *                             or the file does not exist, etc.
         */
        virtual void
        releasePlaylist(Ptr<SessionId>::Ref  sessionId,
                        Ptr<Playlist>::Ref   playlist) const
                                            throw (XmlRpcException);

        /**
         *  Delete a playlist with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to be deleted.
         *  @exception XmlRpcException if no playlist with the specified
         *                             id exists.
         */
        virtual void
        deletePlaylist(Ptr<SessionId>::Ref  sessionId,
                       Ptr<UniqueId>::Ref   id)
                                                throw (XmlRpcException);


        /**
         *  Return a list of all playlists in the playlist store.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(Ptr<SessionId>::Ref sessionId) const
                                                throw ();


        /**
         *  Create a new playlist.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return the newly created playlist.
         */
        virtual Ptr<Playlist>::Ref
        createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw ();


        /**
         *  Tell if an audio clip with a given id exists.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to check for.
         *  @return true if an audio clip with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsAudioClip(Ptr<SessionId>::Ref sessionId,
                        Ptr<UniqueId>::Ref  id) const
                                                throw ();

        /**
         *  Return an audio clip with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to return.
         *  @return the requested audio clip.
         *  @exception XmlRpcException if no audio clip with the 
         *                             specified id exists.
         */
        virtual Ptr<AudioClip>::Ref
        getAudioClip(Ptr<SessionId>::Ref    sessionId,
                     Ptr<UniqueId>::Ref     id) const
                                                throw (XmlRpcException);

        /**
         *  Store an audio clip.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param audioClip the audio clip to store.
         *
         *  @exception XmlRpcException if we have not logged in yet.
         */
        virtual void
        storeAudioClip(Ptr<SessionId>::Ref sessionId,
                       Ptr<AudioClip>::Ref audioClip)
                                                throw (XmlRpcException);

        /**
         *  Acquire the resources for the audio clip with the specified id.
         *
         *  Returns an AudioClip instance with a valid uri field, which points
         *  to the binary sound file.
         *  Assumes URIs in the config file are relative paths prefixed by
         *  "file:"; e.g., "file:var/test1.mp3".
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to acquire.
         *  @return a new AudioClip instance, containing a uri field which
         *          points to (a way of getting) the sound file.
         *  @exception XmlRpcException if no audio clip with the 
         *                             specified id exists. 
         */
        virtual Ptr<AudioClip>::Ref
        acquireAudioClip(Ptr<SessionId>::Ref  sessionId,
                         Ptr<UniqueId>::Ref   id) const
                                                throw (XmlRpcException);


        /**
         *  Release the resource (sound file) used by an audio clip.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param audioClip the id of the audio clip to release.
         *  @exception XmlRpcException if the audio clip has no uri field, 
         *                  or the file does not exist, etc. 
         */
        virtual void
        releaseAudioClip(Ptr<SessionId>::Ref sessionId,
                         Ptr<AudioClip>::Ref audioClip) const
                                                throw (XmlRpcException);


        /**
         *  Delete an audio clip with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to be deleted.
         *  @exception XmlRpcException if no audio clip with the
         *                             specified id exists.
         */
        virtual void
        deleteAudioClip(Ptr<SessionId>::Ref   sessionId,
                        Ptr<UniqueId>::Ref    id)
                                                throw (XmlRpcException);


        /**
         *  Return a list of all audio clips in the playlist store.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(Ptr<SessionId>::Ref sessionId) const
                                                throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // TestStorageClient_h

