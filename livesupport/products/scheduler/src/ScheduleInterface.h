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
    Version  : $Revision: 1.1 $
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
 *  @version $Revision: 1.1 $
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
         *  @exception std::invalid_argument if the there is something
         *             already scheduled for the duration of the playlist.
         */
        virtual void
        schedulePlaylist(Ptr<Playlist>::Ref     playlist,
                         Ptr<ptime>::Ref        playtime)
                                                throw (std::invalid_argument)
                                                                        = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // ScheduleInterface_h

