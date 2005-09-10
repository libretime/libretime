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

    const xmlpp::Attribute    * attribute;
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
    Ptr<const ptime>::Ref   result;

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
    Ptr<const ptime>::Ref   result;

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
    Ptr<const ptime>::Ref   result;

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
 *  Add an audio clip to a playlist.
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
SchedulerDaemonXmlRpcClient :: addAudioClipToPlaylist(
                                Ptr<SessionId>::Ref     sessionId,
                                Ptr<UniqueId>::Ref      playlistId,
                                Ptr<UniqueId>::Ref      audioClipId,
                                Ptr<time_duration>::Ref relativeOffset)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::playlistIdToXmlRpcValue(playlistId, xmlRpcParams);
    XmlRpcTools::audioClipIdToXmlRpcValue(audioClipId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("addAudioClipToPlaylist",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                    "cannot execute XML-RPC method 'addAudioClipToPlaylist'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'addAudioClipToPlaylist' returned "
                "error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    Ptr<UniqueId>::Ref      playlistElementId;
    try {
        playlistElementId = XmlRpcTools::extractPlaylistElementId(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return playlistElementId;
}


/*------------------------------------------------------------------------------
 *  Create a new playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
SchedulerDaemonXmlRpcClient :: createPlaylist(
                                Ptr<SessionId>::Ref  sessionId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("createPlaylist",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                    "cannot execute XML-RPC method 'createPlaylist'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'createPlaylist' returned "
                "error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    Ptr<Playlist>::Ref      playlist;
    try {
        playlist = XmlRpcTools::extractPlaylist(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Delete a playlist.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClient :: deletePlaylist(
                                Ptr<SessionId>::Ref     sessionId,
                                Ptr<UniqueId>::Ref      playlistId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::playlistIdToXmlRpcValue(playlistId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("deletePlaylist",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                    "cannot execute XML-RPC method 'deletePlaylist'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'deletePlaylist' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  Return an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
SchedulerDaemonXmlRpcClient :: displayAudioClip(
                                Ptr<SessionId>::Ref     sessionId,
                                Ptr<UniqueId>::Ref      audioClipId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::audioClipIdToXmlRpcValue(audioClipId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("displayAudioClip",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                    "cannot execute XML-RPC method 'displayAudioClip'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'displayAudioClip' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    Ptr<AudioClip>::Ref     audioClip;
    try {
        audioClip = XmlRpcTools::extractAudioClip(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return audioClip;
}


/*------------------------------------------------------------------------------
 *  Return a list of audio clips.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
SchedulerDaemonXmlRpcClient :: displayAudioClips(
                                Ptr<SessionId>::Ref     sessionId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("displayAudioClips",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                    "cannot execute XML-RPC method 'displayAudioClips'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'displayAudioClips' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref     audioClipVector;
    try {
        audioClipVector = XmlRpcTools::extractAudioClipVector(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return audioClipVector;
}


/*------------------------------------------------------------------------------
 *  Return a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
SchedulerDaemonXmlRpcClient :: displayPlaylist(
                                Ptr<SessionId>::Ref     sessionId,
                                Ptr<UniqueId>::Ref      playlistId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);
    XmlRpcTools::playlistIdToXmlRpcValue(playlistId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("displayPlaylist",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                    "cannot execute XML-RPC method 'displayPlaylist'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'displayPlaylist' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    Ptr<Playlist>::Ref     playlist;
    try {
        playlist = XmlRpcTools::extractPlaylist(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Return a list of playlists.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
SchedulerDaemonXmlRpcClient :: displayPlaylists(
                                Ptr<SessionId>::Ref     sessionId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue             xmlRpcParams;
    XmlRpcValue             xmlRpcResult;
    Ptr<const ptime>::Ref   result;

    XmlRpcClient            xmlRpcClient(xmlRpcHost->c_str(),
                                         xmlRpcPort,
                                         xmlRpcUri->c_str(),
                                         false);

    XmlRpcTools::sessionIdToXmlRpcValue(sessionId, xmlRpcParams);

    xmlRpcResult.clear();
    if (!xmlRpcClient.execute("displayPlaylists",
                              xmlRpcParams,
                              xmlRpcResult)) {
        throw Core::XmlRpcCommunicationException(
                    "cannot execute XML-RPC method 'displayPlaylists'");
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method 'displayPlaylists' returned error message:\n"
             << xmlRpcResult;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    xmlRpcClient.close();

    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref     playlistVector;
    try {
        playlistVector = XmlRpcTools::extractPlaylistVector(xmlRpcResult);
    } catch (std::invalid_argument &e) {
        throw Core::XmlRpcInvalidArgumentException(e);
    }

    return playlistVector;
}

