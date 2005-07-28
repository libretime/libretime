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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.46 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/WebStorageClient.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif

#include <iostream>
#include <fstream>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>
#include <XmlRpcUtil.h>
#include <curl/curl.h>
#include <curl/easy.h>

#include "LiveSupport/Core/Md5.h"
#include "LiveSupport/Core/XmlRpcCommunicationException.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Core/XmlRpcMethodResponseException.h"
#include "LiveSupport/Core/XmlRpcInvalidArgumentException.h"
#include "LiveSupport/Core/XmlRpcIOException.h"
#include "LiveSupport/Core/XmlRpcInvalidDataException.h"
#include "LiveSupport/Core/TimeConversion.h"
#include "WebStorageClient.h"

using namespace boost::posix_time;
using namespace XmlRpc;

using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  configuration file constants */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string WebStorageClient::configElementNameStr = "webStorage";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the temp files
 *----------------------------------------------------------------------------*/
static const std::string    localTempStorageAttrName = "tempFiles";

/*------------------------------------------------------------------------------
 *  The name of the config child element for the storage server location
 *----------------------------------------------------------------------------*/
static const std::string    locationConfigElementName = "location";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the storage server name
 *----------------------------------------------------------------------------*/
static const std::string    locationServerAttrName = "server";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the storage server port
 *----------------------------------------------------------------------------*/
static const std::string    locationPortAttrName = "port";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the storage server php page
 *----------------------------------------------------------------------------*/
static const std::string    locationPathAttrName = "path";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  constants for the SMIL file */

/*------------------------------------------------------------------------------
 *  The XML version used to create the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    xmlVersion = "1.0";

/*------------------------------------------------------------------------------
 *  The name of the SMIL root node.
 *----------------------------------------------------------------------------*/
static const std::string    smilRootNodeName = "smil";

/*------------------------------------------------------------------------------
 *  The name of the SMIL language description attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilLanguageAttrName = "xmlns";

/*------------------------------------------------------------------------------
 *  The value of the SMIL language description attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilLanguageAttrValue
                            = "http://www.w3.org/2001/SMIL20/Language";

/*------------------------------------------------------------------------------
 *  The name of the body node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilBodyNodeName = "body";

/*------------------------------------------------------------------------------
 *  The name of the parallel audio clip list node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilParNodeName = "par";

/*------------------------------------------------------------------------------
 *  The name of the audio clip or playlist element node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilPlayableNodeName = "audio";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the URI of the Playable element.
 *----------------------------------------------------------------------------*/
static const std::string    smilPlayableUriAttrName = "src";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the relative offset of the element.
 *----------------------------------------------------------------------------*/
static const std::string    smilRelativeOffsetAttrName = "begin";

/*------------------------------------------------------------------------------
 *  The name of the animation element in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateNodeName = "animate";

/*------------------------------------------------------------------------------
 *  The name of the "attribute name" attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateNameAttrName = "attributeName";

/*------------------------------------------------------------------------------
 *  The value of the "attribute name" attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateNameAttrValue = "soundLevel";

/*------------------------------------------------------------------------------
 *  The name of the starting sound level % attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateFromAttrName = "from";

/*------------------------------------------------------------------------------
 *  The name of the ending sound level % attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateToAttrName = "to";

/*------------------------------------------------------------------------------
 *  The name of the "calculation mode" attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateCalcModeAttrName = "calcMode";

/*------------------------------------------------------------------------------
 *  The value of the "calculation mode" attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateCalcModeAttrValue = "linear";

/*------------------------------------------------------------------------------
 *  The name of the rel. offset of the start of the animation attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateBeginAttrName = "begin";

/*------------------------------------------------------------------------------
 *  The name of the rel. offset of the end of the animation attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateEndAttrName = "end";

/*------------------------------------------------------------------------------
 *  The name of the "what to do after done" attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateFillAttrName = "fill";

/*------------------------------------------------------------------------------
 *  The value of the "what to do after done" attribute of the animation element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAnimateFillAttrValue = "freeze";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: error reports */

/*------------------------------------------------------------------------------
 *  The name of the error code parameter in the returned struct
 *----------------------------------------------------------------------------*/
static const std::string    errorCodeParamName = "faultCode";

/*------------------------------------------------------------------------------
 *  The name of the error message parameter in the returned struct
 *----------------------------------------------------------------------------*/
static const std::string    errorMessageParamName = "faultString";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: resetStorage */

/*------------------------------------------------------------------------------
 *  The name of the get version method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getVersionMethodName = "locstor.getVersion";

/*------------------------------------------------------------------------------
 *  The name of version return parameter for getVersion
 *----------------------------------------------------------------------------*/
static const std::string    getVersionResultParamName = "version";

/*------------------------------------------------------------------------------
 *  The name of the reset storage method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    resetStorageMethodName 
                            = "locstor.resetStorage";

/*------------------------------------------------------------------------------
 *  The name of the audio clips result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    resetStorageAudioClipResultParamName = "audioclips";

/*------------------------------------------------------------------------------
 *  The name of the playlists result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    resetStoragePlaylistResultParamName = "playlists";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: search */

/*------------------------------------------------------------------------------
 *  The name of the search method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    searchMethodName 
                            = "locstor.searchMetadata";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    searchSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the search criteria parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    searchCriteriaParamName = "criteria";

/*------------------------------------------------------------------------------
 *  The name of the audio clips result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    searchAudioClipResultParamName = "audioClipResults";

/*------------------------------------------------------------------------------
 *  The name of the playlists result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    searchPlaylistResultParamName = "playlistResults";

/*------------------------------------------------------------------------------
 *  The name of the audio clip count parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    searchAudioClipCountParamName = "audioClipCnt";

/*------------------------------------------------------------------------------
 *  The name of the playlist count parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    searchPlaylistCountParamName = "playlistCnt";

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: browse */

/*------------------------------------------------------------------------------
 *  The name of the search method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    browseMethodName 
                            = "locstor.browseCategory";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    browseSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the metadata type parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    browseMetadataParamName = "category";

/*------------------------------------------------------------------------------
 *  The name of the search criteria parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    browseCriteriaParamName = "criteria";

/*------------------------------------------------------------------------------
 *  The name of the list of metadata values parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    browseResultParamName = "results";

/*------------------------------------------------------------------------------
 *  The name of the count parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    browseResultCountParamName = "cnt";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ playlist methods */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: createPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the create playlist method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    createPlaylistMethodName 
                            = "locstor.createPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    createPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the playlist unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    createPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    createPlaylistResultParamName = "plid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: existsPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the exists playlist method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    existsPlaylistMethodName 
                            = "locstor.existsPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    existsPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    existsPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    existsPlaylistResultParamName = "exists";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: getPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the opening 'get playlist' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getPlaylistOpenMethodName 
                            = "locstor.accessPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the closing 'get playlist' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getPlaylistCloseMethodName 
                            = "locstor.releasePlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    getPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the playlist unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    getPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    getPlaylistUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned (for open) or input (for close)
 *----------------------------------------------------------------------------*/
static const std::string    getPlaylistTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: editPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the 'edit playlist' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    editPlaylistMethodName 
                            = "locstor.editPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    editPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the playlist unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    editPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    editPlaylistUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    editPlaylistTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: savePlaylist */

/*------------------------------------------------------------------------------
 *  The name of the 'save playlist' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    savePlaylistMethodName 
                            = "locstor.savePlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    savePlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    savePlaylistTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the new playlist parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    savePlaylistNewPlaylistParamName = "newPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    savePlaylistResultParamName = "plid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: revertPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the 'revert playlist' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    revertPlaylistMethodName 
                            = "locstor.revertEditedPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    revertPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    revertPlaylistTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    revertPlaylistResultParamName = "plid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ audio clip methods */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: existsAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the exists audio clip method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipMethodName 
                            = "locstor.existsAudioClip";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipResultParamName = "exists";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: getAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the opening 'get audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipOpenMethodName 
                            = "locstor.downloadMetadataOpen";

/*------------------------------------------------------------------------------
 *  The name of the closing 'get audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipCloseMethodName 
                            = "locstor.downloadMetadataClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned (for open) or input (for close)
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: storeAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the opening 'store audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipOpenMethodName 
                            = "locstor.storeAudioClipOpen";

/*------------------------------------------------------------------------------
 *  The name of the closing 'store audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipCloseMethodName 
                            = "locstor.storeAudioClipClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter for both 'open' and 'close'
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the metadata file name parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipMetadataParamName = "metadata";

/*------------------------------------------------------------------------------
 *  The name of the binary file name parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipFileNameParamName = "fname";

/*------------------------------------------------------------------------------
 *  The name of the checksum of the binary file name in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipChecksumParamName = "chsum";

/*------------------------------------------------------------------------------
 *  The name of the URL parameter returned by the 'open' method
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter for both 'open' and 'close' methods
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: acquireAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the acquire audio clip method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    acquireAudioClipMethodName 
                            = "locstor.accessRawAudioData";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    acquireAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    acquireAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    acquireAudioClipUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    acquireAudioClipTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: releaseAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the release audio clip method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    releaseAudioClipMethodName 
                            = "locstor.releaseRawAudioData";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    releaseAudioClipTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    releaseAudioClipResultParamName = "status";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the web storage client.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(localTempStorageAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += localTempStorageAttrName;
        throw std::invalid_argument(eMsg);
    }

    localTempStorage = attribute->get_value();

    // read the storage server location
    xmlpp::Node::NodeList   childNodes 
                            = element.get_children(locationConfigElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it == childNodes.end()) {
        std::string eMsg = "missing ";
        eMsg += locationConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Element      * locationConfigElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
    if (!(attribute = locationConfigElement
                      ->get_attribute(locationServerAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += locationServerAttrName;
        throw std::invalid_argument(eMsg);
    }
    storageServerName = attribute->get_value();

    if (!(attribute = locationConfigElement
                      ->get_attribute(locationPortAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += locationPortAttrName;
        throw std::invalid_argument(eMsg);
    }
    std::stringstream   storageServerPortValue(attribute->get_value());
    storageServerPortValue >> storageServerPort;
    
    if (!(attribute = locationConfigElement
                      ->get_attribute(locationPathAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += locationPathAttrName;
        throw std::invalid_argument(eMsg);
    }
    storageServerPath = attribute->get_value();

    ++it;
    if (it != childNodes.end()) {
        std::string eMsg = "more than one ";
        eMsg += locationConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Return the version string of the test storage.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
WebStorageClient :: getVersion(void)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    // add a dummy parameter, as this is the only way to enforce parameters
    // to be of XML-RPC type struct
    parameters["dummy"] = 0;
    result.clear();
    if (!xmlRpcClient.execute(getVersionMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += getVersionMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getVersionMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (!result.hasMember(getVersionResultParamName)
            || result[getVersionResultParamName].getType() 
                                            != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getVersionMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    Ptr<Glib::ustring>::Ref     version(new Glib::ustring(
                                            result[getVersionResultParamName]));

    xmlRpcClient.close();

    return version;
}


/*------------------------------------------------------------------------------
 *  Create a new playlist.
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
WebStorageClient :: createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[createPlaylistSessionIdParamName] 
            = sessionId->getId();

    result.clear();
    if (!xmlRpcClient.execute(createPlaylistMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += createPlaylistMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << createPlaylistMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(createPlaylistResultParamName)
            || result[createPlaylistResultParamName].getType() 
                                            != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << createPlaylistMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    Ptr<UniqueId>::Ref          newId(new UniqueId(std::string(
                                    result[createPlaylistResultParamName] )));

    Ptr<const std::string>::Ref url, token;
    
    editPlaylistGetUrl(sessionId, newId, url, token);
    
    Ptr<time_duration>::Ref     playlength(new time_duration(0,0,0,0));
    Ptr<Playlist>::Ref          playlist(new Playlist(newId, playlength));
    playlist->setToken(token);
    
    editedPlaylists[newId->getId()] = std::make_pair(sessionId, playlist);
    savePlaylist(sessionId, playlist);
    
    token.reset();
    playlist->setToken(token);
    
    return playlist->getId();
}


/*------------------------------------------------------------------------------
 *  Tell if a playlist exists.
 *----------------------------------------------------------------------------*/
const bool
WebStorageClient :: existsPlaylist(Ptr<SessionId>::Ref sessionId,
                                   Ptr<UniqueId>::Ref  id) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[existsPlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[existsPlaylistPlaylistIdParamName] 
            = std::string(*id);
    
    result.clear();
    if (!xmlRpcClient.execute(existsPlaylistMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += existsPlaylistMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();
    
    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << existsPlaylistMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(existsPlaylistResultParamName) 
       || result[existsPlaylistResultParamName].getType() 
                                                != XmlRpcValue::TypeBoolean) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << existsPlaylistMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    return bool(result[existsPlaylistResultParamName]);
}
 

/*------------------------------------------------------------------------------
 *  Return a playlist to be displayed.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: getPlaylist(Ptr<SessionId>::Ref sessionId,
                                Ptr<UniqueId>::Ref  id) const
                                                throw (Core::XmlRpcException)
{
    EditedPlaylistsType::const_iterator
                    editIt = editedPlaylists.find(id->getId());

    if (editIt != editedPlaylists.end()                     // is being edited
            && (*editIt->second.first == *sessionId)) {        // by us
         return editIt->second.second;
    }

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[getPlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[getPlaylistPlaylistIdParamName] 
            = std::string(*id);
    
    result.clear();
    if (!xmlRpcClient.execute(getPlaylistOpenMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += getPlaylistOpenMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getPlaylistOpenMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(getPlaylistUrlParamName)
            || result[getPlaylistUrlParamName].getType() 
                                                != XmlRpcValue::TypeString
            || ! result.hasMember(getPlaylistTokenParamName)
            || result[getPlaylistTokenParamName].getType() 
                                                != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getPlaylistOpenMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    const std::string   url     = result[getPlaylistUrlParamName];
    const std::string   token   = result[getPlaylistTokenParamName];

    Ptr<Playlist>::Ref playlist(new Playlist(id));

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        playlist->configure(*root);

    } catch (std::invalid_argument &e) {
        throw XmlRpcInvalidDataException(
                                    "semantic error in playlist metafile");
    } catch (xmlpp::exception &e) {
        throw XmlRpcInvalidDataException(
                                    "error parsing playlist metafile");
    }

    parameters.clear();
    parameters[getPlaylistSessionIdParamName] = sessionId->getId();
    parameters[getPlaylistTokenParamName]     = token;
    
    result.clear();
    if (!xmlRpcClient.execute(getPlaylistCloseMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += getPlaylistCloseMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();
    
    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getPlaylistCloseMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    if (! result.hasMember(getPlaylistPlaylistIdParamName)
            || result[getPlaylistPlaylistIdParamName].getType() 
                                                    != XmlRpcValue::TypeString
            || std::string(result[getPlaylistPlaylistIdParamName])
                                                    != std::string(*id)) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getPlaylistCloseMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Return a playlist to be edited.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: editPlaylist(Ptr<SessionId>::Ref sessionId,
                                 Ptr<UniqueId>::Ref  id)
                                                throw (Core::XmlRpcException)
{
    if (editedPlaylists.find(id->getId()) != editedPlaylists.end()) {
        throw XmlRpcInvalidArgumentException("playlist is already"
                                             " being edited");
    }
    
    Ptr<const std::string>::Ref     url, token;

    editPlaylistGetUrl(sessionId, id, url, token);

    Ptr<Playlist>::Ref              playlist(new Playlist(id));
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(*url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        playlist->configure(*root);

    } catch (std::invalid_argument &e) {
        throw XmlRpcMethodResponseException(
                                    "semantic error in playlist metafile");
    } catch (xmlpp::exception &e) {
        throw XmlRpcMethodResponseException(
                                    "error parsing playlist metafile");
    }

    playlist->setToken(token);
    editedPlaylists[id->getId()] = std::make_pair(sessionId, playlist);

    return playlist;
}


void
WebStorageClient :: editPlaylistGetUrl(Ptr<SessionId>::Ref sessionId,
                                       Ptr<UniqueId>::Ref  id,
                                       Ptr<const std::string>::Ref& url,
                                       Ptr<const std::string>::Ref& token)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[editPlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[editPlaylistPlaylistIdParamName] 
            = std::string(*id);
    
    result.clear();
    if (!xmlRpcClient.execute(editPlaylistMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += editPlaylistMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << editPlaylistMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(editPlaylistUrlParamName)
            || result[editPlaylistUrlParamName].getType() 
                                                != XmlRpcValue::TypeString
            || ! result.hasMember(editPlaylistTokenParamName)
            || result[editPlaylistTokenParamName].getType() 
                                                != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << editPlaylistMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    url.reset(new const std::string(result[getPlaylistUrlParamName]));
    token.reset(new const std::string(result[getPlaylistTokenParamName]));
}


/*------------------------------------------------------------------------------
 *  Save a playlist after editing.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: savePlaylist(Ptr<SessionId>::Ref sessionId,
                                 Ptr<Playlist>::Ref  playlist)
                                                throw (Core::XmlRpcException)
{
    if (!playlist || !playlist->getToken()) {
        throw XmlRpcInvalidArgumentException("playlist has no token field");
    }
    
    EditedPlaylistsType::iterator
                    editIt = editedPlaylists.find(playlist->getId()->getId());
    
    if ((editIt == editedPlaylists.end()) 
            || *editIt->second.first != *sessionId) {
        throw XmlRpcInvalidArgumentException("savePlaylist() called without "
                                             "editPlaylist()");
    }
    editedPlaylists.erase(editIt);

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[savePlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[savePlaylistTokenParamName] 
            = *playlist->getToken();
    parameters[savePlaylistNewPlaylistParamName] 
            = std::string(*playlist->getXmlDocumentString());

    result.clear();
    if (!xmlRpcClient.execute(savePlaylistMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += savePlaylistMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << savePlaylistMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(savePlaylistResultParamName)
            || result[savePlaylistResultParamName].getType() 
                                        != XmlRpcValue::TypeString
            || std::string(result[savePlaylistResultParamName])
                                        != std::string(*playlist->getId())) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << savePlaylistMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    Ptr<const std::string>::Ref     nullpointer;
    playlist->setToken(nullpointer);
}


/*------------------------------------------------------------------------------
 *  Revert a playlist to its pre-editing state.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: revertPlaylist(Ptr<const Glib::ustring>::Ref playlistToken)
                                                throw (XmlRpcException)
{
    if (!playlistToken) {
        throw XmlRpcInvalidArgumentException("null pointer in argument");
    }
    
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[revertPlaylistSessionIdParamName]        // dummy parameter
            = "";
    parameters[revertPlaylistTokenParamName] 
            = *playlistToken;

    result.clear();
    if (!xmlRpcClient.execute(revertPlaylistMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += revertPlaylistMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << revertPlaylistMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(revertPlaylistResultParamName)
            || result[revertPlaylistResultParamName].getType() 
                                        != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << revertPlaylistMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: acquirePlaylist(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id) const
                                                throw (Core::XmlRpcException)
{
    Ptr<Playlist>::Ref      oldPlaylist = getPlaylist(sessionId, id);
    
    Ptr<time_duration>::Ref playlength = oldPlaylist->getPlaylength();
    Ptr<Playlist>::Ref      newPlaylist(new Playlist(UniqueId::generateId(),
                                                     playlength));
    Ptr<xmlpp::Document>::Ref
                        smilDocument(new xmlpp::Document(xmlVersion));
    xmlpp::Element    * smilRootNode 
                        = smilDocument->create_root_node(smilRootNodeName);
    smilRootNode->set_attribute(smilLanguageAttrName,
                                smilLanguageAttrValue);

    xmlpp::Element    * smilBodyNode
                        = smilRootNode->add_child(smilBodyNodeName);
    xmlpp::Element    * smilParNode
                        = smilBodyNode->add_child(smilParNodeName);
    
    Playlist::const_iterator it = oldPlaylist->begin();

    while (it != oldPlaylist->end()) {
        Ptr<PlaylistElement>::Ref   plElement = it->second;
        Ptr<time_duration>::Ref     relativeOffset
                                              = plElement->getRelativeOffset();
        Ptr<FadeInfo>::Ref          fadeInfo  = plElement->getFadeInfo();

        Ptr<Playable>::Ref playable;
        switch (plElement->getType()) {
            case PlaylistElement::AudioClipType :
                playable = acquireAudioClip(sessionId, plElement
                                                        ->getAudioClip()
                                                        ->getId());
                break;
            case PlaylistElement::PlaylistType :
                playable = acquirePlaylist(sessionId, plElement
                                                        ->getPlaylist()
                                                        ->getId());
                break;
            default :     // this should never happen
                throw XmlRpcInvalidArgumentException(
                                           "unexpected playlist element type "
                                           "(neither audio clip nor playlist)");
        }
        newPlaylist->addPlayable(playable, relativeOffset, fadeInfo);

        xmlpp::Element* smilPlayableNode
                        = smilParNode->add_child(smilPlayableNodeName);
        smilPlayableNode->set_attribute(
                        smilPlayableUriAttrName, 
                        *playable->getUri() );
        smilPlayableNode->set_attribute(
                        smilRelativeOffsetAttrName, 
                        *TimeConversion::timeDurationToSmilString(
                                                            relativeOffset ));

        if (fadeInfo) {
            Ptr<time_duration>::Ref     fadeIn  = fadeInfo->getFadeIn();
            Ptr<time_duration>::Ref     fadeOut = fadeInfo->getFadeOut();

            if (fadeIn) {
                xmlpp::Element* smilFadeInNode
                                = smilPlayableNode->add_child(
                                                        smilAnimateNodeName);
                smilFadeInNode->set_attribute(
                                    smilAnimateNameAttrName,
                                    smilAnimateNameAttrValue );
                smilFadeInNode->set_attribute(
                                    smilAnimateFromAttrName,
                                    "0%" );
                smilFadeInNode->set_attribute(
                                    smilAnimateToAttrName,
                                    "100%" );
                smilFadeInNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                smilFadeInNode->set_attribute(
                                    smilAnimateBeginAttrName,
                                    "0s" );
                smilFadeInNode->set_attribute(
                                    smilAnimateEndAttrName,
                                    *TimeConversion::timeDurationToSmilString(
                                                                fadeIn ));
                smilFadeInNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                smilFadeInNode->set_attribute(
                                    smilAnimateFillAttrName,
                                    smilAnimateFillAttrValue );
            }

            if (fadeOut) {
                xmlpp::Element* smilFadeOutNode
                                = smilPlayableNode->add_child(
                                                        smilAnimateNodeName);
                smilFadeOutNode->set_attribute(
                                    smilAnimateNameAttrName,
                                    smilAnimateNameAttrValue );
                smilFadeOutNode->set_attribute(
                                    smilAnimateFromAttrName,
                                    "100%" );
                smilFadeOutNode->set_attribute(
                                    smilAnimateToAttrName,
                                    "0%" );
                smilFadeOutNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                Ptr<time_duration>::Ref  playlength = playable->getPlaylength();
                Ptr<time_duration>::Ref  fadeBegin(new time_duration(
                                                    *playlength - *fadeOut ));
                smilFadeOutNode->set_attribute(
                                    smilAnimateBeginAttrName,
                                    *TimeConversion::timeDurationToSmilString(
                                                                fadeBegin ));
                smilFadeOutNode->set_attribute(
                                    smilAnimateEndAttrName,
                                    *TimeConversion::timeDurationToSmilString(
                                                                playlength ));
                smilFadeOutNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                smilFadeOutNode->set_attribute(
                                    smilAnimateFillAttrName,
                                    smilAnimateFillAttrValue );
            }
        }
        ++it;        
    }

    std::stringstream fileName;
    fileName << localTempStorage << std::string(*newPlaylist->getId())
             << "-" << std::rand() << ".smil";

    smilDocument->write_to_file_formatted(fileName.str(), "UTF-8");
   
    Ptr<std::string>::Ref   playlistUri(new std::string(fileName.str()));
    newPlaylist->setUri(playlistUri);
    return newPlaylist;
}


/*------------------------------------------------------------------------------
 *  Release a playlist.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releasePlaylist(Ptr<Playlist>::Ref  playlist) const
                                                throw (Core::XmlRpcException)
{
    if (! playlist->getUri()) {
        throw XmlRpcInvalidArgumentException("playlist URI not found");
    }
    
    std::ifstream ifs(playlist->getUri()->substr(7).c_str());
    if (!ifs) {                                              // cut of "file://"
        ifs.close();
        throw XmlRpcIOException("playlist temp file not found");
    }
    ifs.close();

    std::remove(playlist->getUri()->substr(7).c_str());
   
    std::string                 eMsg = "";
    Playlist::const_iterator    it   = playlist->begin();
    while (it != playlist->end()) {
        Ptr<PlaylistElement>::Ref   plElement = it->second;
        if (plElement->getType() == PlaylistElement::AudioClipType) {
            try {
                releaseAudioClip(it->second->getAudioClip());
            }
            catch (XmlRpcException &e) {
                eMsg += e.what();
                eMsg += "\n";
            }
            ++it;
        } else if (plElement->getType() == PlaylistElement::PlaylistType) {
            try {
                releasePlaylist(it->second->getPlaylist());
            }
            catch (XmlRpcException &e) {
                eMsg += e.what();
                eMsg += "\n";
            }
            ++it;
        } else {                      // this should never happen
                eMsg += "unexpected playlist element type\n";
        }        
    }

    Ptr<std::string>::Ref   nullPointer;
    playlist->setUri(nullPointer);

    if (eMsg != "") {
        eMsg.insert(0, "some playlist elements could not be released:\n");
        throw XmlRpcInvalidArgumentException(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Tell if an audio clip exists.
 *----------------------------------------------------------------------------*/
const bool
WebStorageClient :: existsAudioClip(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[existsAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[existsAudioClipAudioClipIdParamName] 
            = std::string(*id);
    
    result.clear();
    if (!xmlRpcClient.execute(existsAudioClipMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += existsAudioClipMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();
    
    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << existsAudioClipMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(existsAudioClipResultParamName) 
       || result[existsAudioClipResultParamName].getType() 
                                                != XmlRpcValue::TypeBoolean) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << existsAudioClipMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    return bool(result[existsAudioClipResultParamName]);
}
 

/*------------------------------------------------------------------------------
 *  Retrieve an audio clip from the storage.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: getAudioClip(Ptr<SessionId>::Ref sessionId,
                                 Ptr<UniqueId>::Ref  id) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[getAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[getAudioClipAudioClipIdParamName] 
            = std::string(*id);
    
    result.clear();
    if (!xmlRpcClient.execute(getAudioClipOpenMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += getAudioClipOpenMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getAudioClipOpenMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(getAudioClipUrlParamName)
            || result[getAudioClipUrlParamName].getType() 
                                                != XmlRpcValue::TypeString
            || ! result.hasMember(getAudioClipTokenParamName)
            || result[getAudioClipTokenParamName].getType() 
                                                != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getAudioClipOpenMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    const std::string   url     = result[getAudioClipUrlParamName];
    const std::string   token   = result[getAudioClipTokenParamName];

    Ptr<AudioClip>::Ref audioClip(new AudioClip(id));

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        audioClip->configure(*root);

    } catch (std::invalid_argument &e) {
        std::string eMsg = "semantic error in audio clip metafile:\n";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    } catch (xmlpp::exception &e) {
        std::string eMsg = "error parsing audio clip metafile";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    }

    parameters.clear();
    parameters[getAudioClipSessionIdParamName] = sessionId->getId();
    parameters[getAudioClipTokenParamName]     = token;
    
    result.clear();
    if (!xmlRpcClient.execute(getAudioClipCloseMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += getAudioClipCloseMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getAudioClipCloseMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    if (! result.hasMember(getAudioClipAudioClipIdParamName)
            || result[getAudioClipAudioClipIdParamName].getType() 
                                                    != XmlRpcValue::TypeString
            || std::string(result[getAudioClipAudioClipIdParamName])
                                                    != std::string(*id)) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getAudioClipCloseMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    return audioClip;
}


/*------------------------------------------------------------------------------
 *  Upload an audio clip to the local storage.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: storeAudioClip(Ptr<SessionId>::Ref sessionId,
                                   Ptr<AudioClip>::Ref audioClip)
                                                throw (Core::XmlRpcException)
{
    if (!audioClip || !audioClip->getUri()) {
        throw XmlRpcInvalidArgumentException(
                                        "binary audio clip file not found");
    }
    
    // temporary hack; we will expect an absolute file name from getUri()
    //   in the final version
    std::string     binaryFileName = audioClip->getUri()->substr(5);
    std::ifstream   ifs(binaryFileName.c_str());
    if (!ifs) {
        ifs.close();
        throw XmlRpcIOException("could not read audio clip");
    }
    std::string     md5string = Md5(ifs);
    ifs.close();

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[storeAudioClipSessionIdParamName] 
            = sessionId->getId();
    if (audioClip->getId()) {
        parameters[storeAudioClipAudioClipIdParamName]
            = std::string(*audioClip->getId());
    }
    parameters[storeAudioClipMetadataParamName] 
            = std::string(*audioClip->getXmlDocumentString());
    parameters[storeAudioClipFileNameParamName] 
            = std::string(*audioClip->getUri());
    parameters[storeAudioClipChecksumParamName] 
            = md5string;

    result.clear();
    if (!xmlRpcClient.execute(storeAudioClipOpenMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += storeAudioClipOpenMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << storeAudioClipOpenMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(storeAudioClipUrlParamName)
            || result[storeAudioClipUrlParamName].getType() 
                                            != XmlRpcValue::TypeString
            || ! result.hasMember(storeAudioClipTokenParamName)
            || result[storeAudioClipTokenParamName].getType() 
                                            != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << storeAudioClipOpenMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }


    std::string url     = std::string(result[storeAudioClipUrlParamName]);
    std::string token   = std::string(result[storeAudioClipTokenParamName]);

    FILE*   binaryFile      = fopen(binaryFileName.c_str(), "rb");
    if (!binaryFile) {
        throw XmlRpcIOException("Binary audio clip file not found.");
    }
    fseek(binaryFile, 0, SEEK_END);
    long    binaryFileSize  = ftell(binaryFile);
    rewind(binaryFile);

    CURL*    handle     = curl_easy_init();
    if (!handle) {
        throw XmlRpcCommunicationException("Could not obtain curl handle.");
    }
    
    int    status = curl_easy_setopt(handle, CURLOPT_READDATA, binaryFile);
    status |=   curl_easy_setopt(handle, CURLOPT_INFILESIZE, binaryFileSize); 
                                         // works for files of size up to 2 GB
    status |=   curl_easy_setopt(handle, CURLOPT_PUT, 1); 
    status |=   curl_easy_setopt(handle, CURLOPT_URL, url.c_str()); 
//  status |=   curl_easy_setopt(handle, CURLOPT_HEADER, 1);
//  status |=   curl_easy_setopt(handle, CURLOPT_ENCODING, "deflate");

    if (status) {
        throw XmlRpcCommunicationException("Could not set curl options.");
    }

    status =    curl_easy_perform(handle);

    if (status) {
        throw XmlRpcCommunicationException("Error uploading file.");
    }

    curl_easy_cleanup(handle);
    fclose(binaryFile);
    
    parameters.clear();
    parameters[storeAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[storeAudioClipTokenParamName] 
            = token;
    
    result.clear();
    if (!xmlRpcClient.execute(storeAudioClipCloseMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += storeAudioClipCloseMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << storeAudioClipCloseMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(storeAudioClipAudioClipIdParamName)
            || result[storeAudioClipAudioClipIdParamName].getType() 
                                        != XmlRpcValue::TypeString
            || (audioClip->getId()
               &&
               std::string(result[storeAudioClipAudioClipIdParamName])
                                        != std::string(*audioClip->getId()))) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << storeAudioClipCloseMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    if (!audioClip->getId()) {
        Ptr<UniqueId>::Ref  newId(new UniqueId(std::string(
                                result[storeAudioClipAudioClipIdParamName] )));
        audioClip->setId(newId);
    }
}


/*------------------------------------------------------------------------------
 *  Acquire resources for an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: acquireAudioClip(Ptr<SessionId>::Ref sessionId,
                                     Ptr<UniqueId>::Ref  id) const
                                                throw (Core::XmlRpcException)
{
    Ptr<AudioClip>::Ref  audioClip = getAudioClip(sessionId, id);

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[acquireAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[acquireAudioClipAudioClipIdParamName] 
            = std::string(*id);
    
    result.clear();
    if (!xmlRpcClient.execute(acquireAudioClipMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += acquireAudioClipMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << acquireAudioClipMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(acquireAudioClipUrlParamName)
            || result[acquireAudioClipUrlParamName].getType() 
                                                != XmlRpcValue::TypeString
            || ! result.hasMember(acquireAudioClipTokenParamName)
            || result[acquireAudioClipTokenParamName].getType() 
                                                != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << acquireAudioClipMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    Ptr<const std::string>::Ref uri(new const std::string(
                                    result[acquireAudioClipUrlParamName] ));
    Ptr<const std::string>::Ref token(new const std::string( 
                                    result[acquireAudioClipTokenParamName] ));

    audioClip->setUri(uri);
    audioClip->setToken(token);

    return audioClip;    
}


/*------------------------------------------------------------------------------
 *  Release an audio clip.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releaseAudioClip(Ptr<AudioClip>::Ref audioClip) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[releaseAudioClipTokenParamName] 
            = *audioClip->getToken();
    
    result.clear();
    if (!xmlRpcClient.execute(releaseAudioClipMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += releaseAudioClipMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << releaseAudioClipMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(releaseAudioClipResultParamName)
            || result[releaseAudioClipResultParamName].getType() 
                                                != XmlRpcValue::TypeBoolean) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << releaseAudioClipMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    if (! bool(result[releaseAudioClipResultParamName])) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << releaseAudioClipMethodName
             << "' returned 'false'";
        throw XmlRpcMethodResponseException(eMsg.str());
    }
    
    Ptr<const std::string>::Ref     nullpointer;
    audioClip->setToken(nullpointer);
    audioClip->setUri(nullpointer);
}


/*------------------------------------------------------------------------------
 *  Reset the storage to its initial state.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: reset(void)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters["dummy_param"] = "dummy_value"; 
    
    result.clear();
    if (!xmlRpcClient.execute(resetStorageMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += resetStorageMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << resetStorageMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(resetStorageAudioClipResultParamName)
            || result[resetStorageAudioClipResultParamName].getType() 
                                                != XmlRpcValue::TypeArray
            || ! result.hasMember(resetStoragePlaylistResultParamName)
            || result[resetStoragePlaylistResultParamName].getType() 
                                                != XmlRpcValue::TypeArray) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << resetStorageMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    editedPlaylists.clear();

    XmlRpcValue audioClipArray = result[resetStorageAudioClipResultParamName];
    audioClipIds.reset(new std::vector<Ptr<UniqueId>::Ref>);
    
    for (int i=0; i < audioClipArray.size(); i++) {
        if (audioClipArray[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string audio clip gunid returned by XML-RPC method '"
                 << resetStorageMethodName
                 << "':\n"
                 << result;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        Ptr<UniqueId>::Ref  uniqueId(new UniqueId(std::string(
                                                        audioClipArray[i])));
        audioClipIds->push_back(uniqueId);
    }

    XmlRpcValue playlistArray = result[resetStoragePlaylistResultParamName];
    playlistIds.reset(new std::vector<Ptr<UniqueId>::Ref>);
    
    for (int i=0; i < playlistArray.size(); i++) {
        if (playlistArray[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string playlist gunid returned by XML-RPC method '"
                 << resetStorageMethodName
                 << "':\n"
                 << result;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        Ptr<UniqueId>::Ref  uniqueId(new UniqueId(std::string(
                                                        playlistArray[i])));
        playlistIds->push_back(uniqueId);
    }
}


/*------------------------------------------------------------------------------
 *  Search for audio clips or playlists.
 *----------------------------------------------------------------------------*/
int
WebStorageClient :: search(Ptr<SessionId>::Ref      sessionId,
                           Ptr<SearchCriteria>::Ref searchCriteria) 
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[searchSessionIdParamName] 
            = sessionId->getId();
    parameters[searchCriteriaParamName] 
            = *searchCriteria;

    result.clear();
    if (!xmlRpcClient.execute(searchMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += searchMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << searchMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(searchAudioClipResultParamName)
            || result[searchAudioClipResultParamName].getType() 
                                                != XmlRpcValue::TypeArray
            || ! result.hasMember(searchPlaylistResultParamName)
            || result[searchPlaylistResultParamName].getType() 
                                                != XmlRpcValue::TypeArray) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << searchMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    XmlRpcValue audioClipArray = result[searchAudioClipResultParamName];
    audioClipIds.reset(new std::vector<Ptr<UniqueId>::Ref>);
    
    for (int i=0; i < audioClipArray.size(); i++) {
        if (audioClipArray[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string audio clip gunid returned by XML-RPC method '"
                 << searchMethodName
                 << "':\n"
                 << result;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        Ptr<UniqueId>::Ref  uniqueId(new UniqueId(std::string(
                                                        audioClipArray[i])));
        audioClipIds->push_back(uniqueId);
    }

    XmlRpcValue playlistArray = result[searchPlaylistResultParamName];
    playlistIds.reset(new std::vector<Ptr<UniqueId>::Ref>);
    
    for (int i=0; i < playlistArray.size(); i++) {
        if (playlistArray[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string playlist gunid returned by XML-RPC method '"
                 << searchMethodName
                 << "':\n"
                 << result;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        Ptr<UniqueId>::Ref  uniqueId(new UniqueId(std::string(
                                                        playlistArray[i])));
        playlistIds->push_back(uniqueId);
    }
    
    if (! result.hasMember(searchAudioClipCountParamName)
            || result[searchAudioClipCountParamName].getType() 
                                                != XmlRpcValue::TypeInt
            || ! result.hasMember(searchPlaylistCountParamName)
            || result[searchPlaylistCountParamName].getType() 
                                                != XmlRpcValue::TypeInt) {
        std::stringstream eMsg;
        eMsg << "Missing or bad count returned by XML-RPC method '" 
             << searchMethodName
             << "':\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    return int(result[searchAudioClipCountParamName])
           + int(result[searchPlaylistCountParamName]);
}


/*------------------------------------------------------------------------------
 *  Browse for metadata values.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Glib::ustring> >::Ref
WebStorageClient :: browse(Ptr<SessionId>::Ref              sessionId,
                           Ptr<const Glib::ustring>::Ref    metadataType,
                           Ptr<SearchCriteria>::Ref         searchCriteria) 
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[browseSessionIdParamName] 
            = sessionId->getId();
    parameters[browseMetadataParamName] 
            = std::string(*metadataType);
    parameters[browseCriteriaParamName] 
            = *searchCriteria;

    result.clear();
    if (!xmlRpcClient.execute(browseMethodName.c_str(),
                              parameters, result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += browseMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << browseMethodName
             << "' returned error message:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(browseResultParamName)
            || result[browseResultParamName].getType() 
                                                != XmlRpcValue::TypeArray) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << browseMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    XmlRpcValue     metadataValues = result[browseResultParamName];
    Ptr<std::vector<Glib::ustring> >::Ref 
                                    results(new std::vector<Glib::ustring>);
    
    for (int i=0; i < metadataValues.size(); i++) {
        if (metadataValues[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string metadata value returned by XML-RPC method '"
                 << browseMethodName
                 << "':\n"
                 << result;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        results->push_back(Glib::ustring(metadataValues[i]));
    }

    return results;
}


/*------------------------------------------------------------------------------
 *  Return a list of all playlists in the storage.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
WebStorageClient :: getAllPlaylists(Ptr<SessionId>::Ref sessionId,
                                    const int limit, const int offset)
                                                throw (XmlRpcException)
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria("playlist"));
    criteria->setLimit(limit);
    criteria->setOffset(offset);
    search(sessionId, criteria);
    
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref      playlists(
                                        new std::vector<Ptr<Playlist>::Ref>);
    
    std::vector<Ptr<UniqueId>::Ref>::const_iterator it, end;
    it  = getPlaylistIds()->begin();
    end = getPlaylistIds()->end();
    while (it != end) {
        playlists->push_back(getPlaylist(sessionId, *it));
        ++it;
    }
    
    return playlists;
}


/*------------------------------------------------------------------------------
 *  Return a list of all audio clips in the storage.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
WebStorageClient :: getAllAudioClips(Ptr<SessionId>::Ref sessionId,
                                     const int limit, const int offset)
                                                throw (XmlRpcException)
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria("audioClip"));
    criteria->setLimit(limit);
    criteria->setOffset(offset);
    search(sessionId, criteria);
    
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref     audioClips(
                                        new std::vector<Ptr<AudioClip>::Ref>);
    
    std::vector<Ptr<UniqueId>::Ref>::const_iterator it, end;
    it  = getAudioClipIds()->begin();
    end = getAudioClipIds()->end();
    while (it != end) {
        audioClips->push_back(getAudioClip(sessionId, *it));
        ++it;
    }

    return audioClips;
}

