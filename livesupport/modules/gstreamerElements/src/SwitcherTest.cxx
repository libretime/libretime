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
    Version  : $Revision: 1.3 $
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

static const char *         testFile = "var/5seccounter.mp3";


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
 *  Play an audio file
 *----------------------------------------------------------------------------*/
gint64
SwitcherTest :: playFiles(const char     ** audioFiles,
                          unsigned int      noFiles,
                          const char      * sourceConfig)
                                                throw (CPPUNIT_NS::Exception)
{
    GstElement    * pipeline;
    GstElement    * sw;
    GstElement    * switcher;
    GstElement    * sink;
    unsigned int    i;
    GstFormat       format;
    gint64          timePlayed;

    /* initialize GStreamer */
    gst_init(0, 0);

    /* create elements */
    pipeline = gst_pipeline_new("audio-player");
    sw       = gst_element_factory_make("switch", "sw");
    switcher = gst_element_factory_make("switcher", "switcher");
    sink     = gst_element_factory_make("alsasink", "alsa-output");

    gst_element_link_many(sw, switcher, sink, NULL);
    gst_bin_add_many(GST_BIN(pipeline), sw, switcher, sink, NULL);

    for (i = 0; i < noFiles; ++i) {
        GstElement    * source;
        GstElement    * decoder;
        char            str[256];
        gboolean        ret;

        g_snprintf(str, 256, "source_%d", i);
        source   = gst_element_factory_make("filesrc", str);
        CPPUNIT_ASSERT(source);

        g_snprintf(str, 256, "decoder_%d", i);
        decoder  = gst_element_factory_make("mad", str);
        CPPUNIT_ASSERT(decoder);

        g_object_set(G_OBJECT(source), "location", audioFiles[i], NULL);

        ret = gst_element_link_many(source, decoder, sw, NULL);
        CPPUNIT_ASSERT(ret);
        gst_bin_add_many(GST_BIN(pipeline), source, decoder, NULL);
    }

    g_object_set(G_OBJECT(switcher), "source-config", sourceConfig, NULL);
    /* listen for the eos event on switcher, so the pipeline can be stopped */
    g_signal_connect(switcher, "eos", G_CALLBACK(eos_signal_handler), pipeline);

    gst_element_set_state(sink, GST_STATE_READY);
    gst_element_set_state(pipeline, GST_STATE_PLAYING);

    while (gst_bin_iterate(GST_BIN(pipeline)));

    format = GST_FORMAT_TIME;
    gst_element_query(sink, GST_QUERY_POSITION, &format, &timePlayed);

    /* clean up nicely */
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(GST_OBJECT (pipeline));

    return timePlayed;
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


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playFiles(&testFile, 1, "0[3s]");
    CPPUNIT_ASSERT(timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 3.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: openEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playFiles(&testFile, 1, "0[]");
    CPPUNIT_ASSERT(timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: multipleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    const char    * testFiles[2] = { testFile, testFile };
    gint64          timePlayed;

    timePlayed = playFiles(testFiles, 2, "0[2s];1[2s]");
    CPPUNIT_ASSERT(timePlayed > 3.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 4.1 * GST_SECOND);
}


