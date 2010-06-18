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

/** @file
 *  This file contains the main entry point to the Scheduler daemon.
 */

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

#include <memory>
#include <string>
#include <iostream>
#include <stdexcept>

#include "SchedulerDaemon.h"


using namespace LiveSupport::Scheduler;

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
static const char   options[] = "c:dhv";

/**
 *  Structure describing the long options
 */
static const struct option longOptions[] = {
    { "config", required_argument, 0, 'c' },
    { "debug", no_argument, 0, 'd' },
    { "help", no_argument, 0, 'h' },
    { "version", no_argument, 0, 'v' },
    { 0, 0, 0, 0 }
};

/**
 *  The start command: "start"
 */
static const std::string startCommand = "start";

/**
 *  The status command: "status"
 */
static const std::string statusCommand = "status";

/**
 *  The stop command: "stop"
 */
static const std::string stopCommand = "stop";


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
    int         i;
    std::string configFileName;
    bool        debugMode = false;

    while ((i = getopt_long(argc, argv, options, longOptions, 0)) != -1) {
        switch (i) {
            case 'c':
                configFileName = optarg;
                break;

            case 'd':
                debugMode = true;
                break;

            case 'h':
                printUsage(argv[0], std::cout);
                exit(EXIT_SUCCESS);

            case 'v':
                printVersion(std::cout);
                exit(EXIT_SUCCESS);

            default:
                printUsage(argv[0], std::cout);
                exit(EXIT_FAILURE);
        }
    }

    if (optind != argc - 1) {
        printUsage(argv[0], std::cout);
        exit(EXIT_FAILURE);
    }

    std::cerr << "using config file '" << configFileName << '\'' << std::endl;

    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();

    try {
        std::auto_ptr<xmlpp::DomParser>
                            parser(new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        daemon->configure(*(document->get_root_node()));
    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in configuration file" << std::endl
                  << e.what() << std::endl;
        exit(EXIT_FAILURE);
    } catch (xmlpp::exception &e) {
        std::cerr << "error parsing configuration file" << std::endl
                  << e.what() << std::endl;
        exit(EXIT_FAILURE);
    }

    daemon->setBackground(!debugMode);

    try {
        if (startCommand == argv[optind]) {
            daemon->start();
        } else if (statusCommand == argv[optind]) {
            std::cout << "The Scheduler Daemon is "
                      << (daemon->isRunning() ? "" : "not ")
                      << "running" << std::endl;
        } else if (stopCommand == argv[optind]) {
            daemon->stop();
        } else {
            printUsage(argv[0], std::cout);
            exit(EXIT_FAILURE);
        }
    } catch (std::exception &e) {
        std::cerr << "error executing command " << argv[optind] << std::endl;
        std::cerr << e.what() << std::endl;
        exit(EXIT_FAILURE);
    }

    exit(EXIT_SUCCESS);
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
       << "Usage: " << invocation << " [OPTION] COMMAND"
       << std::endl
       << "  COMMAND is one of: start, stop, or status"
                                                                    << std::endl
       << std::endl
       << "  mandatory options:" << std::endl
       << "  -c, --config=file.name   scheduler configuration file" << std::endl
       << "  optional options:" << std::endl
       << "  -d, --debug              don't fork into background" << std::endl
       << "  -h, --help               display this help and exit" << std::endl
       << "  -v, --version            display version information and exit"
                                                                    << std::endl
       << std::endl
       << "Report bugs to " << PACKAGE_BUGREPORT << std::endl;
}

