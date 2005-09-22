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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "smil-util.h"


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

#define NSEC_PER_SEC        1000000000LL
#define SEC_PER_MIN         60
#define SEC_PER_HOUR        3600
#define NSEC_PER_SEC_FLOAT  1000000000.0
#define SEC_PER_MIN_FLOAT   60.0
#define SEC_PER_HOUR_FLOAT  3600.0


/* ===============================================  local function prototypes */

/**
 *  Convert an hour - minute - second triplet into a nanosecond value.
 *
 *  @param hours the number of hours
 *  @param minutes the number of minutes (may be more than 59)
 *  @param seconds the number of seconds (may be mora than 59)
 *  @return the supplied time in nanoseconds
 */
static gint64
hms_to_nanosec(int      hours,
               int      minutes,
               double   seconds);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Convert a hour-minute-second triplet to nanoseconds
 *----------------------------------------------------------------------------*/
static gint64
hms_to_nanosec(int      hours,
               int      minutes,
               double   seconds)
{
    gint64  nanosec;
    double  nsec;

    nsec = seconds * NSEC_PER_SEC_FLOAT;

    nanosec  = (gint64) nsec;
    nanosec += ((gint64) hours) * NSEC_PER_SEC;
    nanosec += ((gint64) minutes) * SEC_PER_MIN * NSEC_PER_SEC;

    return nanosec;
}


/*------------------------------------------------------------------------------
 *  Parse the clock value according to the SMIL clock spec
 *
 *  see http://www.w3.org/TR/2005/REC-SMIL2-20050107/smil-timing.html#Timing-ClockValueSyntax
 *
 *  the BNF for the value is:
 *  
 *  Clock-value         ::= ( Full-clock-value | Partial-clock-value
 *                          | Timecount-value )
 *  Full-clock-value    ::= Hours ":" Minutes ":" Seconds ("." Fraction)?
 *  Partial-clock-value ::= Minutes ":" Seconds ("." Fraction)?
 *  Timecount-value     ::= Timecount ("." Fraction)? (Metric)?
 *  Metric              ::= "h" | "min" | "s" | "ms"
 *  Hours               ::= DIGIT+; any positive number
 *  Minutes             ::= 2DIGIT; range from 00 to 59
 *  Seconds             ::= 2DIGIT; range from 00 to 59
 *  Fraction            ::= DIGIT+
 *  Timecount           ::= DIGIT+
 *  2DIGIT              ::= DIGIT DIGIT
 *  DIGIT               ::= [0-9]
 *----------------------------------------------------------------------------*/
gint64
smil_clock_value_to_nanosec(const gchar    * value)
{
    int     hours;
    int     minutes;
    double  seconds;

    /* see if it's a full-clock-value */
    if (sscanf(value, "%2d:%2d:%lf", &hours, &minutes, &seconds) == 3) {
        return hms_to_nanosec(hours, minutes, seconds);
    }

    /* see if it's a partial-clock-value */
    if (sscanf(value, "%2d:%lf", &minutes, &seconds) == 2) {
        return hms_to_nanosec(0, minutes, seconds);
    }

    /* see if it's a timecount-value, in hours */
    if (g_str_has_suffix(value, "h")
     && sscanf(value, "%lfh", &seconds) == 1) {
        return hms_to_nanosec(0, 0, seconds * SEC_PER_HOUR_FLOAT);
    }

    /* see if it's a timecount-value, in minutes */
    if (g_str_has_suffix(value, "min")
     && sscanf(value, "%lfmin", &seconds) == 1) {
        return hms_to_nanosec(0, 0, seconds * SEC_PER_MIN_FLOAT);
    }

    /* see if it's a timecount-value, in millisecs */
    if (g_str_has_suffix(value, "ms")
     && sscanf(value, "%lfms", &seconds) == 1) {
        return hms_to_nanosec(0, 0, seconds / 100.0);
    }

    /* it's a timecount-value, either with no metric, or explicit seconds */
    if (sscanf(value, "%lfs", &seconds) == 1) {
        return hms_to_nanosec(0, 0, seconds);
    }

    return -1LL;
}


/*------------------------------------------------------------------------------
 *  Convert a percent value to a double.
 *----------------------------------------------------------------------------*/
gboolean
smil_parse_percent(const gchar    * str,
                   double         * value)
{
    double  val;

    if (g_str_has_suffix(str, "%")
     && sscanf(str, "%lf%%", &val) == 1) {
        *value = val / 100.0;
        return TRUE;
    }

    return FALSE;
}

