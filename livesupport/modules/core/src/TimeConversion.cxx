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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/TimeConversion.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TimeConversion.h"


using namespace boost::posix_time;
using namespace boost::gregorian;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Convert a struct timeval to a boost::ptime
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
TimeConversion :: timevalToPtime(const struct timeval *timeval)
                                                                    throw ()
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

