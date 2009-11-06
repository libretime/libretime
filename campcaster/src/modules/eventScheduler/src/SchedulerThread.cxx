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

#define DEBUG_PREFIX "SchedulerThread"
#include "LiveSupport/Core/Debug.h"

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
      : eventContainer(eventContainer),
        granularity(granularity),
        shouldRun(false)
{
    //DEBUG_FUNC_INFO
}


/*------------------------------------------------------------------------------
 *  Get the next event from the eventContainer
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: getNextEvent(Ptr<ptime>::Ref     when)       throw ()
{
    //DEBUG_FUNC_INFO

    nextEvent = eventContainer->getNextEvent(when);
    if (nextEvent.get()) {
        nextEventTime = nextEvent->getScheduledTime();
        nextInitTime.reset(new ptime(*nextEventTime
                                   - *granularity
                                   - *nextEvent->maxTimeToInitialize()));
        nextEventEnd.reset(new ptime(*nextEventTime
                                   + *nextEvent->eventLength()));
        debug() << "::getNextEvent() - nextInitTime:  "
                << to_simple_string(*nextInitTime) << endl;
        debug() << "                 - nextEventTime: "
                << to_simple_string(*nextEventTime) << endl;
        debug() << "                 - nextEventEnd:  "
                << to_simple_string(*nextEventEnd) << endl;
    }
}

/*------------------------------------------------------------------------------
 *  Get the next event from the eventContainer
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: getCurrentEvent()       throw ()
{
    //DEBUG_FUNC_INFO

    nextEvent = eventContainer->getCurrentEvent();
    if (nextEvent.get()) {
		//fake these events here so we can preload
        nextInitTime.reset(new ptime(*TimeConversion::now() + *granularity));
        nextEventTime.reset(new ptime(*nextInitTime + *nextEvent->maxTimeToInitialize()));
        nextEventEnd.reset(new ptime(*nextEvent->getScheduledTime()
                                   + *nextEvent->eventLength()));
        debug() << "::getCurrentEvent() - nextInitTime:  "
                << to_simple_string(*nextInitTime) << endl;
        debug() << "                 - nextEventTime: "
                << to_simple_string(*nextEventTime) << endl;
        debug() << "                 - nextEventEnd:  "
                << to_simple_string(*nextEventEnd) << endl;
    }
}


/*------------------------------------------------------------------------------
 *  The main execution body of the thread.
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: nextStep(Ptr<ptime>::Ref     now)            throw ()
{
    nextEventMutex.lock();
    
    if (nextEvent) {
        if (imminent(now, nextInitTime)) {
            preloadMutex.lock();
            debug() << "::nextStep() - Init [" << *TimeConversion::now()
                    << "]" << endl;
            try {
                nextEvent->initialize();
            } catch (std::exception &e) {
                preloadMutex.unlock();
                // cancel event by getting the next event after this was
                // supposed to finish
                getNextEvent(nextEventEnd);
                // TODO: log error
                std::cerr << "event initialization error: " << e.what()
                          << std::endl;
            }
        } else if (imminent(now, nextEventTime)) {
            Ptr<time_duration>::Ref timePassed(new time_duration(*now
                                                             - *nextEvent->getScheduledTime()));
            Ptr<time_duration>::Ref timeNull(new time_duration(0, 0, 0, 0));
			if(*timePassed > *timeNull) {
				debug() << "::nextStep() with offset - Start [" << *TimeConversion::now()
						<< "]" << endl;
				nextEvent->start(timePassed);
			} else {
				Ptr<time_duration>::Ref timeLeft(new time_duration(*nextEventTime
																 - *now));
				TimeConversion::sleep(timeLeft);
				debug() << "::nextStep() - Start [" << *TimeConversion::now()
						<< "]" << endl;
				nextEvent->start(timeNull);
			}
            currentEvent = nextEvent;
            currentEventEnd = nextEventEnd;
            getNextEvent(TimeConversion::now());
            preloadMutex.unlock();
        }
    }

    nextEventMutex.unlock();
    
    if (currentEvent && imminent(now, currentEventEnd)) {
        Ptr<time_duration>::Ref timeLeft(new time_duration(*currentEventEnd
                                                         - *now));
        TimeConversion::sleep(timeLeft);
        currentEvent->stop();
        currentEvent->deInitialize();
        currentEvent.reset();
        debug() << "::nextStep() - End [" << *TimeConversion::now()
                << "]" << endl;
    }
}


/*------------------------------------------------------------------------------
 *  The main execution body of the thread.
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: run(void)                                    throw ()
{
    //DEBUG_FUNC_INFO

    shouldRun = true;
    getCurrentEvent();//implements scheduler autostart
//    getNextEvent(TimeConversion::now());//use if you do not want autostart

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


/*------------------------------------------------------------------------------
 *  Accept a signal.
 *----------------------------------------------------------------------------*/
void
SchedulerThread :: signal(int signalId)                         throw ()
{
    //DEBUG_FUNC_INFO
    debug() << "::signal() - [" << *TimeConversion::now() << "]" << endl;

    switch (signalId) {
        case UpdateSignal:
            if (nextEventMutex.tryLock()) {
                if (preloadMutex.tryLock()) {
                    getNextEvent(TimeConversion::now());
                    preloadMutex.unlock();
                    nextEventMutex.unlock();
                } else {
                    nextEventMutex.unlock();
                }
            }
            break;

        default:
            break;
    }
}


