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
#ifndef PostresqlPlayLog_h
#define PostresqlPlayLog_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Db/ConnectionManagerInterface.h"
#include "PlayLogInterface.h"


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An object containing a log of the clips played in a PostreSQL database.
 *
 *  This object has to be configured with a simple empty element, as
 *  the following:
 *
 *  <pre><code>
 *      &lt;postgresqlPlayLog/&gt;
 *  </code></pre>
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT postgresqlPlayLog EMPTY &gt;
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class PostgresqlPlayLog : public Configurable,
                          public PlayLogInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  A SQL statement to check if the database can be accessed.
         */
        static const std::string    check1Stmt;

        /**
         *  A SQL statement to check if the log table exists.
         */
        static const std::string    logCountStmt;

        /**
         *  The SQL create statement used in the installation step.
         */
        static const std::string    createStmt;

        /**
         *  The SQL drop statement used in the uninstallation step.
         */
        static const std::string    dropStmt;

        /**
         *  The SQL statement for adding a play log entry.
         */
        static const std::string    addPlayLogEntryStmt;

        /**
         *  The SQL statement for getting the play log for a time interval
         */
        static const std::string    getPlayLogEntriesStmt;

        /**
         *  The database connection manager to use for connecting the
         *  database.
         */
        Ptr<Db::ConnectionManagerInterface>::Ref    cm;

        /**
         *  The default constructor.
         */
        PostgresqlPlayLog(void)                            throw()
        {
        }


    public:
        /**
         *  Construct a PostgresqlPlayLog.
         *
         *  @param cm the connection manager the PostgresqlPlayLog will use to
         *         connect to the database.
         */
        PostgresqlPlayLog(Ptr<Db::ConnectionManagerInterface>::Ref cm)
                                                           throw ()
        {
            this->cm = cm;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~PostgresqlPlayLog(void)                           throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                         throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Configure the object based on the XML element supplied.
         *  The supplied element is expected to be of the name
         *  returned by configElementName().
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
         *  @exception std::exception on installation problems.
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
         *  @exception std::exception on unistallation problems.
         */
        virtual void
        uninstall(void)                         throw (std::exception);

        /**
         *  Add a new entry to the play log.
         *
         *  @param audioClipId the audio clip played.
         *  @param timeStamp the time the clip was played (started).
         *  @return the id of the newly created play log entry.
         */
        virtual Ptr<UniqueId>::Ref
        addPlayLogEntry(Ptr<const UniqueId>::Ref   audioClipId,
                        Ptr<const ptime>::Ref      timeStamp)
                                                throw (std::invalid_argument);

        /**
         *  Return the list of play log entries for a specified time interval.
         *
         *  @param fromTime the start of the time of the interval queried,
         *          inclusive
         *  @param toTime to end of the time of the interval queried,
         *          non-inclusive
         *  @return a vector of the play log entries for the time region.
         */
        virtual Ptr<std::vector<Ptr<PlayLogEntry>::Ref> >::Ref
        getPlayLogEntries(Ptr<const ptime>::Ref  fromTime,
                          Ptr<const ptime>::Ref  toTime)
                                                throw (std::invalid_argument);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // PostresqlPlayLog_h

