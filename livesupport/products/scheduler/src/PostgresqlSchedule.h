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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PostgresqlSchedule.h,v $

------------------------------------------------------------------------------*/
#ifndef PostresqlSchedule_h
#define PostresqlSchedule_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Db/ConnectionManagerInterface.h"
#include "ScheduleInterface.h"


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An object containing the schedule of events in a PostreSQL database.
 *
 *  This object has to be configured with a simple empty element, as
 *  the following:
 *
 *  <pre><code>
 *      &lt;postgresqlSchedule/&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT postgresqlSchedule EMPTY &gt;
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.6 $
 */
class PostgresqlSchedule : public Configurable,
                           public ScheduleInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  The SQL create statement used in the installation step.
         */
        static const std::string    createStmt;

        /**
         *  The SQL drop statement used in the uninstallation step.
         */
        static const std::string    dropStmt;

        /**
         *  The SQL statement for querying if a time frame is available.
         */
        static const std::string    isTimeframaAvailableStmt;

        /**
         *  The SQL statement for scheduling a playlist.
         */
        static const std::string    schedulePlaylistStmt;

        /**
         *  The SQL statement for rescheduling an entry.
         */
        static const std::string    reschedulePlaylistStmt;

        /**
         *  The SQL statement for getting a schedule entry based on its id
         */
        static const std::string    getScheduleEntryStmt;

        /**
         *  The SQL statement for getting the schedules for a time interval
         */
        static const std::string    getScheduleEntriesStmt;

        /**
         *  The SQL statement for getting the next schedule entry after a
         *  timepoint.
         */
        static const std::string    getNextEntryStmt;

        /**
         *  The SQL statement for telling if a schedule entry exists.
         */
        static const std::string    scheduleEntryExistsStmt;

        /**
         *  The SQL statement for removing a schedule entry.
         */
        static const std::string    removeFromScheduleStmt;

        /**
         *  The database connection manager to use for connecting the
         *  database.
         */
        Ptr<Db::ConnectionManagerInterface>::Ref    cm;

        /**
         *  The default constructor.
         */
        PostgresqlSchedule(void)                            throw()
        {
        }


    public:
        /**
         *  Construct a PostgresqlSchedule.
         *
         *  @param cm the connection manager the PostgresqlSchedule will use to
         *         connect to the database.
         */
        PostgresqlSchedule(Ptr<Db::ConnectionManagerInterface>::Ref cm)
                                                                throw ()
        {
            this->cm = cm;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~PostgresqlSchedule(void)                           throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                          throw ()
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
         *  @exception std::logic_error if the object has already
         *             been configured, and can not be reconfigured.
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  Install the component.
         *  This step involves creating the environment in which the component
         *  will run. This may be creation of coniguration files,
         *  database tables, etc.
         *
         *  @exception std::exception on installation problems.
         */
        virtual void
        install(void)                           throw (std::exception);

        /**
         *  Uninstall the component.
         *  Removes all the resources created in the install step.
         *
         *  @exception std::exception on unistallation problems.
         */
        virtual void
        uninstall(void)                         throw (std::exception);

        /**
         *  Check if a timeframe is available for scheduling.
         *
         *  @param from the start time of the timeframe.
         *  @param to the end time of the timeframe.
         *  @return true if the timeframe is available, false otherwise.
         */
        virtual bool
        isTimeframeAvailable(Ptr<ptime>::Ref    from,
                             Ptr<ptime>::Ref    to)             throw ();

        /**
         *  Schedule a playlist.
         *
         *  @param playlist the playlist to schedule.
         *  @param playtime the time to schedule the playlist for.
         *  @return the id of the newly created playlist.
         *  @exception std::invalid_argument if the there is something
         *             already scheduled for the duration of the playlist.
         */
        virtual Ptr<UniqueId>::Ref
        schedulePlaylist(Ptr<Playlist>::Ref     playlist,
                         Ptr<ptime>::Ref        playtime)
                                                throw (std::invalid_argument);

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
                                                            throw ();

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
                                                            throw ();

        /**
         *  Tell if a schedule entry exists by the give name.
         *
         *  @param entryId the id of the schedule entry to check for.
         *  @return true if the schedule entry exists in the Schedule,
         *          false otherwise.
         */
        virtual bool
        scheduleEntryExists(Ptr<const UniqueId>::Ref    entryId)
                                                            throw ();

        /**
         *  Remove a schedule entry from the schedule.
         *
         *  @param entryId the id of the schedule to remove.
         *  @exception std::invalid_argument if no schedule with the specified
         *             id exists.
         */
        virtual void
        removeFromSchedule(Ptr<const UniqueId>::Ref     entryId)
                                                throw (std::invalid_argument);

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
                                            throw (std::invalid_argument);

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
                                            throw (std::invalid_argument);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // PostresqlSchedule_h

