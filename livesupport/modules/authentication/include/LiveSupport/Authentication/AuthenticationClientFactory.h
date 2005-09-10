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
#ifndef LiveSupport_Authentication_AuthenticationClientFactory_h
#define LiveSupport_Authentication_AuthenticationClientFactory_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Authentication/AuthenticationClientInterface.h"


namespace LiveSupport {
namespace Authentication {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The factory to create AuthenticationClientInterface objects.
 *
 *  This object has to be configured with an XML configuration element
 *  called authenticationClientFactory. This element contains a child element
 *  specifying and configuring the kind of AuthenticationClient that the
 *  factory builds. This client is either a TestAuthenticationClient or
 *  a WebAuthenticationClient, and the child element name is either
 *  testAuthentication or webAuthentication, correspondingly.
 *
 *  An authenticationClientFactory configuration element may look like 
 *  the following:
 *
 *  <pre><code>
 *  &lt;authenticationClientFactory&gt;
 *      &lt;webAuthentication&gt;
 *          ...
 *      &lt;/webAuthentication&gt;
 *  &lt;/authenticationClientFactory&gt;
 *  </code></pre>
 *
 *  For detais of the testAuthentication and webAuthentication elements, see the 
 *  documentation for the TestAuthenticationClient and WebAuthenticationClient
 *  classes.
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT authenticationClientFactory (testAuthentication|
 *                                         webAuthentication) &gt;
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see TestAuthenticationClient
 *  @see WebAuthenticationClient
 */
class AuthenticationClientFactory :
                        virtual public Configurable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string                        configElementNameStr;

        /**
         *  The singleton instance of this object.
         */
        static Ptr<AuthenticationClientFactory>::Ref    singleton;

        /**
         *  The authentication client created by this factory.
         */
        Ptr<AuthenticationClientInterface>::Ref         authenticationClient;

        /**
         *  The default constructor.
         */
        AuthenticationClientFactory(void)       throw()
        {
        }


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~AuthenticationClientFactory(void)      throw ()
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
        static Ptr<AuthenticationClientFactory>::Ref
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
         *  Return an authentication client.
         *
         *  @return the appropriate authentication client, according to the
         *          configuration of this factory.
         */
        Ptr<AuthenticationClientInterface>::Ref
        getAuthenticationClient(void)           throw ()
        {
            return authenticationClient;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Authentication
} // namespace LiveSupport

#endif // LiveSupport_Authentication_AuthenticationClientFactory_h

