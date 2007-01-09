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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "PostgresqlSchedule.h"
#include "ScheduleFactory.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Db;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string ScheduleFactory::configElementNameStr =
                                                        "scheduleFactory";

/*------------------------------------------------------------------------------
 *  The singleton instance of Scheduleactory
 *----------------------------------------------------------------------------*/
Ptr<ScheduleFactory>::Ref ScheduleFactory::singleton;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to ScheduleFactory
 *----------------------------------------------------------------------------*/
Ptr<ScheduleFactory>::Ref
ScheduleFactory :: getInstance(void)                    throw ()
{
    if (!singleton.get()) {
        singleton.reset(new ScheduleFactory());
    }

    return singleton;
}


/*------------------------------------------------------------------------------
 *  Configure the schedule factory.
 *----------------------------------------------------------------------------*/
void
ScheduleFactory :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    schedule.reset();

    Ptr<ConnectionManagerFactory>::Ref   cmf =
                                    ConnectionManagerFactory::getInstance();
    Ptr<ConnectionManagerInterface>::Ref cm = cmf->getConnectionManager();

    // try to look for a PostgresqlSchedule configuration element
    xmlpp::Node::NodeList   nodes =
               element.get_children(PostgresqlSchedule::getConfigElementName());
    if (nodes.size() >= 1) {
        const xmlpp::Element  * configElement =
                         dynamic_cast<const xmlpp::Element*> (*(nodes.begin()));
        Ptr<PostgresqlSchedule>::Ref     dbs(new PostgresqlSchedule(cm));
        dbs->configure(*configElement);
        schedule = dbs;
    }

    if (!schedule) {
        throw std::invalid_argument("no storage client factories to configure");
    }
}
