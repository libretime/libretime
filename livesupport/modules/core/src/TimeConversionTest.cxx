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
    Version  : $Revision: 1.9 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/TimeConversionTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <iostream>

#include "LiveSupport/Core/TimeConversion.h"
#include "TimeConversionTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(TimeConversionTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test the timevalToPtime function
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: timevalToPtimeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    struct tm       tm;
    time_t          time;
    struct timeval  timeval;
    Ptr<ptime>::Ref pTime;

    // first create a time_t with the time for 2004-11-04 12:58:30
    tm.tm_year   = 104;     // number of years since 1900, 104 means 2004
    tm.tm_mon    = 10;      // number of months since January, 10 means November
    tm.tm_mday   = 4;
    tm.tm_hour   = 12;
    tm.tm_min    = 58;
    tm.tm_sec    = 30;
    tm.tm_isdst  = 0;
    time = mktime(&tm);

    // now fill the timeval with timet, and 1234 useconds
    timeval.tv_sec  = time;
    timeval.tv_usec = 1234;

    // and now convert, and see if it is correct
    pTime = TimeConversion::timevalToPtime(&timeval);
    CPPUNIT_ASSERT(pTime->date().year() == 2004);
    CPPUNIT_ASSERT(pTime->date().month() == 11);
    CPPUNIT_ASSERT(pTime->date().day() == 4);
    CPPUNIT_ASSERT(pTime->time_of_day().hours() == 12);
    CPPUNIT_ASSERT(pTime->time_of_day().minutes() == 58);
    CPPUNIT_ASSERT(pTime->time_of_day().seconds() == 30);
    CPPUNIT_ASSERT((pTime->time_of_day().total_microseconds()
              - ((uint64_t) (pTime->time_of_day().total_seconds()) * 1000000UL))
              == 1234);
}


/*------------------------------------------------------------------------------
 *  Test the tmToPtime function
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: tmToPtimeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    struct tm       tm;
    Ptr<ptime>::Ref pTime;

    // first create a time_t with the time for 2004-11-04 12:58:30
    tm.tm_year   = 104;     // number of years since 1900, 104 means 2004
    tm.tm_mon    = 10;      // number of months since January, 10 means November
    tm.tm_mday   = 4;
    tm.tm_hour   = 12;
    tm.tm_min    = 58;
    tm.tm_sec    = 30;
    tm.tm_isdst  = 0;

    // and now convert, and see if it is correct
    pTime = TimeConversion::tmToPtime(&tm);
    CPPUNIT_ASSERT(pTime->date().year() == 2004);
    CPPUNIT_ASSERT(pTime->date().month() == 11);
    CPPUNIT_ASSERT(pTime->date().day() == 4);
    CPPUNIT_ASSERT(pTime->time_of_day().hours() == 12);
    CPPUNIT_ASSERT(pTime->time_of_day().minutes() == 58);
    CPPUNIT_ASSERT(pTime->time_of_day().seconds() == 30);
}


/*------------------------------------------------------------------------------
 *  Test the ptimeToTm function
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: ptimeToTmTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    struct tm       tm;
    Ptr<ptime>::Ref pTime(new ptime(time_from_string("1770-12-17 10:20:30")));

    TimeConversion::ptimeToTm(pTime, tm);
    CPPUNIT_ASSERT(tm.tm_year + 1900    == 1770);
    CPPUNIT_ASSERT(tm.tm_mon  + 1       == 12);
    CPPUNIT_ASSERT(tm.tm_mday           == 17);
    CPPUNIT_ASSERT(tm.tm_hour   == 10);
    CPPUNIT_ASSERT(tm.tm_min    == 20);
    CPPUNIT_ASSERT(tm.tm_sec    == 30);
}


/*------------------------------------------------------------------------------
 *  Test the now function
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: nowTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    struct tm       tm;
    time_t          tTime;
    Ptr<ptime>::Ref pTime;

    tTime = time(0);
    pTime = TimeConversion::now();

    localtime_r(&tTime, &tm);

    // the below checking is a bit phone, what if the two times actually
    // spill over the second barrier (or, for that instance, the year
    // barrier?)
    CPPUNIT_ASSERT(pTime->date().year() == (1900 + tm.tm_year));
    CPPUNIT_ASSERT(pTime->date().month() == (1 + tm.tm_mon));
    CPPUNIT_ASSERT(pTime->date().day() == tm.tm_mday);
    CPPUNIT_ASSERT(pTime->time_of_day().hours() == tm.tm_hour);
    CPPUNIT_ASSERT(pTime->time_of_day().minutes() == tm.tm_min);
    CPPUNIT_ASSERT(pTime->time_of_day().seconds() == tm.tm_sec);
}


/*------------------------------------------------------------------------------
 *  Test the sleep function
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: sleepTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<ptime>::Ref             start;
    Ptr<ptime>::Ref             end;
    Ptr<time_duration>::Ref     duration;

    duration.reset(new time_duration(seconds(2)));

    start = TimeConversion::now();
    TimeConversion::sleep(duration);
    end = TimeConversion::now();

    CPPUNIT_ASSERT((*end - *start) >= *duration);
}


/*------------------------------------------------------------------------------
 *  Test the timeDurationToSmilString() and timeDurationToHhMmSs() functions
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: durationToStringTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref duration(new time_duration(duration_from_string(
                                                        "01:02:03.503700" )));

    Ptr<std::string>::Ref   smilString
                            = TimeConversion::timeDurationToSmilString(
                                                                    duration);
    CPPUNIT_ASSERT_EQUAL(std::string("3723.504s"), *smilString);

    Ptr<std::string>::Ref   hhMmSsString
                            = TimeConversion::timeDurationToHhMmSsString(
                                                                    duration);
    CPPUNIT_ASSERT_EQUAL(std::string("01:02:04"), *hhMmSsString);

    duration.reset(new time_duration(duration_from_string("111:22:33")));
    hhMmSsString = TimeConversion::timeDurationToHhMmSsString(duration);
    CPPUNIT_ASSERT_EQUAL(std::string("111:22:33"), *hhMmSsString);
}


/*------------------------------------------------------------------------------
 *  Test the parseTimeDuration() function.
 *----------------------------------------------------------------------------*/
void
TimeConversionTest :: parseTimeDurationTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // legal arguments
    Ptr<std::string>::Ref   timeString(new std::string("01:02:03.503700"));
    Ptr<time_duration>::Ref duration;
    CPPUNIT_ASSERT_NO_THROW(
        duration = TimeConversion::parseTimeDuration(timeString)
    );
    CPPUNIT_ASSERT(duration);
    CPPUNIT_ASSERT_EQUAL(std::string("01:02:03.503700"),
                         to_simple_string(*duration));

    timeString.reset(new std::string("02:03.5"));
    CPPUNIT_ASSERT_NO_THROW(
        duration = TimeConversion::parseTimeDuration(timeString)
    );
    CPPUNIT_ASSERT(duration);
    CPPUNIT_ASSERT_EQUAL(std::string("00:02:03.500000"),
                         to_simple_string(*duration));

    timeString.reset(new std::string("77"));
    CPPUNIT_ASSERT_NO_THROW(
        duration = TimeConversion::parseTimeDuration(timeString)
    );
    CPPUNIT_ASSERT(duration);
    CPPUNIT_ASSERT_EQUAL(std::string("00:01:17"),
                        to_simple_string(*duration));

    // illegal arguments
    timeString.reset(new std::string("5 minutes and 2 seconds"));
    CPPUNIT_ASSERT_NO_THROW(
        duration = TimeConversion::parseTimeDuration(timeString)
    );
    CPPUNIT_ASSERT(duration);
    CPPUNIT_ASSERT_EQUAL(std::string("00:00:05"),               // bad!
                        to_simple_string(*duration));

    timeString.reset(new std::string("1.2.3"));
    CPPUNIT_ASSERT_NO_THROW(
        duration = TimeConversion::parseTimeDuration(timeString)
    );
    CPPUNIT_ASSERT(duration);
    CPPUNIT_ASSERT_EQUAL(std::string("00:00:01.000002"),        // bad!
                        to_simple_string(*duration));
}

