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

#include <gst/gst.h>

#include "LiveSupport/GstreamerElements/autoplug.h"


/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */

/**
 *  Program entry point.
 *
 *  @param argc the number of command line arguments.
 *  @param argv the command line argument array.
 *  @return 0 on success, non-0 on failure.
 */
int
main(int        argc,
     char    ** argv);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Program entry point.
 *----------------------------------------------------------------------------*/
int
main(int        argc,
     char    ** argv)
{
    GstElement    * pipeline;
    GstElement    * source;
    GstElement    * decoder;
    GstElement    * sink;
    GstCaps       * caps;
    GstFormat       format;
    gint64          timePlayed;

    /* initialize GStreamer */
    gst_init(&argc, &argv);

    caps = gst_caps_new_simple("audio/x-raw-int",
                               "width", G_TYPE_INT, 16,
                               "depth", G_TYPE_INT, 16,
                               "endianness", G_TYPE_INT, G_BYTE_ORDER,
                               "signed", G_TYPE_BOOLEAN, TRUE,
                               "channels", G_TYPE_INT, 2,
                               "rate", G_TYPE_INT, 44100,
                               NULL);

    if (argc != 2) {
        g_print("Usage: %s <audio filename>\n", argv[0]);
        return -1;
    }

    /* create elements */
    pipeline = gst_pipeline_new("audio-player");
    source   = gst_element_factory_make("filesrc", "source");
    sink     = gst_element_factory_make("alsasink", "alsa-output");

    g_object_set(G_OBJECT(source), "location", argv[1], NULL);

    decoder = ls_gst_autoplug_plug_source(source, "decoder", caps);

    if (!decoder) {
        gst_object_unref(GST_OBJECT(sink));
        gst_object_unref(GST_OBJECT(source));
        gst_object_unref(GST_OBJECT(pipeline));

        return -1;
    }

    gst_element_link(decoder, sink);
    gst_bin_add_many(GST_BIN(pipeline), source, decoder, sink, NULL);

    gst_element_set_state(source, GST_STATE_PAUSED);
    gst_element_set_state(decoder, GST_STATE_PAUSED);
    gst_element_set_state(sink, GST_STATE_PAUSED);
    gst_element_set_state(pipeline, GST_STATE_PLAYING);

    // iterate until playTo is reached
    while (gst_bin_iterate(GST_BIN(pipeline)));

    format = GST_FORMAT_TIME;
    gst_element_query(sink, GST_QUERY_POSITION, &format, &timePlayed);

    g_print("time played: %" G_GINT64_FORMAT " ns\n", timePlayed);

    /* clean up nicely */
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(GST_OBJECT(pipeline));

    return 0;
}

