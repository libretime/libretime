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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/minimal-audio-smil.h,v $

------------------------------------------------------------------------------*/
#ifndef LivesSupport_GstreamerElements_MinimalAudioSmil_h
#define LivesSupport_GstreamerElements_MinimalAudioSmil_h

/**
 *  @file
 *  A gstreamer element that plays SMIL files referencing audio content.
 *  Only a small subset of SMIL is supported.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.1 $
 *  @see http://www.w3.org/TR/SMIL2/
 */

/* ============================================================ include files */

#include <gst/gst.h>


/* =================================================================== macros */

G_BEGIN_DECLS

#define LIVESUPPORT_TYPE_MINIMAL_AUDIO_SMIL         \
    (livesupport_minimal_audio_smil_get_type())

#define LIVESUPPORT_MINIMAL_AUDIO_SMIL(obj) \
    (G_TYPE_CHECK_INSTANCE_CAST((obj),      \
     LIVESUPPORT_TYPE_MINIMAL_AUDIO_SMIL,   \
     LivesupportMinimalAudioSmil))

#define LIVESUPPORT_MINIMAL_AUDIO_SMIL_CLASS(klass) \
    (G_TYPE_CHECK_CLASS_CAST((klass),               \
     LIVESUPPORT_TYPE_MINIMAL_AUDIO_SMIL,           \
     LivesupportMinimalAudioSmil))

#define LIVESUPPORT_IS_MINIMAL_AUDIO_SMIL(obj)  \
    (G_TYPE_CHECK_INSTANCE_TYPE((obj),          \
     LIVESUPPORT_TYPE_MINIMAL_AUDIO_SMIL))

#define LIVESUPPORT_IS_MINIMAL_AUDIO_SMIL_CLASS(obj)    \
    (G_TYPE_CHECK_CLASS_TYPE((klass),                   \
     LIVESUPPORT_TYPE_MINIMAL_AUDIO_SMIL))


/* =============================================================== data types */

typedef struct _LivesupportMinimalAudioSmil LivesupportMinimalAudioSmil;
typedef struct _LivesupportMinimalAudioSmilClass
                                            LivesupportMinimalAudioSmilClass;

/**
 *  The MinimalAudioSmil object structure.
 */
struct _LivesupportMinimalAudioSmil {
    GstBin          parent;

    GstPad        * sinkpad;
    GstPad        * srcpad;

    GstElement    * oneshotReader;
    GstPad        * oneshotReaderSink;
    gboolean        fileProcessed;

    GstBin        * bin;
    GstElement    * finalAdder;
};

/**
 *  The MinimalAudioSmil class.
 */
struct _LivesupportMinimalAudioSmilClass {
    GstBinClass     parent_class;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */

/**
 *  Return the appropriate type for the element.
 *
 *  @return the type structure of the element.
 */
GType
livesupport_minimal_audio_smil_get_type(void);

/**
 *  The plugin initialization function.
 *
 *  @param plugin the plugin itself.
 *  @return TRUE if initialization was successful, FALSE otherwise.
 */
static gboolean
plugin_init (GstPlugin * plugin);


G_END_DECLS

#endif /* LivesSupport_GstreamerElements_MinimalAudioSmil_h */

