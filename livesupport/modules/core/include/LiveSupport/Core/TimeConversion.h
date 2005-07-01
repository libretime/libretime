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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/TimeConversion.h,v $

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
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.8 $
 */
class TimeConversion
{
    private:
        /**
         *  The default constructor.
         */
        TimeConversion(void)                            throw ()
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
        ptimeToTm(Ptr<ptime>::Ref convertFrom, struct tm & convertTo)
                                                                throw ();

        /**
         *  Return the current time, with microsecond precision.
         *
         *  @return the current time, with microsecond precision.
         */
        static Ptr<ptime>::Ref
        now(void)                                               throw ();

        /**
         *  Sleep for the specified time duration, with microsecond precision.
         *
         *  @param duration sleep for this duration.
         */
        static void
        sleep(Ptr<time_duration>::Ref   duration)               throw ();

        /**
         *  Convert a time_duration to a format used in SMILs.
         *
         *  @param duration sleep for this duration.
         */
        static Ptr<std::string>::Ref
        timeDurationToSmilString(Ptr<time_duration>::Ref   duration)
                                                                throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_TimeConversion_h

