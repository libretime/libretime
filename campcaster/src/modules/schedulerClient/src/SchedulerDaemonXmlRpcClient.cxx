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

#include <fstream>
#include <sstream>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/XmlRpcTools.h"
#include "LiveSupport/Core/XmlRpcInvalidArgumentException.h"
#include "LiveSupport/Core/XmlRpcCommunicationException.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Core/XmlRpcMethodResponseException.h"
#include "SchedulerDaemonXmlRpcClient.h"

using namespace boost::posix_time;
using namespace XmlRpc;

using namespace LiveSupport::Core;
using namespace LiveSupport::SchedulerClient;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  configuration file constants */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string SchedulerDaemonXmlRpcClient::configElementNameStr 
                                           = "schedulerDaemonXmlRpcClient";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the XML-RPC host
 *----------------------------------------------------------------------------*/
static const std::string    xmlRpcHostAttrName = "xmlRpcHost";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the XML-RPC port
 *----------------------------------------------------------------------------*/
static const std::string    xmlRpcPortAttrName = "xmlRpcPort";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the XML-RPC URI
 *----------------------------------------------------------------------------*/
static const std::string    xmlRpcUriAttrName = "xmlRpcUri";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the test storage client.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute = 0;
    std::stringstream           strStr;

    // get the XML-RPC host name
    if (!(attribute = element.get_attribute(xmlRpcHostAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += xmlRpcHostAttrName;
        throw std::invalid_argument(eMsg);
    }
    xmlRpcHost.reset(new std::string(attribute->get_value()));

    // get the XML-RPC port
    if (!(attribute = element.get_attribute(xmlRpcPortAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += xmlRpcPortAttrName;
        throw std::invalid_argument(eMsg);
    }
    strStr.str(attribute->get_value());
    strStr >> xmlRpcPort;

    // get the XML-RPC URI
    if (!(attribute = element.get_attribute(xmlRpcUriAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += xmlRpcUriAttrName;
        throw std::invalid_argument(eMsg);
    }
    xmlRpcUri.reset(new std::string(attribute->get_value()));
}


/*------------------------------------------------------------------------------
 *  Get the version string from the scheduler daemon
 *----------------------------------------------------------------------------*/
Ptr<const std::string>::Ref
SchedulerDaemonXmlRpcClient :: getVersion(void)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<std::string>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("getVersion", xmlRpcParams, xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                                "cannot execute XML-RPC method 'getVersion'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'getVersion' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    if (!xmlRpcResult.hasMember("version")
      || xmlRpcResult["version"].getType() != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'getVersion' returned unexpected value:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }

    result.reset(new std::string(xmlRpcResult["version"]));

    return result;
}


/*------------------------------------------------------------------------------
 *  Get the current time from the server.
 *----------------------------------------------------------------------------*/
Ptr<const ptime>::Ref
SchedulerDaemonXmlRpcClient :: getSchedulerTime(void)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("getSchedulerTime", xmlRpcParams, xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                            "cannot execute XML-RPC method 'getSchedulerTime'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'getSchedulerTime' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    if (!xmlRpcResult.hasMember("schedulerTime")
     || xmlRpcResult["schedulerTime"].getType() != XmlRpcValue::TypeDateTime) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'getSchedulerTime' returned unexpected value:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }

    struct tm   time = xmlRpcResult["schedulerTime"];

    try {
        result = TimeConversion::tmToPtime(&time);
    } catch (std::out_of_range &e) {
        throw Core::XmlRpcException("time conversion error", e);
    }

    return result;
}


/*------------------------------------------------------------------------------
 *  Schedule a playlist in the scheduler.
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
SchedulerDaemonXmlRpcClient :: uploadPlaylist(
                        Ptr<SessionId>::Ref                  sessionId,
                        Ptr<UniqueId>::Ref                   playlistId,
                        Ptr<boost::posix_time::ptime>::Ref   playtime)
                                                throw (Core::XmlRpcException)
{
    Ptr<UniqueId>::Ref  scheduleEntryId;

    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::playlistIdToXmlRpcValue(playlistId, xmlRpcParams);
    XmlRpcTools::playtimeToXmlRpcValue(playtime, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("uploadPlaylist", xmlRpcParams, xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                            "cannot execute XML-RPC method 'uploadPlaylist'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'uploadPlaylist' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    try {
        scheduleEntryId = XmlRpcTools::extractScheduleEntryId(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return scheduleEntryId;
}


/*------------------------------------------------------------------------------
 *  Return the scheduled items for a time interval
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
SchedulerDaemonXmlRpcClient :: displaySchedule(
                                Ptr<SessionId>::Ref     sessionId,
                                Ptr<ptime>::Ref         from,
                                Ptr<ptime>::Ref         to)
                                                throw (Core::XmlRpcException)
{
    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
	
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::fromTimeToXmlRpcValue(from, xmlRpcParams);
    XmlRpcTools::toTimeToXmlRpcValue(to, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("displaySchedule", xmlRpcParams, xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                            "cannot execute XML-RPC method 'displaySchedule'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'displaySchedule' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    try {
        entries = XmlRpcTools::extractScheduleEntries(xmlRpcResult);
std::cout << "SchedulerDaemonXmlRpcClient::displaySchedule entries: " << entries->size() << std::endl;
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return entries;
}


/*------------------------------------------------------------------------------
 *  Remove a scheduled entry from the schedule.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClient :: removeFromSchedule(
                                Ptr<SessionId>::Ref  sessionId,
                                Ptr<UniqueId>::Ref   scheduleEntryId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::scheduleEntryIdToXmlRpcValue(scheduleEntryId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("removeFromSchedule",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                        "cannot execute XML-RPC method 'removeFromSchedule'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'removeFromSchedule' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Start the schedule backup creation process.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
SchedulerDaemonXmlRpcClient :: createBackupOpen(
                                    Ptr<SessionId>::Ref         sessionId,
                                    Ptr<SearchCriteria>::Ref    criteria,
                                    Ptr<ptime>::Ref             fromTime,
                                    Ptr<ptime>::Ref             toTime) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::searchCriteriaToXmlRpcValue(criteria, xmlRpcParams);
    XmlRpcTools::fromTimeToXmlRpcValue(fromTime, xmlRpcParams);
    XmlRpcTools::toTimeToXmlRpcValue(toTime, xmlRpcParams);

    if (!xmlRpcClient.execute("createBackupOpen", 
                              xmlRpcParams, 
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                        "cannot execute XML-RPC method 'createBackupOpen'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'createBackupOpen' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    Ptr<Glib::ustring>::Ref     token;
    try {
        token = XmlRpcTools::extractToken(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcMethodResponseException(e);
    }

    return token;
}


/*------------------------------------------------------------------------------
 *  Check on the progress of the schedule backup creation process.
 *----------------------------------------------------------------------------*/
AsyncState
SchedulerDaemonXmlRpcClient :: createBackupCheck(
                        const Glib::ustring &             token,
                        Ptr<const Glib::ustring>::Ref &   url,
                        Ptr<const Glib::ustring>::Ref &   path,
                        Ptr<const Glib::ustring>::Ref &   errorMessage) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    Ptr<const Glib::ustring>::Ref   tokenPtr(new const Glib::ustring(token));
    XmlRpcTools::tokenToXmlRpcValue(tokenPtr, xmlRpcParams);

    if (!xmlRpcClient.execute("createBackupCheck",
                              xmlRpcParams, 
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                        "cannot execute XML-RPC method 'createBackupCheck'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'createBackupCheck' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    AsyncState      state;
    try {
        state = XmlRpcTools::extractBackupStatus(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcMethodResponseException(e);
    }
    
    if (state == AsyncState::finishedState) {
        try {
            url  = XmlRpcTools::extractUrl(xmlRpcResult);
            path = XmlRpcTools::extractPath(xmlRpcResult);
        } catch (std::invalid_argument &e) {
            throw Core::XmlRpcMethodResponseException(e);
        }
    } else if (state == AsyncState::failedState) {
        try {
            errorMessage = XmlRpcTools::extractFaultString(xmlRpcResult);
        } catch (std::invalid_argument &e) {
            throw Core::XmlRpcMethodResponseException(e);
        }
    }
    
    return state;
}


/*------------------------------------------------------------------------------
 *  
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClient :: createBackupClose(
                                    const Glib::ustring &       token) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);
    
    Ptr<const Glib::ustring>::Ref   tokenPtr(new const Glib::ustring(token));
    XmlRpcTools::tokenToXmlRpcValue(tokenPtr, xmlRpcParams);

    if (!xmlRpcClient.execute("createBackupClose",
                              xmlRpcParams, 
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                        "cannot execute XML-RPC method 'createBackupClose'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'createBackupClose' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Restore a schedule backup.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClient :: restoreBackup(
                                    Ptr<SessionId>::Ref             sessionId,
                                    Ptr<const Glib::ustring>::Ref   path)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::pathToXmlRpcValue(path, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("restoreBackup",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                        "cannot execute XML-RPC method 'restoreBackup'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'restoreBackup' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();
}


/*------------------------------------------------------------------------------
 *  Stop the scheduler's audio player.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClient :: stopCurrentlyPlaying(
                                            Ptr<SessionId>::Ref     sessionId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("stopCurrentlyPlaying",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                        "cannot execute XML-RPC method 'stopCurrentlyPlaying'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'stopCurrentlyPlaying'"
             << " returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();
}

