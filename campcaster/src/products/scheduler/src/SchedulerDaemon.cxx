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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_SIGNAL_H
#include <signal.h>
#else
#error "Need signal.h"
#endif

#if HAVE_SYS_STAT_H
#include <sys/stat.h>
#else
#error "Need sys/stat.h"
#endif


#include <iostream>
#include <sstream>
#include <fstream>
#include <cstdio>

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/StorageClient/StorageClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"
#include "ScheduleFactory.h"
#include "PlayLogFactory.h"
#include "BackupFactory.h"
#include "PlaylistEventContainer.h"

#include "SchedulerDaemon.h"

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::Db;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The singleton instance of the Scheduler daemon object.
 */
Ptr<SchedulerDaemon>::Ref   SchedulerDaemon::schedulerDaemon;

namespace {

/**
 *  The name of the XML configuration element for the Scheduler daemon.
 */
const std::string confElement = "scheduler";

/**
 *  The name of the XML configuration element for the XmlRpcDaemon inside.
 */
const std::string xmlRpcDaemonConfElement = "xmlRpcDaemon";

/**
 *  The name of the config child element for the login and password
 */
const std::string    userConfigElementName = "user";

/**
 *  The name of the config element attribute for the login
 */
const std::string    userLoginAttrName = "login";

/**
 *  The name of the config element attribute for the password
 */
const std::string    userPasswordAttrName = "password";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  The default constructor.
 *----------------------------------------------------------------------------*/
SchedulerDaemon :: SchedulerDaemon (void)                   throw ()
                        : XmlRpcDaemon()
{
    displayScheduleMethod.reset(new DisplayScheduleMethod());
    generatePlayReportMethod.reset(new GeneratePlayReportMethod());
    getSchedulerTimeMethod.reset(new GetSchedulerTimeMethod());
    getVersionMethod.reset(new GetVersionMethod());
    removeFromScheduleMethod.reset(new RemoveFromScheduleMethod());
    rescheduleMethod.reset(new RescheduleMethod());
    uploadPlaylistMethod.reset(new UploadPlaylistMethod());
    loginMethod.reset(new LoginMethod());
    logoutMethod.reset(new LogoutMethod());
    resetStorageMethod.reset(new ResetStorageMethod());
    createBackupOpenMethod.reset(new CreateBackupOpenMethod());
    createBackupCheckMethod.reset(new CreateBackupCheckMethod());
    createBackupCloseMethod.reset(new CreateBackupCloseMethod());
    restoreBackupMethod.reset(new RestoreBackupMethod());
    stopCurrentlyPlayingMethod.reset(new StopCurrentlyPlayingMethod());
}


/*------------------------------------------------------------------------------
 *  Return the singleton instnace.
 *----------------------------------------------------------------------------*/
Ptr<SchedulerDaemon>::Ref
SchedulerDaemon :: getInstance (void)                       throw ()
{
    if (!schedulerDaemon) {
        schedulerDaemon.reset(new SchedulerDaemon());
    }

    return schedulerDaemon;
}


/*------------------------------------------------------------------------------
 *  Configure the scheduler daemon
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != confElement) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    xmlpp::Node::NodeList       nodes;
    const xmlpp::Element      * elem = 0;
    const xmlpp::Attribute    * attribute = 0;

    // read in the user data

    nodes = element.get_children(userConfigElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no user element");
    }
    elem = dynamic_cast<const xmlpp::Element*> (*nodes.begin());
    if (!(attribute = elem->get_attribute(userLoginAttrName))) {
        throw std::invalid_argument("missing login attribute");
    }
    login = attribute->get_value();
    if (!(attribute = elem->get_attribute(userPasswordAttrName))) {
        throw std::invalid_argument("missing password attribute");
    }
    password = attribute->get_value();

    // configure the ConnectionManagerFactory
    nodes =
         element.get_children(ConnectionManagerFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no connectionManagerFactory  element");
    }
    Ptr<ConnectionManagerFactory>::Ref cmf
                                = ConnectionManagerFactory::getInstance();
    cmf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the AuthenticationClientFactory
    nodes =
      element.get_children(AuthenticationClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no authenticationClientFactory element");
    }
    Ptr<AuthenticationClientFactory>::Ref acf
                                = AuthenticationClientFactory::getInstance();
    acf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the StorageClientFactory
    nodes = element.get_children(StorageClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no storageClientFactory element");
    }
    Ptr<StorageClientFactory>::Ref scf = StorageClientFactory::getInstance();
    scf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the AudioPlayerFactory
    nodes = element.get_children(AudioPlayerFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no audioPlayer element");
    }
    Ptr<AudioPlayerFactory>::Ref    apf = AudioPlayerFactory::getInstance();
    apf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the ScheduleFactory
    nodes = element.get_children(ScheduleFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no scheduleFactory element");
    }
    Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
    sf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the PlayLogFactory
    nodes = element.get_children(PlayLogFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no playLogFactory element");
    }
    Ptr<PlayLogFactory>::Ref   plf = PlayLogFactory::getInstance();
    plf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the BackupFactory
    nodes = element.get_children(BackupFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no backupFactory element");
    }
    Ptr<BackupFactory>::Ref     bf = BackupFactory::getInstance();
    bf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the XmlRpcDaemon
    nodes = element.get_children(XmlRpcDaemon::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no xmlRpcDaemon element");
    }
    configureXmlRpcDaemon( *((const xmlpp::Element*) *(nodes.begin())) );

    // do some initialization, using the configured objects
    authentication    = acf->getAuthenticationClient();
    connectionManager = cmf->getConnectionManager();
    storage           = scf->getStorageClient();
    audioPlayer       = apf->getAudioPlayer();
    schedule          = sf->getSchedule();
    playLog           = plf->getPlayLog();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
SchedulerDaemon :: ~SchedulerDaemon(void)                       throw ()
{
    if (authentication.get() && sessionId.get()) {
        authentication->logout(sessionId);
    }
}


/*------------------------------------------------------------------------------
 *  Register our XML-RPC methods
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: registerXmlRpcFunctions(
                            Ptr<XmlRpc::XmlRpcServer>::Ref  xmlRpcServer)
                                                    throw (std::logic_error)
{
    xmlRpcServer->addMethod(displayScheduleMethod.get());
    xmlRpcServer->addMethod(generatePlayReportMethod.get());
    xmlRpcServer->addMethod(getSchedulerTimeMethod.get());
    xmlRpcServer->addMethod(getVersionMethod.get());
    xmlRpcServer->addMethod(removeFromScheduleMethod.get());
    xmlRpcServer->addMethod(rescheduleMethod.get());
    xmlRpcServer->addMethod(uploadPlaylistMethod.get());
    xmlRpcServer->addMethod(loginMethod.get());
    xmlRpcServer->addMethod(logoutMethod.get());
    xmlRpcServer->addMethod(resetStorageMethod.get());
    xmlRpcServer->addMethod(createBackupOpenMethod.get());
    xmlRpcServer->addMethod(createBackupCheckMethod.get());
    xmlRpcServer->addMethod(createBackupCloseMethod.get());
    xmlRpcServer->addMethod(restoreBackupMethod.get());
    xmlRpcServer->addMethod(stopCurrentlyPlayingMethod.get());
}


/*------------------------------------------------------------------------------
 *  Execute daemon startup functions.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: startup (void)                   throw (std::logic_error)
{
    try {
        sessionId      = authentication->login(login, password);
    } catch (XmlRpcException &e) {
        throw std::logic_error(std::string("authentication problem: ")
                               + e.what());
    }

    try {
        audioPlayer->initialize();
    } catch (std::exception &e) {
        throw std::logic_error(std::string("audio player initialization "
                               "problem: ") + e.what());
    }
    if (!eventScheduler.get()) {
        Ptr<PlaylistEventContainer>::Ref    eventContainer;
        Ptr<time_duration>::Ref             granularity;
        eventContainer.reset(new PlaylistEventContainer(sessionId,
                                                        storage,
                                                        schedule,
                                                        audioPlayer,
                                                        playLog));
        // TODO: read granularity from config file
        granularity.reset(new time_duration(seconds(1)));

        eventScheduler.reset(
                new LiveSupport::EventScheduler::EventScheduler(eventContainer,
                                                                granularity));
    }
    eventScheduler->start();

    XmlRpcDaemon::startup();
}


/*------------------------------------------------------------------------------
 *  Shut down the daemon
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: shutdown(void)               throw (std::logic_error)
{
    if (eventScheduler.get()) {
        eventScheduler->stop();
    }
    audioPlayer->deInitialize();

    XmlRpcDaemon::shutdown();
}


/*------------------------------------------------------------------------------
 *  Re-read the events from the event container.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: update (void)                throw (std::logic_error)
{
    // TODO: check if we've been configured
    if (eventScheduler.get()) {
        eventScheduler->update();
    }
}

