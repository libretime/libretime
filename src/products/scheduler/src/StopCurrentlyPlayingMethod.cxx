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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/scheduler/src/StopCurrentlyPlayingMethod.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_TIME_H
#include <time.h>
#else
#error need time.h
#endif


#include <string>
#include <stdexcept>

#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"
#include "LiveSupport/Core/XmlRpcTools.h"
#include "SchedulerDaemon.h"
#include "ScheduleInterface.h"
#include "ScheduleFactory.h"

#include "StopCurrentlyPlayingMethod.h"


using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of this XML-RPC method.
 *----------------------------------------------------------------------------*/
const std::string   StopCurrentlyPlayingMethod::methodName 
                                                = "stopCurrentlyPlaying";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int           StopCurrentlyPlayingMethod::errorId = 5000;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
StopCurrentlyPlayingMethod :: StopCurrentlyPlayingMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}


/*------------------------------------------------------------------------------
 *  Execute the remove from schedule XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
StopCurrentlyPlayingMethod :: execute(XmlRpc::XmlRpcValue &     rootParameter,
                                      XmlRpc::XmlRpcValue &     returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    if (!rootParameter.valid() || rootParameter.size() != 1
                               || !rootParameter[0].valid()) {
        XmlRpcTools::markError(errorId+1, 
                               "invalid argument format", 
                               returnValue);
        return;
    }
    XmlRpc::XmlRpcValue      parameters = rootParameter[0];

    Ptr<SessionId>::Ref      sessionId;
    try{
        sessionId = XmlRpcTools::extractSessionId(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+20, 
                               "missing session ID argument",
                               returnValue);
        return;
    }
    // TODO: check the session ID

    Ptr<SchedulerDaemon>::Ref       sd = SchedulerDaemon::getInstance();
    Ptr<ScheduleFactory>::Ref       sf = ScheduleFactory::getInstance();
    Ptr<ScheduleInterface>::Ref     schedule = sf->getSchedule();
    Ptr<ScheduleEntry>::Ref         currentlyPlaying;

    // remove the item from the schedule, so something else can be scheduled
    try {
        currentlyPlaying = schedule->getCurrentlyPlaying();
        if (currentlyPlaying) {
            schedule->removeFromSchedule(currentlyPlaying->getId());
            
            // tell the scheduler daemon to reload the scheduled events
            sd->update();
        }
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+3, "schedule entry not found", 
                               returnValue);
        return;
    }

    // stop the audio player
    Ptr<AudioPlayerInterface>::Ref  audioPlayer = sd->getAudioPlayer();

    try {
        audioPlayer->stop();
        audioPlayer->close();

    } catch (std::logic_error &e) {
        XmlRpcTools::markError(errorId+10, e.what(), returnValue);
        return;
    }
}

