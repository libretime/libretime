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
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"
#include "ScheduleFactory.h"
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



/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  The default constructor.
 *----------------------------------------------------------------------------*/
SchedulerDaemon :: SchedulerDaemon (void)                   throw ()
                        : XmlRpcDaemon()
{
    uploadPlaylistMethod.reset(new UploadPlaylistMethod());
    displayScheduleMethod.reset(new DisplayScheduleMethod());
    displayPlaylistMethod.reset(new DisplayPlaylistMethod());
    removeFromScheduleMethod.reset(new RemoveFromScheduleMethod());
    rescheduleMethod.reset(new RescheduleMethod());
}


/*------------------------------------------------------------------------------
 *  Return the singleton instnace.
 *----------------------------------------------------------------------------*/
class Ptr<SchedulerDaemon>::Ref
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

    xmlpp::Node::NodeList   nodes;

    // configure the ConnectionManagerFactory
    nodes =
         element.get_children(ConnectionManagerFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no connectionManagerFactory  element");
    }
    Ptr<ConnectionManagerFactory>::Ref cmf
                                = ConnectionManagerFactory::getInstance();
    cmf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

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

    // configure the XmlRpcDaemon
    nodes = element.get_children(XmlRpcDaemon::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no xmlRpcDaemon element");
    }
    configureXmlRpcDaemon( *((const xmlpp::Element*) *(nodes.begin())) );


    // do some initialization, using the configured objects
    audioPlayer = apf->getAudioPlayer();

    Ptr<PlaylistEventContainer>::Ref    eventContainer;
    Ptr<time_duration>::Ref             granularity;
    eventContainer.reset(new PlaylistEventContainer(scf->getStorageClient(),
                                                    sf->getSchedule(),
                                                    audioPlayer));
    // TODO: read granularity from config file
    granularity.reset(new time_duration(seconds(30)));

    eventScheduler.reset(
            new LiveSupport::EventScheduler::EventScheduler(eventContainer,
                                                            granularity));
}


/*------------------------------------------------------------------------------
 *  Register our XML-RPC methods
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: registerXmlRpcFunctions(
                            Ptr<XmlRpc::XmlRpcServer>::Ref  xmlRpcServer)
                                                    throw (std::logic_error)
{
    xmlRpcServer->addMethod(uploadPlaylistMethod.get());
    xmlRpcServer->addMethod(displayScheduleMethod.get());
    xmlRpcServer->addMethod(displayPlaylistMethod.get());
    xmlRpcServer->addMethod(removeFromScheduleMethod.get());
    xmlRpcServer->addMethod(rescheduleMethod.get());
}


/*------------------------------------------------------------------------------
 *  Install the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: install(void)                throw (std::exception)
{
    // TODO: check if we have already been configured
    Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
    sf->install();
}


/*------------------------------------------------------------------------------
 *  Install the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: uninstall(void)              throw (std::exception)
{
    // TODO: check if we have already been configured
    Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
    sf->uninstall();
}


/*------------------------------------------------------------------------------
 *  Start the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: start(void)                  throw (std::logic_error)
{
    std::cerr << "SchedulerDaemon::start #1" << std::endl;
    audioPlayer->initialize();
    std::cerr << "SchedulerDaemon::start #2" << std::endl;
    eventScheduler->start();
    std::cerr << "SchedulerDaemon::start #3" << std::endl;
    XmlRpcDaemon::start();
    std::cerr << "SchedulerDaemon::start #4" << std::endl;
}


/*------------------------------------------------------------------------------
 *  Stop the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemon :: stop(void)                   throw (std::logic_error)
{
    eventScheduler->stop();
    audioPlayer->deInitialize();

    XmlRpcDaemon::stop();
}


