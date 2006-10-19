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

#include "SchedulerDaemon.h"
#include "BaseTestMethod.h"


using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The XML-RPC host to connect to.
 *----------------------------------------------------------------------------*/
std::string LiveSupport::Scheduler::BaseTestMethod::xmlRpcHost;

/*------------------------------------------------------------------------------
 *  The XML-RPC port number to connect to.
 *----------------------------------------------------------------------------*/
unsigned int LiveSupport::Scheduler::BaseTestMethod::xmlRpcPort;

/*------------------------------------------------------------------------------
 *  A flag to indicate if configuration has already been done.
 *----------------------------------------------------------------------------*/
bool LiveSupport::Scheduler::BaseTestMethod::configured = false;


/* ===============================================  local function prototypes */


/* =============================================================  module code */
                                                       
/*------------------------------------------------------------------------------
 *  Read configuration information.
 *----------------------------------------------------------------------------*/
void
LiveSupport::Scheduler::
BaseTestMethod :: configure(std::string configFileName)
                                                        throw (std::exception)
{
    if (!configured) {
        Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();

        try {
            std::auto_ptr<xmlpp::DomParser> 
                            parser(new xmlpp::DomParser(configFileName, true));
            const xmlpp::Document * document = parser->get_document();
            scheduler->configure(*(document->get_root_node()));
        } catch (std::invalid_argument &e) {
            std::cerr << "semantic error in configuration file" << std::endl
                      << e.what() << std::endl;
        } catch (xmlpp::exception &e) {
            std::cerr << "error parsing configuration file" << std::endl
                      << e.what() << std::endl;
        }

        xmlRpcHost = scheduler->getXmlRpcHost();
        xmlRpcPort = scheduler->getXmlRpcPort();
        configured = true;
    }
}

