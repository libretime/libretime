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
    Version  : $Revision: 1.19 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/SchedulerDaemon.cxx,v $

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
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"
#include "ScheduleFactory.h"
#include "PlayLogFactory.h"
#include "SchedulerDaemon.h"
#include "PlaylistEventContainer.h"


using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The singleton instance of the Scheduler daemon object.
 */
Ptr<SchedulerDaemon>::Ref   SchedulerDaemon::schedulerDaemon;

/**
 *  The name of the XML configuration element for the Scheduler daemon.
 */
static const std::string confElement = "scheduler";

/**
 *  The name of the XML configuration element for the XmlRpcDaemon inside.
 */
static const std::string xmlRpcDaemonConfElement = "xmlRpcDaemon";

/**
 *  The name of the config child element for the login and password
 */
static const std::string    userConfigElementName = "user";

/**
 *  The name of the config element attribute for the login
 */
static const std::string    userLoginAttrName = "login";

/**
 *  The name of the config element attribute for the password
 */
static const std::string    userPasswordAttrName = "password";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  The default constructor.
 *----------------------------------------------------------------------------*/
SchedulerDaemon :: SchedulerDaemon (void)                   throw ()
                        : XmlRpcDaemon()
{
    addAudioClipToPlaylistMethod.reset(new AddAudioClipToPlaylistMethod());
    createPlaylistMethod.reset(new CreatePlaylistMethod());
    deletePlaylistMethod.reset(new DeletePlaylistMethod());
    displayAudioClipMethod.reset(new DisplayAudioClipMethod());
    displayAudioClipsMethod.reset(new DisplayAudioClipsMethod());
    displayPlaylistMethod.reset(new DisplayPlaylistMethod());
    displayPlaylistsMethod.reset(new DisplayPlaylistsMethod());
    displayScheduleMethod.reset(new DisplayScheduleMethod());
    generatePlayReportMethod.reset(new GeneratePlayReportMethod());
    getSchedulerTimeMethod.reset(new GetSchedulerTimeMethod());
    getVersionMethod.reset(new GetVersionMethod());
    openPlaylistForEditingMethod.reset(new OpenPlaylistForEditingMethod());
    removeAudioClipFromPlaylistMethod.reset(new 
                                        RemoveAudioClipFromPlaylistMethod());
    removeFromScheduleMethod.reset(new RemoveFromScheduleMethod());
    rescheduleMethod.reset(new RescheduleMethod());
    revertEditedPlaylistMethod.reset(new RevertEditedPlaylistMethod());
    savePlaylistMethod.reset(new SavePlaylistMethod());
    updateFadeInFadeOutMethod.reset(new UpdateFadeInFadeOutMethod());
    uploadPlaylistMethod.reset(new UploadPlaylistMethod());
    validatePlaylistMethod.reset(new ValidatePlaylistMethod());
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
    const xmlpp::Element      * elem;
    const xmlpp::Attribute    * attribute;

    // read in the user data
    std::string                 login;
    std::string                 password;

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

    // configure the XmlRpcDaemon
    nodes = element.get_children(XmlRpcDaemon::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no xmlRpcDaemon element");
    }
    configureXmlRpcDaemon( *((const xmlpp::Element*) *(nodes.begin())) );


    // do some initialization, using the configured objects
    authentication = acf->getAuthenticationClient();
    try {
        sessionId      = authentication->login(login, password);
    } catch (XmlRpcException &e) {
        // TODO: mark error
        std::cerr << "authentication problem: " << e.what() << std::endl;
    }

    audioPlayer = apf->getAudioPlayer();

    Ptr<PlaylistEventContainer>::Ref    eventContainer;
    Ptr<time_duration>::Ref             granularity;
    eventContainer.reset(new PlaylistEventContainer(sessionId,
                                                    scf->getStorageClient(),
                                                    sf->getSchedule(),
                                                    audioPlayer));
    // TODO: read granularity from config file
    granularity.reset(new time_duration(seconds(1)));

    eventScheduler.reset(
            new LiveSupport::EventScheduler::EventScheduler(eventContainer,
                                                            granularity));
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
    xmlRpcServer->addMethod(addAudioClipToPlaylistMethod.get());
    xmlRpcServer->addMethod(createPlaylistMethod.get());
    xmlRpcServer->addMethod(deletePlaylistMethod.get());
    xmlRpcServer->addMethod(displayAudioClipMethod.get());
    xmlRpcServer->addMethod(displayAudioClipsMethod.get());
    xmlRpcServer->addMethod(displayPlaylistMethod.get());
    xmlRpcServer->addMethod(displayPlaylistsMethod.get());
    xmlRpcServer->addMethod(displayScheduleMethod.get());
    xmlRpcServer->addMethod(generatePlayReportMethod.get());
    xmlRpcServer->addMethod(getSchedulerTimeMethod.get());
    xmlRpcServer->addMethod(getVersionMethod.get());
    xmlRpcServer->addMethod(openPlaylistForEditingMethod.get());
    xmlRpcServer->addMethod(removeAudioClipFromPlaylistMethod.get());
    xmlRpcServer->addMethod(removeFromScheduleMethod.get());
    xmlRpcServer->addMethod(rescheduleMethod.get());
    xmlRpcServer->addMethod(revertEditedPlaylistMethod.get());
    xmlRpcServer->addMethod(savePlaylistMethod.get());
    xmlRpcServer->addMethod(updateFadeInFadeOutMethod.get());
    xmlRpcServer->addMethod(uploadPlaylistMethod.get());
    xmlRpcServer->addMethod(validatePlaylistMethod.get());
}


/*------------------------------------------------------------------------------
 *  Install the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: install(void)                throw (std::exception)
{
    // TODO: check if we have already been configured
    Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
    try {
        sf->install();
    } catch (std::exception &e) {
        std::cerr << e.what() << std::endl;
    }
    Ptr<PlayLogFactory>::Ref    plf = PlayLogFactory::getInstance();
    plf->install();
}


/*------------------------------------------------------------------------------
 *  Install the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: uninstall(void)              throw (std::exception)
{
    // TODO: check if we have already been configured
    Ptr<PlayLogFactory>::Ref    plf = PlayLogFactory::getInstance();
    try {
        plf->uninstall();
    } catch (std::exception &e) {
        std::cerr << e.what() << std::endl;
    }
    
    Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
    sf->uninstall();
}


/*------------------------------------------------------------------------------
 *  Execute daemon startup functions.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: startup (void)                           throw ()
{
    audioPlayer->initialize();
    eventScheduler->start();
    XmlRpcDaemon::startup();
}


/*------------------------------------------------------------------------------
 *  Shut down the daemon
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: shutdown(void)               throw (std::logic_error)
{
    eventScheduler->stop();
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
    eventScheduler->update();
}

