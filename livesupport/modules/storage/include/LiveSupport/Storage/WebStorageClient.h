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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/include/LiveSupport/Storage/Attic/WebStorageClient.h,v $

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
#include "LiveSupport/Core/SessionId.h"


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
 *  @version $Revision: 1.1 $
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
         *  Decode an escaped %73%74%72%69%6E%67 to a normal string.
         *  This is really bad and low-level; to be replaced later.
         *  
         *  @return a pointer to a newly allocated string which contains
         *          the decoded value.
         */
        Ptr<std::string>::Ref
        decodeString(const std::string &inputString) const
                                                throw ();
        /**
         *  Convert a hex digit 0..9 | a..f | A..F to an int.
         *  This is used in decodeString().
         *  
         *  @return an int with the converted value.
         */
        int
        hexDigitToChar(const char &hexDigit) const
                                                throw ();

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
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to check for.
         *  @return true if a playlist with the specified id exists,
         *          false otherwise.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual const bool
        existsPlaylist(Ptr<SessionId>::Ref sessionId,
                       Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error);

        /**
         *  Return a playlist with the specified id, to be displayed.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception std::logic_error if no playlist with the specified
         *             id exists.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<Playlist>::Ref
        getPlaylist(Ptr<SessionId>::Ref sessionId,
                    Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error);

        /**
         *  Return a playlist with the specified id, to be edited.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception std::logic_error if no playlist with the specified
         *             id exists.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<Playlist>::Ref
        editPlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error);

        /**
         *  Save the playlist after editing.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param playlist the playlist to save.
         *  @exception std::logic_error if the playlist has not been previously
         *             opened by getPlaylist() 
         */
        virtual void
        savePlaylist(Ptr<SessionId>::Ref sessionId,
                     Ptr<Playlist>::Ref  playlist) const
                                            throw (std::logic_error);

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
         *  @exception std::logic_error if no playlist with the specified
         *             specified id exists. 
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<Playlist>::Ref
        acquirePlaylist(Ptr<SessionId>::Ref sessionId,
                        Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error);

        /**
         *  Release the resources (audio clips, other playlists) used 
         *  in a playlist.  The uri of the playlist is no longer valid, and 
         *  the uri field is deleted.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param playlist the playlist to release.
         *  @exception std::logic_error if the playlist has no uri field,
         *             or the file does not exist, etc.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual void
        releasePlaylist(Ptr<SessionId>::Ref sessionId,
                        Ptr<Playlist>::Ref  playlist) const
                                                throw (std::logic_error);

        /**
         *  Delete the playlist with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the playlist to be deleted.
         *  @exception std::logic_error if no playlist with the specified
         *             id exists.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual void
        deletePlaylist(Ptr<SessionId>::Ref sessionId,
                       Ptr<UniqueId>::Ref  id)
                                                throw (std::logic_error);

        /**
         *  Return a list of all playlists in the playlist store.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the playlists.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(Ptr<SessionId>::Ref sessionId) const
                                                throw (std::logic_error);

        /**
         *  Create a new playlist.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return the newly created playlist.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<Playlist>::Ref
        createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw (std::logic_error);

        /**
         *  Tell if an audio clip with a given id exists.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to check for.
         *  @return true if an audio clip with the specified id exists,
         *          false otherwise.
         *  @exception std::logic_error if we have not logged in yet
         */
        virtual const bool
        existsAudioClip(Ptr<SessionId>::Ref sessionId,
                        Ptr<UniqueId>::Ref  id) const
                                                 throw (std::logic_error);

        /**
         *  Return an audio clip with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to return.
         *  @return the requested audio clip.
         *  @exception std::logic_error if no audio clip with the 
         *             specified id exists.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<AudioClip>::Ref
        getAudioClip(Ptr<SessionId>::Ref sessionId,
                     Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error);

        /**
         *  Acquire the resources for the audio clip with the specified id.
         *
         *  Returns an AudioClip instance with a valid uri field, which points
         *  to the binary sound file.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to acquire.
         *  @return a new AudioClip instance, containing a uri field which
         *          points to (a way of getting) the sound file.
         *  @exception std::logic_error if no audio clip with the 
         *             specified id exists.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<AudioClip>::Ref
        acquireAudioClip(Ptr<SessionId>::Ref sessionId,
                         Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error);

        /**
         *  Release the resource (sound file) used by an audio clip.  The
         *  uri of the audio clip is no longer valid, and the uri field is
         *  deleted.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param audioClip the audio clip to release.
         *  @exception std::logic_error if the audio clip has no uri field, 
         *             or the file does not exist, etc.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual void
        releaseAudioClip(Ptr<SessionId>::Ref sessionId,
                         Ptr<AudioClip>::Ref audioClip) const
                                                throw (std::logic_error);

        /**
         *  Delete the audio clip with the specified id.
         *
         *  @param sessionId the session ID from the authentication client
         *  @param id the id of the audio clip to be deleted.
         *  @exception std::logic_error if no audio clip with the 
         *             specified id exists.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual void
        deleteAudioClip(Ptr<SessionId>::Ref sessionId,
                        Ptr<UniqueId>::Ref  id)
                                                throw (std::logic_error);

        /**
         *  Return a list of all audio clips in the playlist store.
         *
         *  @param sessionId the session ID from the authentication client
         *  @return a vector containing the audio clips.
         *  @exception std::logic_error if we have not logged in yet.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(Ptr<SessionId>::Ref sessionId) const
                                                throw (std::logic_error);

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // WebStorageClient_h

