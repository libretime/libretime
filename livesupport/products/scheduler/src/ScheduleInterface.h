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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/ScheduleInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef ScheduleInterface_h
#define ScheduleInterface_h

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
#include "LiveSupport/Core/ScheduleEntry.h"


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
 *  @author  $Author: maroy $
 *  @version $Revision: 1.6 $
 */
class ScheduleInterface : virtual public Installable
{
    public:
        /**
         *  Check if a timeframe is available for scheduling.
         *
         *  @param from the start time of the timeframe.
         *  @param to the end time of the timeframe.
         *  @return true if the timeframe is available, false otherwise.
         */
        virtual bool
        isTimeframeAvailable(Ptr<ptime>::Ref    from,
                             Ptr<ptime>::Ref    to)             throw ()
                                                                        = 0;

        /**
         *  Schedule a playlist.
         *
         *  @param playlist the playlist to schedule.
         *  @param playtime the time to schedule the playlist for.
         *  @return the id of the newly created playlist.
         *  @exception std::invalid_argument if there is something
         *             already scheduled for the duration of the playlist.
         */
        virtual Ptr<UniqueId>::Ref
        schedulePlaylist(Ptr<Playlist>::Ref     playlist,
                         Ptr<ptime>::Ref        playtime)
                                                throw (std::invalid_argument)
                                                                        = 0;

        /**
         *  Return the list of scheduled entries for a specified time interval.
         *
         *  @param fromTime the start of the time of the interval queried,
         *          inclusive
         *  @param toTime to end of the time of the interval queried,
         *          non-inclusive
         *  @return a vector of the scheduled entries for the time region.
         */
        virtual Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
        getScheduleEntries(Ptr<ptime>::Ref  fromTime,
                           Ptr<ptime>::Ref  toTime)
                                                            throw ()
                                                                    = 0;

        /**
         *  Return the next schedule entry, after (but not including)
         *  the specified timepoint.
         *
         *  @param fromTime the start of the time of the interval queried,
         *          inclusive
         *  @return the first schedule entry, after the specified timepoint.
         */
        virtual Ptr<ScheduleEntry>::Ref
        getNextEntry(Ptr<ptime>::Ref  fromTime)
                                                            throw ()
                                                                    = 0;

        /**
         *  Tell if a schedule entry exists by the give name.
         *
         *  @param entryId the id of the schedule entry to check for.
         *  @return true if the schedule entry exists in the Schedule,
         *          false otherwise.
         */
        virtual bool
        scheduleEntryExists(Ptr<const UniqueId>::Ref    entryId)
                                                            throw ()
                                                                    = 0;

        /**
         *  Remove a schedule entry from the schedule.
         *
         *  @param entryId the id of the schedule to remove.
         *  @exception std::invalid_argument if no schedule with the specified
         *             id exists.
         */
        virtual void
        removeFromSchedule(Ptr<const UniqueId>::Ref     entryId)
                                                throw (std::invalid_argument)
                                                                    = 0;

        /**
         *  Return a schedule entry for a specified id.
         *
         *  @param entryId the id of the entry to get.
         *  @return the ScheduleEntry for the specified id.
         *  @exception std::invalid_argument if no entry by the specified
         *             id exists.
         */
        virtual Ptr<ScheduleEntry>::Ref
        getScheduleEntry(Ptr<UniqueId>::Ref entryId)
                                            throw (std::invalid_argument)
                                                                        = 0;

        /**
         *  Reschedule an event to a different time.
         *
         *  @param entryId the id of the entry to reschedule.
         *  @param playtime the new time for the schedule.
         *  @exception std::invalid_argument if there is something already
         *             scheduled for the new duration.
         */
        virtual void
        reschedule(Ptr<UniqueId>::Ref   entryId,
                   Ptr<ptime>::Ref      playtime)
                                            throw (std::invalid_argument)
                                                                        = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // ScheduleInterface_h

