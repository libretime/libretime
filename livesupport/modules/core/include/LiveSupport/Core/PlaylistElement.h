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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/PlaylistElement.h,v $

------------------------------------------------------------------------------*/
#ifndef PlaylistElement_h
#define PlaylistElement_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>

#include <libxml++/libxml++.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/Playable.h"
#include "LiveSupport/Core/AudioClip.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/FadeInfo.h"


namespace LiveSupport {
namespace Core {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

// forward declaration to avoid circular reference
class Playlist;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An item in a Playlist, consisting of an AudioClip or another Playlist
 *  and optional FadeInfo (fade in / fade out information).
 *
 *  The contents of the playlist element can be accessed either by calling
 *  getPlayable(), or, if a specific type of element is needed, by checking
 *  getType() first, and then calling either getAudioClip() or getPlaylist().
 *
 *  This object has to be configured with an XML configuration element
 *  called playlistElement. This may look like the following:
 *
 *  <pre><code>
 *  &lt;playlistElement id="707" relativeOffset="00:12:34.000000" &gt;
 *      &lt;audioClip&gt; ... &lt;/audioClip&gt;
 *      &lt;fadeInfo&gt; ... &lt;/fadeInfo&gt;
 *  &lt;/playlist&gt;
 *  </code></pre>
 *
 *  For detais of the audioClip and fadeInfo elements, see the documentation 
 *  for the AudioClip and FadeInfo classes.
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT playlistElement ((audioClip|playlist), fadeInfo?) &gt;
 *  &lt;!ATTLIST playlistElement  id              NMTOKEN   #REQUIRED  &gt;
 *  &lt;!ATTLIST playlistElement  relativeOffset  NMTOKEN   #REQUIRED  &gt;
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class PlaylistElement : public Configurable 
{
    public:
        /**
         *  The possible types of the playlist element (audio clip or
         *  sub-playlist).
         */
        enum Type { AudioClipType, PlaylistType };

    private:
        /**
         *  The name of the configuration XML element used by Playlist.
         */
        static const std::string    configElementNameStr;

        /**
         *  The id of the playlist element.
         */
        Ptr<UniqueId>::Ref          id;

        /**
         *  The starting time of the event.
         */
        Ptr<time_duration>::Ref     relativeOffset;

        /**
         *  The type of the entry (audio clip or sub-playlist).
         */
        Type                        type;

        /**
         *  The generic playable object associated with the entry.
         *  This is either an audio clip or a playlist.
         */
        Ptr<Playable>::Ref          playable;

        /**
         *  The audio clip associated with the entry.
         */
        Ptr<AudioClip>::Ref         audioClip;

        /**
         *  The playlist associated with the entry.
         */
        Ptr<Playlist>::Ref          playlist;

        /**
         *  The fade in / fade out info associated with the entry.
         */
        Ptr<FadeInfo>::Ref          fadeInfo;

        /**
         *  Convert a time_duration to string, in format HH:MM:SS.ssssss.
         */
        std::string
        toFixedString(Ptr<time_duration>::Ref time) const  throw ()
        {
            if (time->fractional_seconds()) {
                return to_simple_string(*time);
            } else {
                return to_simple_string(*time) + ".000000";
            }
        }


    public:

        /**
         *  The default constructor.
         */
        PlaylistElement(void)                              throw ()
        {
        }

        /**
         *  Create a playlist element by specifying all details.
         *  This is used for testing purposes.
         *
         *  @param id             the id of the entry.
         *  @param relativeOffset the start time of this element, relative to 
         *                                        the start of the playlist.
         *  @param audioClip      (a pointer to) the audio clip associated 
         *                                        with the playlist element.
         *  @param fadeInfo       fade in / fade out information (optional)
         */
        PlaylistElement(Ptr<UniqueId>::Ref       id,
                        Ptr<time_duration>::Ref  relativeOffset,
                        Ptr<AudioClip>::Ref      audioClip,
                        Ptr<FadeInfo>::Ref       fadeInfo 
                                                 = Ptr<FadeInfo>::Ref())
                                                           throw ()
        {
            this->id             = id;
            this->relativeOffset = relativeOffset;
            this->audioClip      = audioClip;
            this->playable       = audioClip;
            this->fadeInfo       = fadeInfo;
        }

        /**
         *  Create a new audio clip playlist element, with a new UniqueId,
         *  to be added to a playlist.
         *
         *  @param relativeOffset the start time of this element, relative to 
         *                                        the start of the playlist.
         *  @param audioClip      (a pointer to) the audio clip associated 
         *                                        with the playlist element.
         *  @param fadeInfo       fade in / fade out information (optional)
         */
        PlaylistElement(Ptr<time_duration>::Ref  relativeOffset,
                        Ptr<AudioClip>::Ref      audioClip,
                        Ptr<FadeInfo>::Ref       fadeInfo 
                                                 = Ptr<FadeInfo>::Ref())
                                                           throw ()
        {
            this->id             = UniqueId::generateId();
            this->relativeOffset = relativeOffset;
            this->audioClip      = audioClip;
            this->playable       = audioClip;
            this->fadeInfo       = fadeInfo;
            this->type           = AudioClipType;
        }

        /**
         *  Create a new sub-playlist playlist element, with a new UniqueId,
         *  to be added to a playlist.
         *
         *  @param relativeOffset the start time of this element, relative to 
         *                                        the start of the playlist.
         *  @param playlist       (a pointer to) the sub-playlist associated 
         *                                        with the playlist element.
         *  @param fadeInfo       fade in / fade out information (optional)
         */
        PlaylistElement(Ptr<time_duration>::Ref  relativeOffset,
                        Ptr<Playlist>::Ref       playlist,
                        Ptr<FadeInfo>::Ref       fadeInfo 
                                                 = Ptr<FadeInfo>::Ref())
                                                           throw ()
        {
            this->id             = UniqueId::generateId();
            this->relativeOffset = relativeOffset;
            this->playlist       = playlist;
            this->playable       = playlist;
            this->fadeInfo       = fadeInfo;
            this->type           = PlaylistType;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~PlaylistElement(void)                         throw ()
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
         *  The supplied element is expected to be of the name
         *  returned by configElementName().
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuration information
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument);

        /**
         *  Return the id of the playlist element.
         *
         *  @return the id of the playlist element.
         */
        Ptr<UniqueId>::Ref
        getId(void) const                                  throw ()
        {
            return id;
        }

        /**
         *  Set the relative offset of the playlist element.
         *  Note: this is very dangerous; use only if you know what you are
         *  doing.  Never ever change the relative offset of a PlaylistElement
         *  which is contained in a Playlist.
         *
         *  @param newOffset    the new relative offset of the element.
         */
        void
        setRelativeOffset(Ptr<time_duration>::Ref   newOffset)
                                                            throw ()
        {
            relativeOffset = newOffset;
        }

        /**
         *  Return the relative offset of the playlist element.
         *
         *  @return the relative offset of the element.
         */
        Ptr<time_duration>::Ref
        getRelativeOffset(void) const                      throw ()
        {
            return relativeOffset;
        }

        /**
         *  Return the type of this playlist element.  If the return
         *  value is PlaylistElement::AudioClipType (resp. PlaylistType),
         *  the getAudioClip() (resp. getPlaylist())
         *  method is guaranteed to return a non-zero value.
         *
         *  @return either AudioClipType or PlaylistType.
         */
        Type
        getType(void) const                                throw ()
        {
            return type;
        }

        /**
         *  Return the Playable instance (an AudioClip or a Playlist)
         *  associated with the playlist element.  Use this if you don't
         *  care which type this playlist element is, e.g., you
         *  just want to play it in an audio player.
         *
         *  @return the Playable instance associated with the element.
         */
        Ptr<Playable>::Ref
        getPlayable(void) const                            throw ()
        {
            return playable;
        }

        /**
         *  Return the audio clip associated with the playlist element.
         *
         *  @see getType()
         *  @return the audio clip associated with the element.
         */
        Ptr<AudioClip>::Ref
        getAudioClip(void) const                           throw ()
        {
            return audioClip;
        }

        /**
         *  Return the sub-playlist associated with the playlist element.
         *
         *  @see getType()
         *  @return the sub-playlist associated with the element.
         */
        Ptr<Playlist>::Ref
        getPlaylist(void) const                            throw ()
        {
            return playlist;
        }

        /**
         *  Set the fade info associated with the playlist element.
         *
         *  @param fadeInfo the fade info to be associated with the element.
         */
        void
        setFadeInfo(Ptr<FadeInfo>::Ref fadeInfo)           throw ()
        {
            this->fadeInfo = fadeInfo;
        }

        /**
         *  Return the fade info associated with the playlist element.
         *
         *  @return the fade info associated with the element.
         */
        Ptr<FadeInfo>::Ref
        getFadeInfo(void) const                            throw ()
        {
            return fadeInfo;
        }


        /**
         *  Return an XML representation of this playlist element.
         *  
         *  This is a string containing a single <playlistElement>
         *  XML element, with its ID and relativeOffset attributes, 
         *  plus an <audioClip> or <playlist> XML element,
         *  plus an optional <fadeInfo> element.
         *
         *  The encoding is UTF-8.  IDs are 16-digit hexadecimal numbers,
         *  time durations have the format "hh:mm:ss.ssssss".
         *
         *  @return a string representation of the audio clip as an XML element
         */
        Ptr<Glib::ustring>::Ref
        getXmlElementString(void)               throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // PlaylistElement_h

