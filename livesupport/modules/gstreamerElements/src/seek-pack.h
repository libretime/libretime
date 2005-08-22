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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/seek-pack.h,v $

------------------------------------------------------------------------------*/
#ifndef SeekPack_h
#define SeekPack_h

/**
 *  @file
 *  A SeekPack - a structure that plays a gstreamer source by playing
 *  some silence and then some specified part of the source.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.7 $
 */

#ifdef __cplusplus
extern "C" {
#endif


/* ============================================================ include files */

#include <gst/gst.h>

#include "seek.h"


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

typedef struct _LivesupportSeekPack LivesupportSeekPack;

/**
 *  A SeekPack structure.
 */
struct _LivesupportSeekPack {
    gchar         * name;

    GstElement    * silence;
    GstElement    * silenceConvert;

    GstElement    * source;
    GstElement    * decoder;
    GstElement    * decoderConvert;
    GstElement    * decoderScale;

    GstElement    * switcher;
    GstElement    * bin;

    gint64          silenceDuration;
    gint64          startTime;
    gint64          endTime;
    gint64          duration;
    gint64          positionAfterSeek;
    gint64          realEndTime;

    gboolean        sendingSilence;
};


/* ====================================================== function prototypes */

/**
 *  Create a new SeekPack.
 *  Initialize the SeekPack before using it, and destroy after it's not
 *  needed anymore.
 *
 *  @param uniqueName a name unique in the SeekPack's context.
 *  @return a new SeekPack.
 *  @see #livesupport_seek_pack_init
 *  @see #livesupport_seek_pack_destroy
 */
LivesupportSeekPack *
livesupport_seek_pack_new(const gchar    * uniqueName);

/**
 *  Initialize a SeekPack.
 *
 *  @param seekPack the SeekPack to initialize.
 *  @param source the source the SeekPack will play.
 *  @param caps the desired capabilites on the src of the SeekPack
 *  @param silenceDuration the number of nanoseconds the SeekPack will
 *         play only silence in the beginning.
 *  @param startTime the offset at which source will start to play after
 *         the silence.
 *  @param endTime the offset until which source will play.
 */
void
livesupport_seek_pack_init(LivesupportSeekPack    * seekPack,
                           GstElement             * source,
                           const GstCaps          * caps,
                           gint64                   silenceDuration,
                           gint64                   startTime,
                           gint64                   endTime);

/**
 *  Destory a SeekPack.
 *
 *  @param seekPack the SeekPack to destroy.
 */
void
livesupport_seek_pack_destroy(LivesupportSeekPack     * seekPack);

/**
 *  Link a SeekPack to an element.
 *
 *  @param seekPack the SeekPack to link.
 *  @param element the element to link to.
 *  @return TRUE if linking was successful, FALSE otherwise.
 */
gboolean
livesupport_seek_pack_link(LivesupportSeekPack    * seekPack,
                           GstElement             * element);

/**
 *  Add a SeekPack to a bin.
 *
 *  @param seekPack the SeekPack to add.
 *  @param bin the bin to add to.
 */
void
livesupport_seek_pack_add_to_bin(LivesupportSeekPack      * seekPack,
                                 GstBin                   * bin);

/**
 *  Remove a SeekPack from a bin.
 *  
 *  @param seekPack the SeekPack to remove.
 *  @param bin the bin to remove from.
 */
void
livesupport_seek_pack_remove_from_bin(LivesupportSeekPack     * seekPack,
                                      GstBin                  * bin);

/**
 *  Set the state of a SeekPack.
 *
 *  @param seekPack the SeekPack to set the state for.
 *  @param state the new state of the SeekPack.
 */
void
livesupport_seek_pack_set_state(LivesupportSeekPack   * seekPack,
                                GstElementState         state);



#ifdef __cplusplus
} /* extern "C" */
#endif

#endif /* SeekPack_h */

