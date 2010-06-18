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

#ifdef HAVE_TIME_H
#include <time.h>
#else
#error need time.h
#endif


#include <string>

#include "ScheduleInterface.h"
#include "ScheduleFactory.h"
#include "LiveSupport/Core/XmlRpcTools.h"

#include "DisplayScheduleMethod.h"


using namespace boost;
using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of this XML-RPC method.
 *----------------------------------------------------------------------------*/
const std::string DisplayScheduleMethod::methodName = "displaySchedule";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int DisplayScheduleMethod::errorId = 1100;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
DisplayScheduleMethod :: DisplayScheduleMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}


/*------------------------------------------------------------------------------
 *  Execute the stop XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethod :: execute(XmlRpc::XmlRpcValue  & rootParameter,
                                 XmlRpc::XmlRpcValue  & returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    if (!rootParameter.valid() || rootParameter.size() != 1
                               || !rootParameter[0].valid()) {
        XmlRpcTools::markError(errorId+1, "invalid argument format", 
                               returnValue);
        return;
    }
    XmlRpc::XmlRpcValue      parameters = rootParameter[0];

    Ptr<SessionId>::Ref      sessionId;
    try{
        sessionId = XmlRpcTools::extractSessionId(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+20, 
                               "missing session ID argument",
                                returnValue);
        return;
    }

    Ptr<ptime>::Ref     fromTime;
    try {
        fromTime = XmlRpcTools::extractFromTime(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+2, "missing or invalid 'from' argument", 
                               returnValue);
        return;
    }

    Ptr<ptime>::Ref     toTime;
    try {
        toTime = XmlRpcTools::extractToTime(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+3, "missing or invalid 'to' argument", 
                               returnValue);
        return;
    }

    Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
    Ptr<ScheduleInterface>::Ref schedule = sf->getSchedule();

    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref  scheduleEntries
                            = schedule->getScheduleEntries(fromTime, toTime);

std::cout << "DisplayScheduleMethod scheduleEntries: " << scheduleEntries->size() << std::endl;

    XmlRpcTools::scheduleEntriesToXmlRpcValue(scheduleEntries, returnValue);
}
