/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef LiveSupport_SchedulerClient_SchedulerClientInterface_h
#define LiveSupport_SchedulerClient_SchedulerClientInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <vector>
#include "boost/date_time/posix_time/posix_time.hpp"

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Core/ScheduleEntry.h"
#include "LiveSupport/Core/XmlRpcException.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/SearchCriteria.h"
#include "LiveSupport/Core/AsyncState.h"
#include "LiveSupport/Core/SearchCriteria.h"
#include "LiveSupport/Core/AsyncState.h"

namespace LiveSupport {
namespace SchedulerClient {

using namespace boost::posix_time;
using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface to access the scheduler daemon as a client.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class SchedulerClientInterface
{
    public:
        /**
         *  Return the XML-RPC host the client connects to.
         *
         *  @return the XML-RPC host the client connects to.
         */
        virtual Ptr<const std::string>::Ref
        getXmlRpcHost(void) const                   throw ()        = 0;

        /**
         *  Return the XML-RPC port the client connects to.
         *
         *  @return the XML-RPC port the client connects to.
         */
        virtual unsigned int
        getXmlRpcPort(void) const                   throw ()        = 0;

        /**
         *  Return the XML-RPC URI prefix used when connecting to the scheduler.
         *
         *  @return the XML-RPC URI prefix.
         */
        virtual Ptr<const std::string>::Ref
        getXmlRpcUriPrefix(void) const              throw ()        = 0;

        /**
         *  Return the version string for the scheduler this client
         *  is connected to.
         *
         *  @return the version string of the scheduler daemon.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<const std::string>::Ref
        getVersion(void)                        throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return the current time at the scheduler server.
         *
         *  @return the current time at the scheduler server.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<const ptime>::Ref
        getSchedulerTime(void)                  throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Schedule a playlist at a given time.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param playlistId the id of the playlist to schedule.
         *  @param playtime the time for which to schedule.
         *  @return the schedule entry id for which the playlist has been
         *          scheduled.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<UniqueId>::Ref
        uploadPlaylist(Ptr<SessionId>::Ref  sessionId,
                       Ptr<UniqueId>::Ref   playlistId,
                       Ptr<ptime>::Ref      playtime)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return the scheduled entries for a specified time interval.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param from the start of the interval, inclusive
         *  @param to the end of the interval, exclusive
         *  @return a vector of the schedule entries for the time period.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
        displaySchedule(Ptr<SessionId>::Ref sessionId,
                        Ptr<ptime>::Ref     from,
                        Ptr<ptime>::Ref     to)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Remove a scheduled item.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param scheduledEntryId the id of the scheduled entry to remove.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual void
        removeFromSchedule(Ptr<SessionId>::Ref  sessionId,
                           Ptr<UniqueId>::Ref   scheduleEntryId)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Start the schedule backup creation process.
         *  This will produce a combined backup, including a storage portion.
         *  The scheduler daemon first calls the storage server, and gets
         *  a storage backup archive file from it; then it adds the schedule
         *  backup to this archive file.
         *
         *  @param  sessionId   a valid, authenticated session id.
         *  @param  criteria    the search criteria for the storage portion
         *                      of the backup.
         *  @param  fromTime    entries are included in the schedule backup
         *                      starting from this time.
         *  @param  toTime      entries are included in the schedule backup
         *                      up to but not including this time.
         *  @return a token which can be used to query the backup process.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<Glib::ustring>::Ref
        createBackupOpen(Ptr<SessionId>::Ref            sessionId,
                         Ptr<SearchCriteria>::Ref       criteria,
                         Ptr<ptime>::Ref                fromTime,
                         Ptr<ptime>::Ref                toTime) const
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Check on the progress of the schedule backup creation process.
         *
         *  @param  token       the token obtained from createBackupOpen().
         *  @param  url     return parameter;
         *                      if a finishedState is returned, it contains the
         *                      URL of the created backup file.
         *  @param  path    return parameter;
         *                      if a finishedState is returned, it contains the
         *                      local access path of the created backup file.
         *  @param  errorMessage    return parameter;
         *                      if a failedState is returned, it contains the
         *                      fault string.
         *  @return the state of the backup process: one of pendingState,
         *                      finishedState, or failedState.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual AsyncState
        createBackupCheck(const Glib::ustring &             token,
                          Ptr<const Glib::ustring>::Ref &   url,
                          Ptr<const Glib::ustring>::Ref &   path,
                          Ptr<const Glib::ustring>::Ref &   errorMessage) const
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Close the schedule backup creation process.
         *
         *  @param  token       the token obtained from createBackupOpen().
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual void
        createBackupClose(const Glib::ustring &         token) const
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Restore a schedule backup.
         *
         *  All playlist IDs contained in the backup should already be in the
         *  storage.  If this is a combined backup, with both storage and 
         *  schedule components, then restore this backup to the storage
         *  first, and then call this function.
         *  
         *  @param  sessionId   a valid session ID to identify the user.
         *  @param  path        the location of the archive to upload.
         *  @exception  XmlRpcException     if there is an error.
         */
        virtual void
        restoreBackup(Ptr<SessionId>::Ref               sessionId,
                      Ptr<const Glib::ustring>::Ref     path)
                                                throw (XmlRpcException)
                                                                        = 0;
        
        /**
         *  Stop the scheduler's audio player.
         *
         *  @param  sessionId   a valid session ID to identify the user.
         *  @exception  XmlRpcException     if there is an error.
         */
        virtual void
        stopCurrentlyPlaying(Ptr<SessionId>::Ref        sessionId)
                                                throw (XmlRpcException) = 0;

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~SchedulerClientInterface(void)             throw ()
        {
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace SchedulerClient
} // namespace LiveSupport

#endif // LiveSupport_SchedulerClient_SchedulerClientInterface_h

