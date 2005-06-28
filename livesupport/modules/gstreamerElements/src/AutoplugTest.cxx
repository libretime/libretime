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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/AutoplugTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <iostream>

#include <gst/gst.h>

#include "LiveSupport/GstreamerElements/autoplug.h"
#include "AutoplugTest.h"


using namespace LiveSupport::GstreamerElements;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(AutoplugTest);

/**
 *  An mp3 test file.
 */
static const char *         mp3TestFile = "var/5seccounter.mp3";

/**
 *  An ogg vorbis test file.
 */
static const char *         oggTestFile = "var/5seccounter.ogg";

/**
 *  A SMIL test file.
 */
static const char *         smilTestFile = "var/simple.smil";

/**
 *  A file we can't plug.
 */
static const char *         badFile = "src/AutoplugTest.cxx";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AutoplugTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
AutoplugTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Play an audio file
 *----------------------------------------------------------------------------*/
gint64
AutoplugTest :: playFile(const char   * audioFile)
                                                throw (CPPUNIT_NS::Exception)
{
    GstElement    * pipeline;
    GstElement    * source;
    GstElement    * decoder;
    GstElement    * sink;
    GstFormat       format;
    gint64          timePlayed;

    /* initialize GStreamer */
    gst_init(0, 0);

    /* create elements */
    pipeline = gst_pipeline_new("audio-player");
    source   = gst_element_factory_make("filesrc", "source");
    sink     = gst_element_factory_make("alsasink", "alsa-output");

    g_object_set(G_OBJECT(source), "location", audioFile, NULL);

    decoder = ls_gst_autoplug_plug_source(source, "decoder");

    if (!decoder) {
        gst_object_unref(GST_OBJECT(sink));
        gst_object_unref(GST_OBJECT(source));
        gst_object_unref(GST_OBJECT(pipeline));

        return 0LL;
    }

    gst_element_link(decoder, sink);
    gst_bin_add_many(GST_BIN(pipeline), source, decoder, sink, NULL);

    gst_element_set_state(source, GST_STATE_PAUSED);
    gst_element_set_state(decoder, GST_STATE_PAUSED);
    gst_element_set_state(sink, GST_STATE_PAUSED);
    gst_element_set_state(pipeline, GST_STATE_PLAYING);

    // iterate until playTo is reached
    while (gst_bin_iterate(GST_BIN(pipeline)));

    /* FIXME: query the decoder, as for some reason, the sink will return
     *        unreal numbers, when playing back mp3s only! */
    format = GST_FORMAT_TIME;
    gst_element_query(decoder, GST_QUERY_POSITION, &format, &timePlayed);

    /* clean up nicely */
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(GST_OBJECT(pipeline));

    return timePlayed;
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
AutoplugTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFile(mp3TestFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  An ogg vorbis test.
 *----------------------------------------------------------------------------*/
void
AutoplugTest :: oggVorbisTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFile(oggTestFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A SMIL test.
 *----------------------------------------------------------------------------*/
void
AutoplugTest :: smilTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFile(smilTestFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test somethign we can't plug.
 *----------------------------------------------------------------------------*/
void
AutoplugTest :: negativeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFile(badFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed == 0LL);
}

