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
#ifndef BackupFactory_h
#define BackupFactory_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include <stdexcept>

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/Installable.h"
#include "BackupInterface.h"


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The factory to create backup objects.
 *
 *  Backup objects are objects which implement the BackupInterface interface,
 *  so they can create and restore schedule backups.
 *
 *  This object has to be configured with an element that contains
 *  the configuration element that the factory should build.
 *  Currently only PostgresqlBackup is supported by this factory.
 *
 *  An example configuration element is the following:
 *
 *  <pre><code>
 *      &lt;backupFactory&gt;
 *          &lt;postgresqlBackup/&gt;
 *      &lt;/backupFactory&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT backupFactory (postgresqlBackup) &gt;
 *  </code></pre>
 *
 *  For details on the &lt;postgresqlBackup&gt; element, see the
 *  PostgresqlBackup documentation.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see PostgresqlBackup
 */
class BackupFactory : virtual public Configurable,
                      virtual public Installable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string            configElementNameStr;

        /**
         *  The singleton instance of this object.
         */
        static Ptr<BackupFactory>::Ref      singleton;

        /**
         *  The backup created by this factory.
         */
        Ptr<BackupInterface>::Ref           backup;

        /**
         *  The default constructor.
         */
        BackupFactory(void)                                         throw()
        {
        }


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~BackupFactory(void)                                        throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                                  throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Returns the singleton instance of this object.
         *
         *  @return the singleton instance of this object.
         */
        static Ptr<BackupFactory>::Ref
        getInstance()                                               throw ();

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
         *  Install the component.
         *  This step involves creating the environment in which the component
         *  will run. This may be creation of coniguration files,
         *  database tables, etc.
         *
         *  @exception std::exception on installation problems,
         *             especially if the BackupFactory was not yet configured.
         */
        virtual void
        install(void)                           throw (std::exception);

        /**
         *  Check to see if the component has already been installed.
         *
         *  @return true if the component is properly installed,
         *          false otherwise
         *  @exception std::exception on generic problems
         */
        virtual bool
        isInstalled(void)                       throw (std::exception);

        /**
         *  Uninstall the component.
         *  Removes all the resources created in the install step.
         *
         *  @exception std::exception on unistallation problems,
         e             especially if the BackupFactory was not yet configured.
         */
        virtual void
        uninstall(void)                         throw (std::exception);

        /**
         *  Return a backup.
         *
         *  @return the appropriate backup, according to the
         *          configuration of this factory.
         */
        Ptr<BackupInterface>::Ref
        getBackup(void)                                             throw ()
        {
            return backup;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Storage
} // namespace LiveSupport

#endif // BackupFactory_h

