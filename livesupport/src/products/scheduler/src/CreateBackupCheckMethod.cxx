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

#include <string>

#include "LiveSupport/Core/XmlRpcTools.h"
#include "LiveSupport/Core/XmlRpcException.h"
#include "BackupFactory.h"

#include "CreateBackupCheckMethod.h"


using namespace LiveSupport;
using namespace LiveSupport::Core;

using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of this XML-RPC method.
 *----------------------------------------------------------------------------*/
const std::string   CreateBackupCheckMethod::methodName = "createBackupCheck";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int           CreateBackupCheckMethod::errorId = 4100;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
CreateBackupCheckMethod :: CreateBackupCheckMethod (
                                Ptr<XmlRpc::XmlRpcServer>::Ref  xmlRpcServer)
                                                                    throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}


/*------------------------------------------------------------------------------
 *  Execute the upload playlist method XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
CreateBackupCheckMethod :: execute(XmlRpc::XmlRpcValue &     rootParameter,
                                  XmlRpc::XmlRpcValue &     returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    if (!rootParameter.valid() || rootParameter.size() != 1
                               || !rootParameter[0].valid()) {
        XmlRpcTools::markError(errorId+1, "invalid argument format", 
                               returnValue);
        return;
    }
    XmlRpc::XmlRpcValue         parameters = rootParameter[0];

    Ptr<Glib::ustring>::Ref     token;
    try{
        token = XmlRpcTools::extractToken(parameters);
        
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+2, 
                               "missing token argument",
                               returnValue);
        return;
    }

    Ptr<BackupFactory>::Ref     bf      = BackupFactory::getInstance();
    Ptr<BackupInterface>::Ref   backup  = bf->getBackup();
    
    Ptr<const Glib::ustring>::Ref       url;
    Ptr<const Glib::ustring>::Ref       path;
    Ptr<const Glib::ustring>::Ref       errorMessage;
    AsyncState                          state;
    try {
        state = backup->createBackupCheck(*token, url, path, errorMessage);
        
    } catch (Core::XmlRpcException &e) {
        XmlRpcTools::markError(errorId+10, e.what(), returnValue);
        return;
    }
    
    XmlRpcTools::backupStatusToXmlRpcValue(state, returnValue);
    
    if (state == AsyncState::finishedState) {
        if (url && path) {
            XmlRpcTools::urlToXmlRpcValue(url, returnValue);
            XmlRpcTools::pathToXmlRpcValue(path, returnValue);
        } else {
            XmlRpcTools::markError(errorId+11, 
                                   "missing url or path return value",
                                   returnValue);
            return;
        }
        
    } else if (state == AsyncState::failedState) {
        if (errorMessage) {
            XmlRpcTools::faultStringToXmlRpcValue(errorMessage, returnValue);
        } else {
            XmlRpcTools::markError(errorId+11, 
                                   "missing faultString return value",
                                   returnValue);
            return;
        }
    }
}

