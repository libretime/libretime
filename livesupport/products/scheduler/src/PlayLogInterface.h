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
#ifndef PlayLogInterface_h
#define PlayLogInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Installable.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/PlayLogEntry.h"


namespace LiveSupport {
namespace Scheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The generic interface for the component scheduling events.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class PlayLogInterface : virtual public Installable
{
    public:
        /**
         *  Add a new entry to the play log.
         *
         *  @param audioClipId the audio clip played.
         *  @param timeStamp the time the clip was played (started).
         *  @return the id of the newly created play log entry.
         */
        virtual Ptr<UniqueId>::Ref
        addPlayLogEntry(Ptr<const UniqueId>::Ref     audioClipId,
                        Ptr<const ptime>::Ref        timeStamp)
                                                throw (std::invalid_argument)
                                                                         = 0;

        /**
         *  Return the list of play log entries for a specified time interval.
         *
         *  @param fromTime the start of the time of the interval queried,
         *          inclusive
         *  @param toTime to end of the time of the interval queried,
         *          non-inclusive
         *  @return a vector of the play log entries for the time region.
         */
        virtual Ptr<std::vector<Ptr<PlayLogEntry>::Ref> >::Ref
        getPlayLogEntries(Ptr<const ptime>::Ref  fromTime,
                          Ptr<const ptime>::Ref  toTime)
                                                throw (std::invalid_argument)
                                                                         = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // PlayLogInterface_h

