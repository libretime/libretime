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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/DisplayScheduleMethod.cxx,v $

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
 *  The name of the from member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string DisplayScheduleMethod::fromName = "from";

/*------------------------------------------------------------------------------
 *  The name of the to member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string DisplayScheduleMethod::toName = "to";


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
 *  Extract the from time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
DisplayScheduleMethod :: extractFrom(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(fromName)) {
        throw std::invalid_argument("no from part in parameter structure");
    }

    struct tm       tm = (struct tm) xmlRpcValue[fromName];
    gregorian::date date(tm.tm_year, tm.tm_mon, tm.tm_mday);
    time_duration   hours(tm.tm_hour, tm.tm_min, tm.tm_sec);
    Ptr<ptime>::Ref ptime(new ptime(date, hours));

    return ptime;
}


/*------------------------------------------------------------------------------
 *  Extract the to time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
DisplayScheduleMethod :: extractTo(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(toName)) {
        throw std::invalid_argument("no to part in parameter structure");
    }

    struct tm       tm = (struct tm) xmlRpcValue[toName];
    gregorian::date date(tm.tm_year, tm.tm_mon, tm.tm_mday);
    time_duration   hours(tm.tm_hour, tm.tm_min, tm.tm_sec);
    Ptr<ptime>::Ref ptime(new ptime(date, hours));

    return ptime;
}


/*------------------------------------------------------------------------------
 *  Convert a boost::posix_time::ptime to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethod :: ptimeToXmlRpcValue(
                            Ptr<const ptime>::Ref   ptime,
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                                throw ()
{
    gregorian::date           date  = ptime->date();
    posix_time::time_duration hours = ptime->time_of_day();
    struct tm                 time;

    time.tm_year  = date.year();
    time.tm_mon   = date.month();
    time.tm_mday  = date.day();
    time.tm_hour  = hours.hours();
    time.tm_min   = hours.minutes();
    time.tm_sec   = hours.seconds();
    // TODO: set tm_wday, tm_yday and tm_isdst fields as well

    xmlRpcValue = XmlRpc::XmlRpcValue(&time);
}

 
/*------------------------------------------------------------------------------
 *  Convert a vector of ScheduleEntries into an XML-RPC value.
 *  This function returns an XML-RPC array of XML-RPC structures.
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethod :: scheduleEntriesToXmlRpcValue(
                Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref scheduleEntries,
                XmlRpc::XmlRpcValue                           & returnValue)
                                                                        throw ()
{
    returnValue.setSize(scheduleEntries->size());
                            // a call to setSize() makes sure it's an XML-RPC
                            // array

    std::vector<Ptr<ScheduleEntry>::Ref>::iterator   it
                                                = scheduleEntries->begin();
    int                     arraySize = 0;
    while (it != scheduleEntries->end()) {
        Ptr<ScheduleEntry>::Ref     entry = *it;
        XmlRpc::XmlRpcValue         returnStruct;
        returnStruct["id"]         = (int) (entry->getId()->getId());
        returnStruct["playlistId"] = (int) (entry->getPlaylistId()->getId());

        XmlRpc::XmlRpcValue         time;
        ptimeToXmlRpcValue(entry->getStartTime(), time);
        returnStruct["start"]      = time;

        ptimeToXmlRpcValue(entry->getEndTime(), time);
        returnStruct["end"]        = time;

        returnValue[arraySize++] = returnStruct;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Execute the stop XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
DisplayScheduleMethod :: execute(XmlRpc::XmlRpcValue  & parameters,
                                 XmlRpc::XmlRpcValue  & returnValue)
                                                                    throw ()
{
    try {
        if (!parameters.valid()) {
            // TODO: mark error
            returnValue = XmlRpc::XmlRpcValue(false);
            return;
        }

        Ptr<ptime>::Ref     fromTime    = extractFrom(parameters[0]);
        Ptr<ptime>::Ref     toTime      = extractTo(parameters[0]);

        Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
        Ptr<ScheduleInterface>::Ref schedule = sf->getSchedule();

        Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref  scheduleEntries
                            = schedule->getScheduleEntries(fromTime, toTime);

        scheduleEntriesToXmlRpcValue(scheduleEntries, returnValue);

    } catch (std::invalid_argument &e) {
        // TODO: mark error
        returnValue = XmlRpc::XmlRpcValue(false);
        return;
    }
}

