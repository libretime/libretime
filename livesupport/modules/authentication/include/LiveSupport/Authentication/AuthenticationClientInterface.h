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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/include/LiveSupport/Authentication/AuthenticationClientInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Authentication_AuthenticationClientInterface_h
#define LiveSupport_Authentication_AuthenticationClientInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <glibmm/ustring.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Core/XmlRpcException.h"
#include "LiveSupport/Core/XmlRpcInvalidArgumentException.h"
#include "LiveSupport/Core/XmlRpcCommunicationException.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Core/XmlRpcMethodResponseException.h"

namespace LiveSupport {
namespace Authentication {

using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface for authentication clients.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.6 $
 */
class AuthenticationClientInterface
{
    public:
    
        /**
         *  Login to the authentication server.
         *  Returns a new session ID; in case of an error, throws
         *  AuthenticationException or one of its subclasses.
         *
         *  @param  login       the login to the server
         *  @param  password    the password to the server
         *  @exception XmlRpcCommunicationException problem with performing
         *                                          XML-RPC call
         *  @exception XmlRpcMethodFaultException XML-RPC method returned
         *                                        fault response
         *  @exception XmlRpcMethodResponseException response from XML-RPC
         *                                           method is incorrect
         *  @exception XmlRpcException other error 
         *                                     (TestAuthenticationClient only)
         *  @return the new session ID
         */
        virtual Ptr<SessionId>::Ref
        login(const std::string &login, const std::string &password)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Logout from the authentication server.
         *
         *  @param  sessionId the ID of the session to end
         *  @exception XmlRpcCommunicationException problem with performing
         *                                          XML-RPC call
         *  @exception XmlRpcMethodFaultException XML-RPC method returned
         *                                        fault response
         *  @exception XmlRpcMethodResponseException response from XML-RPC
         *                                           method is incorrect
         *  @exception XmlRpcException other error 
         *                                     (TestAuthenticationClient only)
         */
        virtual void
        logout(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Load a `user preferences' item from the server.
         *
         *  @param  sessionId the ID of the current session (from login())
         *  @param  key       the name of the item
         *
         *  @exception XmlRpcInvalidArgumentException
         *                    bad sessionId argument
         *  @exception XmlRpcCommunicationException
         *                    problem with performing XML-RPC call
         *  @exception XmlRpcMethodFaultException 
         *                    XML-RPC method returned fault response
         *  @exception XmlRpcMethodResponseException
         *                    response from XML-RPC method is incorrect
         *  @exception XmlRpcException other error 
         *                                     (TestAuthenticationClient only)
         */
        virtual Ptr<Glib::ustring>::Ref
        loadPreferencesItem(Ptr<SessionId>::Ref             sessionId,
                            const Glib::ustring &           key)
                                                throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Store a `user preferences' item on the server.
         *
         *  @param  sessionId the ID of the current session (from login())
         *  @param  key       the name of the item
         *  @param  value     the (new) value of the item
         *
         *  @exception XmlRpcInvalidArgumentException
         *                    bad sessionId or value argument
         *  @exception XmlRpcCommunicationException
         *                    problem with performing XML-RPC call
         *  @exception XmlRpcMethodFaultException 
         *                    XML-RPC method returned fault response
         *  @exception XmlRpcMethodResponseException
         *                    response from XML-RPC method is incorrect
         *  @exception XmlRpcException other error 
         *                                     (TestAuthenticationClient only)
         */
        virtual void
        savePreferencesItem(Ptr<SessionId>::Ref             sessionId,
                            const Glib::ustring &           key,
                            Ptr<const Glib::ustring>::Ref   value)
                                                throw (XmlRpcException)
                                                                        = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Authentication
} // namespace LiveSupport

#endif // LiveSupport_Authentication_AuthenticationClientInterface_h

