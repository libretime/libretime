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
    Version  : $Revision: 1.21 $
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

#include "LiveSupport/Core/Playlist.h"
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
 *  An interface to the php storage server.
 *
 *  This object has to be configured with an XML configuration element
 *  called webStorage. This element contains a child element
 *  specifying the location of the authentication server, and an attribute
 *  called tempFiles which specifies where the temporary playlist files are
 *  going to be created.  The name of the temp files will be the tempFiles
 *  attribute value, plus a random string, plus a ".smil" extension.
 *
 *  A authenticationClientFactory configuration element may look like the following:
 *
 *  <pre><code>
 *  &lt;webStorage
 *          tempFiles="file:///tmp/tempPlaylist" &gt;
 *      &lt;location
 *          server="localhost"
 *          port="80" 
 *          path="/storage/var/xmlrpc/xrLocStor.php"
 *      /&gt;
 *  &lt;/webStorage&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT webStorage (location) &gt;
 *  &lt;!ATTLIST webStorage tempFiles   CDATA       #REQUIRED &gt;
 *  &lt;!ELEMENT location EMPTY &gt;
 *  &lt;!ATTLIST location server        CDATA       #REQUIRED &gt;
 *  &lt;!ATTLIST location port          NMTOKEN     #REQUIRED &gt;
 *  &lt;!ATTLIST location path          CDATA       #REQUIRED &gt;
 *  </code></pre>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.21 $
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
         *  The type for the list of playlists which are currently being edited
         */
        typedef std::map<const UniqueId::IdType, 
                         std::pair<Ptr<SessionId>::Ref, Ptr<Playlist>::Ref> >
                                                            EditedPlaylistsType;

        /**
         *  The list of playlists which are currently being edited
         */
        EditedPlaylistsType         editedPlaylists;

        /**
         *  Auxilliary method used by editPlaylist() and createPlaylist().
         *  Opens the playlist for editing, and returns its URL.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @param url pointer in which the URL of the playlist is returned.
         *  @param token pointer in which the token of the playlist is returned.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
         *                             id exists.
         */
        void
        editPlaylistGetUrl(Ptr<SessionId>::Ref sessionId,
                           Ptr<UniqueId>::Ref  id,
                           Ptr<const std::string>::Ref& url,
                           Ptr<const std::string>::Ref& token)
                                                throw (XmlRpcException);

        /**
         *  A vector containing the unique IDs of the audio clips returned 
         *  by reset() (for testing) or by search().
         */
        Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref  audioClipIds;

        /**
         *  A vector containing the unique IDs of the playlists returned 
         *  by reset() (for testing) or by search().
         */
        Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref  playlistIds;


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~WebStorageClient(void)                 throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)              throw ()
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or the playlist has not been 
         *                             previously opened by getPlaylist() 
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or the playlist has no uri field,
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no playlist with the specified
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
         *  Since this makes no sense whatsoever, this method currently returns
         *  an empty list.  It will be replaced by a method which uses
         *  <code>locstor.searchMetadata</code>.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no audio clip with the 
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
         *  In this testing version, the audio clip URI is expected in the form
         *  <code>file</code><code>:relative_path/file_name.mp3</code>.
         *  Later this should be changed to an absolute URI.
         *
         *  The size of the binary file must be less than 2 GB, because the
         *  storage server can not deal with larger files.
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
                                                throw (XmlRpcException);

        /**
         *  Release the resource (sound file) used by an audio clip.
         *  After the call to this method, the binary sound file is no longer
         *  accessible, and the <code>uri</code> and <code>token</code> fields
         *  of the audioClip are erased (set to null pointers).
         *
         *  @param sessionId the session ID from the authentication client
         *  @param audioClip the id of the audio clip to release.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                  call or  the audio clip has no uri field, 
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
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call or no audio clip with the
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
         *  Since this makes no sense whatsoever, this method currently returns
         *  an empty list.  It will be replaced by a method which uses
         *  <code>locstor.searchMetadata</code>.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(Ptr<SessionId>::Ref sessionId) const
                                                throw (XmlRpcException);


        /**
         *  Reset the storage to its initial state.  
         *  Calls locstor.resetStorage, and puts the unique IDs returned
         *  into testAudioClipIds and testPlaylistIds.  Used for testing.
         *
         *  @exception XmlRpcException if the server returns an error.
         */
        void
        reset(void)
                                                throw (XmlRpcException);


        /**
         *  Search for audio clips or playlists.  The results can be read
         *  using getAudioClipIds() and getPlaylistIds().
         *
         *  @param sessionId the session ID from the authentication client
         *  @param searchCriteria an object containing the search criteria
         *  @return the number of items found.
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


} // namespace Core
} // namespace LiveSupport

#endif // WebStorageClient_h

