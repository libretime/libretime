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
#include "LiveSupport/StorageClient/StorageClientInterface.h"


namespace LiveSupport {
namespace StorageClient {

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
 *  @author  $Author$
 *  @version $Revision$
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
         *  The version string of the test storage client.
         */
        Ptr<const Glib::ustring>::Ref   versionString;

        /**
         *  A copy of the configuration element stored to be used by reset()
         */
        Ptr<xmlpp::Document>::Ref   savedConfigurationElement;

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
         *  A vector containing the items returned by search() or by reset().
         */
        Ptr<SearchResultsType>::Ref localSearchResults;

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
         *  Return the version string from the storage.
         *
         *  @return the version string of the storage.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<const Glib::ustring>::Ref
        getVersion(void)                        throw (XmlRpcException);


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
        virtual bool
        existsPlaylist(Ptr<SessionId>::Ref          sessionId,
                       Ptr<const UniqueId>::Ref     id) const
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
        getPlaylist(Ptr<SessionId>::Ref       sessionId,
                    Ptr<const UniqueId>::Ref  id) const
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
        editPlaylist(Ptr<SessionId>::Ref      sessionId,
                     Ptr<const UniqueId>::Ref id)
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
         *  Revert a playlist to its pre-editing state.
         *  This is only used for clean-up after crashes.  If the GUI
         *  crashed while editing a playlist, it can release the lock on
         *  the playlist (and lose all changes) at the next login using
         *  this method.
         *
         *  @param editToken the token of the edited playlist
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
         *                             token exists.
         */
        virtual void
        revertPlaylist(Ptr<const std::string>::Ref    editToken)
                                                throw (XmlRpcException);


        /**
         *  Acquire the resources for the playlist.
         *  The last saved copy of the playlist is read, and a local copy
         *  is created in SMIL format.  (A local copy is also created for
         *  each sub-playlist contained in the playlist.)
         *  The address of this local copy is
         *  stored in the <code>uri</code> field of the playlist.  The SMIL
         *  file can be played using the audio player.
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
        acquirePlaylist(Ptr<SessionId>::Ref         sessionId,
                        Ptr<const UniqueId>::Ref    id) const
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
         *  @param playlist the playlist to release.
         *  @exception XmlRpcException if the playlist has no uri field,
         *                             or the file does not exist, etc.
         */
        virtual void
        releasePlaylist(Ptr<Playlist>::Ref   playlist) const
                                            throw (XmlRpcException);

        /**
         *  Tell if an audio clip with a given id exists.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to check for.
         *  @return true if an audio clip with the specified id exists,
         *          false otherwise.
         */
        virtual bool
        existsAudioClip(Ptr<SessionId>::Ref         sessionId,
                        Ptr<const UniqueId>::Ref    id) const
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
        getAudioClip(Ptr<SessionId>::Ref      sessionId,
                     Ptr<const UniqueId>::Ref id) const
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
         *  method points to a binary sound file playable by the audio player.
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
        acquireAudioClip(Ptr<SessionId>::Ref        sessionId,
                         Ptr<const UniqueId>::Ref   id) const
                                                throw (XmlRpcException);


        /**
         *  Release the resource (sound file) used by an audio clip.
         *  After the call to this method, the binary sound file is no longer
         *  accessible, and the <code>uri</code> and <code>token</code> fields
         *  of the audioClip are erased (set to null pointers).
         *
         *  @param audioClip the id of the audio clip to release.
         *  @exception XmlRpcException if the audio clip has no uri field, 
         *                  or the file does not exist, etc. 
         */
        virtual void
        releaseAudioClip(Ptr<AudioClip>::Ref audioClip) const
                                                throw (XmlRpcException);


        /**
         *  Reset the storage to its initial state.  
         *  Re-initializes the storage based on the xml element which was
         *  passed to configure() earlier; the new contents of the storage
         *  can be read using getLocalSearchResults().
         *  Used for testing.
         *
         *  @exception XmlRpcException if the server returns an error.
         */
        virtual void
        reset(void)
                                                throw (XmlRpcException);


        /**
         *  Search for audio clips or playlists.  The results can be read
         *  using getLocalSearchResults().
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
         *  Browse for metadata values.  Not implemented; always returns 0.
         *
         *  @param sessionId      the session ID from the authentication client
         *  @param metadataType   the type of metadata to browse for
         *  @param searchCriteria an object containing the search criteria
         *  @return a vector containing the metadata values found
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<std::vector<Glib::ustring> >::Ref
        browse(Ptr<SessionId>::Ref                  sessionId,
               Ptr<const Glib::ustring>::Ref        metadataType,
               Ptr<SearchCriteria>::Ref             searchCriteria) 
                                                throw (XmlRpcException)
        {
            Ptr<std::vector<Glib::ustring> >::Ref    null;
            return null;
        }

        /**
         *  Search for audio clips or playlists on a remote network hub.
         *
         *  This starts the asynchronous function call; check the progress
         *  of the search with checkTransport().
         *
         *  Once checkTransport() reports a finishedState, you can call
         *  remoteSearchClose() to get the search results.
         *
         *  @param  sessionId   the session ID from the authentication client.
         *  @param  searchCriteria  an object containing the search criteria.
         *  @return a transport token which identifies this search.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Glib::ustring>::Ref
        remoteSearchOpen(Ptr<SessionId>::Ref        sessionId,
                         Ptr<SearchCriteria>::Ref   searchCriteria) 
                                                throw (XmlRpcException);

        /**
         *  Download the search results after the remote search has finished.
         *
         *  If this search is in the finishedState, it will be moved to the
         *  closedState, the transport token will be invalidated, and the 
         *  search results can be read using getRemoteSearchResults().
         *
         *  If the search is in any other state, an exception is raised.
         *
         *  You can check the state of the search with checkTransport().
         *
         *  @param token     the transport token from remoteSearchOpen().
         *  @return the number of items found; this may not be equal to the 
         *          number of items returned: see SearchCriteria::setLimit()
         *          and SearchCriteria::setOffset().
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual int
        remoteSearchClose(Ptr<const Glib::ustring>::Ref     token) 
                                                throw (XmlRpcException);

        /**
         *  Return the list of items found by the local search method.
         *
         *  (Or the list of items returned by reset() -- used for testing.)
         *
         *  @return a vector of Playable objects.
         */
        virtual Ptr<SearchResultsType>::Ref
        getLocalSearchResults(void)              throw ()
        {
            return localSearchResults;
        }

        /**
         *  Return the list of items found by the remote search method.
         *  NOT IMPLEMENTED, always returns 0.
         *
         *  @return a vector of Playable objects.
         */
        virtual Ptr<SearchResultsType>::Ref
        getRemoteSearchResults(void)             throw ()
        {
            Ptr<SearchResultsType>::Ref     nullPointer;
            return nullPointer;
        }


        /**
         *  Return a list of all playlists in the storage.
         *  It uses the search method to get a list of playlists, passing
         *  the limit and offset parameters on to it.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param limit     the maximum number of playlists to return
         *  @param offset    skip the first <i>offset</i> playlists
         *  @return a vector containing the playlists.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(Ptr<SessionId>::Ref sessionId,
                        int                 limit  = 0,
                        int                 offset = 0)
                                                throw (XmlRpcException);


        /**
         *  Return a list of all audio clips in the storage.
         *  It uses the search method to get a list of playlists, passing
         *  the limit and offset parameters on to it.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param limit     the maximum number of audio clips to return
         *  @param offset    skip the first <i>offset</i> audio clips
         *  @return a vector containing the playlists.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(Ptr<SessionId>::Ref  sessionId,
                         int                  limit  = 0,
                         int                  offset = 0)
                                                throw (XmlRpcException);

        /**
         *  Initiate the creation of a storage backup.
         *  This is a dummy method; it just returns a fake token.
         *
         *  @param sessionId    the session ID from the authentication client.
         *  @param  criteria    specifies which items should go in the backup.
         *  @return a token which identifies this backup task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Glib::ustring>::Ref
        createBackupOpen(Ptr<SessionId>::Ref        sessionId,
                         Ptr<SearchCriteria>::Ref   criteria) const
                                                throw (XmlRpcException);
        
        /**
         *  Check the status of a storage backup.
         *  This is a dummy method; it always returns a pendingState.
         *
         *  @param  url     return parameter;
         *                      if a finishedState is returned, it contains the
         *                      URL of the created backup file.
         *  @param  path    return parameter;
         *                      if a finishedState is returned, it contains the
         *                      local access path of the created backup file.
         *  @param  errorMessage    return parameter;
         *                      if a failedState is returned, it contains the
         *                      fault string.
         *  @return the state of the backup process: one of pendingState,
         *                      finishedState, or failedState.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual AsyncState
        createBackupCheck(const Glib::ustring &             token,
                          Ptr<const Glib::ustring>::Ref &   url,
                          Ptr<const Glib::ustring>::Ref &   path,
                          Ptr<const Glib::ustring>::Ref &   errorMessage) const
                                                throw (XmlRpcException);
        
        /**
         *  Close the storage backup process.
         *  This is a dummy method; it does nothing.
         *
         *  @param  token           the identifier of this backup task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual void
        createBackupClose(const Glib::ustring &     token) const
                                                throw (XmlRpcException);

        /**
         *  Initiate the uploading of a storage backup to the local storage.
         *  This is a dummy method; it just returns a fake token.
         *
         *  @param  sessionId   the session ID from the authentication client.
         *  @param  path        the location of the archive file to upload.
         *  @return a token which identifies this task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Glib::ustring>::Ref
        restoreBackupOpen(Ptr<SessionId>::Ref               sessionId,
                          Ptr<const Glib::ustring>::Ref     path) const
                                                throw (XmlRpcException);
        
        /**
         *  Check the status of a backup restore.
         *  This is a dummy method; it always returns a pendingState.
         *
         *  @param  token       the identifier of this backup task.
         *  @param  errorMessage    return parameter;
         *                      if a failedState is returned, it contains the
         *                      fault string.
         *  @return the state of the restore process: one of pendingState,
         *                      finishedState, or failedState.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual AsyncState
        restoreBackupCheck(const Glib::ustring &            token,
                           Ptr<const Glib::ustring>::Ref &  errorMessage) const
                                                throw (XmlRpcException);
        
        /**
         *  Close the backup restore process.
         *
         *  @param  token       the identifier of this backup task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual void
        restoreBackupClose(const Glib::ustring &            token) const
                                                throw (XmlRpcException);
        
        /**
         *  Initiate the exporting of a playlist.
         *
         *  @param  sessionId   the session ID from the authentication client.
         *  @param  playlistId  the ID of the playlist to be exported.
         *  @param  format      the format of the exported playlist.
         *  @param  url         return parameter: readable URL pointing to the
         *                      exported playlist.
         *  @return a token which identifies this export task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Glib::ustring>::Ref
        exportPlaylistOpen(Ptr<SessionId>::Ref        sessionId,
                           Ptr<UniqueId>::Ref         playlistId,
                           ExportFormatType           format,
                           Ptr<Glib::ustring>::Ref    url) const
                                                throw (XmlRpcException);

        /**
         *  Close the playlist export process.
         *
         *  @param  token           the identifier of this export task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual void
        exportPlaylistClose(Ptr<const Glib::ustring>::Ref   token) const
                                                throw (XmlRpcException);

        /**
         *  Import a playlist archive to the local storage.
         *  This must be a tar file, in the LS Archive format, as produced
         *  by exportPlaylistOpen/Close() when called with the internalFormat
         *  parameter.
         *
         *  The size of the tar file must be less than 2 GB, because 
         *  the storage server can not deal with larger files.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param path      the path for the playlist archive file.
         *  @return          on success, the unique ID of the imported playlist.
         *
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or we have not logged in yet.
         */
        virtual Ptr<UniqueId>::Ref
        importPlaylist(Ptr<SessionId>::Ref              sessionId,
                       Ptr<const Glib::ustring>::Ref    path)       const
                                                throw (XmlRpcException);

        /**
         *  Check the status of the asynchronous network transport operation.
         *
         *  If the return value is
         *  <ul><li>initState or pendingState, then the operation
         *  is in progress, and you need to call this function again until
         *  a different value is returned;</li>
         *      <li>finishedState, then the asynchronous XML-RPC call has
         *  completed normally;</li>
         *      <li>closedState, then the transport has been
         *  closed or canceled, and the token is no longer valid;</li>
         *      <li>failedState, then an error has occured (and the token is
         *  no longer valid); the error message is returned in the (optional)
         *  errorMessage return parameter.
         *  </ul>
         *
         *  @param token        the transport token of an asynchronous method.
         *  @param errorMessage return parameter: if the transport has failed,
         *                      this will contain the error message (optional).
         *  @return the state of the transport.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual AsyncState
        checkTransport(Ptr<const Glib::ustring>::Ref    token,
                       Ptr<Glib::ustring>::Ref      errorMessage
                                                    = Ptr<Glib::ustring>::Ref())
                                                throw (XmlRpcException);

        /**
         *  Cancel an asynchronous network transport operation.
         *
         *  @param  sessionId   the session ID from the authentication client.
         *  @param token        the transport token of an asynchronous method.
         *  @return the state of the transport.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual void
        cancelTransport(Ptr<SessionId>::Ref             sessionId,
                        Ptr<const Glib::ustring>::Ref   token)
                                                throw (XmlRpcException);

        /**
         *  Upload an audio clip or playlist to the network hub.
         *  The progress of the upload process can be monitored with
         *  checkTransport().
         *
         *  @param  sessionId   the session ID from the authentication client.
         *  @param  id          the ID of the Playable object to be uploaded.
         *  @return a token which identifies this task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Glib::ustring>::Ref
        uploadToHub(Ptr<const SessionId>::Ref       sessionId,
                    Ptr<const UniqueId>::Ref        id)
                                                throw (XmlRpcException);

        /**
         *  Download an audio clip or playlist from the network hub.
         *  The progress of the upload process can be monitored with
         *  checkTransport().
         *
         *  @param  sessionId   the session ID from the authentication client.
         *  @param  id          the ID of the Playable object to be downloaded.
         *  @return a token which identifies this task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Glib::ustring>::Ref
        downloadFromHub(Ptr<const SessionId>::Ref       sessionId,
                        Ptr<const UniqueId>::Ref        id)
                                                throw (XmlRpcException);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */

        /**
         *  Auxilliary method used by satisfiesCondition().
         */
        void
        separateNameAndNameSpace(const std::string & key,
                                 std::string &       name,
                                 std::string &       prefix)
                                                throw ();

} // namespace StorageClient
} // namespace LiveSupport

#endif // TestStorageClient_h

