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
    Version  : $Revision: 1.33 $
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
#include <XmlRpcValue.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/Playable.h"
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
 *  This object has to be configured with an XML configuration element
 *  called playlist. This may look like the following:
 *
 *  <pre><code>
 *  &lt;playlist id="1" title="My Playlist" playlength="00:18:30.000000" &gt;
 *      &lt;playlistElement&gt; ... &lt;/playlistElement&gt;
 *      ...
 *      &lt;playlistElement&gt; ... &lt;/playlistElement&gt;
 *      &lt;metadata
 *                xmlns="http://www.streamonthefly.org/"
 *                xmlns:dc="http://purl.org/dc/elements/1.1/"
 *                xmlns:dcterms="http://purl.org/dc/terms/"
 *                xmlns:xbmf="http://www.streamonthefly.org/xbmf"
 *                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" &gt;
 *             &lt;dc:title  &gt;File Title txt&lt;/dc:title&gt;
 *             &lt;dcterms:extent  &gt;00:02:30.000000&lt;/dcterms:extent&gt;
 *             ...
 *         &lt;/metadata&gt;
 *  &lt;/playlist&gt;
 *  </code></pre>
 *
 *  For detais of the playlistElement element, see the documentation 
 *  for the PlaylistElement class.
 *
 *  The metadata element is optional.  The <code>configure()</code> method
 *  sets only those fields which had not been set previously: e.g., if we set
 *  some or all fields of the Playlist in the constructor, then these fields
 *  in the XML element will be ignored by <code>configure()</code>.
 *  The <code>title</code> attribute and the <code>&lt;dc:title&gt;</code> 
 *  element set the same field; if both are present, the title is set from
 *  the attribute and the element is ignored..
 *  The same is true for the <code>playlength</code> attribute and the 
 *  <code>&lt;dcterms:extent&gt;</code> element.
 *  It is required that by the end of the configure() method, the playlength
 *  is set somehow (from a constructor, the attribute or the element).
 *  If the title is not set by the end of the configure() method, it is then
 *  set to the empty string.
 *  Embedded XML elements are currently ignored: e.g., 
 *  <pre><code>  &lt;group&gt;
 *      &lt;member1&gt;value1&lt;/member1&gt;
 *      &lt;member2&gt;value2&lt;/member2&gt;
 *  &lt;/group&gt;</code></pre>
 *  produces a single metadata field <code>group</code> with an empty value,
 *  and ignores <code>member1</code> and <code>member2</code>.
 *  TODO: fix this?
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT playlist (playlistElement*, metadata?) &gt;
 *  &lt;!ATTLIST playlist  id           NMTOKEN    #REQUIRED &gt;
 *  &lt;!ATTLIST playlist  title        CDATA      ""  &gt;
 *  &lt;!ATTLIST playlist  playlength   NMTOKEN    #IMPLIED  &gt;
 *  </code></pre>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.33 $
 */
class Playlist : public Configurable,
                 public Playable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by Playlist.
         */
        static const std::string    configElementNameStr;

        /**
         *  The unique id of the playlist.
         */
        Ptr<UniqueId>::Ref              id;

        /**
         *  The title of the playlist.
         */
        Ptr<const Glib::ustring>::Ref   title;

        /**
         *  The playling length of the playlist.
         */
        Ptr<time_duration>::Ref         playlength;

        /**
         *  The uri of the SMIL file generated from this playlist (if any).
         */
        Ptr<const std::string>::Ref     uri;

        /**
         *  The token given to this playlist by the storage server.
         */
        Ptr<const std::string>::Ref     token;

        /**
         *  A map type for storing the playlist elements associated with 
         *  this playlist, indexed by their relative offsets.
         */
        typedef std::multimap<time_duration, Ptr<PlaylistElement>::Ref>
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

        /**
         *  A saved copy of this playlist.
         */
        Ptr<Playlist>::Ref          savedCopy;


        /**
         *  This playlist in XML format.
         */
        Ptr<xmlpp::Document>::Ref       xmlPlaylist;

        /**
         *  Set the value of a metadata field in this playlist.
         *
         *  @param value the new value of the metadata field.
         *  @param name    the name of the metadata field (without prefix)
         *  @param prefix  the prefix of the metadata field
         */
        void
        setMetadata(Ptr<const Glib::ustring>::Ref value, 
                    const std::string &name, const std::string &prefix)
                                                throw ();

        /**
         *  Set the total playing length of this playlist.
         *
         *  @param the playing length in microseconds precision.
         */
        void
        setPlaylength(Ptr<time_duration>::Ref playlength) 
                                                throw ();

        /**
         *  A private iterator type for internal use.  It is non-constant;
         *  otherwise it is the same as Playlist::const_iterator.
         */
        typedef PlaylistElementListType::iterator  iterator;

        /**
         *  Get an iterator pointing to a playlist element with a given ID.
         *
         *  @param playlistElementId (a pointer to) the ID of the
         *                  playlist element.
         *  @return an iterator to the playlist element if it exists,
         *                  or <code>this->end()</code> if it does not.
         */
        iterator
        find(Ptr<UniqueId>::Ref playlistElementId)
                                                throw ();

        /**
         *  Convert a time_duration to string, in format HH:MM:SS.ssssss.
         */
        std::string
        toFixedString(Ptr<time_duration>::Ref time) const
                                                throw ()
        {
            if (time->fractional_seconds()) {
                return to_simple_string(*time);
            } else {
                return to_simple_string(*time) + ".000000";
            }
        }


    public:
        /**
         *  Copy constructor.
         *
         *  Copies the <i>pointers</i> for all fields except elementList, 
         *  savedCopy and metadata.  A new copy of these three are created,
         *  but the playlists and strings contained in elementList and
         *  metadata are not duplicated, only a new pointer to them is created.
         *  The remaining fields are immutable; if you want to modify them, 
         *  call the appropriate setter function with (a pointer to) an object
         *  with the new value.
         *
         *  @param otherPlaylist the playlist to be copied
         */
        Playlist(const Playlist & otherPlaylist)
                                                throw ();

        /**
         *  Default constructor.
         *
         *  NOTE: this constructor creates a Playlist with a null pointer 
         *  for both the ID and the playlength fields!  It is meant for
         *  internal use only.
         *
         *  If you want to create an empty Playlist, use the storage client:
         *  <pre><code>
         *  Ptr<StorageClientFactory>::Ref
         *          storageClientFactory = StorageClientFactory::getInstance();
         *  Ptr<StorageClientInterface>::Ref
         *          storageClient = storageClientFactory->getStorageClient();
         *  Ptr<Playlist>::Ref
         *          playlist = storageClient->createPlaylist(sessionId);
         *  </code></pre>
         *
         *  @see Storage::StorageClientFactory
         *  @see Storage::StorageClientInterface
         */
        Playlist(void)                          throw ()
                        : Playable(PlaylistType)
        {
            elementList.reset(new PlaylistElementListType);
        }

        /**
         *  Create a playlist by specifying its ID only.
         *
         *  For internal use; see the note at the default constructor.
         */
        Playlist(Ptr<UniqueId>::Ref id)         throw ()
                        : Playable(PlaylistType)
        {
            this->id         = id;
            
            elementList.reset(new PlaylistElementListType);
        }

        /**
         *  Create a playlist by specifying all details, except the title.
         *
         *  This is used for testing purposes; 
         *  see the note at the default constructor.
         *
         *  @param id the id of the playlist.
         *  @param playlength the playing length of the playlist.
         *  @param uri the location of the SMIL file representing this
         *             playlist (optional)
         */
        Playlist(Ptr<UniqueId>::Ref             id,
                 Ptr<time_duration>::Ref        playlength,
                 Ptr<const std::string>::Ref    uri = Ptr<std::string>::Ref())
                                                throw ();

        /**
         *  Create a playlist by specifying all details.
         *
         *  This is used for testing purposes; 
         *  see the note at the default constructor.
         *
         *  @param id the id of the playlist.
         *  @param playlength the playing length of the playlist.
         *  @param uri the location of the SMIL file representing this
         *             playlist (optional)
         */
        Playlist(Ptr<UniqueId>::Ref             id,
                 Ptr<const Glib::ustring>::Ref  title,
                 Ptr<time_duration>::Ref        playlength,
                 Ptr<const std::string>::Ref    uri = Ptr<std::string>::Ref())
                                                throw ();

        /**
         *  Convert the playlist to an XmlRpcValue (marshalling).
         *
         *  @return an XmlRpcValue struct, containing a
         *         field named <i>playlist</i>, with value of type string,
         *         which contains an XML document representing the playlist.
         */
        operator XmlRpc::XmlRpcValue() const
                                                throw ();

        /**
         *  Construct a playlist from an XmlRpcValue (demarshalling).
         *
         *  @param xmlRpcValue an XmlRpcValue struct, containing a
         *         field named <i>playlist</i>, with value of type string,
         *         which contains an XML document, the root node of which 
         *         can be passed to the configure() method.
         *  @exception std::invalid_argument if the argument is invalid
         */
        Playlist(XmlRpc::XmlRpcValue &  xmlRpcValue)
                                                throw (std::invalid_argument);


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
        getConfigElementName(void)               throw ()
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
        virtual Ptr<UniqueId>::Ref
        getId(void) const                       throw ()
        {
            return id;
        }


        /**
         *  Return the title of this playlist.
         *
         *  @return the title.
         */
        virtual Ptr<const Glib::ustring>::Ref
        getTitle(void) const                    throw ()
        {
            return title;
        }

        /**
         *  Set the title of this playlist.
         *
         *  @param title a new title.
         */
        virtual void
        setTitle(Ptr<const Glib::ustring>::Ref title)
                                                throw ();


        /**
         *  Return the total playing length for this playlist.
         *
         *  @return the playing length in microseconds precision.
         */
        virtual Ptr<time_duration>::Ref
        getPlaylength(void) const               throw ()
        {
            return playlength;
        }

        /**
         *  Return the URI of the SMIL file created from this
         *  playlist, which can be played by the helix client.
         *
         *  @return the URI.
         */
        virtual Ptr<const string>::Ref
        getUri(void) const                      throw ()
        {
            return uri;
        }

        /**
         *  Set the URI of the SMIL file created from this
         *  playlist, which can be played by the helix client.
         *
         *  @param uri the new URI.
         */
        virtual void
        setUri(Ptr<const std::string>::Ref uri) throw ()
        {
            this->uri = uri;
        }

        /**
         *  Return the token which is used to identify this
         *  playlist to the storage server.
         *
         *  @return the token.
         */
        virtual Ptr<const std::string>::Ref
        getToken(void) const                    throw ()
        {
            return token;
        }

        /**
         *  Set the token which is used to identify this
         *  playlist to the storage server.
         *
         *  @param token a new token.
         */
        virtual void
        setToken(Ptr<const std::string>::Ref token) 
                                                throw ()
        {
            this->token = token;
        }

        /**
         *  Test whether the playlist is locked for editing.
         *
         *  @return true if the playlist is currently being edited
         */
        bool
        isLocked() const                         throw ()
        {
            return (token.get() != 0);
        }


        /**
         *  The iterator type for this class.  A Playlist::const_iterator
         *  is a (constant) pointer to a <code>pair &lt; time_duration,
         *  Ptr&lt;PlaylistElement&gt;::Ref &gt;</code>.
         *  If <code>it</code> is such an iterator, then <code>it->second</code>
         *  is the playlist element referenced by the iterator, and
         *  <code>it->first</code> is its relative offset in the playlist.
         *  The playlist elements are listed in the order of their relative
         *  offset (starting time).
         *
         *  @see begin(), end(), find()
         */
        typedef PlaylistElementListType::const_iterator  const_iterator;

        /**
         *  Get an iterator pointing to the first playlist element.
         */
        const_iterator
        begin() const                           throw ()
        {
            return elementList->begin();
        }

        /**
         *  Get an iterator pointing to one after the last playlist element.
         */
        const_iterator
        end() const                             throw ()
        {
            return elementList->end();
        }

        /**
         *  Get an iterator pointing to the first playlist element at a given
         *  relative offset.
         *  
         *  @param relativeOffset (a pointer to) the relative offset where
         *                        the playlist element is.
         *  @return a constant iterator to the playlist element if it exists,
         *          or <code>this->end()</code> if it does not.
         */
        const_iterator
        find(Ptr<const time_duration>::Ref relativeOffset) const
                                                throw ()
        {
            return elementList->find(*relativeOffset);
        }

        /**
         *  Return the number of playlist elements in the playlist.
         */
        int
        size() const                            throw ()
        {
            return elementList->size();
        }

        /**
         *  Add a new audio clip to the playlist.
         *
         *  The playlist is not checked for gaps (use valid() for that),
         *  but the playlength is adjusted if the new audio clip is added
         *  at the end of the playlist.
         *
         *  @param audioClip the new audio clip to be added
         *  @param relativeOffset the start of the audio clip, relative
         *             to the start of the playlist
         *  @param fadeInfo the fade in / fade out info (optional)
         *  @return the ID of the new PlaylistElement
         *  @exception std::invalid_argument if the playlist already contains
         *             a playlist element with the same relative offset
         */
        Ptr<UniqueId>::Ref
        addAudioClip(Ptr<AudioClip>::Ref      audioClip,
                     Ptr<time_duration>::Ref  relativeOffset,
                     Ptr<FadeInfo>::Ref       fadeInfo
                                              = Ptr<FadeInfo>::Ref())
                                                throw (std::invalid_argument);

        /**
         *  Add a new sub-playlist to the playlist.
         *
         *  The playlist is not checked for gaps (use valid() for that),
         *  but the playlength is adjusted if the new sub-playlist is added
         *  at the end of the playlist.
         *
         *  @param playlist the sub-playlist to be added
         *  @param relativeOffset the start of the sub-playlist, relative
         *             to the start of the containing playlist
         *  @param fadeInfo the fade in / fade out info (optional)
         *  @return the ID of the new PlaylistElement
         *  @exception std::invalid_argument if the playlist already contains
         *             a playlist element with the same relative offset
         */
        Ptr<UniqueId>::Ref
        addPlaylist(Ptr<Playlist>::Ref       playlist,
                    Ptr<time_duration>::Ref  relativeOffset,
                    Ptr<FadeInfo>::Ref       fadeInfo
                                              = Ptr<FadeInfo>::Ref())
                                                throw (std::invalid_argument);

        /**
         *  Set the fade in / fade out info for a playlist element.
         *
         *  @param playlistElementId the ID of the playlist element
         *  @param fadeInfo the new fade in / fade out info
         *  @exception std::invalid_argument if there is no playlist element
         *             at the given relative offset
         */
        void
        setFadeInfo(Ptr<UniqueId>::Ref      playlistElementId,
                    Ptr<FadeInfo>::Ref      fadeInfo)
                                                throw (std::invalid_argument);

        /**
         *  Remove a playlist element from the playlist.
         *
         *  @param playlistElementId the ID of the playlist element
         *  @exception std::invalid_argument if the playlist element does not
         *                                   exist
         */
        void
        removePlaylistElement(Ptr<UniqueId>::Ref  playlistElementId)
                                                throw (std::invalid_argument);

        /**
         *  Validate the playlist: check that there are no overlaps or gaps.
         *  If the playlength is the only thing amiss, playlist is considered
         *  valid, and the playlength is fixed.  (Hence no 'const'.)
         */
        bool
        valid(void)                             throw ();


        /**
         *  Create a saved copy of this playlist.  If a saved copy exists
         *  already, it is replaced by the current state.
         */
        void
        createSavedCopy(void)                   throw ();

        /**
         *  Delete the saved copy of the playlist, if exists (or do nothing).
         */
        void
        deleteSavedCopy(void)                   throw ()
        {
            savedCopy.reset();
        }

        /**
         *  Revert to the saved copy of this playlist.  If there is no
         *  saved copy, do nothing and throw an exception.
         */
        void
        revertToSavedCopy(void)                 throw (std::invalid_argument);


        /**
         *  Return the value of a metadata field in this playlist.
         *
         *  @param  key  the name of the metadata field
         *  @return the value of the metadata field; 0 if there is 
         *          no such field;
         */
        virtual Ptr<Glib::ustring>::Ref
        getMetadata(const std::string &key) const
                                                throw ();

        /**
         *  Set the value of a metadata field in this playlist.
         *
         *  @param value the new value of the metadata field.
         *  @param  key  the name of the metadata field
         */
        virtual void
        setMetadata(Ptr<const Glib::ustring>::Ref value, 
                    const std::string &key)
                                                throw ();


        /**
         *  Return a partial XML representation of this audio clip or playlist.
         *  
         *  This is a string containing a single <playlist>
         *  XML element, with minimal information (ID, title, playlength)
         *  only, without an XML header or any other metadata.
         *
         *  The encoding is UTF-8.  IDs are 16-digit hexadecimal numbers,
         *  time durations have the format "hh:mm:ss.ssssss".
         *
         *  @return a string representation of the playlist as an XML element
         */
        virtual Ptr<Glib::ustring>::Ref
        getXmlElementString(void) const         throw ();


        /**
         *  Return a complete XML representation of this playlist.
         *  
         *  This is a string containing a an XML document with a 
         *  <playlist> root node, together with an XML header and a 
         *  <metadata> element (for the outermost playlist only).
         *  
         *  The encoding is UTF-8.  IDs are 16-digit hexadecimal numbers,
         *  time durations have the format "hh:mm:ss.ssssss".
         *  
         *  The playlist can be almost completely reconstructed from 
         *  the string returned by this method:
         *  <pre><code>
         *  Ptr<Playlist>::Ref          playlist1 = ... something ...;
         *  Ptr<xmlpp::DomParser>::Ref  parser;
         *  parser->parse_memory(*playlist1->getXmlDocumentString());
         *  const xmlpp::Document*      document = parser->get_document();
         *  const xmlpp::Element*       root     = document->get_root_node();
         *  Ptr<Playlist>::Ref          playlist2(new Playlist());
         *  playlist2->configure(*root);
         *  </code></pre>
         *  results in two identical playlists if the audio clips
         *  and sub-playlists inside <i>playlist1</i> do not contain any 
         *  metadata other than title and playlength. 
         *  All other metadata fields in the audio clips and sub-playlists 
         *  will be lost.
         *  
         *  The <i>uri</i> and <i>token</i> fields are currently not part
         *  of the XML document string returned.
         *  
         *  @return a string representation of the playlist as an XML document
         */
        virtual Ptr<Glib::ustring>::Ref
        getXmlDocumentString(void) const         throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Playlist_h

