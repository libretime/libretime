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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

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
 *  &lt;playlist id="0000000000000001"
 *            title="My Playlist" 
 *            playlength="00:18:30.000000"&gt;
 *      &lt;playlistElement&gt; ... &lt;/playlistElement&gt;
 *      ...
 *      &lt;playlistElement&gt; ... &lt;/playlistElement&gt;
 *      &lt;metadata xmlns="http://mdlf.org/livesupport/elements/1.0/"
 *                 xmlns:ls="http://mdlf.org/livesupport/elements/1.0/"
 *                 xmlns:dc="http://purl.org/dc/elements/1.1/"
 *                 xmlns:dcterms="http://purl.org/dc/terms/"
 *                 xmlns:xml="http://www.w3.org/XML/1998/namespace"&gt;
 *          &lt;dc:title&gt;File Title txt&lt;/dc:title&gt;
 *          &lt;dcterms:extent&gt;00:02:30.000000&lt;/dcterms:extent&gt;
 *          ...
 *      &lt;/metadata&gt;
 *  &lt;/playlist&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT playlist (playlistElement*, metadata?) &gt;
 *  &lt;!ATTLIST playlist  id           NMTOKEN    #REQUIRED &gt;
 *  &lt;!ATTLIST playlist  title        CDATA      ""        &gt;
 *  &lt;!ATTLIST playlist  playlength   NMTOKEN    #IMPLIED  &gt;
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
 *
 *  It is required that by the end of the configure() method, the playlength
 *  is set somehow (from a constructor, the attribute or the element).
 *  If the title is not set by the end of the configure() method, it is then
 *  set to the empty string.
 *
 *  A Playlist can be of various kinds, depending on what we want to use it
 *  for, and how we got it from the StorageClientInterface:
 *  <ul>
 *      <li>A playlist obtained by getPlaylist() has its <code>uri</code>,
 *          <code>token</code> and <code>editToken</code> fields all unset
 *          (i.e., null).  Such playlists contain sub-playlists which
 *          are just stubs, i.e., <code>id, title, playlength</code> triples,
 *          without actual references to its content objects.</li>
 *      <li>A playlist obtained by acquirePlaylist() has its <code>uri</code>
 *          and <code>token</code> fields set, but its <code>editToken</code>
 *          field unset.  These are complete Playlist objects, and their
 *          sub-playlists contain references to all their sub-objects etc.
 *          The sub-playlists have their <code>uri</code> fields set, which
 *          allows them to be played by the audio player, but their 
 *          <code>token</code> field is unset, because these sub-playlists
 *          are acquired and will be released recursively when the outermost
 *          playlist containing them is acquired and released.</li>
 *      <li>A playlist obtained by editPlaylist() has its <code>editToken</code>
 *          field set (but <code>uri</code> and <code>token</code> unset).
 *          The sub-playlists of these are also just stubs.</li>
 *  </ul>
 *
 *  The playlists are stored by the storage server in the format returned by
 *  the getXmlDocumentString() function:
 *  <ul>
 *      <li>The outermost &lt;playlist&gt; has an id attribute,
 *              a list of &lt;playlistElement&gt; children 
 *              and a &lt;metadata&gt; child.</li>
 *      <li>Each &lt;playlistElement&gt; has an id 
 *              and a relativeOffset attribute,
 *              either a &lt;playlist&gt; or an &lt;audioClip&gt; child,
 *              and optionally a &lt;fadeInfo&gt; child.</li>
 *      <li>Each &lt;playlist&gt; and &lt;audioClip&gt; has id, title
 *              and playlength attributes (and no children).</li>
 *      <li>Each &lt;fadeInfo&gt; has fadeIn and fadeOut attributes 
 *              (and no children).</li>
 *      <li>The &lt;metadata&gt; element contains all the metadata of 
 *              the outermost playlist.
 *              The dc:title and dcterms:extent elements are compulsory,
 *              everything else is optional.</li>
 *  </ul>
 *  An example:
 *
 *  <pre><code>
 *  &lt;playlist id="0000000000000001"&gt;
 *      &lt;playlistElement id="0000000000000101"
 *                       relativeOffset="00:00:00.000000"&gt;
 *          &lt;audioClip id="0000000000010001" 
 *                    title="My Audio Clip"
 *                    playlength="00:02:30.000000"/&gt;
 *          &lt;fadeInfo id="0000000000009901" 
 *                    fadeIn="00:00:02.000000"
 *                    fadeOut="00:00:01.500000"/&gt;
 *      &lt;/playlistElement&gt;
 *      &lt;playlistElement id="0000000000000102"
 *                       relativeOffset="00:02:30.000000"&gt;
 *          &lt;playlist id="0000000000000002" 
 *                    title="Embedded Playlist"
 *                    playlength="00:18:30.000000"/&gt;
 *      &lt;/playlistElement&gt;
 *      &lt;metadata xmlns="http://mdlf.org/livesupport/elements/1.0/"
 *                xmlns:ls="http://mdlf.org/livesupport/elements/1.0/"
 *                xmlns:dc="http://purl.org/dc/elements/1.1/"
 *                xmlns:dcterms="http://purl.org/dc/terms/"
 *                xmlns:xml="http://www.w3.org/XML/1998/namespace"&gt;
 *          &lt;dc:title&gt;My Playlist&lt;/dc:title&gt;
 *          &lt;dcterms:extent&gt;00:21:00.000000&lt;/dcterms:extent&gt;
 *          ...
 *      &lt;/metadata&gt;
 *  &lt;/playlist&gt;
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
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
         *  The token given to this playlist by the storage server when
         *  the playlist is acquired; removed when it is released.
         */
        Ptr<const std::string>::Ref     token;

        /**
         *  The token given to this playlist by the storage server when
         *  it is opened for editing; removed when it is saved or reverted.
         */
        Ptr<const std::string>::Ref     editToken;

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
         *  @exception std::invalid_argument    if the key is dcterms:extent, 
         *                  but the value is not a valid ISO-8601 time
         */
        void
        setMetadata(Ptr<const Glib::ustring>::Ref value, 
                    const std::string &name, const std::string &prefix)
                                                throw (std::invalid_argument);

        /**
         *  Set the total playing length of this playlist.
         *
         *  @param playlength the playing length in microseconds precision.
         */
        void
        setPlaylength(Ptr<time_duration>::Ref playlength) 
                                                throw ();

        /**
         *  Set the playlength member of this audio clip.
         *
         *  @param timeString   the playing length in microseconds precision.
         *  @exception std::invalid_argument    if the argument is not
         *                      a valid ISO-8601 time
         */
        void
        setPlaylength(Ptr<const std::string>::Ref   timeString)
                                                throw (std::invalid_argument);

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
         *  playlist, which can be played by the audio player.
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
         *  playlist, which can be played by the audio player.
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
         *  The token is set when the Playable object is acquired and
         *  unset (made null again) when it is released.
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
         *  The token is set when the Playable object is acquired and
         *  unset (made null again) when it is released.
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
         *  Return the token which is used to identify this
         *  playlist to the storage server.
         *
         *  The edit token is set when the Playable object is opened for
         *  editing and unset (made null again) when it is saved or reverted.
         *
         *  @return the token.
         */
        virtual Ptr<const std::string>::Ref
        getEditToken(void) const                throw ()
        {
            return editToken;
        }

        /**
         *  Set the token which is used to identify this
         *  playlist to the storage server.
         *
         *  The edit token is set when the Playable object is opened for
         *  editing and unset (made null again) when it is saved or reverted.
         *
         *  @param token a new token.
         */
        virtual void
        setEditToken(Ptr<const std::string>::Ref token) 
                                                throw ()
        {
            this->editToken = token;
        }

        /**
         *  Test whether the playlist is locked for editing.
         *
         *  @return true if the playlist is currently being edited
         */
        bool
        isLocked() const                         throw ()
        {
            return (editToken.get() != 0);
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
         *  Add a new audio clip or sub-playlist to the playlist.
         *
         *  Checks the type of the playlist, and calls either addAudioClip()
         *  or addPlaylist().
         *
         *  @param playable         the new playable item to be added
         *  @param relativeOffset   the start of the playable item, relative
         *                              to the start of the playlist
         *  @param fadeInfo         the fade in / fade out info (optional)
         *  @return the ID of the new PlaylistElement
         *  @exception std::invalid_argument if playable is neither an AudioClip
         *                                   nor a Playlist
         */
        Ptr<UniqueId>::Ref
        addPlayable(Ptr<Playable>::Ref       playable,
                    Ptr<time_duration>::Ref  relativeOffset,
                    Ptr<FadeInfo>::Ref       fadeInfo
                                              = Ptr<FadeInfo>::Ref())
                                                throw (std::invalid_argument);

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
         */
        Ptr<UniqueId>::Ref
        addAudioClip(Ptr<AudioClip>::Ref      audioClip,
                     Ptr<time_duration>::Ref  relativeOffset,
                     Ptr<FadeInfo>::Ref       fadeInfo
                                              = Ptr<FadeInfo>::Ref())
                                                throw ();

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
         */
        Ptr<UniqueId>::Ref
        addPlaylist(Ptr<Playlist>::Ref       playlist,
                    Ptr<time_duration>::Ref  relativeOffset,
                    Ptr<FadeInfo>::Ref       fadeInfo
                                              = Ptr<FadeInfo>::Ref())
                                                throw ();

        /**
         *  Add a new playlist element to the playlist.
         *
         *  @param playlistElement the new playlist element to be added
         */
        void
        addPlaylistElement(Ptr<PlaylistElement>::Ref playlistElement)
                                                throw ();

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
         *  Check if the playlist is valid.
         *
         *  This means that 
         *  <ul>
         *      <li>there are no gaps between the playlist elements
         *                  (overlaps are allowed); and</li>
         *      <li>the length of the playlist is equal
         *                  to the ending time of the last item in it.</li>
         *  </ul>
         *
         *  This is checked for the playlist itself, and all sub-playlists
         *  contained inside it.
         *
         *  NOTE: all kinds of overlaps are allowed.
         *  TODO: restrict it somehow?
         */
        bool
        valid(void) const                       throw ();


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
         *  If the playlist does not have this metadata field, returns a null
         *  pointer.
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
         *  @param value    the new value of the metadata field.
         *  @param  key     the name of the metadata field
         *  @exception std::invalid_argument    if the key is dcterms:extent, 
         *                  but the value is not a valid ISO-8601 time
         */
        virtual void
        setMetadata(Ptr<const Glib::ustring>::Ref value, 
                    const std::string &key)
                                                throw (std::invalid_argument);


        /**
         *  Return a partial XML representation of this audio clip or playlist.
         *  
         *  This is a string containing a single &lt;playlist&gt;
         *  XML element, with minimal information { id, title, playlength }
         *  only, without an XML header or any other metadata.
         *  It does not contain the list of playlist elements in the playlist.
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
         *  &lt;playlist&gt; root node, together with an XML header and a 
         *  &lt;metadata&gt; element (for the outermost playlist only).
         *  
         *  The playlist elements listed are only stubs returned by
         *  getXmlElementString(), i.e., { id, title, playlength } triples,
         *  without their contents.  See the more detailed description at
         *  the top of this page.
         *
         *  The encoding is UTF-8.  IDs are 16-digit hexadecimal numbers,
         *  time durations have the format "hh:mm:ss.ssssss".
         *  
         *  The <i>uri</i>, <i>token</i> and <i>editToken</i> fields are not
         *  part of the XML document string returned.
         *  
         *  @return a string representation of the playlist as an XML document
         */
        virtual Ptr<Glib::ustring>::Ref
        getXmlDocumentString(void) const         throw ();


        /**
         *  Eliminate the gaps in the playlist.
         *
         *  If there is a 2s gap between elements 2 and 3, then elements 3,
         *  4, etc. are moved to 2s earlier.  Elements 2 and 3 will not
         *  overlap, even if the first has a fade-out and the second has a
         *  fade-in.
         *
         *  @return true if some gaps have been found and eliminated
         */
        virtual bool
        eliminateGaps(void)                      throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Playlist_h

