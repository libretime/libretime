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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/Attic/PlayLogEntry.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Scheduler_PlayLogEntry_h
#define LiveSupport_Scheduler_PlayLogEntry_h

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
namespace Scheduler {

using namespace boost::posix_time;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing a play log entry.
 *  PlayLogEntries contain information about the audio clips played.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class PlayLogEntry : public Configurable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by PlayLogEntry.
         */
        static const std::string    configElementNameStr;

        /**
         *  The unique id of the play log entry.
         */
        Ptr<UniqueId>::Ref          id;

        /**
         *  The id of the audio clip referenced by this play log entry.
         */
        Ptr<UniqueId>::Ref          audioClipId;

        /**
         *  The time this audio clip was played.
         */
        Ptr<ptime>::Ref             timeStamp;


    public:
        /**
         *  Default constructor.
         */
        PlayLogEntry(void)                                    throw ()
        {
        }

        /**
         *  Create a play log entry by specifying all details.
         *  This is used for testing purposes.
         *
         *  @param id          the ID of the play log entry.
         *  @param audioClipId the ID of the audio clip logged
         *  @param timeStamp   the time this audio clip was played.
         */
        PlayLogEntry(Ptr<UniqueId>::Ref    id,
                     Ptr<UniqueId>::Ref    audioClipId,
                     Ptr<ptime>::Ref       timeStamp)         throw()
        {
            this->id          = id;
            this->audioClipId = audioClipId;
            this->timeStamp   = timeStamp;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~PlayLogEntry(void)                                   throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                            throw ()
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
         *  Return the ID of the play log entry.
         *
         *  @return the unique ID of the play log entry.
         */
        Ptr<const UniqueId>::Ref
        getId(void) const                       throw ()
        {
            return id;
        }

        /**
         *  Return the ID of the audio clip referenced by this entry.
         *
         *  @return the unique ID of the audio clip.
         */
        Ptr<const UniqueId>::Ref
        getAudioClipId(void) const              throw ()
        {
            return audioClipId;
        }

        /**
         *  Return the time this audio clip was played.
         *
         *  @return the the time the audio clip was played.
         */
        Ptr<const ptime>::Ref
        getTimeStamp(void) const                throw ()
        {
            return timeStamp;
        }

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // LiveSupport_Scheduler_PlayLogEntry_h

