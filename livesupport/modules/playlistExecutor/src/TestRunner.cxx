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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/TestRunner.cxx,v $

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

#include <cppunit/BriefTestProgressListener.h>
#include <cppunit/CompilerOutputter.h>
#include <cppunit/XmlOutputter.h>
#include <cppunit/extensions/TestFactoryRegistry.h>
#include <cppunit/TestResult.h>
#include <cppunit/TestResultCollector.h>
#include <cppunit/TestRunner.h>

#include "LiveSupport/Core/Ptr.h"


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
int
main(   int     argc,
        char  * argv[] )                                throw ()
{
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

