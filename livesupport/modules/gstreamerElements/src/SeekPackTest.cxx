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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/SeekPackTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <iostream>

#include <gst/gst.h>

#include "seek-pack.h"
#include "SeekPackTest.h"


using namespace LiveSupport::GstreamerElements;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SeekPackTest);

/**
 *  A test file.
 */
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
SeekPackTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SeekPackTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
gint64
SeekPackTest :: playFile(const char   * audioFile,
                         gint64         silenceDuration,
                         gint64         playFrom,
                         gint64         playTo)
                                                throw (CPPUNIT_NS::Exception)
{
    GstElement            * pipeline;
    GstElement            * source;
    LivesupportSeekPack   * seekPack;
    GstElement            * sink;
    GstFormat               format;
    gint64                  timePlayed;

    /* initialize GStreamer */
    gst_init(0, 0);

    /* create elements */
    pipeline = gst_pipeline_new("audio-player");
    source   = gst_element_factory_make("filesrc", "filesource");
    seekPack = livesupport_seek_pack_new("seekPack");
    sink     = gst_element_factory_make("alsasink", "alsaoutput");

    /* set filename property on the file source */
    g_object_set(G_OBJECT (source), "location", audioFile, NULL);

    livesupport_seek_pack_init(seekPack,
                               source,
                               silenceDuration,
                               playFrom,
                               playTo);
    g_signal_connect(seekPack->bin,
                     "eos",
                     G_CALLBACK(eos_signal_handler),
                     pipeline);

    livesupport_seek_pack_link(seekPack, sink);

    livesupport_seek_pack_add_to_bin(seekPack, GST_BIN(pipeline));
    gst_bin_add(GST_BIN(pipeline), sink);

    gst_element_set_state(sink, GST_STATE_READY);
    livesupport_seek_pack_set_state(seekPack, GST_STATE_PLAYING);
    gst_element_set_state(pipeline, GST_STATE_PLAYING);

    while (gst_bin_iterate(GST_BIN(pipeline)));

    format = GST_FORMAT_TIME;
    gst_element_query(sink, GST_QUERY_POSITION, &format, &timePlayed);

    /* clean up nicely */
    gst_element_set_state(pipeline, GST_STATE_NULL);
    livesupport_seek_pack_destroy(seekPack);
    gst_object_unref(GST_OBJECT(pipeline));

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
SeekPackTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playFile(testFile,
                          2LL * GST_SECOND,
                          1LL * GST_SECOND,
                          3LL * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed > 3.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 4.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A test with no silence.
 *----------------------------------------------------------------------------*/
void
SeekPackTest :: noSilenceTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playFile(testFile,
                          0LL * GST_SECOND,
                          1LL * GST_SECOND,
                          3LL * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed > 1.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 2.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Open ended test
 *----------------------------------------------------------------------------*/
void
SeekPackTest :: openEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playFile(testFile,
                          2LL * GST_SECOND,
                          1LL * GST_SECOND,
                          -1LL);
    CPPUNIT_ASSERT(timePlayed > 5.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 6.1 * GST_SECOND);
}

