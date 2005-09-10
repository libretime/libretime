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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/oneshot-reader.h,v $

------------------------------------------------------------------------------*/
#ifndef LivesSupport_GstreamerElements_OneShotReader_h
#define LivesSupport_GstreamerElements_OneShotReader_h

/**
 *  @file
 *  A gstreamer element that reads all input from it's sink pad, and
 *  returns it in one byte array.
 *
 *  @author $Author$
 *  @version $Revision$
 */


/* ============================================================ include files */

#include <gst/gst.h>
#include <gst/bytestream/bytestream.h>


/* ================================================================ constants */


/* =================================================================== macros */

G_BEGIN_DECLS

#define LIVESUPPORT_TYPE_ONE_SHOT_READER            \
    (livesupport_one_shot_reader_get_type())

#define LIVESUPPORT_ONE_SHOT_READER(obj)    \
    (G_TYPE_CHECK_INSTANCE_CAST((obj),      \
     LIVESUPPORT_TYPE_ONE_SHOT_READER,      \
     LivesupportOneShotReader))

#define LIVESUPPORT_ONE_SHOT_READER_CLASS(klass)    \
    (G_TYPE_CHECK_CLASS_CAST((klass),               \
     LIVESUPPORT_TYPE_ONE_SHOT_READER,              \
     LivesupportOneShotReader))

#define GST_IS_ONE_SHOT_READER(obj)             \
    (G_TYPE_CHECK_INSTANCE_TYPE((obj), LIVESUPPORT_TYPE_ONE_SHOT_READER))

#define GST_IS_ONE_SHOT_READER_CLASS(obj) \
    (G_TYPE_CHECK_CLASS_TYPE((klass), LIVESUPPORT_TYPE_ONE_SHOT_READER))


/* =============================================================== data types */

typedef struct _LivesupportOneShotReader LivesupportOneShotReader;
typedef struct _LivesupportOneShotReaderClass LivesupportOneShotReaderClass;

/**
 *  The OneShotReader structure.
 */
struct _LivesupportOneShotReader {
    GstElement      parent;

    GstPad        * sinkpad;

    GstByteStream * bytestream;
    gboolean        processed;
    guint8        * contents;
    guint32         length;
};

/**
 *  The class of the OneShotReader.
 */
struct _LivesupportOneShotReaderClass {
    GstElementClass parent_class;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */

/**
 *  The plugin initialization function.
 *
 *  @param plugin the plugin itself.
 *  @return TRUE if initialization was successful, FALSE otherwise.
 */
static gboolean
plugin_init(GstPlugin * plugin);

/**
 *  Return the appropriate type for the element.
 *
 *  @return the type structure of the element.
 */
GType
livesupport_one_shot_reader_get_type(void);


G_END_DECLS

#endif /* LivesSupport_GstreamerElements_OneShotReader_h */
