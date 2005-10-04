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

#include <string>
#include <iostream>

#include <gst/gst.h>

#include "LiveSupport/GstreamerElements/autoplug.h"
#include "SwitcherTest.h"


using namespace LiveSupport::GstreamerElements;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SwitcherTest);

/**
 *  An mp3 test file.
 */
static const char *         mp3File = "var/5seccounter.mp3";

/**
 *  An ogg vorbis test file.
 */
static const char *         oggVorbisFile = "var/5seccounter.ogg";

/**
 *  A SMIL test file.
 */
static const char *         smilFile = "var/simple.smil";


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
    GstElement    * switcher;
    GstElement    * sink;
    GstCaps       * caps;
    unsigned int    i;
    GstFormat       format;
    gint64          timePlayed;

    /* initialize GStreamer */
    gst_init(0, 0);

    caps = gst_caps_new_simple("audio/x-raw-int",
                               "width", G_TYPE_INT, 16,
                               "depth", G_TYPE_INT, 16,
                               "endianness", G_TYPE_INT, G_BYTE_ORDER,
                               "signed", G_TYPE_BOOLEAN, TRUE,
                               "channels", G_TYPE_INT, 2,
                               "rate", G_TYPE_INT, 44100,
                               NULL);

    /* create elements */
    pipeline = gst_pipeline_new("audio-player");
    switcher = gst_element_factory_make("switcher", "switcher");
    sink     = gst_element_factory_make("alsasink", "alsa-output");

    for (i = 0; i < noFiles; ++i) {
        GstElement    * source;
        GstElement    * decoder;
        char            str[256];
        gboolean        ret;

        g_snprintf(str, 256, "source_%d", i);
        source   = gst_element_factory_make("filesrc", str);
        CPPUNIT_ASSERT(source);
        g_object_set(G_OBJECT(source), "location", audioFiles[i], NULL);

        g_snprintf(str, 256, "decoder_%d", i);
        decoder = ls_gst_autoplug_plug_source(source, str, caps);
        CPPUNIT_ASSERT(decoder);

        ret = gst_element_link(decoder, switcher);
        CPPUNIT_ASSERT(ret);
        gst_bin_add_many(GST_BIN(pipeline), source, decoder, NULL);
    }

    /* link and add the switcher & sink _after_ the decoders above
     * otherwise we'll get a:
     * "assertion failed: (group->group_links == NULL)"
     * error later on when trying to free up the pipeline
     * see http://bugzilla.gnome.org/show_bug.cgi?id=309122
     */
    gst_element_link_many(switcher, sink, NULL);
    gst_bin_add_many(GST_BIN(pipeline), switcher, sink, NULL);

    g_object_set(G_OBJECT(switcher), "source-config", sourceConfig, NULL);
    /* listen for the eos event on switcher, so the pipeline can be stopped */
    g_signal_connect(switcher, "eos", G_CALLBACK(eos_signal_handler), pipeline);

    gst_element_set_state(sink, GST_STATE_PAUSED);
    /* set the switcher to PAUSED, as it will give
     * "trying to push on unnegotiaded pad" warnings otherwise */
    gst_element_set_state(switcher, GST_STATE_PAUSED);
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
SwitcherTest :: mp3Test(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFiles(&mp3File, 1, "0[3s]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 3.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: mp3OpenEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFiles(&mp3File, 1, "0[]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: mp3MultipleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    const char    * testFiles[] = { mp3File, mp3File };
    gint64          timePlayed;
    char            str[256];

    timePlayed = playFiles(testFiles, 2, "0[2s];1[2s]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 3.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 4.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: mp3MultipleOpenEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    const char    * testFiles[] = { mp3File, mp3File };
    gint64          timePlayed;
    char            str[256];

    timePlayed = playFiles(testFiles, 2, "0[2s];1[]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 6.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 7.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: oggVorbisTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFiles(&oggVorbisFile, 1, "0[3s]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 3.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: oggVorbisOpenEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFiles(&oggVorbisFile, 1, "0[]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: oggVorbisMultipleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    const char    * testFiles[] = { oggVorbisFile, oggVorbisFile };
    gint64          timePlayed;
    char            str[256];

    timePlayed = playFiles(testFiles, 2, "0[2s];1[2s]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 3.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 4.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: oggVorbisMultipleOpenEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    const char    * testFiles[] = { oggVorbisFile, oggVorbisFile };
    gint64          timePlayed;
    char            str[256];

    timePlayed = playFiles(testFiles, 2, "0[2s];1[]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 6.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 7.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: smilTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFiles(&smilFile, 1, "0[3s]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 3.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: smilOpenEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playFiles(&smilFile, 1, "0[]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: smilMultipleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    const char    * testFiles[] = { smilFile, smilFile };
    gint64          timePlayed;
    char            str[256];

    timePlayed = playFiles(testFiles, 2, "0[2s];1[2s]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 3.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 4.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Play a file until its end.
 *----------------------------------------------------------------------------*/
void
SwitcherTest :: smilMultipleOpenEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    const char    * testFiles[] = { smilFile, smilFile };
    gint64          timePlayed;
    char            str[256];

    timePlayed = playFiles(testFiles, 2, "0[2s];1[]");
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 6.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 7.1 * GST_SECOND);
}


