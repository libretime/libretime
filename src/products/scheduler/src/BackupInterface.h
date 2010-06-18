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
#ifndef BackupInterface_h
#define BackupInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include <stdexcept>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/ScheduleEntry.h"
#include "LiveSupport/StorageClient/StorageClientInterface.h"


namespace LiveSupport {
namespace Scheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The generic interface for creating and restoring schedule backups.
 *
 *  There is a singleton instance of this type, which is manufactured by the
 *  BackupFactory class.
 *
 *  There are separate xxxxMethod classes which perform these
 *  functions via XML-RPC, by delegating them to this singleton object.
 *  The xxxxMethod classes are registered in the SchedulerDaemon
 *  object, which calls them when an XML-RPC request is received.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class BackupInterface
{
    public:
        /**
         *  Start to create a backup by calling the storage, and also
         *  adding a backup of the schedule.
         *  To check if the backup procedure is still pending, call
         *  createBackupCheck() regularly.
         *  Make sure to close the backup by calling createBackupClose().
         *
         *  @param sessionId a valid session ID to use for accessing the
         *         storage
         *  @param criteria the criteria to use for backing up the storage
         *  @param fromTime entries are included in the schedule export starting
         *         from this time.
         *  @param toTime entries as included in the schedule export
         *         up to but not including this time.
         *  @return a token, which can be used to query the backup process.
         *  @exception XmlRpcException on XML-RPC issues.
         *  @see #createBackupCheck
         *  @see #createBackupClose
         */
        virtual Ptr<Glib::ustring>::Ref
        createBackupOpen(Ptr<SessionId>::Ref        sessionId,
                         Ptr<SearchCriteria>::Ref   criteria,
                         Ptr<ptime>::Ref            fromTime,
                         Ptr<ptime>::Ref            toTime)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Check the status of a storage backup.
         *
         *  @param  token   the identifier of this backup task.
         *  @param  url     return parameter;
         *                      if the status is "success", it contains the
         *                      URL of the created backup file.
         *  @param  path    return parameter;
         *                      if the status is "success", it contains the
         *                      local access path of the created backup file.
         *  @param  errorMessage    return parameter;
         *                      if the status is "fault", it contains the
         *                      fault string.
         *  @return the state of the backup process: one of pendingState,
         *                      finishedState, or failedState.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         *  @see #createBackupOpen
         *  @see #createBackupClose
         */
        virtual AsyncState
        createBackupCheck(const Glib::ustring &             token,
                          Ptr<const Glib::ustring>::Ref &   url,
                          Ptr<const Glib::ustring>::Ref &   path,
                          Ptr<const Glib::ustring>::Ref &   errorMessage)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Close the storage backup process.
         *  Frees up all resources allocated to the backup.
         *
         *  @param  token           the identifier of this backup task.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         *  @see #createBackupOpen
         *  @see #createBackupCheck
         */
        virtual void
        createBackupClose(const Glib::ustring &     token)
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
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~BackupInterface(void)            throw ()
        {
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // BackupInterface_h

