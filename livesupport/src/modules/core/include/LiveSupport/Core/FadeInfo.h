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
#ifndef LiveSupport_Core_FadeInfo_h
#define LiveSupport_Core_FadeInfo_h

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
 *  A class representing fade in / fade out information of a playlist element.
 *  This is contained in a PlaylistElement, a list of which, in turn, is
 *  contained in a Playlist.
 *
 *  This object has to be configured with an XML configuration element
 *  called fadeInfo. This may look like the following:
 *
 *  <pre><code>
 *  &lt;fadeInfo id="9901" 
 *            fadeIn="00:00:02.000000"
 *            fadeOut="00:00:01.500000" &gt;
 *  &lt;/fadeInfo&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT fadeInfo EMPTY &gt;
 *  &lt;!ATTLIST fadeInfo id         NMTOKEN     #REQUIRED  &gt;
 *  &lt;!ATTLIST fadeInfo fadeIn     NMTOKEN     #REQUIRED  &gt;
 *  &lt;!ATTLIST fadeInfo fadeIn     NMTOKEN     #REQUIRED  &gt;
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class FadeInfo : public Configurable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by FadeInfo.
         */
        static const std::string    configElementNameStr;

        /**
         *  The unique id of the fade info.
         */
        Ptr<UniqueId>::Ref          id;

        /**
         *  The length of fade in period.
         */
        Ptr<time_duration>::Ref     fadeIn;

        /**
         *  The length of fade out period.
         */
        Ptr<time_duration>::Ref     fadeOut;

        /**
         *  Convert a time_duration to string, in format HH:MM:SS.ssssss.
         */
        std::string
        toFixedString(Ptr<time_duration>::Ref time) const throw ()
        {
            if (time->fractional_seconds()) {
                return to_simple_string(*time);
            } else {
                return to_simple_string(*time) + ".000000";
            }
        }


    public:
        /**
         *  Default constructor.
         */
        FadeInfo(void)                                    throw ()
        {
        }

        /**
         *  Create a fade info instance by specifying all details.
         *  This is used for testing purposes.
         *
         *  @param id       the id of the fade info.
         *  @param fadeIn   the length of the fade in period.
         *  @param fadeOut  the length of the fade in period.
         */
        FadeInfo(Ptr<UniqueId>::Ref         id,
                 Ptr<time_duration>::Ref    fadeIn,
                 Ptr<time_duration>::Ref    fadeOut)      throw()
        {
            this->id        = id;
            this->fadeIn    = fadeIn;
            this->fadeOut   = fadeOut;
        }

        /**
         *  Create a fade info instance by specifying the fade in and fade out.
         *
         *  @param fadeIn   the length of the fade in period.
         *  @param fadeOut  the length of the fade in period.
         */
        FadeInfo(Ptr<time_duration>::Ref    fadeIn,
                 Ptr<time_duration>::Ref    fadeOut)      throw()
        {
            this->id        = UniqueId::generateId();
            this->fadeIn    = fadeIn;
            this->fadeOut   = fadeOut;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~FadeInfo(void)                                   throw ()
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
         *  Return the id of the fade info instance.
         *
         *  @return the unique id of the fade info instance.
         */
        Ptr<UniqueId>::Ref
        getId(void) const                       throw ()
        {
            return id;
        }

        /**
         *  Return the length of the fade in period.
         *
         *  @return the length of the fade in period, in microseconds.
         */
        Ptr<time_duration>::Ref
        getFadeIn(void) const                   throw ()
        {
            return fadeIn;
        }

        /**
         *  Return the length of the fade in period.
         *
         *  @return the length of the fade in period, in microseconds.
         */
        Ptr<time_duration>::Ref
        getFadeOut(void) const                  throw ()
        {
            return fadeOut;
        }

        /**
         *  Return an XML representation of this fadeInfo element.
         *  
         *  This is a string containing a single <fadeInfo>
         *  XML element, which is empty, and has a fadeIn and a fadeOut
         *  argument (of format hh:mm:ss.ssssss).
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

#endif // LiveSupport_Core_FadeInfo_h

