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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/include/LiveSupport/GstreamerElements/autoplug.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_GstreamerElements_Autoplug_h
#define LiveSupport_GstreamerElements_Autoplug_h

/**
 *  @file
 *  Functions for autoplugging gstreamer elements based on their MIME types.
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
 *  Autoplug a source element, that contains some form of audio.
 *  The result will be a gstreamer element, that is linked with
 *  source, and produces raw audio on its src pad as it output.
 *
 *  @param source the source to autoplug.
 *  @param name the name of the new element.
 *  @param caps the capabilities expected from the returned element,
 *         on its src pad.
 *  @return a gstreamer element already linked to source, that produces
 *          the audio provided by source in audio/x-raw-int or
 *          audio/x-raw-float format, as needed.
 */
GstElement *
ls_gst_autoplug_plug_source(GstElement        * source,
                            const gchar       * name,
                            const GstCaps     * caps);


#ifdef __cplusplus
} /* extern "C" */
#endif

#endif /* LiveSupport_GstreamerElements_Autoplug_h */

