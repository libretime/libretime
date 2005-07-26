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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/TimeConversionTest.h,v $

------------------------------------------------------------------------------*/
#ifndef TimeConversionTest_h
#define TimeConversionTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the TimeConversion class.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.8 $
 *  @see TimeConversion
 */
class TimeConversionTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(TimeConversionTest);
    CPPUNIT_TEST(timevalToPtimeTest);
    CPPUNIT_TEST(tmToPtimeTest);
    CPPUNIT_TEST(ptimeToTmTest);
    CPPUNIT_TEST(nowTest);
    CPPUNIT_TEST(sleepTest);
    CPPUNIT_TEST(durationToStringTest);
    CPPUNIT_TEST(parseTimeDurationTest);
    CPPUNIT_TEST_SUITE_END();

    protected:

        /**
         *  Test conversion from struct timeval to ptime
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        timevalToPtimeTest(void)                throw (CPPUNIT_NS::Exception);

        /**
         *  Test conversion from struct tm to ptime
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        tmToPtimeTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Test conversion from ptime to struct tm
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        ptimeToTmTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Test the now function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        nowTest(void)                           throw (CPPUNIT_NS::Exception);

        /**
         *  Test the sleep function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        sleepTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Test the timeDurationToSmilString() and timeDurationToHhMmSs()
         *  functions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        durationToStringTest(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Test the parseTimeDuration() function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        parseTimeDurationTest(void)             throw (CPPUNIT_NS::Exception);


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


} // namespace Core
} // namespace LiveSupport

#endif // TimeConversionTest_h

