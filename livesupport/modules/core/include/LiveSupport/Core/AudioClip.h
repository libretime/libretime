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
    Version  : $Revision: 1.12 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/AudioClip.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_AudioClip_h
#define LiveSupport_Core_AudioClip_h

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


namespace LiveSupport {
namespace Core {

using namespace std;
using namespace boost::posix_time;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing an audio clip.
 *  AudioClips contain the basic information about the audio clip.
 *  An AudioClip is contained in a PlaylistElement, which provides the 
 *  relative offset and fade in/fade out information.  A PlaylistElement, 
 *  in turn, is contained in a Playlist.
 *
 *  This object has to be configured with an XML configuration element
 *  called audioClip. This may look like the following:
 *
 *  <pre><code>
 *  &lt;audioClip id="1" 
 *             title="Name of the Song"
 *             playlength="00:18:30.000000"
 *             uri="file:var/test1.mp3" &gt;
 *         &lt;metadata
 *                xmlns="http://www.streamonthefly.org/"
 *                xmlns:dc="http://purl.org/dc/elements/1.1/"
 *                xmlns:dcterms="http://purl.org/dc/terms/"
 *                xmlns:xbmf="http://www.streamonthefly.org/xbmf"
 *                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" &gt;
 *             &lt;dc:title  &gt;File Title txt&lt;/dc:title&gt;
 *             &lt;dcterms:extent  &gt;123&lt;/dcterms:extent&gt;
 *             ...
 *         &lt;/metadata&gt;
 *  &lt;/audioClip&gt;
 *  </code></pre>
 *
 *  The metadata element is optional.  The <code>configure()</code> method
 *  sets only those fields which had not been set previously: e.g., if we set
 *  some or all fields of the AudioClip in the constructor, then these fields
 *  in the XML element will be ignored by <code>configure()</code>. If both the
 *  <code>playlength</code> attribute and the 
 *  <code>&lt;dcterms:extent&gt;</code>
 *  element are present, then the playlength is set from the attribute and 
 *  <code>&lt;dcterms:extent&gt;</code> is ignored.
 *
 *  The URI is not normally part of the XML element; it's only included
 *  as an optional attribute for testing purposes.
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT audioClip (metadata?) &gt;
 *  &lt;!ATTLIST audioClip  id           NMTOKEN     #IMPLIED  &gt;
 *  &lt;!ATTLIST audioClip  title        CDATA       #IMPLIED  &gt;
 *  &lt;!ATTLIST audioClip  playlength   NMTOKEN     #IMPLIED  &gt;
 *  &lt;!ATTLIST audioClip  uri          CDATA       #IMPLIED   &gt;
 *  </code></pre>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.12 $
 */
class AudioClip : public Configurable,
                  public Playable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by AudioClip.
         */
        static const std::string    configElementNameStr;

        /**
         *  The unique id of the audio clip.
         */
        Ptr<UniqueId>::Ref          id;

        /**
         *  The title of the audio clip.
         */
        Ptr<UnicodeString>::Ref     title;

        /**
         *  The playling length of the audio clip.
         */
        Ptr<time_duration>::Ref     playlength;

        /**
         *  The location of the binary audio clip sound file.
         */
        Ptr<const string>::Ref      uri;

        /**
         *  The identifying token returned by the storage server.
         */
        Ptr<const string>::Ref      token;

        /**
         *  The type for storing the metadata.
         */
        typedef std::map<const std::string, Ptr<UnicodeString>::Ref>
                                    metadataType;

        /**
         *  The metadata for this audio clip.
         */
        metadataType                metadata;


    public:
        /**
         *  Default constructor.
         */
        AudioClip(void)                                    throw ()
        {
        }

        /**
         *  Create an audio clip by specifying its unique ID.
         *  The other fields will be filled in by configure().
         *
         *  @param id the id of the audio clip.
         */
        AudioClip(Ptr<UniqueId>::Ref         id)
                                                           throw ()
        {
            this->id         = id;
        }

        /**
         *  Create an audio clip by specifying all details, except
         *  for the title.
         *  This is used for testing purposes.
         *
         *  @param id the id of the audio clip.
         *  @param playlength the playing length of the audio clip.
         *  @param uri the location of the sound file corresponding to
         *             this audio clip object (optional)
         */
        AudioClip(Ptr<UniqueId>::Ref         id,
                  Ptr<time_duration>::Ref    playlength,
                  Ptr<string>::Ref           uri = Ptr<string>::Ref())
                                                           throw ()
        {
            this->id         = id;
            this->title.reset(new UnicodeString(""));
            this->playlength = playlength;
            this->uri        = uri;
        }

        /**
         *  Create an audio clip by specifying all details.
         *  This is used for testing purposes.
         *
         *  @param id the id of the audio clip.
         *  @param playlength the playing length of the audio clip.
         *  @param uri the location of the sound file corresponding to
         *             this audio clip object (optional)
         */
        AudioClip(Ptr<UniqueId>::Ref       id,
                  Ptr<UnicodeString>::Ref  title,
                  Ptr<time_duration>::Ref  playlength,
                  Ptr<string>::Ref         uri = Ptr<string>::Ref())
                                                           throw ()
        {
            this->id         = id;
            this->title      = title;
            this->playlength = playlength;
            this->uri        = uri;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~AudioClip(void)                                   throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                         throw ()
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
         *  Return the id of the audio clip.
         *
         *  @return the unique id of the audio clip.
         */
        virtual Ptr<UniqueId>::Ref
        getId(void) const                       throw ()
        {
            return id;
        }

        /**
         *  Return the total playing length for this audio clip.
         *
         *  @return the playing length in microseconds.
         */
        virtual Ptr<time_duration>::Ref
        getPlaylength(void) const               throw ()
        {
            return playlength;
        }

        /**
         *  Return the URI of the binary sound file of this audio clip, 
         *  which can be played by the helix client.
         *
         *  @return the URI.
         */
        virtual Ptr<const string>::Ref
        getUri(void) const                      throw ()
        {
            return uri;
        }

        /**
         *  Set the URI of the binary sound file of this audio clip, 
         *  which can be played by the helix client.
         *
         *  @param uri the new URI.
         */
        virtual void
        setUri(Ptr<const string>::Ref uri)      throw ()
        {
            this->uri = uri;
        }

        /**
         *  Return the token which is used to identify this audio clip
         *  to the storage server.
         *
         *  @return the token.
         */
        virtual Ptr<const string>::Ref
        getToken(void) const                    throw ()
        {
            return token;
        }

        /**
         *  Set the token which is used to identify this audio clip
         *  to the storage server.
         *
         *  @param token a new token.
         */
        virtual void
        setToken(Ptr<const string>::Ref token)  throw ()
        {
            this->token = token;
        }


        /**
         *  Return the title of this audio clip.
         *
         *  @return the title.
         */
        virtual Ptr<UnicodeString>::Ref
        getTitle(void) const                    throw ()
        {
            return title;
        }

        /**
         *  Set the title of this audio clip.
         *
         *  @param title a new title.
         */
        virtual void
        setTitle(Ptr<UnicodeString>::Ref title)
                                                throw ()
        {
            this->title = title;
        }


        /**
         *  Return the value of a metadata field in this audio clip.
         *
         *  @return the value of the metadata field; 0 if there is 
         *          no such field;
         */
        virtual Ptr<UnicodeString>::Ref
        getMetadata(const string &key) const
                                                throw ();

        /**
         *  Set the value of a metadata field in this audio clip.
         *
         *  @param key the name of the metadata field.
         *  @param value the new value of the metadata field.
         */
        virtual void
        setMetadata(const string &key, Ptr<UnicodeString>::Ref value)
                                                throw ();


        /**
         *  Return an XML representation of this audio clip.  This contains
         *  the metadata fields of the audio clip, and it's roughly the
         *  inverse of the configure() method.
         *
         *  @return an xmlpp::Document containing the metadata.
         */
        Ptr<xmlpp::Document>::Ref
        toXml()                           throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_AudioClip_h

