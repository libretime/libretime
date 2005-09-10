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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/util.h,v $

------------------------------------------------------------------------------*/
#ifndef Util_h
#define Util_h

/**
 *  @file
 *  Utility functions helping to work with SMIL-related data structures.
 *
 *  @author $Author$
 *  @version $Revision$
 */

#ifdef __cplusplus
extern "C" {
#endif


/* ============================================================ include files */

#include <gst/gst.h>


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */


/* ====================================================== function prototypes */

/**
 *  Return the first src pad for an element.
 *
 *  @param element the element to return the pad from.
 *  @return the first src pad for element, or NULL.
 */
GstPad *
get_src_pad(GstElement    * element);


#ifdef __cplusplus
} /* extern "C" */
#endif

#endif /* Util_h */

