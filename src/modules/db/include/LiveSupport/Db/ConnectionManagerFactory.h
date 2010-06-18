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
#ifndef LiveSupport_Db_ConnectionManagerFactory_h
#define LiveSupport_Db_ConnectionManagerFactory_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Db/ConnectionManagerInterface.h"


namespace LiveSupport {
namespace Db {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The factory to create appropriate ConnectionManager objects.
 *  This singleton class has to be configured with an XML element,
 *  describing the ConnectionManagerInterface that it should build
 *  and maintain. This is done by including the configuration element
 *  for the desired type of connection manager inside the configuration
 *  element for the factory.
 *
 *  Currently only the SimpleConnectionManager is supported, thus a
 *  configuration file may look like this:
 *
 *  <pre><code>
 *  &lt;connectionManagerFactory&gt;
 *      <simpleConnectionManager    dsn      = "LiveSupport"
 *                                  userName = "foo"
 *                                  password = "bar"
 *      />
 *  &lt;/connectionManagerFactory&gt;
 *  </code></pre>
 *
 *  The DTD for the above XML structure is:
 *
 *  <pre><code>
 *  <!ELEMENT connectionManagerFactory  (simpleConnectionManager) >
 *  </code></pre>
 *
 *  For the DTD and details of the simpleConnectionManager configuration
 *  element, see the SimpleConnectionManager documentation.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see SimpleConnectionManager
 */
class ConnectionManagerFactory :
                        virtual public Configurable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  The singleton instance of this object.
         */
        static Ptr<ConnectionManagerFactory>::Ref   singleton;

        /**
         *  The connection manager created by this factory.
         */
        Ptr<ConnectionManagerInterface>::Ref    connectionManager;

        /**
         *  The default constructor.
         */
        ConnectionManagerFactory(void)              throw()
        {
        }


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~ConnectionManagerFactory(void)             throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                  throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Returns the singleton instance of this object.
         *
         *  @return the singleton instance of this object.
         */
        static Ptr<ConnectionManagerFactory>::Ref
        getInstance()                                   throw ();

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
         *  Return a connection manager.
         *
         *  @return the appropriate connection manager, according to the
         *          configuration of this factory.
         */
        Ptr<ConnectionManagerInterface>::Ref
        getConnectionManager(void)                  throw ()
        {
            return connectionManager;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Db
} // namespace LiveSupport

#endif // LiveSupport_Db_ConnectionManagerFactory_h

