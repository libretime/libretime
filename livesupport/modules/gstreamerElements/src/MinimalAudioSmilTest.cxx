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
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/MinimalAudioSmilTest.cxx,v $

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


#include <string>
#include <iostream>

#include <gst/gst.h>

#include "MinimalAudioSmilTest.h"


using namespace LiveSupport::GstreamerElements;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(MinimalAudioSmilTest);

/**
 *  A simple smil file.
 */
static const char *         simpleFile = "var/simple.smil";

/**
 *  A simple smil file with clipBegin and clipEnd attributes
 */
static const char *         simpleClipBeginFile =
                                            "var/simple-clipBegin.smil";

/**
 *  A simple smil file with clipBegin and clipEnd attributes
 */
static const char *         simpleClipBeginEndFile =
                                            "var/simple-clipBegin-clipEnd.smil";

/**
 *  A parallel smil file.
 */
static const char *         parallelFile = "var/parallel.smil";


/**
 *  A parallel smil file with clipBegin and clipEnd attributes.
 */
static const char *         parallelClipBeginEndFile =
                                        "var/parallel-clipBegin-clipEnd.smil";

/**
 *  A SMIL file containing an Ogg Vorbis file.
 */
static const char *         oggVorbisSmilFile = "var/simple-ogg.smil";

/**
 *  A SMIL file containing another SMIL file.
 */
static const char *         embeddedSmilFile = "var/embedded.smil";

/**
 *  A SMIL file containing sound animation.
 */
static const char *         soundAnimationSmilFile = "var/animateSound.smil";

/**
 *  A SMIL file containing sound animation with two parallel files.
 */
static const char *         soundAnimationParallelSmilFile =
                                            "var/animateSoundParallel.smil";

/**
 *  A SMIL file containing sound animation in effect of a fade in / out.
 */
static const char *         fadeInOutSmilFile = "var/fadeInOut.smil";

/**
 *  A SMIL file containing sound animation in effect of a fade in / out,
 *  with two overlapping audio files.
 */
static const char *         fadeInOutParallelSmilFile =
                                                "var/fadeInOutParallel.smil";

/**
 *  A SMIL file containing several audio elements in a par, that in effect
 *  play sequentially.
 */
static const char *         sequentialSmilFile =
                                                "var/sequentialSmil.smil";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Play a SMIL file.
 *----------------------------------------------------------------------------*/
gint64
MinimalAudioSmilTest :: playSmilFile(const char   * smilFile)
                                                throw (CPPUNIT_NS::Exception)
{
    GstElement    * pipeline;
    GstElement    * filesrc;
    GstElement    * smil;
    GstElement    * sink;
    GstFormat       format;
    gint64          timePlayed;

    /* initialization */
    gst_init(0, 0);

    /* create elements */
    pipeline = gst_pipeline_new("pipeline");
    filesrc  = gst_element_factory_make("filesrc", "filesource");
    smil     = gst_element_factory_make("minimalaudiosmil", "smil");
    sink     = gst_element_factory_make("alsasink", "audiosink");

    g_object_set(G_OBJECT(filesrc), "location", smilFile, NULL);

    /* link everything together */
    gst_element_link_many(filesrc, smil, sink, NULL);
    gst_bin_add_many(GST_BIN(pipeline), filesrc, smil, sink, NULL);

    /* run */
    gst_element_set_state(pipeline, GST_STATE_PLAYING);
    while (gst_bin_iterate(GST_BIN(pipeline)));

    format = GST_FORMAT_TIME;
    gst_element_query(sink, GST_QUERY_POSITION, &format, &timePlayed);

    /* clean up */
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(GST_OBJECT(pipeline));

    return timePlayed;
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(simpleFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 3.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A simple test with clipBegin attribute
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: simpleClipBeginTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(simpleClipBeginFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A simple test with clipBegin and clipEnd attributes
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: simpleClipBeginEndTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(simpleClipBeginEndFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test <par> SMIL elements
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: parallelTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(parallelFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 7.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 8.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test <par> SMIL elements with clipBegin and clipEnd attributes
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: parallelClipBeginEndTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(parallelClipBeginEndFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 7.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 8.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test a SMIL file pointing to an Ogg Vorbis file.
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: oggVorbisTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(oggVorbisSmilFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test a SMIL file pointing to another SMIL file.
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: embeddedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(embeddedSmilFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 9.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 10.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test a SMIL file containing sound level animation.
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: soundAnimationTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(soundAnimationSmilFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test a SMIL file containing sound level animation with two files in
 *  parallel.
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: soundAnimationParallelTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(soundAnimationParallelSmilFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test a SMIL file containing sound level animation resulting in a fade
 *  in / fade out effect.
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: fadeInOutTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(fadeInOutSmilFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test a SMIL file containing sound level animation resulting in a fade
 *  in / fade out effect, with two parallel files
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: fadeInOutParallelTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(fadeInOutParallelSmilFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A sequential par element test.
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: sequentialSmilTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;
    char    str[256];

    timePlayed = playSmilFile(sequentialSmilFile);
    g_snprintf(str, 256, "time played: %" G_GINT64_FORMAT, timePlayed);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed > 34.7 * GST_SECOND);
    CPPUNIT_ASSERT_MESSAGE(str, timePlayed < 35.0 * GST_SECOND);
}

