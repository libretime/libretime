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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/src/SchedulerDaemonXmlRpcClient.h,v $

------------------------------------------------------------------------------*/
#ifndef SchedulerDaemonXmlRpcClient_h
#define SchedulerDaemonXmlRpcClient_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>

#include <XmlRpcClient.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/SchedulerClient/SchedulerClientInterface.h"


namespace LiveSupport {
namespace SchedulerClient {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An XML-RPC client to the Scheduler Daemon.
 *
 *  This object has to be configured with an XML configuration element
 *  called schedulerDaemonXmlRpcClient.
 *
 *  A schedulerDaemonXmlRpcClient configuration element may look like the
 *  following:
 *
 *  <pre><code>
 *  &lt;schedulerDaemonXmlRpcClient xmlRpcHost = "localhost"
 *                                  xmlRpcPort = "3344"
 *                                  xmlRpcUri  = "/RC2"
 *  /&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  <!ELEMENT schedulerDaemonXmlRpcClient   EMPTY >
 *  <!ATTLIST schedulerDaemonXmlRpcClient xmlRpcHost    CDATA       #REQUIRED >
 *  <!ATTLIST schedulerDaemonXmlRpcClient xmlRpcPort    NMTOKEN     #REQUIRED >
 *  <!ATTLIST schedulerDaemonXmlRpcClient xmlRpcUri     CDATA       #REQUIRED >
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class SchedulerDaemonXmlRpcClient :
                    virtual public Configurable,
                    virtual public SchedulerClientInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by 
         *  SchedulerDaemonXmlRpcClient
         */
        static const std::string    configElementNameStr;

        /**
         *  The host name of the schedulers XML-RPC interface.
         */
        Ptr<std::string>::Ref       xmlRpcHost;

       /**
        *  The port of the schedulers XML-RPC interface.
        */
        unsigned int                xmlRpcPort;

        /**
         *  The URI to send to the schedulers XML-RPC interface.
         */
        Ptr<std::string>::Ref       xmlRpcUri;


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~SchedulerDaemonXmlRpcClient(void)                  throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                          throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Configure the object based on the XML element supplied.
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the scheduler daemon has already
         *             been configured, and can not be reconfigured.
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  Return the version string for the scheduler this client
         *  is connected to.
         *
         *  @return the version string of the scheduler daemon.
         */
        virtual Ptr<const std::string>::Ref
        getVersion(void)                            throw ();

        /**
         *  Return the current time at the scheduler server.
         *
         *  @return the current time at the scheduler server.
         */
        virtual Ptr<const boost::posix_time::ptime>::Ref
        getSchedulerTime(Ptr<SessionId>::Ref    sessionId)      throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace SchedulerClient
} // namespace LiveSupport

#endif // SchedulerDaemonXmlRpcClient_h

