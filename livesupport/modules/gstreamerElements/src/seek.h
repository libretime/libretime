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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/seek.h,v $

------------------------------------------------------------------------------*/
#ifndef Seek_h
#define Seek_h

/**
 *  @file
 *  Utility functions to help seeking in gstreamer elements.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.2 $
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
 *  Seek on an element.
 *
 *  @param element the element to seek on.
 *  @param seekType the type of seek.
 *  @param seekTime the time of seek, in nanoseconds.
 *  @return TRUE if the seek was successful, FALSE otherwise.
 */
gboolean
livesupport_seek(GstElement   * element,
                 GstSeekType    seekType,
                 gint64         seekTime);

/**
 *  Seek a number of seconds on an element.
 *
 *  @param element the element to seek on.
 *  @param seconds the number of seconds to seek.
 *  @return TRUE if the seek was successful, FALSE otherwise.
 */
gboolean
livesupport_seek_seconds(GstElement   * element,
                         gint64         seconds);


#ifdef __cplusplus
} /* extern "C" */
#endif

#endif /* Seek_h */

