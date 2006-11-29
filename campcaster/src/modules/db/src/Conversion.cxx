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
Conversion :: ptimeToTimestamp(Ptr<const posix_time::ptime>::Ref ptime,
                               RoundingType                      round)
                                                                    throw ()
{
    posix_time::ptime   newPtime    = *ptime;
    if (round == roundUp && newPtime.time_of_day().fractional_seconds() != 0) {
        newPtime += posix_time::seconds(1);
    } else if (round == roundNearest) {
        newPtime += posix_time::microseconds(500000);
    }
    
    gregorian::date           date  = newPtime.date();
    posix_time::time_duration time  = newPtime.time_of_day();

    Ptr<odbc::Timestamp>::Ref   timestamp(new odbc::Timestamp(date.year(),
                                                              date.month(),
                                                              date.day(),
                                                              time.hours(),
                                                              time.minutes(),
                                                              time.seconds()));
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

