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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/eventScheduler/src/SchedulerThread.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TimeConversion.h"

#include "SchedulerThread.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::EventScheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
SchedulerThread :: SchedulerThread(
                        Ptr<EventContainerInterface>::Ref   eventContainer,
                        Ptr<time_duration>::Ref             granularity)
                                                                    throw ()
{
    this->eventContainer = eventContainer;
    this->granularity    = granularity;
    this->shouldRun      = false;
}


/*------------------------------------------------------------------------------
 *  Get the next event from the eventContainer
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: getNextEvent(Ptr<ptime>::Ref     when)       throw ()
{
    nextEvent = eventContainer->getNextEvent(when);
    if (nextEvent.get()) {
        nextEventTime = nextEvent->getScheduledTime();
        nextInitTime.reset(new ptime(*nextEventTime
                                   - *granularity
                                   - *nextEvent->maxTimeToInitialize()));
        nextEventEnd.reset(new ptime(*nextEventTime
                                   + *nextEvent->eventLength()));
    }
}


/*------------------------------------------------------------------------------
 *  The main execution body of the thread.
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: nextStep(Ptr<ptime>::Ref     now)            throw ()
{
    if (!nextEvent.get()) {
        return;
    }

    if (imminent(now, nextInitTime)) {
        try {
            nextEvent->initialize();
        } catch (std::exception &e) {
            // cancel event by getting the next event after this was
            // supposed to finish
            getNextEvent(nextEventEnd);
            // TODO: log error
            std::cerr << "event initialization error: " << e.what()
                      << std::endl;
        }
    } else if (imminent(now, nextEventTime)) {
        Ptr<time_duration>::Ref timeLeft(new time_duration(*nextEventTime
                                                         - *now));
        TimeConversion::sleep(timeLeft);
        nextEvent->start();
    } else if (imminent(now, nextEventEnd)) {
        Ptr<time_duration>::Ref timeLeft(new time_duration(*nextEventEnd
                                                         - *now));
        TimeConversion::sleep(timeLeft);
        nextEvent->stop();
        nextEvent->deInitialize();
    }
}


/*------------------------------------------------------------------------------
 *  The main execution body of the thread.
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: run(void)                                    throw ()
{
    shouldRun = true;
    getNextEvent(TimeConversion::now());

    while (shouldRun) {
        Ptr<ptime>::Ref     start = TimeConversion::now();

        nextStep(start);

        // sleep until the next granularity
        Ptr<ptime>::Ref             end = TimeConversion::now();
        Ptr<time_duration>::Ref     diff(new time_duration(*end - *start));
        if (*diff <= *granularity) {
            Ptr<time_duration>::Ref sleepTime(new time_duration(*granularity
                                                              - *diff));
            TimeConversion::sleep(sleepTime);
        }
    }
}

