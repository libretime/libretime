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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <odbc++/statement.h>
#include <odbc++/preparedstatement.h>
#include <odbc++/resultset.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Db/Conversion.h"
#include "PostgresqlSchedule.h"

using namespace odbc;

using namespace LiveSupport::Core;
using namespace LiveSupport::Db;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::configElementNameStr =
                                                        "postgresqlSchedule";

/*------------------------------------------------------------------------------
 *  A statement to check if the database can be accessed.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::check1Stmt = "SELECT 1";

/*------------------------------------------------------------------------------
 *  A statement to check if the schedule table exists.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::scheduleCountStmt =
                                        "SELECT COUNT(*) FROM schedule";

/*------------------------------------------------------------------------------
 *  The SQL create statement, used for installation.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::createStmt =
    "CREATE TABLE schedule\n"
    "(\n"
    "   id          BIGINT      NOT NULL,\n"
    "   playlist    BIGINT      NOT NULL,\n"
    "   starts      TIMESTAMP   NOT NULL,\n"
    "   ends        TIMESTAMP   NOT NULL,\n"
    "\n"
    "   PRIMARY KEY(id)\n"
    ");";

/*------------------------------------------------------------------------------
 *  The SQL create statement, used for installation.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::dropStmt =
    "DROP TABLE schedule;";

/*------------------------------------------------------------------------------
 *  The SQL statement for querying if a timeframe is available.
 *  The parameters for this call are: starts, starts, ends, ends, starts, ends,
 *  and returns the number of items falling into the quieried timeframe.
 *  Basically checks if the starts or ends value falls within the queried frame
 *  or starts before and ends after the queried timeframe.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::isTimeframaAvailableStmt =
    "SELECT COUNT(*) FROM schedule WHERE "
    "((starts <= ? AND ? < ends) OR (starts < ? AND ? <= ends)) "
    "OR (? <= starts AND ends <= ?)";

/*------------------------------------------------------------------------------
 *  The SQL statement for scheduling a playlist.
 *  It's a simple insert.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::schedulePlaylistStmt =
    "INSERT INTO schedule(id, playlist, starts, ends) VALUES(?, ?, ?, ?)";

/*------------------------------------------------------------------------------
 *  The SQL statement for getting a schedule entry based on its id
 *  The parameters for this call are: entryId
 *  and returns the properties: id, playlist, starts, ends for the entry
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::getScheduleEntryStmt =
    "SELECT id, playlist, starts, ends FROM schedule WHERE id = ?";

/*------------------------------------------------------------------------------
 *  The SQL statement for rescheduling a playlist (an UPDATE call).
 *  There parameters for this call are: new start, new end, id.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::reschedulePlaylistStmt =
    "UPDATE schedule SET starts = ?, ends = ? WHERE id = ?";

/*------------------------------------------------------------------------------
 *  The SQL statement for querying scheduled entries for a time interval
 *  The parameters for this call are: from, to
 *  and returns the properties: id, playlist, starts, ends for all
 *  schedule entries between from and to, ordered by starts.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::getScheduleEntriesStmt =
    "SELECT id, playlist, starts, ends FROM schedule WHERE "
    "(? < ends) AND (starts < ?) "
    "ORDER BY starts";

/*------------------------------------------------------------------------------
 *  The SQL statement for getting the currently playing schedule entry.
 *  The parameters for this call are: from
 *  and returns the properties: id, playlist, starts, ends for the next
 *  schedule entry after the specified timepoint
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::getCurrentlyPlayingStmt =
    "SELECT id, playlist, starts, ends FROM schedule "
    " WHERE starts <= ? AND ? < ends";

/*------------------------------------------------------------------------------
 *  The SQL statement for querying the next scheduled entry from the
 *  specified timepoint.
 *  The parameters for this call are: from
 *  and returns the properties: id, playlist, starts, ends for the next
 *  schedule entry after the specified timepoint
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::getNextEntryStmt =
    "SELECT id, playlist, starts, ends FROM schedule WHERE ? < starts "
    "ORDER BY starts";

/*------------------------------------------------------------------------------
 *  The SQL statement for querying if a schedule entry exists.
 *  Expects a single argument, the id of the schedule to check.
 *  Returns 1 if the entry exists, 0 otherwise.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::scheduleEntryExistsStmt =
    "SELECT COUNT(*) FROM schedule WHERE id = ?";

/*------------------------------------------------------------------------------
 *  The SQL statement for removing a schedule.
 *  Expects a single argument, the id of the schedule to remove.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::removeFromScheduleStmt =
    "DELETE FROM schedule WHERE id = ?";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the schedule.
 *----------------------------------------------------------------------------*/
void
PostgresqlSchedule :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    // nothing to do here, really
}


/*------------------------------------------------------------------------------
 *  Install the PostgresqlSchedule.
 *----------------------------------------------------------------------------*/
void
PostgresqlSchedule :: install(void)                     throw (std::exception)
{
    if (!isInstalled()) {
        Ptr<Connection>::Ref    conn;
        try {
            conn = cm->getConnection();
            Ptr<Statement>::Ref     stmt(conn->createStatement());
            stmt->execute(createStmt);
            cm->returnConnection(conn);
        } catch (std::exception &e) {
            if (conn) {
                cm->returnConnection(conn);
            }
            throw;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Check to see if the PostgresqlSchedule has already been installed.
 *----------------------------------------------------------------------------*/
bool
PostgresqlSchedule :: isInstalled(void)                 throw (std::exception)
{
    Ptr<Connection>::Ref    conn;
    try {
        Ptr<Statement>::Ref     stmt;
        ResultSet             * res;

        conn = cm->getConnection();

        // see if we can connect at all
        stmt.reset(conn->createStatement());
        stmt->execute(check1Stmt);
        res = stmt->getResultSet();
        if (!res->next() || (res->getInt(1) != 1)) {
            throw std::runtime_error("Can't connect to database");
        }

        // see if the schedule table exists
        try {
            stmt.reset(conn->createStatement());
            stmt->execute(scheduleCountStmt);
            res = stmt->getResultSet();
            if (!res->next() || (res->getInt(1) < 0)) {
                return false;
                cm->returnConnection(conn);
            }
        } catch (std::exception &e) {
            cm->returnConnection(conn);
            return false;
        }

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        throw;
    }

    return true;
}


/*------------------------------------------------------------------------------
 *  Uninstall the PostgresqlSchedule.
 *----------------------------------------------------------------------------*/
void
PostgresqlSchedule :: uninstall(void)                   throw (std::exception)
{
    Ptr<Connection>::Ref    conn;
    try {
        conn = cm->getConnection();
        Ptr<Statement>::Ref     stmt(conn->createStatement());
        stmt->execute(dropStmt);
        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        throw;
    }
}


/*------------------------------------------------------------------------------
 *  Check if a timeframe is available.
 *----------------------------------------------------------------------------*/
bool
PostgresqlSchedule :: isTimeframeAvailable(
                        Ptr<ptime>::Ref     from,
                        Ptr<ptime>::Ref     to)                 throw ()
{
    Ptr<Connection>::Ref    conn;
    bool                    result = false;

    try {
        conn = cm->getConnection();
        Ptr<Timestamp>::Ref         timestamp;
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                            isTimeframaAvailableStmt));
        timestamp = Conversion::ptimeToTimestamp(from);
        pstmt->setTimestamp(1, *timestamp);
        pstmt->setTimestamp(2, *timestamp);
        pstmt->setTimestamp(5, *timestamp);

        timestamp = Conversion::ptimeToTimestamp(to);
        pstmt->setTimestamp(3, *timestamp);
        pstmt->setTimestamp(4, *timestamp);
        pstmt->setTimestamp(6, *timestamp);

        Ptr<ResultSet>::Ref     rs(pstmt->executeQuery());
        result = (rs->next()) ? (rs->getLong(1) == 0) : false;

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        return false;
    }

    return result;
}


/*------------------------------------------------------------------------------
 *  Schedule a playlist
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
PostgresqlSchedule :: schedulePlaylist(
                        Ptr<Playlist>::Ref  playlist,
                        Ptr<ptime>::Ref     playtime)
                                                throw (std::invalid_argument)
{
    Ptr<Connection>::Ref    conn;
    bool                    result = false;
    Ptr<UniqueId>::Ref      id;

    try {
        conn = cm->getConnection();
        Ptr<Timestamp>::Ref         timestamp;
        Ptr<ptime>::Ref             ends;
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                                        schedulePlaylistStmt));
        id = UniqueId::generateId();
        pstmt->setLong(1, id->getId());
        pstmt->setLong(2, playlist->getId()->getId());
 
        timestamp = Conversion::ptimeToTimestamp(playtime);
        pstmt->setTimestamp(3, *timestamp);

        ends.reset(new ptime((*playtime) + *(playlist->getPlaylength())));
        timestamp = Conversion::ptimeToTimestamp(ends);
        pstmt->setTimestamp(4, *timestamp);

        result = pstmt->executeUpdate() == 1;

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        throw std::invalid_argument(e.what());
    }

    if (!result) {
        throw std::invalid_argument("couldn't insert into database");
    }

    return id;
}


/*------------------------------------------------------------------------------
 *  Get the scheduled entries for a given timepoint
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
PostgresqlSchedule :: getScheduleEntries(
                                    Ptr<ptime>::Ref  fromTime,
                                    Ptr<ptime>::Ref  toTime)
                                                                throw ()
{
    Ptr<Connection>::Ref                                conn;
    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     result(
                                    new std::vector<Ptr<ScheduleEntry>::Ref>());

    try {
        conn = cm->getConnection();
        Ptr<Timestamp>::Ref         timestamp;
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                            getScheduleEntriesStmt));
        timestamp = Conversion::ptimeToTimestamp(fromTime);
        pstmt->setTimestamp(1, *timestamp);
        timestamp = Conversion::ptimeToTimestamp(toTime);
        pstmt->setTimestamp(2, *timestamp);

        Ptr<ResultSet>::Ref     rs(pstmt->executeQuery());
        while (rs->next()) {
            Ptr<UniqueId>::Ref  id(new UniqueId(rs->getLong(1)));
            Ptr<UniqueId>::Ref  playlistId(new UniqueId(rs->getLong(2)));

            *timestamp = rs->getTimestamp(3);
            Ptr<ptime>::Ref startTime = Conversion::timestampToPtime(timestamp);

            *timestamp = rs->getTimestamp(4);
            Ptr<ptime>::Ref endTime = Conversion::timestampToPtime(timestamp);

            Ptr<ScheduleEntry>::Ref entry(new ScheduleEntry(id,
                                                            playlistId,
                                                            startTime,
                                                            endTime));
            result->push_back(entry);
        }

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        // TODO: report error
        return result;
    }

    return result;
}


/*------------------------------------------------------------------------------
 *  Get the currently playing entry
 *----------------------------------------------------------------------------*/
Ptr<ScheduleEntry>::Ref
PostgresqlSchedule :: getCurrentlyPlaying(void)                 throw ()
{
    Ptr<Connection>::Ref        conn;
    Ptr<ScheduleEntry>::Ref     result;
    Ptr<ptime>::Ref             now = TimeConversion::now();

    try {
        conn = cm->getConnection();
        Ptr<Timestamp>::Ref         timestamp;
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                            getCurrentlyPlayingStmt));
        timestamp = Conversion::ptimeToTimestamp(now);
        pstmt->setTimestamp(1, *timestamp);
        pstmt->setTimestamp(2, *timestamp);

        Ptr<ResultSet>::Ref     rs(pstmt->executeQuery());
        if (rs->next()) {
            Ptr<UniqueId>::Ref  id(new UniqueId(rs->getLong(1)));
            Ptr<UniqueId>::Ref  playlistId(new UniqueId(rs->getLong(2)));

            *timestamp = rs->getTimestamp(3);
            Ptr<ptime>::Ref startTime = Conversion::timestampToPtime(timestamp);

            *timestamp = rs->getTimestamp(4);
            Ptr<ptime>::Ref endTime = Conversion::timestampToPtime(timestamp);

            result.reset(new ScheduleEntry(id, playlistId, startTime, endTime));
        }

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        // TODO: report error
        return result;
    }

    return result;
}


/*------------------------------------------------------------------------------
 *  Get the next schedule entry after a specified timepoint
 *----------------------------------------------------------------------------*/
Ptr<ScheduleEntry>::Ref
PostgresqlSchedule :: getNextEntry(Ptr<ptime>::Ref  fromTime)
                                                                throw ()
{
    Ptr<Connection>::Ref        conn;
    Ptr<ScheduleEntry>::Ref     result;

    try {
        conn = cm->getConnection();
        Ptr<Timestamp>::Ref         timestamp;
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                            getNextEntryStmt));
        timestamp = Conversion::ptimeToTimestamp(fromTime);
        pstmt->setTimestamp(1, *timestamp);

        Ptr<ResultSet>::Ref     rs(pstmt->executeQuery());
        if (rs->next()) {
            Ptr<UniqueId>::Ref  id(new UniqueId(rs->getLong(1)));
            Ptr<UniqueId>::Ref  playlistId(new UniqueId(rs->getLong(2)));

            *timestamp = rs->getTimestamp(3);
            Ptr<ptime>::Ref startTime = Conversion::timestampToPtime(timestamp);

            *timestamp = rs->getTimestamp(4);
            Ptr<ptime>::Ref endTime = Conversion::timestampToPtime(timestamp);

            result.reset(new ScheduleEntry(id, playlistId, startTime, endTime));
        }

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        // TODO: report error
        return result;
    }

    return result;
}


/*------------------------------------------------------------------------------
 *  Tell if a schedule entry exists.
 *----------------------------------------------------------------------------*/
bool
PostgresqlSchedule :: scheduleEntryExists(
                            Ptr<const UniqueId>::Ref    entryId)
                                                                    throw ()
{
    Ptr<Connection>::Ref    conn;
    bool                    result = false;

    try {
        conn = cm->getConnection();
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                                    scheduleEntryExistsStmt));
        pstmt->setLong(1, entryId->getId());

        Ptr<ResultSet>::Ref     rs(pstmt->executeQuery());
        result = (rs->next()) ? (rs->getLong(1) == 1) : false;

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        throw std::invalid_argument(e.what());
    }

    return result;
}


/*------------------------------------------------------------------------------
 *  Remove a schedule entry from a schedule
 *----------------------------------------------------------------------------*/
void
PostgresqlSchedule :: removeFromSchedule(
                            Ptr<const UniqueId>::Ref     entryId)
                                            throw (std::invalid_argument)
{
    Ptr<Connection>::Ref    conn;
    bool                    result = false;

    try {
        conn = cm->getConnection();
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                                    removeFromScheduleStmt));
        pstmt->setLong(1, entryId->getId());

        result = pstmt->executeUpdate() == 1;

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        throw std::invalid_argument(e.what());
    }

    if (!result) {
        throw std::invalid_argument("specified schedule entry does not exist");
    }
}


/*------------------------------------------------------------------------------
 *  Get a ScheduleEntry based on a schedule entry id.
 *----------------------------------------------------------------------------*/
Ptr<ScheduleEntry>::Ref
PostgresqlSchedule :: getScheduleEntry(Ptr<UniqueId>::Ref   entryId)
                                                throw (std::invalid_argument)
{
    Ptr<Connection>::Ref    conn;
    Ptr<ScheduleEntry>::Ref entry;

    try {
        conn = cm->getConnection();
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                                        getScheduleEntryStmt));
        pstmt->setLong(1, entryId->getId());

        Ptr<ResultSet>::Ref     rs(pstmt->executeQuery());
        if (rs->next()) {
            Ptr<Timestamp>::Ref     timestamp(new Timestamp());

            Ptr<UniqueId>::Ref  id(new UniqueId(rs->getLong(1)));
            Ptr<UniqueId>::Ref  playlistId(new UniqueId(rs->getLong(2)));

            *timestamp = rs->getTimestamp(3);
            Ptr<ptime>::Ref startTime = Conversion::timestampToPtime(timestamp);

            *timestamp = rs->getTimestamp(4);
            Ptr<ptime>::Ref endTime = Conversion::timestampToPtime(timestamp);

            entry.reset(new ScheduleEntry(id, playlistId, startTime, endTime));
        }

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        // TODO: report error
        return entry;
    }

    if (!entry) {
        throw std::invalid_argument("no schedule entry by the specified id");
    }

    return entry;
}


/*------------------------------------------------------------------------------
 *  Reschedule an entry
 *----------------------------------------------------------------------------*/
void
PostgresqlSchedule :: reschedule(Ptr<UniqueId>::Ref   entryId,
                                 Ptr<ptime>::Ref      playtime)
                                                throw (std::invalid_argument)
{
    Ptr<ScheduleEntry>::Ref entry = getScheduleEntry(entryId);

    Ptr<ptime>::Ref         ends(new ptime((*playtime)
                                         + *(entry->getPlaylength())));

    if (!isTimeframeAvailable(playtime, ends)) {
        throw std::invalid_argument("new playtime not available");
    }

    Ptr<Connection>::Ref    conn;
    bool                    result = false;

    try {
        conn = cm->getConnection();
        Ptr<Timestamp>::Ref         timestamp;
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                                      reschedulePlaylistStmt));

        timestamp = Conversion::ptimeToTimestamp(playtime);
        pstmt->setTimestamp(1, *timestamp);

        timestamp = Conversion::ptimeToTimestamp(ends);
        pstmt->setTimestamp(2, *timestamp);

        pstmt->setLong(3, entryId->getId());

        result = pstmt->executeUpdate() == 1;

        cm->returnConnection(conn);
    } catch (std::exception &e) {
        if (conn) {
            cm->returnConnection(conn);
        }
        throw std::invalid_argument(e.what());
    }

    if (!result) {
        throw std::invalid_argument("couldn't insert into database");
    }
}


