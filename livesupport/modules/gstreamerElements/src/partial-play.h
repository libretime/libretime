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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/partial-play.h,v $

------------------------------------------------------------------------------*/
#ifndef LivesSupport_GstreamerElements_PartialPlay_h
#define LivesSupport_GstreamerElements_PartialPlay_h

/**
 *  @file
 *  A gstreamer element that plays its source partially, first by playing
 *  some specified silence, then playing the source from a specified
 *  offset until a specified offset.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.2 $
 */


/* ============================================================ include files */

#include <gst/gst.h>

#include "seek-pack.h"

/* ================================================================ constants */


/* =================================================================== macros */

G_BEGIN_DECLS

#define LIVESUPPORT_TYPE_PARTIAL_PLAY       \
    (livesupport_partial_play_get_type())

#define LIVESUPPORT_PARTIAL_PLAY(obj)       \
    (G_TYPE_CHECK_INSTANCE_CAST((obj),      \
     LIVESUPPORT_TYPE_PARTIAL_PLAY,         \
     LivesupportPartialPlay))

#define LIVESUPPORT_PARTIAL_PLAY_CLASS(klass)   \
    (G_TYPE_CHECK_CLASS_CAST((klass),           \
    LIVESUPPORT_TYPE_PARTIAL_PLAY,              \
    LivesupportPartialPlay))

#define LIVESUPPORT_IS_PARTIAL_PLAY(obj)    \
    (G_TYPE_CHECK_INSTANCE_TYPE((obj),      \
    LIVESUPPORT_TYPE_PARTIAL_PLAY))

#define LIVESUPPORT_IS_PARTIAL_PLAY_CLASS(obj)  \
    (G_TYPE_CHECK_CLASS_TYPE((klass),LIVESUPPORT_TYPE_PARTIAL_PLAY))


/* =============================================================== data types */

typedef struct _LivesupportPartialPlay      LivesupportPartialPlay;
typedef struct _LivesupportPartialPlayClass LivesupportPartialPlayClass;

/**
 *  The PartialPlay structure.
 */
struct _LivesupportPartialPlay
{
    GstBin                  parent;

    GstCaps               * caps;

    GstPad                * srcpad;
    GstElement            * source;
    LivesupportSeekPack   * seekPack;
    gboolean                seekPackInited;
    
    gchar                 * location;
    gchar                 * config;
    gint64                  silenceDuration;
    gint64                  playFrom;
    gint64                  playTo;
};

/**
 *  The PartialPlay class.
 */
struct _LivesupportPartialPlayClass 
{
    GstBinClass     parent_class;
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */

/**
 *  Initilize (register, etc.) the plugin.
 *
 *  @param plugin the plugin itself.
 *  @return TRUE if initialization was successful, FALSE otherwise.
 */
static gboolean
plugin_init(GstPlugin     * plugin);

/**
 *  Return the appropriate type for the element.
 *
 *  @return the type structure of the element.
 */
GType livesupport_partial_play_get_type(void);


G_END_DECLS

#endif /* LivesSupport_GstreamerElements_PartialPlay_h */

