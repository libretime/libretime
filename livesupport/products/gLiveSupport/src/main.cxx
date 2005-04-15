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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/main.cxx,v $

------------------------------------------------------------------------------*/

/** @file
 *  This file contains the main entry point to the Scheduler daemon.
 */

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_GETOPT_H
#include <getopt.h>
#else
#error "Need getopt.h"
#endif

#include <iostream>

#include <unicode/resbund.h>
#include <libxml++/libxml++.h>
#include <gtkmm/main.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

#include "GLiveSupport.h"

using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

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
static const char   options[] = "c:hv";

/**
 *  Structure describing the long options
 */
static const struct option longOptions[] = {
    { "config", required_argument, 0, 'c' },
    { "help", no_argument, 0, 'h' },
    { "version", no_argument, 0, 'v' },
    { 0, 0, 0, 0 }
};


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


/* =============================================================  module code */

/**
 *  Program entry point.
 *
 *  @param argc the number of command line arguments passed by the user.
 *  @param argv the command line arguments passed by the user.
 *  @return 0 on success, non-0 on failure.
 */
int main (  int     argc,
            char  * argv[] )
{
    // initialize the Gtk library, with the Gtk options first
    Gtk::Main kit(argc, argv);

    // take a look at our options
    int         i;
    std::string configFileName;

    while ((i = getopt_long(argc, argv, options, longOptions, 0)) != -1) {
        switch (i) {
            case 'c':
                configFileName = optarg;
                break;

            case 'h':
                printUsage(argv[0], std::cout);
                return 0;

            case 'v':
                printVersion(std::cout);
                return 0;

            default:
                printUsage(argv[0], std::cout);
                return 1;
        }
    }

    if (optind != argc) {
        printUsage(argv[0], std::cout);
        return 1;
    }

    std::cerr << "using config file '" << configFileName << '\'' << std::endl;

    Ptr<LiveSupport::GLiveSupport::GLiveSupport>::Ref
                    gLiveSupport(new LiveSupport::GLiveSupport::GLiveSupport());

    try {
        std::auto_ptr<xmlpp::DomParser> 
                            parser(new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        gLiveSupport->configure(*(document->get_root_node()));
    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in configuration file" << std::endl
                  << e.what() << std::endl;
        return 1;
    } catch (xmlpp::exception &e) {
        std::cerr << "error parsing configuration file" << std::endl
                  << e.what() << std::endl;
        return 1;
    } catch (std::logic_error &e) {
        std::cerr << "error configuring..." << std::endl
                  << e.what() << std::endl;
        return 1;
    }

    if (!gLiveSupport->checkConfiguration()) {
        std::cerr << "some problem with the configuration" << std::endl;
        return 1;
    }

    gLiveSupport->show();

    return 0;
}


/*------------------------------------------------------------------------------
 *  Print program version.
 *----------------------------------------------------------------------------*/
static void
printVersion (  std::ostream  & os )
{
    os << PACKAGE_NAME << ' ' << PACKAGE_VERSION << std::endl
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
       << std::endl
       << "Usage: " << invocation << " [OPTION]"
       << std::endl
       << "  mandatory options:" << std::endl
       << "  -c, --config=file.name   scheduler configuration file" << std::endl
       << "  optional options:" << std::endl
       << "  -h, --help               display this help and exit" << std::endl
       << "  -v, --version            display version information and exit"
                                                                    << std::endl
       << std::endl
       << "Report bugs to " << PACKAGE_BUGREPORT << std::endl;
}

