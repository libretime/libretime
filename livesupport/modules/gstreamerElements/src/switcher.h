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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/switcher.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_GstreamerElements_Switcher_h
#define LiveSupport_GstreamerElements_Switcher_h

/**
 *  @file
 *  A gstreamer element that expects to be linked after a switch
 *  element. The element will switch the active source on the attached
 *  switcher according to the time positions it is configured with.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.1 $
 */


/* ============================================================ include files */

#include <gst/gst.h>


/* ================================================================ constants */


/* =================================================================== macros */


G_BEGIN_DECLS

#define LIVESUPPORT_TYPE_SWITCHER   \
    (livesupport_switcher_get_type())

#define LIVESUPPORT_SWITCHER(obj)           \
    (G_TYPE_CHECK_INSTANCE_CAST((obj),      \
     LIVESUPPORT_TYPE_SWITCHER,             \
     LivesupportSwitcher))

#define LIVESUPPORT_SWITCHER_CLASS(klass)   \
    (G_TYPE_CHECK_CLASS_CAST((klass),       \
     LIVESUPPORT_TYPE_SWITCHER,             \
     LivesupportSwitcher))

#define LIVESUPPORT_IS_SWITCHER(obj)        \
    (G_TYPE_CHECK_INSTANCE_TYPE((obj),      \
     LIVESUPPORT_TYPE_SWITCHER))

#define LIVESUPPORT_IS_SWITCHER_CLASS(obj)  \
    (G_TYPE_CHECK_CLASS_TYPE((klass),       \
     LIVESUPPORT_TYPE_SWITCHER))


/* =============================================================== data types */

typedef struct _LivesupportSwitcherSourceConfig LivesupportSwitcherSourceConfig;
typedef struct _LivesupportSwitcher      LivesupportSwitcher;
typedef struct _LivesupportSwitcherClass LivesupportSwitcherClass;

/**
 *  A source configuration, describing how long a source should be played
 *  before switching to the next.
 */
struct _LivesupportSwitcherSourceConfig {
    gint        sourceId;
    gint64      duration;
};

/**
 *  The Switcher object.
 */
struct _LivesupportSwitcher
{
    GstElement      element;

    GstPad        * sinkpad;
    GstPad        * srcpad;

    gint64          elapsedTime;
    gint64          nextOffset;
    gboolean        eos;

    gchar         * sourceConfig;
    GList         * sourceConfigList;
    GList         * currentConfig;
};

/**
 *  The Switcher class.
 */
struct _LivesupportSwitcherClass 
{
    GstElementClass     parent_class;
};


/* ====================================================== function prototypes */

/**
 *  Return the type structure for the Switcher class.
 *
 *  @return the type structure for the Switcher class.
 */
GType
livesupport_switcher_get_type(void);

/**
 *  Initialize the plugin.
 *
 *  @param plugin the plugin to initialize.
 */
static gboolean
plugin_init(GstPlugin     * plugin);


G_END_DECLS

#endif /* LiveSupport_GstreamerElements_Switcher_h */

