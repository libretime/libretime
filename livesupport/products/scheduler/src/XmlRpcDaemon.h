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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/XmlRpcDaemon.h,v $

------------------------------------------------------------------------------*/
#ifndef XmlRpcDaemon_h
#define XmlRpcDaemon_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_SYS_TYPES_H
#include <sys/types.h>
#else
#error "Need sys/types.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif

#include <string>
#include <stdexcept>
#include <libxml++/libxml++.h>
#include <XmlRpc.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Scheduler {

using namespace XmlRpc;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A generic XML-RPC daemon, that has to be sublclassed to provide
 *  real functionality.
 *
 *  To use this class, subclass it, and override the configure() and
 *  registerXmlRpcFunctions() functions.
 *
 *  The typical usage of the XmlRpcDaemon is as follows. To start the
 *  daemon:
 *  <ol>
 *      <li>create an instance of a subclass of XmlRpcDaemon</li>
 *      <li>call the configure() method with a proper XML element</li>
 *      <li>optionally call setBackground()</li>
 *      <li>call start() to start the daemon</li>
 *  </ol>
 *  Stopping the daemon is similar:
 *  <ol>
 *      <li>create an instance of a subclass of XmlRpcDaemon</li>
 *      <li>call the configure() method with a proper XML element
 *          if it has not yet been configured</li>
 *      <li>call stop() to stop the daemon</li>
 *  </ol>
 *
 *  The structure of the XML configuration element used to configure the
 *  XML-RPC daemon is as follows:
 *
 *  <pre><code>
 *  <xmlRpcDaemon xmlRpcHost  = "hostname"
 *                xmlRpcPort  = "portnumber"
 *                pidFileName = "pidfilename"
 *                background  = "true"
 *  />
 *  </code></pre>
 *
 *  The DTD for the above is:
 *  <pre><code>
 *  <!ELEMENT xmlRpcDaemon EMPTY >
 *  <!ATTLIST xmlRpcDaemon xmlRpcHost   CDATA          #REQUIRED >
 *  <!ATTLIST xmlRpcDaemon xmlRpcPort   NMTOKEN        #REQUIRED >
 *  <!ATTLIST xmlRpcDaemon pidFileName  CDATA          #REQUIRED >
 *  <!ATTLIST xmlRpcDaemon background   (true|false)   "true"    >
 *  </code></pre>
 *
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class XmlRpcDaemon
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  The host the XML-RPC server the daemon is
         *  listening on.
         */
        std::string             xmlRpcHost;

        /**
         *  The port the XML-RPC server the daemon is
         *  listening on.
         */
        unsigned int            xmlRpcPort;

        /**
         *  The name of the file where the daemon saves it's process id when
         *  running.
         */
        std::string             pidFileName;

        /**
         *  Flag indicating if the singleton instnace has been
         *  configured already.
         */
        bool                    configured;

        /**
         *  Flag indicating wether to fork into background as a daemon
         *  or don't (good for debugging, for example).
         *  Defaults to true.
         */
        bool                    background;

        /**
         *  The XML-RPC server running within the Scheduler Daemon.
         */
        Ptr<XmlRpcServer>::Ref xmlRpcServer;

        /**
         *  Do all the necessary tasks of becoming a daemon.
         *
         *  @return true if we're in the daemon process, false
         *          if we're in the parent process that should not continue
         *  @exception std::runtime_error on forking errors
         */
        bool
        daemonize(void)                         throw (std::runtime_error);

        /**
         *  Save the current process id to the process id file
         */
        void
        savePid(void)                                   throw();

        /**
         *  Return the saved process id.
         *
         *  @return the saved process id of the daemon, or 0 if none saved.
         */
        pid_t
        loadPid(void)                                   throw();

    protected:
        /**
         *  Default constructor.
         */
        XmlRpcDaemon (void)                             throw ()
        {
            background = true;
            configured = false;
            xmlRpcServer.reset(new XmlRpcServer());
        }

        /**
         *  Virtual destructor.
         */
        virtual
        ~XmlRpcDaemon(void)                             throw ()
        {
        }

        /**
         *  Check if the daemon has already been configured,
         *  and raise an exception if not so.
         *
         *  @exception std::logic_error if the daemon has not yet been
         *             configured.
         */
        void
        checkForConfiguration(void) const       throw (std::logic_error)
        {
            if (!configured) {
                throw std::logic_error("not yet configured");
            }
        }

        /**
         *  Configure the XML-RPC daemon itself. Pass an &lt;xmlRpcDaemon&gt;
         *  element to this function.
         *
         *  @param element the XML element to configure the XML-RPC
         *                 daemon from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the daemon has already
         *             been configured.
         */
        void
        configureXmlRpcDaemon(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);


        /**
         *  Register your XML-RPC functions by implementing this function.
         */
        virtual void
        registerXmlRpcFunctions(Ptr<XmlRpcServer>::Ref  xmlRpcServer)
                                                    throw (std::logic_error)
                                                                          = 0;

    public:
        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                      throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Tell if the daemon has already been configured.
         *  If so, an attempt to configure it again will result in an
         *  exception.
         *
         *  @return true if the daemon has already been
         *          configured, false otherwise.
         */
        const bool
        isConfigured(void) const                        throw ()
        {
            return configured;
        }

        /**
         *  Set if the XML-RPC server should fork into background.
         *
         *  @param background if true, the XML-RPC server will fork into
         *         background, otherwise not (good for debugging).
         */
        void
        setBackground(const bool    background)         throw ()
        {
            this->background = background;
        }

        /**
         *  Tell if the XML-RPC server will fork into background.
         *
         *  @return if true, the XML-RPC server will fork into
         *         background, otherwise not (good for debugging).
         */
        const bool
        getBackground(void) const                       throw ()
        {
            return background;
        }

        /**
         *  Configure the daemon based on the XML element
         *  supplied.
         *  Implemtors should call the funtion configureXmlRpcDaemon
         *  from here with a proper &lt;xmlRpcDaemon&gt; element.
         *
         *  @param element the XML element to configure the 
         *                 daemon from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the daemon has already
         *             been configured.
         *  @see #configureXmlRpcDaemon
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
                                                                          = 0;

        /**
         *  Tell the host name of the XML-RPC server the 
         *  daemon is listening on.
         *
         *  @return the host name of the XML-RPC server the 
         *          daemon is listening on.
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        const std::string
        getXmlRpcHost(void) const                       throw (std::logic_error)
        {
            checkForConfiguration();
            return xmlRpcHost;
        }

        /**
         *  Tell the port of the XML-RPC server the 
         *  daemon is listening on.
         *
         *  @return the port of the XML-RPC server the 
         *          daemon is listening on.
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        const unsigned int
        getXmlRpcPort(void) const                       throw (std::logic_error)
        {
            checkForConfiguration();
            return xmlRpcPort;
        }

        /**
         *  Tell the file name where the daemon stores its process id.
         *
         *  @return the name of the file where the process id is stored.
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        const std::string
        getPidFileName(void) const                      throw (std::logic_error)
        {
            checkForConfiguration();
            return pidFileName;
        }

        /**
         *  Start the daemon.
         *
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        void
        start (void)                                throw (std::logic_error);

        /**
         *  Tell if the daemon is running.
         *  If there is a stale pid file stored for the daemon, it is
         *  removed during checking (and correctly false is returned).
         *
         *  @return true of the daemon is running, false otherwise.
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        bool
        isRunning (void)                            throw (std::logic_error);

        /**
         *  Stop the daemon.
         *
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        void
        stop (void)                                 throw (std::logic_error);

        /**
         *  Shut down the daemon.
         *  This function is public only because the signal handler
         *  needs visibility to this function, which will call it.
         *
         *  @exception std::logic_error if the daemon has not
         *             yet been configured.
         */
        void
        shutdown (void)                             throw (std::logic_error);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // XmlRpcDaemon_h

