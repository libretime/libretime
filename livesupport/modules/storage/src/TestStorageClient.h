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
    Version  : $Revision: 1.27 $
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

#include "LiveSupport/Core/Configurable.h"
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
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.27 $
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
                                                            PlaylistMapType;

        /**
         *  The map holding all contained playlists, by ids.
         */
        PlaylistMapType             playlistMap;

        /**
         *  The type for the list of playlists which are currently being edited
         */
        typedef std::map<const UniqueId::IdType, Ptr<Playlist>::Ref>
                                                            EditedPlaylistsType;

        /**
         *  The list of playlists which are currently being edited
         */
        EditedPlaylistsType         editedPlaylists;

        /**
         *  The map type containing the audio clips by their ids.
         */
        typedef std::map<const UniqueId::IdType, Ptr<AudioClip>::Ref>
                                                            AudioClipMapType;

        /**
         *  The map holding all contained audio clips, by ids.
         */
        AudioClipMapType            audioClipMap;

        /**
         *  The map type containing the URIs of the audio clips
         */
        typedef std::map<const UniqueId::IdType, Ptr<const std::string>::Ref>
                                                            AudioClipUrisType;

        /**
         *  The map type containing the URIs of the audio clips
         */
        AudioClipUrisType           audioClipUris;

        /**
         *  The path where the temporary SMIL files are strored.
         */
        std::string                 localTempStorage;

        /**
         *  A vector containing the unique IDs of the audio clips returned 
         *  by search().
         */
        Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref  audioClipIds;

        /**
         *  A vector containing the unique IDs of the playlists returned 
         *  by search().
         */
        Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref  playlistIds;

        /**
         *  Auxilliary method used by search().
         */
        bool 
        matchesCriteria(Ptr<Playable>::Ref         playable,
                        Ptr<SearchCriteria>::Ref   criteria)
                                                throw (XmlRpcException);

        /**
         *  Auxilliary method used by matchesCriteria().
         */
        bool 
        satisfiesCondition(
                        Ptr<Playable>::Ref                          playable,
                        const SearchCriteria::SearchConditionType & condition)
                                                throw (XmlRpcException);

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
         *  Create a new, empty, playlist.  Does not automatically open the
         *  playlist for editing; for that, use editPlaylist() and
         *  savePlaylist(). 
         *
         *  @param sessionId the session ID from the authentication client
         *  @return the ID of the newly created playlist.
         */
        virtual Ptr<UniqueId>::Ref
        createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException);


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
                                                throw (XmlRpcException);


        /**
         *  Return a playlist with the specified id to be displayed.
         *  If the playlist is being edited, and this method is called
         *  by the same user who is editing the playlist,
         *  (i.e., the method is called with the same sessionId and playlistId
         *  that editPlaylist() was), then the working copy of the playlist
         *  is returned.
         *  Any other user gets the old (pre-editPlaylist()) copy from storage.
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
         *  Return a playlist with the specified id to be edited.
         *  This puts a lock on the playlist, and nobody else can edit it
         *  until we release it using savePlaylist().
         *
         *  This method creates a working copy of the playlist, which will
         *  be returned by getPlaylist() if it is called with the same
         *  sessionId and playlistId, until we call savePlaylist().
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception XmlRpcException if no playlist with the specified
         *                             id exists.
         */
        virtual Ptr<Playlist>::Ref
        editPlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<UniqueId>::Ref  id)
                                                throw (XmlRpcException);


        /**
         *  Save the playlist after editing.
         *  Can only be called after we obtained a lock on the playlist using
         *  editPlaylist(); this method releases the lock.
         *
         *  This method destroys the working copy created by editPlaylist().
         *
         *  @param sessionId the session ID from the authentication client
         *  @param playlist the playlist to save.
         */
        virtual void
        savePlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<Playlist>::Ref  playlist)
                                                throw (XmlRpcException);


        /**
         *  Acquire the resources for the playlist.
         *  The last saved copy of the playlist is read, and a local copy
         *  is created in SMIL format.  (A local copy is also created for
         *  each sub-playlist contained in the playlist.)
         *  The address of this local copy is
         *  stored in the <code>uri</code> field of the playlist.  The SMIL
         *  file can be played using the Helix client.
         *  For each audio clip contained (directly or indirectly) in the
         *  playlist, acquireAudioClip() is called
         *
         *  The URI of the SMIL file is a random string
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
         *  For each audio clip contained (directly or indirectly) in the
         *  playlist, releaseAudioClip() is called, and the local copy of
         *  the playlist (and sub-playlists, if any) is removed.
         *  The <code>uri</code> field of the playlist is erased (set to
         *  a null pointer).
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
         *  Will refuse to delete the playlist if it is being edited (i.e., 
         *  has been opened with editPlaylist() but has not yet been released
         *  with savePlaylist()).
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
         *  This is for testing only; will be replaced by a search method.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(Ptr<SessionId>::Ref sessionId) const
                                                throw (XmlRpcException);


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
                                                throw (XmlRpcException);

        /**
         *  Return an audio clip with the specified id to be displayed.
         *  The audio clip returned contains all the metadata (title, author, 
         *  etc.) available for the audio clip, but no binary sound file.
         *  If you want to play the audio clip, use acquireAudioClip().
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
         *  The audio clip is expected to have valid <code>title</code>,
         *  <code>playlength</code> and <code>uri</code> fields, the latter
         *  containing the URI of a binary sound file.
         *  
         *  If the audio clip does not have
         *  an ID field (i.e., <code>audioClip->getId()</code> is a null 
         *  pointer), one will be generated, and <code>audioClip->getId()</code>
         *  will contain a valid UniqueId after the method returns.
         *  If the audio clip had an ID already, then it remains unchanged.
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
         *  The <code>uri</code> field of the audio clip returned by the
         *  method points to a binary sound file playable by the Helix client.
         *  This binary sound file can be randomly accessed.
         *
         *  The returned audio clip also contains a <code>token</code> field
         *  which identifies it to the storage server; this is used by
         *  releaseAudioClip().
         *
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
         *  After the call to this method, the binary sound file is no longer
         *  accessible, and the <code>uri</code> and <code>token</code> fields
         *  of the audioClip are erased (set to null pointers).
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
         *  This is for testing only; will be replaced by a search method.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(Ptr<SessionId>::Ref sessionId) const
                                                throw (XmlRpcException);


        /**
         *  Search for audio clips or playlists.  The results can be read
         *  using getAudioClipIds() and getPlaylistIds().
         *  
         *  If an audio clip or playlist does not have a metadata field X,
         *  it does not match any condition about field X.  In particular,
         *  a search for ("X", "partial", "") returns all records
         *  which contain a metadata field X.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param searchCriteria an object containing the search criteria
         *  @return the number of items found; this may not be equal to the 
         *          number of items returned: see SearchCriteria::setLimit()
         *          and SearchCriteria::setOffset()
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual int
        search(Ptr<SessionId>::Ref      sessionId,
               Ptr<SearchCriteria>::Ref searchCriteria) 
                                                throw (XmlRpcException);

        /**
         *  Return the list of audio clip IDs found by the search method.
         *
         *  (Or the list of audio clip IDs returned by the reset() method
         *  -- used for testing.)
         *
         *  @return a vector of UniqueId objects.
         */
        virtual Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref
        getAudioClipIds(void)                   throw ()
        {
            return audioClipIds;
        }


        /**
         *  Return the list of playlist IDs found by the search method.
         *
         *  (Or the list of playlist IDs returned by the reset() method
         *  -- used for testing.)
         *
         *  @return a vector of UniqueId objects.
         */
        virtual Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref
        getPlaylistIds(void)                    throw ()
        {
            return playlistIds;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */

        /**
         *  Auxilliary method used by satisfiesCondition().
         */
        void
        separateNameAndNameSpace(const std::string & key,
                                 std::string &       name,
                                 std::string &       nameSpace)
                                                throw ();

} // namespace Storage
} // namespace LiveSupport

#endif // TestStorageClient_h

