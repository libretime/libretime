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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/ThreadTest.cxx,v $

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
    Ptr<TestRunnable>::Ref          runnable(new TestRunnable());
    Ptr<Thread>::Ref                thread(new Thread(runnable));
    Ptr<time_duration>::Ref         sleepTime(new time_duration(seconds(1)));

    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::created);
    thread->start();
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::running);
    TimeConversion::sleep(sleepTime);
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::running);
    thread->stop();
    TimeConversion::sleep(sleepTime);
    CPPUNIT_ASSERT(runnable->getState() == TestRunnable::stopped);
    thread->join();
}

