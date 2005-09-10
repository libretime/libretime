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

#include "LiveSupport/SchedulerClient/SchedulerClientFactory.h"
#include "SchedulerDaemonXmlRpcClient.h"

using namespace LiveSupport::Core;
using namespace LiveSupport::SchedulerClient;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string SchedulerClientFactory::configElementNameStr =
                                                    "schedulerClientFactory";

/*------------------------------------------------------------------------------
 *  The singleton instance of SchedulerClientFactory
 *----------------------------------------------------------------------------*/
Ptr<SchedulerClientFactory>::Ref SchedulerClientFactory::singleton;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to SchedulerClientFactory
 *----------------------------------------------------------------------------*/
Ptr<SchedulerClientFactory>::Ref
SchedulerClientFactory :: getInstance(void)                   throw ()
{
    if (!singleton.get()) {
        singleton.reset(new SchedulerClientFactory());
    }

    return singleton;
}


/*------------------------------------------------------------------------------
 *  Configure the test authentication client.
 *----------------------------------------------------------------------------*/
void
SchedulerClientFactory :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    schedulerClient.reset();

    // try to look for a SchedulerDaemonXmlRpcClient configuration element
    xmlpp::Node::NodeList   nodes =
        element.get_children(
                        SchedulerDaemonXmlRpcClient::getConfigElementName());
    if (nodes.size() >= 1) {
        const xmlpp::Element  * configElement =
                         dynamic_cast<const xmlpp::Element*> (*(nodes.begin()));

        Ptr<SchedulerDaemonXmlRpcClient>::Ref   client;
        client.reset(new SchedulerDaemonXmlRpcClient());
        client->configure(*configElement);

        schedulerClient = client;
        return;
    }

    throw std::invalid_argument("no scheduler client configuration found");
}

