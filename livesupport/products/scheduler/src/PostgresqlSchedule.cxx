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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PostgresqlSchedule.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <odbc++/statement.h>
#include <odbc++/preparedstatement.h>
#include <odbc++/resultset.h>

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
 *  The SQL create statement, used for installation.
 *----------------------------------------------------------------------------*/
const std::string PostgresqlSchedule::createStmt =
    "CREATE TABLE schedule\n"
    "(\n"
    "   id          INT         NOT NULL,\n"
    "   playlist    INT         NOT NULL,\n"
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
        result = (rs->next()) ? (rs->getInt(1) == 0) : false;

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
void
PostgresqlSchedule :: schedulePlaylist(
                        Ptr<Playlist>::Ref  playlist,
                        Ptr<ptime>::Ref     playtime)
                                                throw (std::invalid_argument)
{
    Ptr<Connection>::Ref    conn;
    bool                    result = false;

    try {
        conn = cm->getConnection();
        Ptr<Timestamp>::Ref         timestamp;
        Ptr<UniqueId>::Ref          id;
        Ptr<ptime>::Ref             ends;
        Ptr<PreparedStatement>::Ref pstmt(conn->prepareStatement(
                                                        schedulePlaylistStmt));
        id = UniqueId::generateId();
        pstmt->setInt(1, id->getId());
        
        pstmt->setInt(2, playlist->getId()->getId());
        
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
}

