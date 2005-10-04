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


#include <string>
#include <iostream>
#include <fstream>

#include <gst/gst.h>

#include "OneshotReaderTest.h"


using namespace LiveSupport::GstreamerElements;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(OneshotReaderTest);

static const char *         testFile = "var/oneshotReader.input";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
OneshotReaderTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
OneshotReaderTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
OneshotReaderTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    GstElement    * pipeline;
    GstElement    * filesrc;
    GstElement    * oneshot;
    GstElement    * bin;
    guint8        * contents;
    guint           length;
    char          * verifyContents;
    std::ifstream   ifs;

    /* initialize GStreamer */
    gst_init(0, 0);

    /* create elements */
    pipeline = gst_pipeline_new ("my_pipeline");

    filesrc = gst_element_factory_make("filesrc", "my_filesource");
    oneshot = gst_element_factory_make("oneshotreader", "oneshot");
    bin     = gst_bin_new("bin");

    g_object_set(G_OBJECT(filesrc), "location", testFile, NULL);

    gst_bin_add(GST_BIN(bin), oneshot);
    gst_element_add_ghost_pad(bin,
                              gst_element_get_pad(oneshot, "sink"),
                              "sink");

    /* link everything together */
    gst_element_link_many(filesrc, bin, NULL);
    gst_bin_add_many(GST_BIN(pipeline), filesrc, bin, NULL);

    /* run */
    gst_element_set_state(pipeline, GST_STATE_PLAYING);
    // well, actually don't run, by setting to state PLAYING,
    // we already have what we're looking for.
    while (gst_bin_iterate(GST_BIN(pipeline)));

    g_object_get(G_OBJECT(oneshot), "contents", &contents, NULL);
    g_object_get(G_OBJECT(oneshot), "length", &length, NULL);

    // read in the file contents with an ifstream, and see if
    // we get the same
    verifyContents = new char[length];
    ifs.open(testFile);
    CPPUNIT_ASSERT(ifs.good());
    ifs.read(verifyContents, length);
    CPPUNIT_ASSERT(!memcmp(contents, verifyContents, length));
    
    /* clean up */
    delete[] verifyContents;
    gst_element_set_state(pipeline, GST_STATE_NULL);
    gst_object_unref(GST_OBJECT(pipeline));
}

