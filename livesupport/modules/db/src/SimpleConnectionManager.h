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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/db/src/SimpleConnectionManager.h,v $

------------------------------------------------------------------------------*/
#ifndef SimpleConnectionManager_h
#define SimpleConnectionManager_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include <LiveSupport/Core/Configurable.h>
#include <LiveSupport/Db/ConnectionManagerInterface.h>


namespace LiveSupport {
namespace Db {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A simple connection manager, basically a facade to the underlying
 *  odbc::DriverManager class.
 *  This class can be configured with the following XML element, containing
 *  the ODBC Data Source Name (DSN), ODBC user name and ODBC password
 *  the manager will connect with to the ODBC source. The XML element looks
 *  as follows:
 *
 *  <pre><code>
 *  <simpleConnectionManager    dsn      = "LiveSupport"
 *                              userName = "foo"
 *                              password = "bar"
 *  />
 *  </code></pre>
 *
 *  The DTD for the above XML structure is:
 *
 *  <pre><code>
 *  <!ELEMENT simpleConnectionManager   EMPTY >
 *  <!ATTLIST simpleConnectionManager   dsn         CDATA   #REQUIRED >
 *  <!ATTLIST simpleConnectionManager   userName    CDATA   #REQUIRED >
 *  <!ATTLIST simpleConnectionManager   password    CDATA   #REQUIRED >
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class SimpleConnectionManager :
                        virtual public Configurable,
                        virtual public ConnectionManagerInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  The ODBC Data Source Name this manager connects to.
         */
        std::string                 dsn;

        /**
         *  The user name to use when connecting to the ODBC DSN.
         */
        std::string                 userName;

        /**
         *  The password to use when connecting to the ODBC DSN.
         */
        std::string                 password;


    public:
        /**
         *  The default constructor.
         */
        SimpleConnectionManager(void)              throw ()
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~SimpleConnectionManager(void)              throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                      throw ()
        {
            return configElementNameStr;
        }

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
         *  Get a database connection from the manager.
         *  After use, the connection must be returned by calling
         *  returnConnection().
         *
         *  @return a database connection to the database this manager
         *          serves.
         *  @see #returnConnection
         */
        virtual Ptr<odbc::Connection>::Ref
        getConnection(void)                     throw (std::runtime_error);

        /**
         *  Return a database connection previously aquired by a call to
         *  getConnection(), after it is not needed anymore.
         *
         *  @param connection the connection to return.
         *  @see #getConnection
         */
        virtual void
        returnConnection(Ptr<odbc::Connection>::Ref connection)
                                                throw (std::runtime_error);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Db
} // namespace LiveSupport

#endif // SimpleConnectionManager_h

