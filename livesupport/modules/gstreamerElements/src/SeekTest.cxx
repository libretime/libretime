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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/SeekTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <iostream>

#include <gst/gst.h>

#include "seek.h"
#include "SeekTest.h"


using namespace LiveSupport::GstreamerElements;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SeekTest);

static const char *         testFile = "var/5seccounter.mp3";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
SeekTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SeekTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Play an audio file
 *----------------------------------------------------------------------------*/
gint64
SeekTest :: playFile(const char   * audioFile,
                     gint64         seekTo,
                     gint64         playTo)
                                                throw (CPPUNIT_NS::Exception)
{
    GstElement    * pipeline;
    GstElement    * source;
    GstElement    * decoder;
    GstElement    * sink;
    GstSeekType     seekType;
    GstFormat       format;
    gint64          timePlayed;
    gint64          timeAfterSeek;
    gboolean        ret;

    /* initialize GStreamer */
    gst_init(0, 0);

    /* create elements */
    seekType = (GstSeekType) (GST_FORMAT_TIME |
                              GST_SEEK_METHOD_SET |
                              GST_SEEK_FLAG_FLUSH);

    pipeline = gst_pipeline_new("audio-player");
    source   = gst_element_factory_make("filesrc", "source");
    decoder  = gst_element_factory_make("mad", "decoder");
    sink     = gst_element_factory_make("alsasink", "alsa-output");

    g_object_set(G_OBJECT(source), "location", audioFile, NULL);

    gst_element_link_many(source, decoder, sink, NULL);
    gst_bin_add_many(GST_BIN(pipeline), source, decoder, sink, NULL);

    gst_element_set_state(sink, GST_STATE_READY);
    gst_element_set_state(pipeline, GST_STATE_PLAYING);

    // iterate on the piplinee until the played time becomes more than 0
    // as the seek even will only be taken into consideration after that
    // by gstreamer
    for (timePlayed = 0;
         timePlayed == 0 && gst_bin_iterate(GST_BIN(pipeline)); ) {

        format = GST_FORMAT_TIME;
        gst_element_query(sink, GST_QUERY_POSITION, &format, &timePlayed);
    }

    // so, seek now
    timeAfterSeek = -1LL;
    ret = livesupport_seek(decoder, seekType, seekTo);
    CPPUNIT_ASSERT(ret);

    // iterate until playTo is reached
    while (gst_bin_iterate(GST_BIN(pipeline))) {
        format = GST_FORMAT_TIME;
        gst_element_query(sink, GST_QUERY_POSITION, &format, &timePlayed);

        if (timeAfterSeek == -1LL && timePlayed > seekTo) {
            timeAfterSeek = timePlayed;
        }

        if (playTo > 0 && timePlayed > playTo) {
            break;
        }
    }

    /* clean up nicely */
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(GST_OBJECT (pipeline));

    return timePlayed - timeAfterSeek;
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
SeekTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    timePlayed = playFile(testFile, 1LL * GST_SECOND, 4LL * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 3.1 * GST_SECOND);
}


/*------------------------------------------------------------------------------
 *  Seek and play until the end of the auido file.
 *----------------------------------------------------------------------------*/
void
SeekTest :: openEndedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    gint64  timePlayed;

    // see http://bugzilla.gnome.org/show_bug.cgi?id=308312
    // as why this seek is not precise
    timePlayed = playFile(testFile, 2LL * GST_SECOND, -1LL);
    CPPUNIT_ASSERT(timePlayed > 2.9 * GST_SECOND);
    CPPUNIT_ASSERT(timePlayed < 3.1 * GST_SECOND);
}

