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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PostgresqlScheduleTest.h,v $

------------------------------------------------------------------------------*/
#ifndef PostgresqlScheduleTest_h
#define PostgresqlScheduleTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>

#include "LiveSupport/Db/ConnectionManagerInterface.h"


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Db;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the PostgresqlSchedule class.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.4 $
 *  @see PostgresqlSchedule
 */
class PostgresqlScheduleTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(PostgresqlScheduleTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(simpleScheduleTest);
    CPPUNIT_TEST(scheduleAndQueryTest);
    CPPUNIT_TEST(getScheduleEntriesTest);
    CPPUNIT_TEST(scheduleEntryExistsTest);
    CPPUNIT_TEST(removeFromScheduleTest);
    CPPUNIT_TEST(rescheduleTest);
    CPPUNIT_TEST_SUITE_END();

    private:
        /**
         *  The connection manager used for testing.
         */
        Ptr<ConnectionManagerInterface>::Ref    cm;

        /**
         *  The schedule used for testing.
         */
        Ptr<PostgresqlSchedule>::Ref                    schedule;

    protected:

        /**
         *  Test for an available timeframe in an empty schedule database.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Schedule a single playlist.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simpleScheduleTest(void)                throw (CPPUNIT_NS::Exception);

        /**
         *  Schedule a single playlist, and then query for available timeframes
         *  around it.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        scheduleAndQueryTest(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Schedule some playlists, then get the list of scheduled playlists
         *  for different time intervals.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getScheduleEntriesTest(void)            throw (CPPUNIT_NS::Exception);

        /**
         *  Schedule some playlists, then check if they exist.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        scheduleEntryExistsTest(void)           throw (CPPUNIT_NS::Exception);

        /**
         *  Schedule some playlists, then remove them.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        removeFromScheduleTest(void)           throw (CPPUNIT_NS::Exception);

        /**
         *  Schedule some playlists, then reschedule them.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        rescheduleTest(void)                    throw (CPPUNIT_NS::Exception);

    public:
        
        /**
         *  Set up the environment for the test case.
         */
        void
        setUp(void)                                     throw ();

        /**
         *  Clean up the environment after the test case.
         */
        void
        tearDown(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // PostgresqlScheduleTest_h

