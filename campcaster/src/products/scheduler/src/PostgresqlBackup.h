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
#ifndef PostgresqlBackup_h
#define PostgresqlBackup_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include <stdexcept>
#include <string>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Db/ConnectionManagerInterface.h"
#include "LiveSupport/StorageClient/StorageClientInterface.h"
#include "ScheduleInterface.h"
#include "BackupInterface.h"


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::Db;
using namespace LiveSupport::StorageClient;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An object for creating and restoring combined schedule and storage backups.
 *
 *  This an implementation of the BackupInterface type.  It stores the token
 *  used for the createBackupXxxx() functions in a PostgreSQL database.
 *
 *  This object has to be configured with a simple empty element, as
 *  the following:
 *
 *  <pre><code>
 *      &lt;postgresqlBackup/&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT postgresqlBackup EMPTY &gt;
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class PostgresqlBackup : public Configurable,
                         public BackupInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  The database connection manager to use for connecting to the
         *  database.
         */
        Ptr<ConnectionManagerInterface>::Ref    connectionManager;

        /**
         *  The storage client to use for connecting to the storage server.
         */
        Ptr<StorageClientInterface>::Ref        storage;

        /**
         *  The schedule to use for reading the schedule entries from.
         */
        Ptr<ScheduleInterface>::Ref             schedule;

        /**
         *  The default constructor.
         */
        PostgresqlBackup(void)                                      throw()
        {
        }

        /**
         *  Insert a schedule export XML file into an existing tarball.
         *
         *  @param path the file path to the existing tarball.
         *  @param fromTime the time to generate the XML export from
         *  @param toTime the time to generate the XML export to
         *  @throws std::runtime_error on file / tarball handling issues.
         */
        void
        putScheduleExportIntoTar(
                            Ptr<const Glib::ustring>::Ref &     path,
                            Ptr<ptime>::Ref                     fromTime,
                            Ptr<ptime>::Ref                     toTime)
                                                throw (std::runtime_error);

        /**
         *  Convert a string status to an AsyncState.
         *  It converts
         *  <ul>
         *      <li> "working"      -> pendingState </li>
         *      <li> "success"      -> finishedState </li>
         *      <li> "fault"        -> failedState </li>
         *      <li> anything else  -> invalidState <li>
         *  </ul>
         */
        AsyncState
        stringToAsyncState(const std::string &      statusString)   throw ();

        /**
         *  Convert an AsyncState to a string.
         *  It converts
         *  <ul>
         *      <li> initState or pendingState    -> "working" </li>
         *      <li> finishedState                -> "success" </li>
         *      <li> failedState                  -> "fault"   </li>
         *      <li> anything else                -> "invalid" </li>
         *  </ul>
         */
        std::string
        asyncStateToString(AsyncState   status)
                                                                    throw ();


    public:
        /**
         *  Construct a PostgresqlBackup.
         *
         *  @param cm the connection manager the PostgresqlBackup will use to
         *         connect to the database.
         */
        PostgresqlBackup(
                Ptr<ConnectionManagerInterface>::Ref    connectionManager,
                Ptr<StorageClientInterface>::Ref        storage,
                Ptr<ScheduleInterface>::Ref             schedule)
                                                                    throw ()
              : connectionManager(connectionManager),
                storage(storage),
                schedule(schedule)
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~PostgresqlBackup(void)                                     throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                                  throw ()
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
                                                throw (XmlRpcException);

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
                                                throw (XmlRpcException);

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
                                                throw (XmlRpcException);

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
                                                throw (XmlRpcException);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // PostgresqlBackup_h

