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
#ifndef LiveSupport_Core_ScheduleEntry_h
#define LiveSupport_Core_ScheduleEntry_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
#include <boost/date_time/posix_time/posix_time.hpp>
#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"


namespace LiveSupport {
namespace Core {

using namespace boost::posix_time;

using namespace LiveSupport;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A scheduled event.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class ScheduleEntry
{
    private:
        /**
         *  The id of the schedule entry.
         */
        Ptr<UniqueId>::Ref          id;

        /**
         *  The id of the playlist associated with the entry.
         */
        Ptr<UniqueId>::Ref          playlistId;

        /**
         *  The starting time of the event.
         */
        Ptr<ptime>::Ref             startTime;

        /**
         *  The end time for the event.
         */
        Ptr<ptime>::Ref             endTime;

        /**
         *  The playling length of the event.
         */
        Ptr<time_duration>::Ref     playlength;

        /**
         *  The default constructor.
         */
        ScheduleEntry(void)                             throw ()
        {
        }


    public:

        /**
         *  A constructor with initialization values.
         *
         *  @param id the id of the entry.
         *  @param playlistId the id of the playlist associated with the entry.
         *  @param startTime the starting time for the entry.
         *  @param endTime the ending time for the entry.
         */
        ScheduleEntry(Ptr<UniqueId>::Ref    id,
                      Ptr<UniqueId>::Ref    playlistId,
                      Ptr<ptime>::Ref       startTime,
                      Ptr<ptime>::Ref       endTime)
                                                            throw ()
        {
            this->id         = id;
            this->playlistId = playlistId;
            this->startTime  = startTime;
            this->endTime    = endTime;

            playlength.reset(new time_duration(*endTime - *startTime));
        }

        /**
         *  Return the id of the entry.
         *
         *  @return the id of the entry.
         */
        Ptr<const UniqueId>::Ref
        getId(void) const                               throw ()
        {
            return id;
        }

        /**
         *  Return the id of the playlist associated with the entry.
         *
         *  @return the id of the playlist associated with the entry.
         */
        Ptr<const UniqueId>::Ref
        getPlaylistId(void) const                       throw ()
        {
            return playlistId;
        }

        /**
         *  Return the starting time for the entry.
         *
         *  @return the starting time for the entry.
         */
        Ptr<const ptime>::Ref
        getStartTime(void) const                        throw ()
        {
            return startTime;
        }

        /**
         *  Return the ending time for the entry.
         *
         *  @return the ending time for the entry.
         */
        Ptr<const ptime>::Ref
        getEndTime(void) const                          throw ()
        {
            return endTime;
        }

        /**
         *  Return the playlength of the schedule entry.
         *
         *  @return the playing length of the schedule entry.
         */
        Ptr<const time_duration>::Ref
        getPlaylength(void) const                       throw ()
        {
            return playlength;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_ScheduleEntry_h

