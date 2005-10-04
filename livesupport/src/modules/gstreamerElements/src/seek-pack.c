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

#ifdef HAVE_STRING_H
#include <string.h>
#else
#error need string.h
#endif


#include <gst/gst.h>

#include "LiveSupport/GstreamerElements/autoplug.h"
#include "util.h"
#include "seek.h"
#include "seek-pack.h"


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
 *  Signal handler for the eos event of the switcher element.
 *
 *  @param element the element emitting the eos signal
 *  @param userData pointer to the container bin of the switcher.
 */
static void
switcher_eos_signal_handler(GstElement     * element,
                            gpointer         userData);

/**
 *  Perform the seeks on the SeekPack, set by the initialization function.
 *
 *  @param seekPack the SeekPack to perform the seek on.
 *  @see #livesupport_seek_pack_init
 */
static void
livesupport_seek_pack_seek(LivesupportSeekPack    * seekPack);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  eos signal handler for the switcher element
 *----------------------------------------------------------------------------*/
static void
switcher_eos_signal_handler(GstElement     * element,
                            gpointer         userData)
{
    GstElement    * container = GST_ELEMENT(userData);

    g_return_if_fail(container != NULL);
    g_return_if_fail(GST_IS_ELEMENT(container));

    /* set the container into eos state */
    GST_DEBUG("SeekPack.switcher setting SeekPack.bin to eos");
    gst_element_set_eos(container);
}


/*------------------------------------------------------------------------------
 *  Create a new SeekPack.
 *----------------------------------------------------------------------------*/
LivesupportSeekPack *
livesupport_seek_pack_new(const gchar    * uniqueName,
                          const GstCaps  * caps)
{
    unsigned int            len      = strlen(uniqueName) + 64;
    gchar                 * str      = g_malloc(len);
    LivesupportSeekPack   * seekPack = g_malloc(sizeof(LivesupportSeekPack));
    GValue                  gvalue   = { 0 };

    seekPack->name      = g_strdup(uniqueName);

    seekPack->caps      = gst_caps_copy(caps);

    g_snprintf(str, len, "%s_seekPackSilence", uniqueName);
    seekPack->silence   = gst_element_factory_make("silence", str);

    seekPack->source    = NULL;

    /* generate decoder later, by autoplugging */
    seekPack->decoder        = 0;
    seekPack->decoderScale   = 0;
    g_snprintf(str, len, "%s_seekPackSwitcher", uniqueName);
    seekPack->switcher = gst_element_factory_make("switcher", str);
    g_snprintf(str, len, "%s_seekPackBin", uniqueName);
    seekPack->bin       = gst_bin_new(str);
    g_free(str);

    g_value_init(&gvalue, G_TYPE_POINTER);
    g_value_set_pointer(&gvalue, seekPack->caps);
    gst_element_set_property(seekPack->switcher, "caps", &gvalue);
    g_value_unset(&gvalue);

    g_signal_connect(seekPack->switcher,
                     "eos",
                     G_CALLBACK(switcher_eos_signal_handler),
                     seekPack->bin);

    seekPack->silenceDuration   = 0LL;
    seekPack->startTime         = 0LL;
    seekPack->endTime           = 0LL;
    seekPack->duration          = 0LL;
    seekPack->positionAfterSeek = 0LL;
    seekPack->realEndTime       = 0LL;
    
    seekPack->sendingSilence = TRUE;

    gst_element_add_ghost_pad(seekPack->bin,
                              gst_element_get_pad(seekPack->switcher, "src"),
                              "src");

    return seekPack;
}


/*------------------------------------------------------------------------------
 *  Initialize a SeekPack.
 *----------------------------------------------------------------------------*/
void
livesupport_seek_pack_init(LivesupportSeekPack    * seekPack,
                           GstElement             * source,
                           gint64                   silenceDuration,
                           gint64                   startTime,
                           gint64                   endTime)
{
    GValue          gvalue = { 0 };
    gchar           str[256];
    unsigned int    len      = strlen(seekPack->name) + 64;
    gchar         * name     = g_malloc(len);

    seekPack->source            = source;

    seekPack->silenceDuration   = silenceDuration;
    seekPack->startTime         = startTime;
    seekPack->endTime           = endTime;
    seekPack->duration          = endTime - startTime;
    seekPack->positionAfterSeek = 0LL;
    seekPack->realEndTime       = 0LL;

    g_value_init(&gvalue, G_TYPE_STRING);
    if (seekPack->endTime >= 0) {
        g_snprintf(str, 256, "0[%lfs];1[%lfs]",
                             seekPack->silenceDuration / NSEC_PER_SEC_FLOAT,
                             seekPack->duration / NSEC_PER_SEC_FLOAT);
    } else {
        g_snprintf(str, 256, "0[%lfs];1[]",
                             seekPack->silenceDuration / NSEC_PER_SEC_FLOAT);
    }
    g_value_set_string(&gvalue, str);
    gst_element_set_property(seekPack->switcher, "source-config", &gvalue);
    g_value_unset(&gvalue);

    g_snprintf(name, len, "%s_seekPackDecoder", seekPack->name);
    seekPack->decoder = ls_gst_autoplug_plug_source(seekPack->source,
                                                    name,
                                                    seekPack->caps);
    /* TODO: only add scale element if needed */
    g_snprintf(name, len, "%s_seekPackDecoderScale", seekPack->name);
    seekPack->decoderScale = gst_element_factory_make("audioscale", name);
    g_free(name);

    /* link up the silence element with the switcher first */
    gst_element_link_filtered(seekPack->silence,
                              seekPack->switcher,
                              seekPack->caps);

    if (seekPack->decoder) {
        /* seek on the decoder, and link it up with the switcher */
        gst_element_link(seekPack->source, seekPack->decoder);
        livesupport_seek_pack_seek(seekPack);
    } else {
        /* just fake the content with silence,
         * if it could not be auto-plugged */
        seekPack->decoder = gst_element_factory_make("silence", "decoder");
    }
    gst_element_link_many(seekPack->decoder,
                          seekPack->decoderScale,
                          NULL);
    gst_element_link_filtered(seekPack->decoderScale,
                              seekPack->switcher,
                              seekPack->caps);

    /* put all inside the bin, and link up a ghost pad to switch's src pad */
    gst_bin_add_many(GST_BIN(seekPack->bin),
                     seekPack->silence,
                     seekPack->source,
                     seekPack->decoder,
                     seekPack->decoderScale,
                     NULL);

    /* put the switcher last into the bin, and also link it as last
     * otherwise we'll get a:
     * "assertion failed: (group->group_links == NULL)"
     * error later on when trying to free up the pipeline
     * see http://bugzilla.gnome.org/show_bug.cgi?id=309122
     */
    gst_bin_add(GST_BIN(seekPack->bin), seekPack->switcher);
}


/*------------------------------------------------------------------------------
 *  Destroy a SeekPack.
 *----------------------------------------------------------------------------*/
void
livesupport_seek_pack_destroy(LivesupportSeekPack     * seekPack)
{
    gst_element_set_state(seekPack->bin, GST_STATE_NULL);
    g_object_unref(seekPack->bin);
    gst_caps_free(seekPack->caps);
    g_free(seekPack->name);
    g_free(seekPack);
}


/*------------------------------------------------------------------------------
 *  Link a SeekPack to another element.
 *----------------------------------------------------------------------------*/
gboolean
livesupport_seek_pack_link(LivesupportSeekPack    * seekPack,
                           GstElement             * element)
{
    return gst_element_link_filtered(seekPack->bin, element, seekPack->caps);
}


/*------------------------------------------------------------------------------
 *  Add a SeekPack to a bin.
 *----------------------------------------------------------------------------*/
void
livesupport_seek_pack_add_to_bin(LivesupportSeekPack      * seekPack,
                                 GstBin                   * bin)
{
    /* put an extra ref on our elements, as the bin will decrease the
     * ref when they are removed from there */
    g_object_ref(seekPack->bin);
    gst_bin_add(bin, seekPack->bin);
}


/*------------------------------------------------------------------------------
 *  Remove a SeekPack from a bin.
 *----------------------------------------------------------------------------*/
void
livesupport_seek_pack_remove_from_bin(LivesupportSeekPack     * seekPack,
                                      GstBin                  * bin)
{
    gst_bin_remove(bin, seekPack->bin);
}


/*------------------------------------------------------------------------------
 *  Set the state of a SeekPack.
 *----------------------------------------------------------------------------*/
void
livesupport_seek_pack_set_state(LivesupportSeekPack   * seekPack,
                                GstElementState         state)
{
    /* FIXME: resetting the source from PLAYING state would make it lose
     *        it's seek position */
    if (seekPack->silence) {
        gst_element_set_state(seekPack->silence, state);
    }
    if (seekPack->decoder) {
        gst_element_set_state(seekPack->decoder, state);
    }
    if (seekPack->decoderScale) {
        gst_element_set_state(seekPack->decoderScale, state);
    }
    if (seekPack->switcher) {
        gst_element_set_state(seekPack->switcher, state);
    }
    if (seekPack->bin) {
        gst_element_set_state(seekPack->bin, state);
    }
}


/*------------------------------------------------------------------------------
 *  Do the seeking on a SeekPack.
 *----------------------------------------------------------------------------*/
static void
livesupport_seek_pack_seek(LivesupportSeekPack    * seekPack)
{
    GstElement    * pipeline;
    GstElement    * fakesink;
    gboolean        ret;
    gint64          value;
    GstSeekType     seekType;

    seekType = (GstSeekType) (GST_FORMAT_TIME |
                              GST_SEEK_METHOD_SET |
                              GST_SEEK_FLAG_FLUSH);
    pipeline = gst_pipeline_new("seek_pipeline");
    fakesink = gst_element_factory_make("fakesink", "seek_fakesink");

    gst_element_link_filtered(seekPack->decoder, fakesink, seekPack->caps);
    /* ref the objects we want to keep after pipeline, as it will unref them */
    g_object_ref(seekPack->source);
    g_object_ref(seekPack->decoder);
    gst_bin_add_many(GST_BIN(pipeline),
                     seekPack->source,
                     seekPack->decoder,
                     fakesink,
                     NULL);

    GST_DEBUG("setting seek pipeline to PLAYING state");
    gst_element_set_state(seekPack->decoder, GST_STATE_PAUSED);
    gst_element_set_state(fakesink, GST_STATE_PAUSED);
    gst_element_set_state(pipeline, GST_STATE_PLAYING);

    GST_DEBUG("starting to iterate...");
    for (value = 0; value == 0 && gst_bin_iterate(GST_BIN(pipeline)); ) {
        GstFormat   format = GST_FORMAT_DEFAULT;
        gst_element_query(fakesink, GST_QUERY_POSITION, &format, &value);
        GST_DEBUG("position value: %" G_GINT64_FORMAT, value);
    }
    GST_DEBUG("seeking on element");
    ret = livesupport_seek(seekPack->decoder, seekType, seekPack->startTime);
    GST_DEBUG("seek result: %d", ret);

    gst_bin_remove_many(GST_BIN(pipeline),
                        seekPack->source,
                        seekPack->decoder,
                        NULL);
    gst_element_unlink(seekPack->decoder, fakesink);
    gst_object_unref(GST_OBJECT(pipeline));
}

