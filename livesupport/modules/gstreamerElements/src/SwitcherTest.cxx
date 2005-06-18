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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/SwitcherTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <iostream>

#include <gst/gst.h>

#include "SwitcherTest.h"


using namespace LiveSupport::GstreamerElements;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SwitcherTest);

static const char *         testFile = "var/1minutecounter.mp3";


/* ===============================================  local function prototypes */

/**
 *  Signal handler for the eos event of the switcher element.
 *
 *  @param element the element emitting the eos signal
 *  @param userData pointer to the container bin of the switcher.
 */
static void
eos_signal_handler(GstElement     * element,
                   gpointer         userData);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    GstElement    * pipeline;
    GstElement    * source;
    GstElement    * decoder;
    GstElement    * sw;
    GstElement    * switcher;
    GstElement    * sink;

    /* initialize GStreamer */
    gst_init(0, 0);

    /* create elements */
    pipeline = gst_pipeline_new("audio-player");
    source   = gst_element_factory_make("filesrc", "source");
    decoder  = gst_element_factory_make("mad", "decoder");
    sw       = gst_element_factory_make("switch", "sw");
    switcher = gst_element_factory_make("switcher", "switcher");
    sink     = gst_element_factory_make("alsasink", "alsa-output");

    /* set filename property on the file source */
    g_object_set(G_OBJECT(source), "location", testFile, NULL);
    g_object_set(G_OBJECT(switcher), "source-config", "0[3s]", NULL);
    /* listen for the eos event on switcher, so the pipeline can be stopped */
    g_signal_connect(switcher, "eos", G_CALLBACK(eos_signal_handler), pipeline);

    gst_element_link_many(source, decoder, sw, switcher, sink, NULL);
    gst_bin_add_many(GST_BIN(pipeline),
                     source, decoder, sw, switcher, sink, NULL);

    gst_element_set_state(sink, GST_STATE_READY);
    gst_element_set_state(pipeline, GST_STATE_PLAYING);

    while (gst_bin_iterate(GST_BIN(pipeline)));

    /* clean up nicely */
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(GST_OBJECT (pipeline));
}


/*------------------------------------------------------------------------------
 *  eos signal handler for the switcher element
 *----------------------------------------------------------------------------*/
static void
eos_signal_handler(GstElement     * element,
                   gpointer         userData)
{
    GstElement    * container = GST_ELEMENT(userData);

    g_return_if_fail(container != NULL);
    g_return_if_fail(GST_IS_ELEMENT(container));

    // set the container into eos state
    gst_element_set_eos(container);
}

