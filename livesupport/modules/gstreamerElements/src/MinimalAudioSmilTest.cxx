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
    Version  : $Revision: 1.4 $
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

    timePlayed = playSmilFile(simpleFile);
    CPPUNIT_ASSERT(timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 3.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A simple test with clipBegin attribute
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: simpleClipBeginTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playSmilFile(simpleClipBeginFile);
    CPPUNIT_ASSERT(timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  A simple test with clipBegin and clipEnd attributes
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: simpleClipBeginEndTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playSmilFile(simpleClipBeginEndFile);
    CPPUNIT_ASSERT(timePlayed > 4.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 5.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test <par> SMIL elements
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: parallelTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playSmilFile(parallelFile);
    CPPUNIT_ASSERT(timePlayed > 7.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 8.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Test <par> SMIL elements with clipBegin and clipEnd attributes
 *----------------------------------------------------------------------------*/
void
MinimalAudioSmilTest :: parallelClipBeginEndTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playSmilFile(parallelClipBeginEndFile);
    CPPUNIT_ASSERT(timePlayed > 7.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 8.1 * GST_SECOND);
}

