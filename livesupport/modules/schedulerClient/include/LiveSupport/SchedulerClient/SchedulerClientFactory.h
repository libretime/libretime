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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/include/LiveSupport/SchedulerClient/SchedulerClientFactory.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_SchedulerClient_SchedulerClientFactory_h
#define LiveSupport_SchedulerClient_SchedulerClientFactory_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

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
 *  The factory to create SchedulerClientInterface objects.
 *
 *  This object has to be configured with an XML configuration element
 *  called schedulerClientFactory. This element contains a child element
 *  specifying and configuring the kind of SchedulerClient that the
 *  factory builds.
 *  Currently only one kind of client, SchedulerDaemonXmlRpcClient is
 *  supported by this factory.
 *
 *  An schedulerClientFactory configuration element may look like 
 *  the following:
 *
 *  <pre><code>
 *  &lt;schedulerClientFactory&gt;
 *      &lt;schedulerDaemonXmlRpcClient&gt;
 *          ...
 *      &lt;/schedulerDaemonXmlRpcClient&gt;
 *  &lt;/schedulerClientFactory&gt;
 *  </code></pre>
 *
 *  For detais of the schedulerDaemonXmlRpcClient element, see the
 *  documentation for the SchedulerDaemonXmlRpcClient class.
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  <!ELEMENT schedulerClientFactory        (schedulerDaemonXmlRpcClient) >
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 *  @see SchedulerDaemonXmlRpcClient
 */
class SchedulerClientFactory : virtual public Configurable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string                        configElementNameStr;

        /**
         *  The singleton instance of this object.
         */
        static Ptr<SchedulerClientFactory>::Ref         singleton;

        /**
         *  The authentication client created by this factory.
         */
        Ptr<SchedulerClientInterface>::Ref              schedulerClient;

        /**
         *  The default constructor.
         */
        SchedulerClientFactory(void)                throw ()
        {
        }


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~SchedulerClientFactory(void)           throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)              throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Returns the singleton instance of this object.
         *
         *  @return the singleton instance of this object.
         */
        static Ptr<SchedulerClientFactory>::Ref
        getInstance()                           throw ();

        /**
         *  Configure the object based on the XML element supplied.
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the object has already
         *             been configured, and can not be reconfigured.
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  Return a scheduler client.
         *
         *  @return the appropriate scheduler client, according to the
         *          configuration of this factory.
         */
        Ptr<SchedulerClientInterface>::Ref
        getSchedulerClient(void)                throw ()
        {
            return schedulerClient;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace SchedulerClient
} // namespace LiveSupport

#endif // LiveSupport_SchedulerClient_SchedulerClientFactory_h

