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

#include <string>
#include <iostream>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/Thread.h"
#include "TestRunnable.h"
#include "ThreadTest.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(ThreadTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
ThreadTest :: setUp(void)                                       throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
ThreadTest :: tearDown(void)                                    throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple thread test.
 *----------------------------------------------------------------------------*/
void
ThreadTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref         cycle(new time_duration(seconds(1)));
    Ptr<TestRunnable>::Ref          runnable(new TestRunnable(cycle));
    Ptr<Thread>::Ref                thread(new Thread(runnable));
    Ptr<time_duration>::Ref         sleepTime(new time_duration(seconds(1)));

    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::created);
    thread->start();
    Thread::yield();
    TimeConversion::sleep(sleepTime);
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::running);
    TimeConversion::sleep(sleepTime);
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::running);
    thread->stop();
    TimeConversion::sleep(sleepTime);
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::stopped);
    thread->join();
}


/*------------------------------------------------------------------------------
 *  A test to see if a thread respoding slowly for a stop()
 *  call is joined correctly.
 *----------------------------------------------------------------------------*/
void
ThreadTest :: slowThreadTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref         cycle(new time_duration(seconds(10)));
    Ptr<TestRunnable>::Ref          runnable(new TestRunnable(cycle));
    Ptr<Thread>::Ref                thread(new Thread(runnable));
    Ptr<time_duration>::Ref         sleepTime(new time_duration(seconds(1)));

    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::created);
    thread->start();
    Thread::yield();
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::running);
    TimeConversion::sleep(sleepTime);
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::running);
    thread->stop();
    TimeConversion::sleep(sleepTime);
    thread->join();
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::stopped);
}

