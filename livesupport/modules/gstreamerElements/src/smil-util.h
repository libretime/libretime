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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/smil-util.h,v $

------------------------------------------------------------------------------*/
#ifndef SmilUtil_h
#define SmilUtil_h

/**
 *  @file
 *  Utility functions helping to work with SMIL-related data structures.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.2 $
 */


/* ============================================================ include files */

#include <gst/gst.h>


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */


/* ====================================================== function prototypes */

/**
 *  Parse the clock value according to the SMIL clock spec
 *
 *  see http://www.w3.org/TR/2005/REC-SMIL2-20050107/smil-timing.html#Timing-ClockValueSyntax
 *
 *  the BNF for the value is:
 *  
 *  <pre><code>
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
 *  </code></pre>
 *
 *  @param value the SMIL clock value in string form
 *  @return the clock value in nanoseconds
 */
gint64
smil_clock_value_to_nanosec(const gchar    * value);


/**
 *  Parse a string as a percentage value, and return the result as a
 *  float. Indicate parse error.
 *
 *  @param str the string to parse.
 *  @param value the parsed value (out parameter).
 *  @return TRUE if parsing went OK, FALSE otherwise.
 */
gboolean
smil_parse_percent(const gchar    * str,
                   double         * value);


#endif /* SmilUtil_h */

