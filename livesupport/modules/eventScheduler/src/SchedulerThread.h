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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/eventScheduler/src/SchedulerThread.h,v $

------------------------------------------------------------------------------*/
#ifndef SchedulerThread_h
#define SchedulerThread_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/RunnableInterface.h"
#include "LiveSupport/EventScheduler/ScheduledEventInterface.h"
#include "LiveSupport/EventScheduler/EventContainerInterface.h"


namespace LiveSupport {
namespace EventScheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The main, executing thread of the scheduler.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.4 $
 */
class SchedulerThread : public virtual RunnableInterface
{
    public:
        /**
         *  Enumerated type for the signals accepted by this object.
         *
         *  @see #signal
         */
        typedef enum {  UpdateSignal }  SignalTypes;

    private:
        /**
         *  The event container, to get the events from.
         */
        Ptr<EventContainerInterface>::Ref   eventContainer;

        /**
         *  The next event to execute.
         */
        Ptr<ScheduledEventInterface>::Ref   nextEvent;

        /**
         *  The execution time of the next event.
         */
        Ptr<const ptime>::Ref               nextEventTime;

        /**
         *  The time to start the initialization of the next event.
         */
        Ptr<ptime>::Ref                     nextInitTime;

        /**
         *  The ending time of the next event.
         */
        Ptr<ptime>::Ref                     nextEventEnd;

        /**
         *  The granularity of the scheduler: the time it will sleep
         *  between checking up on the state of things.
         */
        Ptr<time_duration>::Ref             granularity;

        /**
         *  Flag indicating whether the thread should still run, or
         *  actually terminate.
         */
        bool                                shouldRun;

        /**
         *  Default constructor.
         */
        SchedulerThread(void)                           throw ()
        {
        }

        /**
         *  Get the next event.
         *
         *  @param when get the first event after this specified time.
         */
        void
        getNextEvent(Ptr<ptime>::Ref    when)           throw ();

        /**
         *  Tell if the specified time falls within now and the next
         *  waking up. Basically tells if it is within now and
         *  now + granularity.
         *
         *  @param now the current time.
         *  @param time check this time if it is imminent.
         *  @return true if the specified time falls within now and
         *          now + granularity, false otherwise.
         */
        bool
        imminent(Ptr<const ptime>::Ref    now,
                 Ptr<const ptime>::Ref    when) const         throw ()
        {
            return *when >= *now && (*now + *granularity) > *when;
        }

        /**
         *  Execute the next step on the active event, if any is imminent.
         *
         *  @param now the current time.
         */
        void
        nextStep(Ptr<ptime>::Ref    now)                throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param eventContainer the container this thread will get its
         *         events to schedule from.
         *  @param granularity the granularity of the thread: the time the
         *         thread will sleep between checking up on things.
         */
        SchedulerThread(Ptr<EventContainerInterface>::Ref   eventContainer,
                        Ptr<time_duration>::Ref             granularity)
                                                                    throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~SchedulerThread(void)                          throw ()
        {
        }

        /**
         *  The main execution loop for the thread.
         */
        virtual void
        run(void)                                       throw ();

        /**
         *  Accept a signal.
         *  Currently supported signal values are:
         *  <ul>
         *      <li>UpdateSignal - re-read the event container</li>
         *  </ul>
         *
         *  @param signalId a value from SignalTypes.
         *  @see #signalTypes
         */
        virtual void
        signal(int signalId)                            throw ();
        

        /**
         *  Signal the thread to stop, gracefully.
         */
        virtual void
        stop(void)                                      throw ()
        {
            shouldRun = false;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace EventScheduler
} // namespace LiveSupport


#endif // SchedulerThread_h

