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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/ResetStorageMethod.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Storage/StorageClientInterface.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Core/XmlRpcTools.h"

#include "ResetStorageMethod.h"


using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of this XML-RPC method.
 *----------------------------------------------------------------------------*/
const std::string ResetStorageMethod::methodName = "resetStorage";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int ResetStorageMethod::errorId = 3000;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
ResetStorageMethod :: ResetStorageMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}


/*------------------------------------------------------------------------------
 *  Execute the XML-RPC function call.
 *  (Overrides 'execute' in XmlRpcServerMethod.)
 *----------------------------------------------------------------------------*/
void
ResetStorageMethod :: execute(XmlRpc::XmlRpcValue  & rootParameter,
                              XmlRpc::XmlRpcValue  & returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    Ptr<StorageClientFactory>::Ref     scf
                                       = StorageClientFactory::getInstance();
    Ptr<StorageClientInterface>::Ref   storage
                                       = scf->getStorageClient(); 

    try {
        storage->reset();
    } catch (Core::XmlRpcException &e) {
        std::string eMsg = "storage reset() returned error:\n";
        eMsg += e.what();
        XmlRpcTools::markError(errorId+1, eMsg, returnValue);
        return;
    }
}

