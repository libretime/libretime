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
    Version  : $Revision: 1.10 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/src/TestAuthenticationClient.h,v $

------------------------------------------------------------------------------*/
#ifndef TestAuthenticationClient_h
#define TestAuthenticationClient_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
#include <set>

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
 *  A dummy authentication client.  It only supports one user, whose name and
 *  password are read from a configuration file.  It issues session IDs when
 *  login() is called, and deletes these session IDs when logout() is called.
 *  It also stores (key, value) pairs of user preferences for this one user.
 *
 *  This object has to be configured with an XML configuration element
 *  called testAuthentication. This element contains a child element
 *  specifying the login and password.
 *
 *  A testAuthentication configuration element may look like the following:
 *
 *  <pre><code>
 *  &lt;testAuthentication&gt;
 *      &lt;user
 *          login="root"
 *          password="q" 
 *      /&gt;
 *  &lt;/testAuthentication&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT testAuthentication (user) &gt;
 *  &lt;!ELEMENT user EMPTY &gt;
 *  &lt;!ATTLIST user login      CDATA      #REQUIRED &gt;
 *  &lt;!ATTLIST user password   CDATA      #REQUIRED &gt;
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.10 $
 */
class TestAuthenticationClient :
                    virtual public Configurable,
                    virtual public AuthenticationClientInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by 
         *      TestAuthenticationClient
         */
        static const std::string    configElementNameStr;

        /**
         *  The version string of the test storage client.
         */
        Ptr<const Glib::ustring>::Ref   versionString;

        /**
         *  The login name of the (one) authorized test user.
         */
        std::string                 userLogin;

       /**
        *  The password for the test user.
        */
        std::string                 userPassword;

       /**
        *  A type for the list of sessionId's.
        */
        typedef std::set<std::string>
                                    SessionIdListType;

       /**
        *  A list of the sessionId's we have issued.
        */
        SessionIdListType           sessionIdList;

       /**
        *  The number of the sessionId's we have issued.
        */
        int                         sessionCounter;

       /**
        *  A type for the list of user preferences.
        */
        typedef std::map<const Glib::ustring, Ptr<const Glib::ustring>::Ref>
                                    PreferencesType;

       /**
        *  A list of the user preferences items stored.
        */
        PreferencesType             preferences;


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~TestAuthenticationClient(void)                 throw ()
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
         *  Returns a new session ID; in case of an error, returns a
         *  null pointer.
         *  Note that an incorrect login or password is considered an error
         *  and will throw an XmlRpcException.
         *
         *  @return the new session ID
         *  @exception XmlRpcException login or password is incorrect
         *             (does not match those given in the configuration file)
         */
        virtual Ptr<SessionId>::Ref
        login(const std::string &login, const std::string &password)
                                                throw (XmlRpcException);

        /**
         *  Logout from the authentication server.
         *
         *  @param  sessionId the ID of the session to end
         *  @exception XmlRpcException the sessionId does not match
         *                                     one issued by login()
         */
        virtual void
        logout(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException);

        /**
         *  Load a `user preferences' item from the server.
         *
         *  @param  sessionId the ID of the current session (from login())
         *  @param  key       the name of the item
         *  @exception XmlRpcException invalid session ID 
         *                             or key does not match anything stored
         */
        virtual Ptr<Glib::ustring>::Ref
        loadPreferencesItem(Ptr<SessionId>::Ref             sessionId,
                            const Glib::ustring &           key)
                                                throw (XmlRpcException);

        /**
         *  Store a `user preferences' item on the server.
         *
         *  @param  sessionId the ID of the current session (from login())
         *  @param  key       the name of the item
         *  @param  value     the (new) value of the item
         *  @exception XmlRpcException invalid session ID
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
         *  @exception XmlRpcException invalid session ID, or no such key
         */
        virtual void
        deletePreferencesItem(Ptr<SessionId>::Ref           sessionId,
                              const Glib::ustring &         key)
                                                throw (XmlRpcException);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Authentication
} // namespace LiveSupport

#endif // TestAuthenticationClient_h

