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
    Version  : $Revision: 1.10 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/TimeConversion.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iomanip>

#include "LiveSupport/Core/TimeConversion.h"


using namespace boost::posix_time;
using namespace boost::gregorian;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The number of digits used for fractional seconds in time durations.
 */
static const int   numberOfDigitsPrecision = 6;

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Convert a struct timeval to a boost::ptime
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
TimeConversion :: timevalToPtime(const struct timeval *timeval)
                                                    throw (std::out_of_range)
{
    // don't convert through the boost::posix_time::from_time_t() function
    // as probably because of timezone settings it ruins the actual value
    struct tm   tm;
    localtime_r(&timeval->tv_sec, &tm);
    Ptr<ptime>::Ref time(new ptime(date(1900 + tm.tm_year,
                                        1 + tm.tm_mon,
                                        tm.tm_mday),
                                   time_duration(tm.tm_hour,
                                                 tm.tm_min,
                                                 tm.tm_sec)
                                   + microseconds(timeval->tv_usec)));

    return time;
}


/*------------------------------------------------------------------------------
 *  Convert a struct tm to a boost::ptime
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
TimeConversion :: tmToPtime(const struct tm *time)
                                                    throw (std::out_of_range)
{
    // don't convert through the boost::posix_time::from_time_t() function
    // as probably because of timezone settings it ruins the actual value
    Ptr<ptime>::Ref pTime(new ptime(date(1900 + time->tm_year,
                                         1 + time->tm_mon,
                                         time->tm_mday),
                                    time_duration(time->tm_hour,
                                                  time->tm_min,
                                                  time->tm_sec)));

    return pTime;
}


/*------------------------------------------------------------------------------
 *  Convert a boost::ptime to a struct tm
 *----------------------------------------------------------------------------*/
void
TimeConversion :: ptimeToTm(Ptr<ptime>::Ref convertFrom, struct tm & convertTo)
                                                                    throw ()
{
    date            date = convertFrom->date();
    time_duration   time = convertFrom->time_of_day();

    convertTo.tm_year = date.year()  - 1900;
    convertTo.tm_mon  = date.month() - 1;
    convertTo.tm_mday = date.day();
    convertTo.tm_hour = time.hours();
    convertTo.tm_min  = time.minutes();
    convertTo.tm_sec  = time.seconds();
}


/*------------------------------------------------------------------------------
 *  Return the current time.
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
TimeConversion :: now(void)
                                                                    throw ()
{
    struct timeval  timeval;

    // TODO: check for -1 return value, to see if there are errors
    gettimeofday(&timeval, 0);
    return timevalToPtime(&timeval);
}


/*------------------------------------------------------------------------------
 *  Sleep for the specified duration.
 *----------------------------------------------------------------------------*/
void
TimeConversion :: sleep(Ptr<time_duration>::Ref duration)
                                                                    throw ()
{
    int                 ret;
    struct timespec     tv;

    tv.tv_sec  = duration->total_seconds();
    tv.tv_nsec = duration->fractional_seconds();

    // if fractional digits is in microseconds, convert it to nanoseconds
    if (time_duration::num_fractional_digits() == 6) {
        tv.tv_nsec *= 1000L;
    }

    if ((ret = nanosleep(&tv, 0))) {
        // TODO: signal error
    }
}


/*------------------------------------------------------------------------------
 *  Convert a time_duration to a format used in SMILs.
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
TimeConversion :: timeDurationToSmilString(
                                Ptr<time_duration>::Ref  duration)
                                                                    throw ()
{
    std::stringstream   stringStream;
    stringStream << std::dec
                 << duration->total_seconds();

    int     microseconds = duration->fractional_seconds();
    stringStream << "."
                 << std::setw(3) 
                 << std::setfill('0')
                 << (microseconds + 500) / 1000
                 << 's';
    Ptr<std::string>::Ref   result(new std::string(stringStream.str()));
    return result;
}


/*------------------------------------------------------------------------------
 *  Convert a time_duration to a rounded format used on the screen.
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
TimeConversion :: timeDurationToHhMmSsString(
                                Ptr<time_duration>::Ref  duration)
                                                                    throw ()
{
    std::stringstream   stringStream;
    stringStream << std::dec
                 << std::setw(2) 
                 << std::setfill('0')
                 << duration->hours()
                 << ":" 
                 << std::setw(2) 
                 << std::setfill('0')
                 << duration->minutes();

    int   seconds = duration->seconds();
    if (duration->fractional_seconds() >= 500000) {
        ++seconds;
    }
    stringStream << ":"
                 << std::setw(2) 
                 << std::setfill('0')
                 << seconds;

    Ptr<std::string>::Ref   result(new std::string(stringStream.str()));
    return result;
}


/*------------------------------------------------------------------------------
 *  Parse a string to a time_duration.
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
TimeConversion :: parseTimeDuration(Ptr<std::string>::Ref     durationString)
                                                                    throw ()
{
    int     micros  = 0;
    int     seconds = 0;
    int     minutes = 0;
    int     hours   = 0;

    Ptr<std::string>::Ref   temp(new std::string(*durationString));

    if (temp->length() > 0) {
        Ptr<std::string>::Ref   secondsString   = nextNumberFromEnd(temp, ':');
        Ptr<std::string>::Ref   fractionsString = nextNumberFromStart(
                                                           secondsString, '.');
        if (fractionsString->length() > 0) {
            std::stringstream       fractionsStream;
            fractionsStream << std::left 
                            << std::setw(
                                TimeConversion::getNumberOfDigitsPrecision() )
                            << std::setfill('0') 
                            << *fractionsString;
            fractionsStream >> micros;
        }
        if (secondsString->length() > 0) {
            std::stringstream       secondsStream(*secondsString);
            secondsStream >> seconds;
        }
    }

    if (temp->length() > 0) {
        Ptr<std::string>::Ref   minutesString = nextNumberFromEnd(temp, ':');
        std::stringstream       minutesStream(*minutesString);
        minutesStream >> minutes;
    }
    
    if (temp->length() > 0) {
        std::stringstream       hoursStream(*temp);
        hoursStream >> hours;
    }

    Ptr<time_duration>::Ref result(new time_duration(
                                            hours, minutes, seconds, micros ));
    return result;
}


/*------------------------------------------------------------------------------
 *  Parse a time string.
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
TimeConversion :: nextNumberFromEnd(Ptr<std::string>::Ref timeString,
                                    char                  separator)
                                                                    throw ()
{
    Ptr<std::string>::Ref   result;
    unsigned int            pos = timeString->find_last_of(separator);
    
    if (pos != std::string::npos) {
        if (pos != timeString->length()-1) {
            result.reset(new std::string(*timeString, pos+1));
        } else {
            result.reset(new std::string);
        }
        *timeString = timeString->substr(0, pos);
    } else {
        result.reset(new std::string(*timeString));
        *timeString = std::string("");
    }
    
    return result;
}


/*------------------------------------------------------------------------------
 *  Parse a decimal string.
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
TimeConversion :: nextNumberFromStart(Ptr<std::string>::Ref timeString,
                                      char                  separator)
                                                                    throw ()
{
    Ptr<std::string>::Ref   result;
    unsigned int            pos = timeString->find(separator);
    
    if (pos != std::string::npos) {
        if (pos != timeString->length()-1) {
            result.reset(new std::string(*timeString, pos+1));
        } else {
            result.reset(new std::string);
        }
        *timeString = timeString->substr(0, pos);
    } else {
        result.reset(new std::string(""));
    }
    
    return result;
}


/*------------------------------------------------------------------------------
 *  Get the number of digits used for fractional seconds in time durations.
 *----------------------------------------------------------------------------*/
int
TimeConversion :: getNumberOfDigitsPrecision(void)                  throw ()
{
    return numberOfDigitsPrecision;
}

