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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/db/src/Conversion.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Db/Conversion.h"


using namespace boost;

using namespace LiveSupport::Core;
using namespace LiveSupport::Db;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Convert a boost::ptime to an odbc::Timestamp
 *----------------------------------------------------------------------------*/
Ptr<odbc::Timestamp>::Ref
Conversion :: ptimeToTimestamp(Ptr<const posix_time::ptime>::Ref ptime)
                                                                    throw ()
{
    gregorian::date           date  = ptime->date();
    posix_time::time_duration hours = ptime->time_of_day();

    Ptr<odbc::Timestamp>::Ref   timestamp(new odbc::Timestamp(date.year(),
                                                              date.month(),
                                                              date.day(),
                                                              hours.hours(),
                                                              hours.minutes(),
                                                              hours.seconds()));
    return timestamp;
}


/*------------------------------------------------------------------------------
 *  Convert an odbc::Timestamp to a boost::ptime
 *----------------------------------------------------------------------------*/
Ptr<posix_time::ptime>::Ref
Conversion :: timestampToPtime(Ptr<odbc::Timestamp>::Ref   timestamp)
                                                                    throw()
{
    // don't convert through the time_t format, as probably because of
    // timezone settings, boost::posix_time::from_time_t() ruins the
    // actual value
    std::string                     timeStr = timestamp->toString();
    Ptr<posix_time::ptime>::Ref     ptime(new posix_time::ptime(
                                        posix_time::time_from_string(timeStr)));
    return ptime;
}

