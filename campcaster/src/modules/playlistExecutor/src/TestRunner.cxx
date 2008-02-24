/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif

#if HAVE_GETOPT_H
#include <getopt.h>
#else
#error "Need getopt.h"
#endif

#include <fstream>

#include <gst/gst.h>
#include <gst/controller/gstcontroller.h>
#include <gst/controller/gstinterpolationcontrolsource.h>


#include <cppunit/BriefTestProgressListener.h>
#include <cppunit/CompilerOutputter.h>
#include <cppunit/XmlOutputter.h>
#include <cppunit/extensions/TestFactoryRegistry.h>
#include <cppunit/TestResult.h>
#include <cppunit/TestResultCollector.h>
#include <cppunit/TestRunner.h>

#include "LiveSupport/Core/Ptr.h"

#include "GstreamerPlayContext.h"
#include "SmilHandler.h"

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  Our copyright notice, should be at most 80 columns
 */
static const char copyrightNotice[] =
        "Copyright (c) 2004 Media Development Loan Fund under the GNU GPL";

/**
 *  String describing the short options.
 */
static const char   options[] = "ho:s:v";

/**
 *  Structure describing the long options
 */
static const struct option longOptions[] = {
    { "help", no_argument, 0, 'h' },
    { "output", required_argument, 0, 'o' },
    { "stylesheet", required_argument, 0, 's' },
    { "version", no_argument, 0, 'v' },
    { 0, 0, 0, 0 }
};

/**
 *  The encoding to use for the output file.
 */
static const std::string encoding = "utf-8";

/**
 *  The output XML file name.
 */
static Ptr<std::string>::Ref xmlOutFileName;

/**
 *  The XSLT attached to the output file.
 */
static Ptr<std::string>::Ref xsltFileName;


/* ===============================================  local function prototypes */

/**
 *  Print program version.
 *
 *  @param os the std::ostream to print to.
 */
static void
printVersion (  std::ostream  & os );

/**
 *  Print program usage information.
 *
 *  @param invocation the command line command used to invoke this program.
 *  @param os the std::ostream to print to.
 */
static void
printUsage (    const char      invocation[],
                std::ostream  & os );

/**
 *  Process command line arguments.
 *
 *  @param argc the number of arguments.
 *  @param argv the arguments themselves.
 *  @return true of all went well, false in case the program should exit
 *          after this call.
 */
static bool
processArguments(int argc, char *argv[]);


/* =============================================================  module code */


/*------------------------------------------------------------------------------
 *  Run all tests
 *----------------------------------------------------------------------------*/



static GMainLoop *loop;
static GstElement *play=NULL;


static GstreamerPlayContext *pContext=NULL;
static GstreamerPlayContext *pContextNext=NULL;

static SmilHandler *smil = NULL;


static int cnt=0;

static gboolean
my_bus_callback (GstBus     *bus,
         GstMessage *message,
         gpointer    data)
{
  g_print ("Got %s message\n", GST_MESSAGE_TYPE_NAME (message));

  switch (GST_MESSAGE_TYPE (message)) {
    case GST_MESSAGE_ERROR: {
      GError *err;
      gchar *debug;

      gst_message_parse_error (message, &err, &debug);
      g_print ("Error: %s\n", err->message);
      g_error_free (err);
      g_free (debug);

      g_main_loop_quit (loop);
      break;
    }
    case GST_MESSAGE_EOS:
      if(cnt<0){
//      if(cnt<5){
          char tmp[255]={0};
          sprintf(tmp, "file:///tmp/campcaster/%d.ogg", cnt+1);//use when file name needed
            if(pContext){
                pContext->closeContext();
                delete pContext;
                pContext = NULL;
            }

/*            if(pContextNext){
                pContext=pContextNext;
                pContextNext=new GstreamerPlayContext(tmp);
            }
*/

            pContext=new GstreamerPlayContext();
//            pContext->setAudioDevice("default");
            pContext->openSource(tmp);



            if(pContext){
                pContext->playContext();
            }

          cnt++;
      }else if(smil != NULL){
            pContext->closeContext();
            AudioDescription *audioDescription = smil->getNext();
            if(audioDescription == NULL){//no more audio entries to play
                delete smil;
                smil = NULL;
                delete pContext;
                g_main_loop_quit (loop);
                break;
            }
            pContext->openSource(audioDescription);
            pContext->playContext();
      }else{
            if(pContext){
                pContext->closeContext();
                delete pContext;
                pContext = NULL;
            }
          g_main_loop_quit (loop);
      }
      break;
    default:
      break;
  }

   //we want to be notified again the next time there is a message
   //on the bus, so returning TRUE (FALSE means we want to stop watching
   //for messages on the bus and our callback should not be called again)
  return TRUE;
}


int
main(   int     argc,
        char  * argv[] )                                throw ()
{
//    sleep(10);//hook for debugging, allows time to attach kdbg
  
/*
  gint res = 1;
  GstElement *src, *sink;
  GstElement *bin;
  GstController *ctrl;
  GstInterpolationControlSource *csource1;//, *csource2;
  GstClock *clock;
  GstClockID clock_id;
  GstClockReturn wait_ret;
  GValue vol = { 0, };

  gst_init (&argc, &argv);
  gst_controller_init (&argc, &argv);

  // build pipeline 
  bin = gst_pipeline_new ("pipeline");
  clock = gst_pipeline_get_clock (GST_PIPELINE (bin));
  src = gst_element_factory_make ("audiotestsrc", "gen_audio");
//  src = gst_element_make_from_uri (GST_URI_SRC, "file:///tmp/campcaster/login.ogg", NULL);
  sink = gst_element_factory_make ("alsasink", "play_audio");
  gst_bin_add_many (GST_BIN (bin), src, sink, NULL);
  if (!gst_element_link (src, sink)) {
    GST_WARNING ("can't link elements");
    goto Error;
  }

  // square wave
  //   g_object_set (G_OBJECT(src), "wave", 1, NULL);

  // add a controller to the source 
//  if (!(ctrl = gst_controller_new (G_OBJECT (src), "freq", "volume", NULL))) {
  if (!(ctrl = gst_controller_new (G_OBJECT (src), "volume", NULL))) {
    GST_WARNING ("can't control source element");
    goto Error;
  }

  csource1 = gst_interpolation_control_source_new ();
//  csource2 = gst_interpolation_control_source_new ();

  gst_controller_set_control_source (ctrl, "volume", GST_CONTROL_SOURCE (csource1));
//  gst_controller_set_control_source (ctrl, "freq", GST_CONTROL_SOURCE (csource2));

  // Set interpolation mode 

  gst_interpolation_control_source_set_interpolation_mode (csource1, GST_INTERPOLATE_LINEAR);
//  gst_interpolation_control_source_set_interpolation_mode (csource2, GST_INTERPOLATE_LINEAR);

  // set control values 
  g_value_init (&vol, G_TYPE_DOUBLE);

  g_value_set_double (&vol, 0.0);
  gst_interpolation_control_source_set (csource1, 0 * GST_SECOND, &vol);
  g_value_set_double (&vol, 1.0);
  gst_interpolation_control_source_set (csource1, 1 * GST_SECOND, &vol);

  g_value_set_double (&vol, 1.0);
  gst_interpolation_control_source_set (csource1, 2 * GST_SECOND, &vol);
  g_value_set_double (&vol, 0.0);
  gst_interpolation_control_source_set (csource1, 3 * GST_SECOND, &vol);

  g_object_unref (csource1);

//   g_value_set_double (&vol, 220.0);
//   gst_interpolation_control_source_set (csource2, 0 * GST_SECOND, &vol);
//   g_value_set_double (&vol, 3520.0);
//   gst_interpolation_control_source_set (csource2, 3 * GST_SECOND, &vol);
//   g_value_set_double (&vol, 440.0);
//   gst_interpolation_control_source_set (csource2, 6 * GST_SECOND, &vol);
// 
//   g_object_unref (csource2);

  clock_id =
      gst_clock_new_single_shot_id (clock,
      gst_clock_get_time (clock) + (6 * GST_SECOND));

  // run for 7 seconds 
  if (gst_element_set_state (bin, GST_STATE_PLAYING)) {
    if ((wait_ret = gst_clock_id_wait (clock_id, NULL)) != GST_CLOCK_OK) {
      GST_WARNING ("clock_id_wait returned: %d", wait_ret);
    }
    gst_element_set_state (bin, GST_STATE_NULL);
  }
  
  // cleanup 
  g_object_unref (G_OBJECT (ctrl));
  gst_object_unref (G_OBJECT (clock));
  gst_object_unref (G_OBJECT (bin));
  res = 0;
Error:
  return (res);
*/
/*
    //quick gnonlin playback test
    gst_init (NULL, NULL);
    loop = g_main_loop_new (NULL, FALSE);
    
    
    GstElement *composition = gst_element_factory_make("gnlcomposition", NULL);
//    GstElement *silentGnlSource = gst_element_factory_make("gnlsource", NULL);
//    GstElement *silenceAudioSource = gst_element_factory_make("audiotestsrc", NULL);
    
    GstElement *volumeFadeBin = gst_element_factory_make("bin", "Volume_fades_bin");
    GstElement *volumeFadeElement = gst_element_factory_make("volume", "Volume_Fade_Element");
    GstElement *volumeFadeStartConvert = gst_element_factory_make("audioconvert", "Start_fadebin_converter");
    GstElement *volumeFadeEndConvert = gst_element_factory_make("audioconvert", "End_fadebin_converter");
    GstElement *volumeFadeOperation = gst_element_factory_make("gnloperation", "gnloperation");
    GstController *volumeFadeController = gst_controller_new(G_OBJECT(volumeFadeElement), "volume");
    
    gst_bin_add(GST_BIN(volumeFadeBin), volumeFadeElement);
    gst_bin_add(GST_BIN(volumeFadeBin), volumeFadeStartConvert);
    gst_bin_add(GST_BIN(volumeFadeBin), volumeFadeEndConvert);
    GstPad *volumeFadeBinSink = gst_ghost_pad_new("sink", gst_element_get_pad(volumeFadeStartConvert, "sink"));
    gst_element_add_pad(volumeFadeBin, volumeFadeBinSink);
    GstPad *volumeFadeBinSrc = gst_ghost_pad_new("src", gst_element_get_pad(volumeFadeEndConvert, "src"));
    gst_element_add_pad(volumeFadeBin, volumeFadeBinSrc);
    
//    g_object_set(G_OBJECT(silenceAudioSource), "wave", 4, NULL); //4 is silence
        
//    g_object_set(G_OBJECT(silentGnlSource), "priority", (2 ^ 32 - 1), NULL);
//    g_object_set(G_OBJECT(silentGnlSource), "start", 0, NULL);
//    g_object_set(G_OBJECT(silentGnlSource), "duration", 1000 * GST_SECOND, NULL);
//    g_object_set(G_OBJECT(silentGnlSource), "media-start", 0, NULL);
//    g_object_set(G_OBJECT(silentGnlSource), "media-duration", 1000 * GST_SECOND, NULL);
    
    g_object_set(G_OBJECT(volumeFadeOperation), "start", long(0) * GST_SECOND, NULL);
    g_object_set(G_OBJECT(volumeFadeOperation), "duration", long(20) * GST_SECOND, NULL);
    g_object_set(G_OBJECT(volumeFadeOperation), "priority", 1, NULL);
    
    gst_controller_set_interpolation_mode(volumeFadeController, "volume", GST_INTERPOLATE_LINEAR);
    
    gst_bin_add(GST_BIN(volumeFadeOperation), volumeFadeBin);
//    gst_bin_add(GST_BIN(silentGnlSource), silenceAudioSource);
//    gst_bin_add(GST_BIN(composition), silentGnlSource);
    gst_bin_add(GST_BIN(composition), volumeFadeOperation);//this is where we hook up to the rest of the pipeline
    
    gst_element_link(volumeFadeStartConvert, volumeFadeElement);
    gst_element_link(volumeFadeElement, volumeFadeEndConvert);
    
    
    g_main_loop_run (loop);

    return 0;
*/

    //quick smil playback test
    gst_init (NULL, NULL);
    loop = g_main_loop_new (NULL, FALSE);
    //quick test for smil parser
    smil = new SmilHandler();
    smil->openSmilFile("file:///tmp/campcaster/animatedSmil.smil");
    
    pContext=new GstreamerPlayContext();
    pContext->setParentData((gpointer)pContext);
    
    AudioDescription *audDesc = smil->getNext();
    pContext->openSource(audDesc);
    pContext->playContext();
    
    g_main_loop_run (loop);

    return 0;

/*
    //quick test for play context
  gst_init (NULL, NULL);
  loop = g_main_loop_new (NULL, FALSE);

    pContext=new GstreamerPlayContext();
    pContext->setParentData((gpointer)pContext);
//    pContext->setAudioDevice("default");
//    pContext->openSource("file:///tmp/campcaster/login.ogg");
    pContext->openSource("file:///tmp/campcaster/introduction.ogg");
//    pContext->openSource("file:///tmp/campcaster/starter.ogg");

//    pContext->openSource("http://www.sicksiteradio.com/contents/radio_shows/sicksiteradio57.mp3");

//    pContext->openSource("file:///tmp/campcaster/test-short.mp3");
//    pContextNext=new GstreamerPlayContext("file:///tmp/campcaster/test.mp3");
//    cnt++;
    pContext->playContext();


    g_main_loop_run (loop);

    if(pContext){
        pContext->closeContext();
        delete pContext;
    }

  return 0;    // initialize the gst parameters
*/
/*
    gst_init(&argc, &argv);

    if (!processArguments(argc, argv)) {
        return 0;
    }

    // Create the event manager and test controller
    CPPUNIT_NS::TestResult controller;
                                                                                
    // Add a listener that colllects test result
    CPPUNIT_NS::TestResultCollector result;
    controller.addListener( &result );

    // Add a listener that print dots as test run.
    CPPUNIT_NS::BriefTestProgressListener progress;
    controller.addListener( &progress );

    // Add the top suite to the test runner
    CPPUNIT_NS::TestRunner runner;
    runner.addTest( CPPUNIT_NS::TestFactoryRegistry::getRegistry().makeTest() );
    runner.run( controller );

    // Print test in a compiler compatible format.
    CPPUNIT_NS::CompilerOutputter outputter( &result, std::cerr );
    outputter.setLocationFormat("%p:%l:");
    outputter.write();

    // also generate an XML document as an output
    std::ofstream    xmlOutFile(xmlOutFileName->c_str());
    CPPUNIT_NS::XmlOutputter    xmlOutputter(&result, xmlOutFile, encoding);
    xmlOutputter.setStandalone(false);
    if (xsltFileName) {
        xmlOutputter.setStyleSheet(*xsltFileName);
    }
    xmlOutputter.write();
    xmlOutFile.flush();
    xmlOutFile.close();

    return result.wasSuccessful() ? 0 : 1;
*/
}


/*------------------------------------------------------------------------------
 *  Process command line arguments.
 *----------------------------------------------------------------------------*/
static bool
processArguments(int argc, char *argv[])
{
    int     i;

    while ((i = getopt_long(argc, argv, options, longOptions, 0)) != -1) {
        switch (i) {
            case 'h':
                printUsage(argv[0], std::cout);
                return false;

            case 'o':
                xmlOutFileName.reset(new std::string(optarg));
                break;

            case 's':
                xsltFileName.reset(new std::string(optarg));
                break;

            case 'v':
                printVersion(std::cout);
                return false;

            default:
                printUsage(argv[0], std::cout);
                return false;
        }
    }

    if (optind < argc) {
        std::cerr << "error processing command line arguments" << std::endl;
        printUsage(argv[0], std::cout);
        return false;
    }

    if (!xmlOutFileName) {
        std::cerr << "mandatory option output file name not specified"
                  << std::endl;
        printUsage(argv[0], std::cout);
        return false;
    }

    std::cerr << "writing output to '" << *xmlOutFileName << '\'' << std::endl;
    if (xsltFileName) {
        std::cerr << "using XSLT file '" << *xsltFileName << '\'' << std::endl;
    }

    return true;
}


/*------------------------------------------------------------------------------
 *  Print program version.
 *----------------------------------------------------------------------------*/
static void
printVersion (  std::ostream  & os )
{
    os << PACKAGE_NAME << ' ' << PACKAGE_VERSION << std::endl
       << "Unit test runner" << std::endl
       << copyrightNotice << std::endl;
}


/*------------------------------------------------------------------------------
 *  Print program usage.
 *----------------------------------------------------------------------------*/
static void
printUsage (    const char      invocation[],
                std::ostream  & os )
{
    os << PACKAGE_NAME << ' ' << PACKAGE_VERSION << std::endl
       << "Unit test runner" << std::endl
       << std::endl
       << "Usage: " << invocation << " [OPTION]"
       << std::endl
       << "  mandatory options:" << std::endl
       << "  -o, --output=file.name   write test results into this XML file"
                                                                    << std::endl
       << "  optional options:" << std::endl
       << "  -s, --stylesheet         specify this XSLT for the output file"
                                                                    << std::endl
       << "                           this is either an absolute URI, or a"
                                                                    << std::endl
       << "                           relative path for the output document"
                                                                    << std::endl
       << "  -h, --help               display this help and exit" << std::endl
       << "  -v, --version            display version information and exit"
                                                                    << std::endl
       << std::endl
       << "Report bugs to " << PACKAGE_BUGREPORT << std::endl;
}

