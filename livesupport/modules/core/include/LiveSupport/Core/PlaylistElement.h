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
    Version  : $Revision: 1.3 $
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
#include "LiveSupport/Core/AudioClip.h"


namespace LiveSupport {
namespace Core {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An item in a playlist.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.3 $
 */
class PlaylistElement : public Configurable 
{
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
         *  The audio clip associated with the entry.
         */
        Ptr<AudioClip>::Ref         audioClip;


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
         *  @param id the id of the entry.
         *  @param audioClipId the ID of the audio clip associated 
         *                                        with the playlist element.
         *  @param relativeOffset the start time of this element, relative to 
         *                                        the start of the playlist.
         */
        PlaylistElement(Ptr<UniqueId>::Ref       id,
                        Ptr<time_duration>::Ref  relativeOffset,
                        Ptr<AudioClip>::Ref      audioClip)
                                                           throw ()
        {
            this->id             = id;
            this->relativeOffset = relativeOffset;
            this->audioClip      = audioClip;
        }

        /**
         *  Create a new playlist element, with a new UniqueId,
         *  to be added to a playlist.
         *
         *  @param audioClipId the ID of the audio clip associated 
         *                                        with the playlist element.
         *  @param relativeOffset the start time of this element, relative to 
         *                                        the start of the playlist.
         */
        PlaylistElement(Ptr<time_duration>::Ref  relativeOffset,
                        Ptr<AudioClip>::Ref      audioClip)
                                                           throw ()
        {
            this->id             = UniqueId::generateId();
            this->relativeOffset = relativeOffset;
            this->audioClip      = audioClip;
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
        Ptr<const UniqueId>::Ref
        getId(void) const                                  throw ()
        {
            return id;
        }

        /**
         *  Return the relative offset of the playlist element.
         *
         *  @return the relative offset of the element.
         */
        Ptr<const time_duration>::Ref
        getRelativeOffset(void) const                      throw ()
        {
            return relativeOffset;
        }

        /**
         *  Return the audio clip associated with the playlist element.
         *
         *  @return the audio clip associated with the element.
         */
        Ptr<const AudioClip>::Ref
        getAudioClip(void) const                           throw ()
        {
            return audioClip;
        }

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // PlaylistElement_h

