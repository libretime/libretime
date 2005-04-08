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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/eventScheduler/src/EventSchedulerTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif


#include <string>
#include <iostream>

#include "LiveSupport/Core/TimeConversion.h"

#include "LiveSupport/EventScheduler/EventScheduler.h"
#include "TestScheduledEvent.h"
#include "TestEventContainer.h"
#include "EventSchedulerTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::EventScheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(EventSchedulerTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
EventSchedulerTest :: setUp(void)                           throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
EventSchedulerTest :: tearDown(void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple test for the event scheduler thread
 *----------------------------------------------------------------------------*/
void
EventSchedulerTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<TestScheduledEvent>::Ref        event;
    Ptr<TestEventContainer>::Ref        container;
    Ptr<EventScheduler>::Ref            eventScheduler;
    Ptr<ptime>::Ref                     now;
    Ptr<ptime>::Ref                     when;
    Ptr<time_duration>::Ref             initTime;
    Ptr<time_duration>::Ref             eventLength;
    Ptr<time_duration>::Ref             granularity;
    TestScheduledEvent::State           state;

    /* time timeline for this test is:
       initialize - 1sec, sometime before start
       start      - now + 5sec
       stop       - start + 3 sec
     */

    now = TimeConversion::now();
    when.reset(new ptime(*now + seconds(5)));
    initTime.reset(new time_duration(seconds(1)));
    eventLength.reset(new time_duration(seconds(3)));
    granularity.reset(new time_duration(seconds(1)));

    event.reset(new TestScheduledEvent(when, initTime, eventLength));
    container.reset(new TestEventContainer(event));

    eventScheduler.reset(new EventScheduler(container, granularity));

    eventScheduler->start();

    CPPUNIT_ASSERT(event->getState() == TestScheduledEvent::created);
    state = event->getState();

    for (bool running = true; running; ) {
        // check for each state, and see if they are entered into in a correct
        // order
        now = TimeConversion::now();
        switch (event->getState()) {
            case TestScheduledEvent::created:
                CPPUNIT_ASSERT(state == TestScheduledEvent::created);
                break;

            case TestScheduledEvent::initializing:
                // as the init time is same as granularity, we will only
                // see initializing once, and can assume that the previous
                // state was 'created'
                CPPUNIT_ASSERT(state == TestScheduledEvent::created);
                break;

            case TestScheduledEvent::initialized:
                CPPUNIT_ASSERT(state == TestScheduledEvent::initializing
                            || state == TestScheduledEvent::initialized);
                break;

            case TestScheduledEvent::running:
                CPPUNIT_ASSERT(state == TestScheduledEvent::initialized
                            || state == TestScheduledEvent::running);
                // see if the state changed from initialized to running
                // at the appropriate time
                if (state == TestScheduledEvent::initialized) {
                    CPPUNIT_ASSERT(*when <= *now
                                && *now <= *when + *granularity);
                }
                break;

            case TestScheduledEvent::stopped:
                CPPUNIT_ASSERT(state == TestScheduledEvent::running
                            || state == TestScheduledEvent::stopped);
                break;

            case TestScheduledEvent::deInitialized:
                // accept running as a possible previous state, as we might
                // not catch the stopped state at all
                CPPUNIT_ASSERT(state == TestScheduledEvent::running
                            || state == TestScheduledEvent::stopped);
                running = false;
                break;

            default:
                CPPUNIT_FAIL("unrecognized event state");
        }
        state = event->getState();
        TimeConversion::sleep(granularity);
    }

    eventScheduler->stop();
}


/*------------------------------------------------------------------------------
 *  A test to see what happens, when an event is scheduled after it should
 *  have been initialized, but not to be started yet.
 *  See http://bugs.campware.org/view.php?id=757 for details.
 *----------------------------------------------------------------------------*/
void
EventSchedulerTest :: postInitTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<TestScheduledEvent>::Ref        event;
    Ptr<TestEventContainer>::Ref        container;
    Ptr<EventScheduler>::Ref            eventScheduler;
    Ptr<ptime>::Ref                     now;
    Ptr<ptime>::Ref                     when;
    Ptr<time_duration>::Ref             initTime;
    Ptr<time_duration>::Ref             eventLength;
    Ptr<time_duration>::Ref             granularity;
    TestScheduledEvent::State           state;

    /* time timeline for this test is:
       initialize - 10 sec
       start      - now + 5sec
       stop       - start + 3 sec
     */

    now = TimeConversion::now();
    when.reset(new ptime(*now + seconds(5)));
    initTime.reset(new time_duration(seconds(10)));
    eventLength.reset(new time_duration(seconds(3)));
    granularity.reset(new time_duration(seconds(1)));

    event.reset(new TestScheduledEvent(when, initTime, eventLength));
    container.reset(new TestEventContainer(event));

    eventScheduler.reset(new EventScheduler(container, granularity));

    eventScheduler->start();

    CPPUNIT_ASSERT(event->getState() == TestScheduledEvent::created);
    state = event->getState();

    Ptr<ptime>::Ref     end(new ptime(*when + seconds(10)));
    while (*TimeConversion::now() < *end) {
        // nothing should happen here, just wait for some to see that
        // indeed it doesn't
        CPPUNIT_ASSERT(event->getState() == TestScheduledEvent::created);
        TimeConversion::sleep(granularity);
    }

    eventScheduler->stop();
}

