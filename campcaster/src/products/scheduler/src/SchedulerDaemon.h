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
#ifndef SchedulerDaemon_h
#define SchedulerDaemon_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_SYS_TYPES_H
#include <sys/types.h>
#else
#error "Need sys/types.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif

#include <string>
#include <stdexcept>
#include <libxml++/libxml++.h>
#include <XmlRpc.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Db/ConnectionManagerInterface.h"
#include "LiveSupport/StorageClient/StorageClientInterface.h"
#include "LiveSupport/Authentication/AuthenticationClientInterface.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"
#include "LiveSupport/EventScheduler/EventScheduler.h"
#include "PlayLogInterface.h"

#include "DisplayScheduleMethod.h"
#include "GeneratePlayReportMethod.h"
#include "GetSchedulerTimeMethod.h"
#include "GetVersionMethod.h"
#include "RemoveFromScheduleMethod.h"
#include "RescheduleMethod.h"
#include "ScheduleInterface.h"
#include "UploadPlaylistMethod.h"
#include "XmlRpcDaemon.h"
#include "LoginMethod.h"
#include "LogoutMethod.h"
#include "ResetStorageMethod.h"
#include "CreateBackupOpenMethod.h"
#include "CreateBackupCheckMethod.h"
#include "CreateBackupCloseMethod.h"
#include "RestoreBackupMethod.h"
#include "StopCurrentlyPlayingMethod.h"

namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::Db;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::PlaylistExecutor;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Scheduler daemon main class.
 *  This class is responsible for starting, running and stopping the
 *  Scheduler daemon.
 *
 *  The SchedulerDaemon has to configured by an XML element called
 *  scheduler. This element contains configuration elements for the
 *  compontents used by the scheduler. The configuration file looks
 *  like the following:
 *
 *  <pre><code>
 *  &lt;scheduler&gt;
 *      &lt;user login="userid" password="pwd" /&gt;
 *      &lt;connectionManagerFactory&gt;
 *          ...
 *      &lt;/connectionManagerFactory&gt;
 *      &lt;authenticationClientFactory&gt;
 *          ...
 *      &lt;/authenticationClientFactory&gt;
 *      &lt;storageClientFactory&gt;
 *          ...
 *      &lt;/storageClientFactory&gt;
 *      &lt;scheduleFactory&gt;
 *          ...
 *      &lt;/scheduleFactory&gt;
 *      &lt;playLogFactory&gt;
 *          ...
 *      &lt;/playLogFactory&gt;
 *      &lt;backupFactory&gt;
 *          ...
 *      &lt;/backupFactory&gt;
 *      &lt;xmlRpcDaemon&gt;
 *          ...
 *      &lt;/xmlRpcDaemon&gt;
 *  &lt;/scheduler&gt;
 *  </code></pre>
 *
 *  The user element holds creditentials for accessing the storage,
 *  configured below.
 *
 *  For details on the included elements, see the corresponding documentation
 *  for XmlRpcDaemon, StorageClientFactory, ConnectionManagerFactory
 *  ScheduleFactory and AuthenticationClientFactory.
 *
 *  The DTD for the above element is the following:
 *
 *  <pre><code>
 *  &lt;!ELEMENT scheduler (user,
 *                       connectionManagerFactory,
 *                       authenticationClientFactory,
 *                       storageClientFactory,
 *                       scheduleFactory,
 *                       playLogFactory,
 *                       backupFactory,
 *                       audioPlayer,
 *                       xmlRpcDaemon) &gt;
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see ConnectionManagerFactory
 *  @see AuthenticationClientFactory
 *  @see StorageClientFactory
 *  @see ScheduleFactory
 *  @see XmlRpcDaemon
 */
class SchedulerDaemon : public Configurable,
                        public XmlRpcDaemon
{
    private:

        /**
         *  A SQL statement to check if the database can be accessed.
         */
        static const std::string    check1Stmt;

        /**
         *  The singleton instance of the scheduler daemon.
         */
        static Ptr<SchedulerDaemon>::Ref            schedulerDaemon;

        /**
         *  The authentication client.
         */
        Ptr<AuthenticationClientInterface>::Ref     authentication;

        /**
         *  The connection manager used by the scheduler.
         */
        Ptr<ConnectionManagerInterface>::Ref        connectionManager;

        /**
         *  The storage client.
         */
        Ptr<StorageClientInterface>::Ref            storage;

        /**
         *  The schedule used by the scheduler daemon.
         */
        Ptr<ScheduleInterface>::Ref                 schedule;

        /**
         *  The session id for the scheduler user.
         */
        Ptr<SessionId>::Ref                         sessionId;

        /**
         *  The event scheduler.
         */
        Ptr<LiveSupport::EventScheduler::EventScheduler>::Ref
                                                    eventScheduler;

        /**
         *  The audio player.
         */
        Ptr<AudioPlayerInterface>::Ref              audioPlayer;

        /**
         *  The play logging facility.
         */
        Ptr<PlayLogInterface>::Ref                  playLog;

        /**
         *  The displayScheduleMethod the daemon is providing.
         */
        Ptr<DisplayScheduleMethod>::Ref         displayScheduleMethod;

        /**
         *  The generatePlayReportMethod the daemon is providing.
         */
        Ptr<GeneratePlayReportMethod>::Ref      generatePlayReportMethod;

        /**
         *  The getSchedulerTimeMethod the daemon is providing.
         */
        Ptr<GetSchedulerTimeMethod>::Ref        getSchedulerTimeMethod;

        /**
         *  The getVersion the daemon is providing.
         */
        Ptr<GetVersionMethod>::Ref              getVersionMethod;

        /**
         *  The removeFromScheduleMethod the daemon is providing.
         */
        Ptr<RemoveFromScheduleMethod>::Ref      removeFromScheduleMethod;

        /**
         *  The rescheduleMethod the daemon is providing.
         */
        Ptr<RescheduleMethod>::Ref              rescheduleMethod;

        /**
         *  The uploadPlaylistMethod the daemon is providing.
         */
        Ptr<UploadPlaylistMethod>::Ref          uploadPlaylistMethod;

        /**
         *  The loginMethod the daemon is providing.
         */
        Ptr<LoginMethod>::Ref                   loginMethod;

        /**
         *  The logoutMethod the daemon is providing.
         */
        Ptr<LogoutMethod>::Ref                  logoutMethod;

        /**
         *  The resetStorageMethod the daemon is providing.
         */
        Ptr<ResetStorageMethod>::Ref            resetStorageMethod;

        /**
         *  The createBackupOpenMethod the daemon is providing.
         */
        Ptr<CreateBackupOpenMethod>::Ref        createBackupOpenMethod;

        /**
         *  The createBackupCheckMethod the daemon is providing.
         */
        Ptr<CreateBackupCheckMethod>::Ref       createBackupCheckMethod;

        /**
         *  The createBackupCloseMethod the daemon is providing.
         */
        Ptr<CreateBackupCloseMethod>::Ref       createBackupCloseMethod;

        /**
         *  The restoreBackupMethod the daemon is providing.
         */
        Ptr<RestoreBackupMethod>::Ref           restoreBackupMethod;

        /**
         *  The stopCurrentlyPlayingMethod the daemon is providing.
         */
        Ptr<StopCurrentlyPlayingMethod>::Ref    stopCurrentlyPlayingMethod;

        /**
         *  The login to the authentication system.
         */
        std::string                             login;

        /**
         *  The password to the authentication system.
         */
        std::string                             password;

        /**
         *  Default constructor.
         */
        SchedulerDaemon (void)                                      throw ();


    protected:

        /**
         *  Register your XML-RPC functions by implementing this function.
         */
        virtual void
        registerXmlRpcFunctions(Ptr<XmlRpc::XmlRpcServer>::Ref  xmlRpcServer)
                                                    throw (std::logic_error);

        /**
         *  Execute any calls when the daemon is starting up.
         *  All resources allocated here should be freed up in shutdown().
         *
         *  @exception std::logic_error if startup could not succeed.
         *  @see #shutdown
         */
        virtual void
        startup (void)                              throw (std::logic_error);


    public:

        /**
         *  Virtual destructor.
         */
        virtual
        ~SchedulerDaemon(void)                          throw ();

        /**
         *  Return a pointer to the singleton instance of SchedulerDaemon.
         *
         *  @return a pointer to the singleton instance of SchedulerDaemon
         */
        static Ptr<SchedulerDaemon>::Ref
        getInstance (void)                              throw ();

        /**
         *  Configure the scheduler daemon based on the XML element
         *  supplied.
         *
         *  @param element the XML element to configure the scheduler
         *                 daemon from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the scheduler daemon has already
         *             been configured.
         */
        void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  Return the connection manager used by the scheduler.
         *
         *  @return the connection manager used by the scheduler.
         */
        Ptr<ConnectionManagerInterface>::Ref
        getConnectionManager(void)                      throw ()
        {
            return connectionManager;
        }

        /**
         *  Return the storage client used by the scheduler.
         *
         *  @return the storage client used by the scheduler.
         */
        Ptr<StorageClientInterface>::Ref
        getStorage(void)                                throw ()
        {
            return storage;
        }

        /**
         *  Return the authentication client used by the scheduler.
         *
         *  @return the authentication client used by the scheduler.
         */
        Ptr<AuthenticationClientInterface>::Ref
        getAuthentication(void)                         throw ()
        {
            return authentication;
        }

        /**
         *  Return the schedule used by the scheduler.
         *
         *  @return the schedule used by the scheduler.
         */
        Ptr<ScheduleInterface>::Ref
        getSchedule(void)                               throw ()
        {
            return schedule;
        }

        /**
         *  Return the play log used by the scheduler.
         *
         *  @return the play log used by the scheduler.
         */
        Ptr<PlayLogInterface>::Ref
        getPlayLog(void)                                throw ()
        {
            return playLog;
        }

        /**
         *  Return the audio player used by the scheduler.
         *
         *  @return the audio player used by the scheduler.
         */
        Ptr<AudioPlayerInterface>::Ref
        getAudioPlayer(void)                            throw ()
        {
            return audioPlayer;
        }

        /**
         *  Shut down the daemon.
         *  This function is public only because the signal handler
         *  needs visibility to this function, which will call it.
         *  A call to stop() will trigger a signal that will call shutdown().
         *
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        virtual void
        shutdown (void)                             throw (std::logic_error);

        /**
         *  Re-read the scheduled events.
         *  Call this when the events in the schedule under the event container
         *  have changed.
         *
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        virtual void
        update(void)                                throw (std::logic_error);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // SchedulerDaemon_h

