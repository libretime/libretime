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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/LoginMethod.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include "LiveSupport/Authentication/AuthenticationClientInterface.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/Core/XmlRpcTools.h"

#include "LoginMethod.h"


using namespace LiveSupport::Authentication;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of this XML-RPC method.
 *----------------------------------------------------------------------------*/
const std::string LoginMethod::methodName = "login";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int LoginMethod::errorId = 2000;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
LoginMethod :: LoginMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}


/*------------------------------------------------------------------------------
 *  Execute the XML-RPC function call.
 *  (Overrides 'execute' in XmlRpcServerMethod.)
 *----------------------------------------------------------------------------*/
void
LoginMethod :: execute(XmlRpc::XmlRpcValue  & rootParameter,
                       XmlRpc::XmlRpcValue  & returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    if (!rootParameter.valid() || rootParameter.size() != 1
                               || !rootParameter[0].valid()) {
        XmlRpcTools::markError(errorId+1, "invalid argument format", 
                               returnValue);
        return;
    }
    XmlRpc::XmlRpcValue parameters = rootParameter[0];

    Ptr<std::string>::Ref   loginName;
    try{
        loginName = XmlRpcTools::extractLoginName(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+2, "missing login argument",
                               returnValue);
        return;
    }

    Ptr<std::string>::Ref   password;
    try{
        password = XmlRpcTools::extractPassword(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+3, "missing password argument",
                               returnValue);
        return;
    }

    Ptr<AuthenticationClientFactory>::Ref 
                acf             = AuthenticationClientFactory::getInstance();
    Ptr<AuthenticationClientInterface>::Ref 
                authentication  = acf->getAuthenticationClient(); 

    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login(*loginName, *password);
    } catch (Core::XmlRpcException &e) {
        std::string eMsg = "authentication login() returned error:\n";
        eMsg += e.what();
        XmlRpcTools::markError(errorId+4, eMsg, returnValue);
        return;
    }

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, returnValue);
}

