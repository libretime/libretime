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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/StorageClientFactory.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


#include "LiveSupport/Storage/StorageClientFactory.h"
#include "TestStorageClient.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string StorageClientFactory::configElementNameStr =
                                                        "storageClientFactory";

/*------------------------------------------------------------------------------
 *  The singleton instance of StorageClientFactory
 *----------------------------------------------------------------------------*/
Ptr<StorageClientFactory>::Ref StorageClientFactory::singleton;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to StorageClientFactory
 *----------------------------------------------------------------------------*/
Ptr<StorageClientFactory>::Ref
StorageClientFactory :: getInstance(void)                   throw ()
{
    if (!singleton.get()) {
        singleton.reset(new StorageClientFactory());
    }

    return singleton;
}


/*------------------------------------------------------------------------------
 *  Configure the test storage client.
 *----------------------------------------------------------------------------*/
void
StorageClientFactory :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    storageClient.reset();

    // try to look for an TestStorageClient configuration element
    xmlpp::Node::NodeList   nodes =
               element.get_children(TestStorageClient::getConfigElementName());
    if (nodes.size() >= 1) {
        const xmlpp::Element  * configElement =
                         dynamic_cast<const xmlpp::Element*> (*(nodes.begin()));
        Ptr<TestStorageClient>::Ref     tsc(new TestStorageClient());
        tsc->configure(*configElement);
        storageClient = tsc;
    }

    if (!storageClient) {
        throw std::invalid_argument("no storage client factories to configure");
    }
}


