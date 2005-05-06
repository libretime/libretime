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
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/src/WebAuthenticationClient.h,v $

------------------------------------------------------------------------------*/
#ifndef WebAuthenticationClient_h
#define WebAuthenticationClient_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Authentication/AuthenticationClientInterface.h"


namespace LiveSupport {
namespace Authentication {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface to the authentication methods on the php authentication
 *  server (which is currently the same as the storage server).
 *
 *  This object has to be configured with an XML configuration element
 *  called webAuthentication. This element contains a child element
 *  specifying the location of the authentication server.
 *
 *  A webAuthentication configuration element may look like the following:
 *
 *  <pre><code>
 *  &lt;webAuthentication&gt;
 *      &lt;location
 *          server="localhost"
 *          port="80" 
 *          path="/livesupportStorageServer/xmlrpc/xrLocStor.php"
 *      /&gt;
 *  &lt;/webAuthentication&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT webAuthentication (location) &gt;
 *  &lt;!ELEMENT location EMPTY &gt;
 *  &lt;!ATTLIST location server   CDATA       #REQUIRED &gt;
 *  &lt;!ATTLIST location port     NMTOKEN     #REQUIRED &gt;
 *  &lt;!ATTLIST location path     CDATA       #REQUIRED &gt;
 *  </code></pre>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.8 $
 */
class WebAuthenticationClient :
                    virtual public Configurable,
                    virtual public AuthenticationClientInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by 
         *      WebAuthenticationClient
         */
        static const std::string    configElementNameStr;

        /**
         *  The name of the authentication server, e.g. 
         *  "myserver.mycompany.com".
         */
        std::string                 storageServerName;

        /**
         *  The port wher the authentication server is listening 
         *  (default is 80).
         */
        int                         storageServerPort;

        /**
         *  The path to the authentication server php page.
         */
        std::string                 storageServerPath;


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~WebAuthenticationClient(void)                 throw ()
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
                                                throw (std::invalid_argument);

        /**
         *  Return the version string from the storage.
         *
         *  @return the version string of the storage.
         *  @exception XmlRpcException if there is a problem with the XML-RPC
         *                             call.
         */
        virtual Ptr<const Glib::ustring>::Ref
        getVersion(void)                        throw (XmlRpcException);

        /**
         *  Login to the authentication server, using the data read from the
         *  configuration file.
         *  Returns a new session ID; in case of an error, throws one of three
         *  types of AuthenticationException.
         *  Note that an incorrect login or password is considered an error
         *  and will throw an XmlRpcMethodFaultException.
         *
         *  @param  login       the login to the server
         *  @param  password    the password to the server
         *  @exception XmlRpcCommunicationException problem with performing
         *                                          XML-RPC call
         *  @exception XmlRpcMethodFaultException XML-RPC method returned
         *                                        fault response
         *  @exception XmlRpcMethodResponseException response from XML-RPC
         *                                           method is incorrect
         *  @return the new session ID
         */
        virtual Ptr<SessionId>::Ref
        login(const std::string &login, const std::string &password)
                                                throw (XmlRpcException);

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
         *  @return true if logged out successfully, false if not
         */
        virtual void
        logout(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException);

        /**
         *  Load a `user preferences' item from the server.
         *
         *  @param  sessionId the ID of the current session (from login())
         *  @param  key       the name of the item
         *
         *  @exception std::invalid_argument
         *                    no such preference key found
         *  @exception XmlRpcInvalidArgumentException
         *                    bad sessionId argument
         *  @exception XmlRpcCommunicationException
         *                    problem with performing XML-RPC call
         *  @exception XmlRpcMethodFaultException 
         *                    XML-RPC method returned fault response
         *  @exception XmlRpcMethodResponseException
         *                    response from XML-RPC method is incorrect
         */
        virtual Ptr<Glib::ustring>::Ref
        loadPreferencesItem(Ptr<SessionId>::Ref             sessionId,
                            const Glib::ustring &           key)
                                                throw (XmlRpcException,
                                                       std::invalid_argument);

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
         */
        virtual void
        savePreferencesItem(Ptr<SessionId>::Ref             sessionId,
                            const Glib::ustring &           key,
                            Ptr<const Glib::ustring>::Ref   value)
                                                throw (XmlRpcException);

        /**
         *  Delete a `user preferences' item from the server.
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
         */
        virtual void
        deletePreferencesItem(Ptr<SessionId>::Ref           sessionId,
                              const Glib::ustring &         key)
                                                throw (XmlRpcException);

        /**
         *  Reset the list of preferences to its initial (empty) state.
         *
         *  @exception XmlRpcException if the server returns an error.
         */
        void
        reset(void)
                                                throw (XmlRpcException);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Authentication
} // namespace LiveSupport

#endif // WebAuthenticationClient_h

