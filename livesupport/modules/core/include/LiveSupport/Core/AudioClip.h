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
    Version  : $Revision: 1.7 $
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
 *             playlength="00:18:30.000000"
 *             uri="file:var/test1.mp3" &gt;
 *  &lt;/audioClip&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT audioClip EMPTY &gt;
 *  &lt;!ATTLIST audioClip  id           NMTOKEN     #REQUIRED  &gt;
 *  &lt;!ATTLIST audioClip  playlength   NMTOKEN     #REQUIRED  &gt;
 *  &lt;!ATTLIST audioClip  uri          CDATA       #REQUIRED  &gt;
 *  </code></pre>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.7 $
 */
class AudioClip : public Configurable
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
         *  The playling length of the audio clip.
         */
        Ptr<time_duration>::Ref     playlength;

        /**
         *  The location of the audio clip.
         */
        Ptr<string>::Ref            uri;


    public:
        /**
         *  Default constructor.
         */
        AudioClip(void)                                    throw ()
        {
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
        AudioClip(Ptr<UniqueId>::Ref         id,
                  Ptr<time_duration>::Ref    playlength,
                  Ptr<string>::Ref           uri = Ptr<string>::Ref())
                                                           throw ()
        {
            this->id         = id;
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
        Ptr<UniqueId>::Ref
        getId(void) const                       throw ()
        {
            return id;
        }

        /**
         *  Return the total playing length for this audio clip.
         *
         *  @return the playing length of this audio clip, in microseconds.
         */
        Ptr<const time_duration>::Ref
        getPlaylength(void) const               throw ()
        {
            return playlength;
        }

        /**
         *  Return the URI of this audio clip.
         *
         *  @return the URI of this audio clip.
         */
        Ptr<const string>::Ref
        getUri(void) const                      throw ()
        {
            return uri;
        }

        /**
         *  Change the URI of this audio clip.  This is only used in testing.
         *
         *  @return the URI of this audio clip.
         */
        void
        setUri(Ptr<string>::Ref uri)            throw ()
        {
            this->uri = uri;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_AudioClip_h

