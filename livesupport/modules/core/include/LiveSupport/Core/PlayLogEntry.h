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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/PlayLogEntry.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_PlayLogEntry_h
#define LiveSupport_Core_PlayLogEntry_h

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


namespace LiveSupport {
namespace Core {

using namespace boost::posix_time;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing a play log entry.
 *  PlayLogEntries contain information about the audio clips played.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class PlayLogEntry
{
    private:
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
        Ptr<ptime>::Ref             timestamp;


    public:
        /**
         *  Default constructor.
         */
        PlayLogEntry(void)                                    throw ()
        {
        }

        /**
         *  Create a play log entry by specifying all details.
         *
         *  @param id          the ID of the play log entry.
         *  @param audioClipId the ID of the audio clip logged
         *  @param timestamp   the time this audio clip was played.
         */
        PlayLogEntry(Ptr<UniqueId>::Ref    id,
                     Ptr<UniqueId>::Ref    audioClipId,
                     Ptr<ptime>::Ref       timestamp)         throw()
        {
            this->id          = id;
            this->audioClipId = audioClipId;
            this->timestamp   = timestamp;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~PlayLogEntry(void)                                   throw ()
        {
        }

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
        getTimestamp(void) const                throw ()
        {
            return timestamp;
        }

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_PlayLogEntry_h

