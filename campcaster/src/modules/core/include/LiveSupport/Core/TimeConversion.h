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
#ifndef LiveSupport_Core_TimeConversion_h
#define LiveSupport_Core_TimeConversion_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_SYS_TIME_H
#include <sys/time.h>
#else
#error need sys/time.h
#endif

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Core {

using namespace boost::posix_time;

using namespace LiveSupport;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A helper object holding static time conversion functions.
 *
 *  TODO: clean this up, and use the boost conversion functions, instead of
 *  converting stuff manually
 *  (see http://boost.org/doc/html/date_time/posix_time.html).
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class TimeConversion
{
    private:
        /**
         *  Parse a time string.
         *
         *  It cuts off the portion between the end of the string and the last
         *  occurrence of the separator character.  For example, if called with
         *  the parameters "00:01:02" and ':', the function returns "02" and 
         *  truncates the original string to "00:01".
         *
         *  If the separator character is not found in <code>timeString</code>,
         *  a copy of the whole <code>timeString</code> is returned, and the
         *  original <code>timeString</code> is changed to the empty string.
         *
         *  @param timeString   the input argument; on return, the rest of the
         *                          string, after the return value cut off
         *  @param separator    a separator character, usually ':'
         *  @return             the part of the string which was cut off
         */
        static Ptr<std::string>::Ref
        nextNumberFromEnd(Ptr<std::string>::Ref timeString,
                          char                  separator)      throw ();

        /**
         *  Parse a decimal string.
         *
         *  It cuts off the portion between the end of the string and the last
         *  occurrence of the separator character.  For example, if called with
         *  the parameters "1.23" and '.', the function returns "23" and 
         *  truncates the original string to "1".
         *
         *  If the separator character is not found in <code>timeString</code>,
         *  then an empty string is returned, and the
         *  original <code>timeString</code> remains unchanged.
         *
         *  @param decimalString    the input argument; on return, the rest of
         *                          the string, after the return value cut off
         *  @param separator    a separator character, usually '.'
         *  @return             the part of the string which was cut off
         */
        static Ptr<std::string>::Ref
        nextNumberFromStart(Ptr<std::string>::Ref decimalString,
                            char                  separator)      throw ();

        /**
         *  The default constructor.
         */
        TimeConversion(void)                                    throw ()
        {
        }


    public:
        /**
         *  Convert a struct timeval to a boost::posix_time::ptime,
         *  with microsecond precision.
         *
         *  @param timeval the struct timeval to convert.
         *  @return a boost::posix_time::ptime, holding the same time.
         *  @exception std::out_of_range if timeval represents a time that
         *             can not be handled by ptime
         */
        static Ptr<ptime>::Ref
        timevalToPtime(const struct timeval *timeval)
                                                    throw (std::out_of_range);

        /**
         *  Convert a struct tm to a boost::posix_time::ptime,
         *  with second precision.
         *
         *  @param time the struct tm to convert.
         *  @return a boost::posix_time::ptime, holding the same time.
         *  @exception std::out_of_range if time represents a time that
         *             can not be handled by ptime
         */
        static Ptr<ptime>::Ref
        tmToPtime(const struct tm *time)
                                                    throw (std::out_of_range);

        /**
         *  Convert a boost::posix_time::ptime to a struct tm,
         *  with second precision.
         *
         *  @param convertFrom the boost::posix_time::ptime to convert.
         *  @param convertTo holds the result of the conversion
         *  @return a struct tm, holding the same time.
         */
        static void
        ptimeToTm(Ptr<const ptime>::Ref convertFrom, struct tm & convertTo)
                                                                throw ();

        /**
         *  Return the current time, with microsecond precision.
         *
         *  @return the current time, with microsecond precision.
         */
        static Ptr<ptime>::Ref
        now(void)                                               throw ();

        /**
         *  Return the current time, with microsecond precision, as a string.
         *
         *  @return the current time, with microsecond precision.
         */
        static Ptr<std::string>::Ref
        nowString(void)                                         throw ();

        /**
         *  Sleep for the specified time duration, with microsecond precision.
         *
         *  @param duration sleep for this duration.
         */
        static void
        sleep(Ptr<const time_duration>::Ref   duration)         throw ();

        /**
         *  Convert a time_duration to a format used in SMILs.
         *  This means number of seconds, rounded to the nearest millisecond.
         *  For example: "1234.567s", "0.890s", or "3.000s".
         *
         *  @param duration the time duration to convert.
         */
        static Ptr<std::string>::Ref
        timeDurationToSmilString(Ptr<const time_duration>::Ref    duration)
                                                                throw ();

        /**
         *  Convert a time_duration to a rounded format used on the screen.
         *
         *  This means a hh:mm:ss format, rounded to the nearest second.
         *
         *  For example: "01:02:03" or "00:10:00".  The hours field can be
         *  more than two characters wide, e.g.: "8765:48:45".
         *
         *  @param duration the time duration to convert.
         *  @return the time duration in string format
         */
        static Ptr<std::string>::Ref
        timeDurationToHhMmSsString(Ptr<const time_duration>::Ref  duration)
                                                                throw ();

        /**
         *  Convert a time_duration to a format used for fade info.
         *
         *  This means a hh:mm:ss.ffffff format, with hours, minutes and
         *  fractions left off when zero.
         *
         *  For example: 01:02:03.004, 1:02 (meaning 1m 2s), 3 (meaning 3s),
         *  0.002 (meaning 2ms).  Zero is represented as 0.
         *
         *  @param duration the time duration to convert.
         *  @return the time duration in string format
         */
        static Ptr<std::string>::Ref
        timeDurationToShortString(Ptr<const time_duration>::Ref  duration)
                                                                throw ();

        /**
         *  Parse a string to a time_duration.
         *  Similar to boost::posix_time::duration_from_string(), only
         *  not broken quite as badly.
         *  
         *  Parsing is right-to-left, starting with seconds: for example,
         *  5 means 5 seconds; 01:02.03 means 1m 2.03s; 1:2:3 means 1h 2m 3s.
         *
         *  If the time format is invalid, no exception is thrown, but the
         *  result is undefined (usually 00:00:00).
         *  TODO: fix this, by adding a format check
         *
         *  @param durationString   the duration as string
         *  @return                 the duration as a time_duration
         */
        static Ptr<time_duration>::Ref
        parseTimeDuration(Ptr<const std::string>::Ref     durationString)
                                                                throw ();

        /**
         *  Get the number of digits used for fractional seconds 
         *  in time durations.
         *  @return the constant 6, for microsecond precision.
         */
        static int
        getNumberOfDigitsPrecision(void)                        throw ();

        /**
         *  Round the time duration to the nearest second.
         *
         *  @param  duration    the time to be rounded; it will not be
         *                      modified.
         *  @return the rounded value.
         */
        static Ptr<time_duration>::Ref
        roundToNearestSecond(Ptr<const time_duration>::Ref  duration)
                                                                throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_TimeConversion_h

