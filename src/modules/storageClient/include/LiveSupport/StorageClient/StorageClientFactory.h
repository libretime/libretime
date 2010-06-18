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
#ifndef LiveSupport_Storage_StorageClientFactory_h
#define LiveSupport_Storage_StorageClientFactory_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/StorageClient/StorageClientInterface.h"


namespace LiveSupport {
namespace StorageClient {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The factory to create StorageClientInterface objects.
 *
 *  This object has to be configured with an XML configuration element
 *  called storageClientFactory. This element contains a child element
 *  specifying and configuring the kind of StorageClient that the
 *  factory builds. Currently TestStorageClient and WebStorageClient
 *  are supported.
 *
 *  A storageClientFactory configuration element may look like the following:
 *
 *  <pre><code>
 *  &lt;storageClientFactory&gt;
 *      &lt;testStorage&gt;
 *          ...
 *      &lt;/testStorage&gt;
 *  &lt;/storageClientFactory&gt;
 *  </code></pre>
 *
 *  or:
 *
 *  <pre><code>
 *  &lt;storageClientFactory&gt;
 *      &lt;webStorage&gt;
 *          ...
 *      &lt;/webStorage&gt;
 *  &lt;/storageClientFactory&gt;
 *  </code></pre>
 *
 *  For detais of the respective elements, see the documentation for the
 *  TestStorageClient and WebStorageClient classes.
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  <!ELEMENT storageClientFactory (testStorage|webStorage) >
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see TestStorageClient
 *  @see WebStorageClient
 */
class StorageClientFactory :
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
        static Ptr<StorageClientFactory>::Ref   singleton;

        /**
         *  The storage client created by this factory.
         */
        Ptr<StorageClientInterface>::Ref    storageClient;

        /**
         *  The default constructor.
         */
        StorageClientFactory(void)              throw()
        {
        }


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~StorageClientFactory(void)                     throw ()
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
         *  Returns the singleton instance of this object.
         *
         *  @return the singleton instance of this object.
         */
        static Ptr<StorageClientFactory>::Ref
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
         *  Return a storage client.
         *
         *  @return the appropriate storage client, according to the
         *          configuration of this factory.
         */
        Ptr<StorageClientInterface>::Ref
        getStorageClient(void)                  throw ()
        {
            return storageClient;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace StorageClient
} // namespace LiveSupport

#endif // LiveSupport_Storage_StorageClientFactory_h

