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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/XmlRpcDaemon.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_SIGNAL_H
#include <signal.h>
#else
#error "Need signal.h"
#endif

#if HAVE_SYS_STAT_H
#include <sys/stat.h>
#else
#error "Need sys/stat.h"
#endif


#include <iostream>
#include <sstream>
#include <fstream>
#include <cstdio>

#include "SignalDispatcher.h"
#include "XmlRpcDaemonShutdownSignalHandler.h"
#include "XmlRpcDaemon.h"


using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the XML configuration element for the Scheduler daemon.
 */
static const std::string confElement = "xmlRpcDaemon";

/**
 *  The name of the XML configuration attribute for the XML-RPC host name.
 */
static const std::string confXmlRpcHostAttr = "xmlRpcHost";

/**
 *  The name of the XML configuration attribute for the XML-RPC port.
 */
static const std::string confXmlRpcPortAttr = "xmlRpcPort";

/**
 *  The name of the XML configuration attribute for the process id file.
 */
static const std::string confPidFileNameAttr = "pidFileName";

/**
 *  The umask used by the daemon for file creations
 */
static const mode_t uMask = 027;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the Scheder daemon according to an XML element
 *----------------------------------------------------------------------------*/
void
XmlRpcDaemon :: configureXmlRpcDaemon(
                        const xmlpp::Element   & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (configured) {
        throw std::logic_error("already configured");
    }

    const xmlpp::Attribute    * attribute;
    std::stringstream           strStr;

    if (element.get_name() != confElement) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    if (!(attribute = element.get_attribute(confXmlRpcHostAttr))) {
        std::string eMsg = "Missing attribute ";
        eMsg += confXmlRpcHostAttr;
        throw std::invalid_argument(eMsg);
    }
    xmlRpcHost = attribute->get_value();

    if (!(attribute = element.get_attribute(confXmlRpcPortAttr))) {
        std::string eMsg = "Missing attribute ";
        eMsg += confXmlRpcPortAttr;
        throw std::invalid_argument(eMsg);
    }
    strStr.str(attribute->get_value());
    strStr >> xmlRpcPort;

    if (!(attribute = element.get_attribute(confPidFileNameAttr))) {
        std::string eMsg = "Missing attribute ";
        eMsg += confPidFileNameAttr;
        throw std::invalid_argument(eMsg);
    }
    pidFileName = attribute->get_value();

    configured = true;
}


/*------------------------------------------------------------------------------
 *  Do all the necessary work of becoming a daemon.
 *  See http://www.enderunix.org/docs/eng/daemon.php and
 *  http://www.linuxprofilm.com/articles/linux-daemon-howto.html
 *  for hints.
 *----------------------------------------------------------------------------*/
void
XmlRpcDaemon :: daemonize(void)                  throw (std::runtime_error)
{
    int     i;

    if (getppid() == 1) {
        // we're already a daemon
        return;
    }

    i = fork();
    if (i < 0) {
        throw std::runtime_error("fork error");
    } else if (i > 0) {
        // this is the parent, simply return
        return;
    }

    // now we're in the child process

    // obtain a new process group
    setsid();

    // change the umask
    umask(uMask);

    // close standard file descriptors
    /* TODO: don't close these until we don't have logging
    std::cin.close();
    std::cout.close();
    std::cerr.close();
    */

    // save the process id
    savePid();

    // ignore some signals
    /* TODO
    signal(SIGCHLD,SIG_IGN);
    signal(SIGTSTP,SIG_IGN);
    signal(SIGTTOU,SIG_IGN);
    signal(SIGTTIN,SIG_IGN);
    */

    // register our signal hanlder
    SignalDispatcher * signalDispatcher = SignalDispatcher::getInstance();
    XmlRpcDaemonShutdownSignalHandler * handler =
                                    new XmlRpcDaemonShutdownSignalHandler(this);
    signalDispatcher->registerHandler(SIGHUP, handler);
    signalDispatcher->registerHandler(SIGTERM, handler);
    // FIXME: this signal handler will not be deleted by anyone,
    //        poddible memory leak
}


/*------------------------------------------------------------------------------
 *  Save the current process id.
 *----------------------------------------------------------------------------*/
void
XmlRpcDaemon :: savePid(void)                            throw ()
{
    std::ofstream   pidFile(pidFileName.c_str());
    pidFile << getpid();
    pidFile.flush();
    pidFile.close();
}


/*------------------------------------------------------------------------------
 *  Return the saved process id.
 *----------------------------------------------------------------------------*/
pid_t
XmlRpcDaemon :: loadPid(void)                            throw ()
{
    pid_t   pid;

    std::ifstream   pidFile(pidFileName.c_str());
    if (pidFile.fail()) {
        return 0;
    }

    pidFile >> pid;
    pidFile.close();

    return pid;
}


/*------------------------------------------------------------------------------
 *  Start the daemon.
 *----------------------------------------------------------------------------*/
void
XmlRpcDaemon :: start (void)                         throw (std::logic_error)
{
    checkForConfiguration();

    if (background) {
        daemonize();
    }

    // and now our own XML-RPC methods
    registerXmlRpcFunctions(xmlRpcServer);

    // bind & run
    XmlRpc::setVerbosity(5);
    xmlRpcServer.bindAndListen(xmlRpcPort);
    xmlRpcServer.work(-1.0);
}


/*------------------------------------------------------------------------------
 *  Tell if the daemon is running.
 *----------------------------------------------------------------------------*/
bool
XmlRpcDaemon :: isRunning (void)                     throw (std::logic_error)
{
    checkForConfiguration();

    return loadPid();
}


/*------------------------------------------------------------------------------
 *  Stop the daemon.
 *----------------------------------------------------------------------------*/
void
XmlRpcDaemon :: stop (void)                          throw (std::logic_error)
{
    checkForConfiguration();

    pid_t   pid = loadPid();
    kill(pid, SIGTERM);
}


/*------------------------------------------------------------------------------
 *  Shut down the daemon.
 *----------------------------------------------------------------------------*/
void
XmlRpcDaemon :: shutdown (void)                      throw (std::logic_error)
{
    checkForConfiguration();

    xmlRpcServer.shutdown();
    remove(pidFileName.c_str());
}


