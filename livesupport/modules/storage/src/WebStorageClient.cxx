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
    Version  : $Revision: 1.14 $
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
 *  The name of the SMIL real networks extension attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilExtensionsAttrName = "xmlns:rn";

/*------------------------------------------------------------------------------
 *  The value of the SMIL real networks extension attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilExtensionsAttrValue
                            = "http://features.real.com/2001/SMIL20/Extensions";

/*------------------------------------------------------------------------------
 *  The name of the body node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilBodyNodeName = "body";

/*------------------------------------------------------------------------------
 *  The name of the sequential audio clip list node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilSeqNodeName = "seq";

/*------------------------------------------------------------------------------
 *  The name of the audio clip element node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilAudioClipNodeName = "audio";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the URI of the audio clip element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAudioClipUriAttrName = "src";


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
 *  The name of the reset storage method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    resetStorageMethodName 
                            = "locstor.resetStorage";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    resetStorageResultParamName = "gunids";


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
 *  The name of the get audio clip method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipOpenMethodName 
                            = "locstor.downloadMetadataOpen";

/*------------------------------------------------------------------------------
 *  The name of the get audio clip method on the storage server
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

// TODO: fix; this method does not exist any more

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
 *  Tell if a playlist exists.
 *----------------------------------------------------------------------------*/
const bool
WebStorageClient :: existsPlaylist(Ptr<SessionId>::Ref sessionId,
                                   Ptr<UniqueId>::Ref  id) const
                                                throw (StorageException)
{
    return false;
}
 

/*------------------------------------------------------------------------------
 *  Return a playlist to be displayed.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: getPlaylist(Ptr<SessionId>::Ref sessionId,
                                Ptr<UniqueId>::Ref  id) const
                                                throw (StorageException)
{
    Ptr<Playlist>::Ref  playlist(new Playlist);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Return a playlist to be edited.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: editPlaylist(Ptr<SessionId>::Ref sessionId,
                                 Ptr<UniqueId>::Ref  id) const
                                                throw (StorageException)
{
    Ptr<Playlist>::Ref  playlist(new Playlist);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Save a playlist after editing.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: savePlaylist(Ptr<SessionId>::Ref sessionId,
                                 Ptr<Playlist>::Ref  playlist) const
                                                throw (StorageException)
{
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: acquirePlaylist(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id) const
                                                throw (StorageException)
{
    Ptr<Playlist>::Ref  playlist(new Playlist);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Release a playlist.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releasePlaylist(Ptr<SessionId>::Ref sessionId,
                                    Ptr<Playlist>::Ref  playlist) const
                                                throw (StorageException)
{

}


/*------------------------------------------------------------------------------
 *  Delete a playlist.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: deletePlaylist(Ptr<SessionId>::Ref sessionId,
                                   Ptr<UniqueId>::Ref  id)
                                                throw (StorageException)
{

}


/*------------------------------------------------------------------------------
 *  Return a listing of all the playlists in the playlist store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
WebStorageClient :: getAllPlaylists(Ptr<SessionId>::Ref sessionId) const
                                                throw (StorageException)
{
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref  playlistVector(
                                        new std::vector<Ptr<Playlist>::Ref>);
    return playlistVector;
}


/*------------------------------------------------------------------------------
 *  Create a new playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw (StorageException)
{
    Ptr<Playlist>::Ref  playlist(new Playlist);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Tell if an audio clip exists.
 *----------------------------------------------------------------------------*/
const bool
WebStorageClient :: existsAudioClip(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id) const
                                                throw (StorageException)
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
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += existsAudioClipMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    
    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << existsAudioClipMethodName
             << "' returned error message:\n"
             << result;
        throw XmlRpcMethodFaultException(eMsg.str());
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
                                                throw (StorageException)
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
        throw XmlRpcMethodFaultException(eMsg.str());
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

    std::string         url     = result[getAudioClipUrlParamName];
    std::string         token   = result[getAudioClipTokenParamName];

    Ptr<AudioClip>::Ref audioClip(new AudioClip(id));

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        audioClip->configure(*root);

    } catch (std::invalid_argument &e) {
        throw XmlRpcMethodResponseException(
                                    "semantic error in audio clip metafile");
    } catch (xmlpp::exception &e) {
        throw XmlRpcMethodResponseException(
                                    "error parsing audio clip metafile");
    }

    parameters.clear();
    parameters[getAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[getAudioClipTokenParamName] 
            = token;
    
    result.clear();
    if (!xmlRpcClient.execute(getAudioClipCloseMethodName.c_str(),
                              parameters, result)) {
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += getAudioClipCloseMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getAudioClipCloseMethodName
             << "' returned error message:\n"
             << result;
        throw XmlRpcMethodFaultException(eMsg.str());
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
bool
WebStorageClient :: storeAudioClip(Ptr<SessionId>::Ref sessionId,
                                   Ptr<AudioClip>::Ref audioClip)
                                                throw (StorageException)
{
    if (!audioClip || !audioClip->getUri()) {
        throw InvalidArgumentException("binary audio clip file not found");
    }
    
    std::string     metadata = audioClip->toXml()
                                        ->write_to_string("UTF-8");

    // temporary hack; we will expect an absolute file name from getUri()
    //   in the final version
    std::string     binaryFileName = audioClip->getUri()->substr(5);
    std::ifstream   ifs(binaryFileName.c_str());
    if (!ifs) {
        ifs.close();
        throw IOException("could not read audio clip");
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
    parameters[storeAudioClipAudioClipIdParamName] 
            = std::string(*audioClip->getId());
    parameters[storeAudioClipMetadataParamName] 
            = metadata;
    parameters[storeAudioClipChecksumParamName] 
            = md5string;

    result.clear();
    if (!xmlRpcClient.execute(storeAudioClipOpenMethodName.c_str(),
                              parameters, result)) {
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
        throw XmlRpcMethodFaultException(eMsg.str());
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
        throw IOException("Binary audio clip file not found.");
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
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += storeAudioClipCloseMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << storeAudioClipCloseMethodName
             << "' returned error message:\n"
             << result;
        throw XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(storeAudioClipAudioClipIdParamName)
            || result[storeAudioClipAudioClipIdParamName].getType() 
                                        != XmlRpcValue::TypeString
            || std::string(result[storeAudioClipAudioClipIdParamName])
                                        != std::string(*audioClip->getId())) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << storeAudioClipCloseMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    return true;
}


/*------------------------------------------------------------------------------
 *  Acquire resources for an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: acquireAudioClip(Ptr<SessionId>::Ref sessionId,
                                     Ptr<UniqueId>::Ref  id) const
                                                throw (StorageException)
{
    Ptr<AudioClip>::Ref  playlist(new AudioClip);
    return playlist;

}


/*------------------------------------------------------------------------------
 *  Release an audio clip.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releaseAudioClip(Ptr<SessionId>::Ref sessionId,
                                     Ptr<AudioClip>::Ref audioClip) const
                                                throw (StorageException)
{

}


/*------------------------------------------------------------------------------
 *  Delete an audio clip.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: deleteAudioClip(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id)
                                                throw (StorageException)
{

}


/*------------------------------------------------------------------------------
 *  Return a listing of all the audio clips in the audio clip store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
WebStorageClient :: getAllAudioClips(Ptr<SessionId>::Ref sessionId)
                                                                        const
                                                throw (StorageException)
{
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref  audioClipVector(
                                        new std::vector<Ptr<AudioClip>::Ref>);
    return audioClipVector;
}


/*------------------------------------------------------------------------------
 *  Reset the storage to its initial state.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref
WebStorageClient :: reset(void)
                                                throw (StorageException)
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
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += resetStorageMethodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << resetStorageMethodName
             << "' returned error message:\n"
             << result;
        throw XmlRpcMethodFaultException(eMsg.str());
    }
    
    if (! result.hasMember(resetStorageResultParamName)
       || result[resetStorageResultParamName].getType() 
                                                != XmlRpcValue::TypeArray) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << resetStorageMethodName
             << "' returned unexpected value:\n"
             << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    XmlRpcValue uniqueIdArray = result[resetStorageResultParamName];
    Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref returnValue(
                                        new std::vector<Ptr<UniqueId>::Ref>);
    
    for (int i=0; i < uniqueIdArray.size(); i++) {
        if (uniqueIdArray[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string gunid returned by XML-RPC method '" 
                 << resetStorageMethodName
                 << "':\n"
                 << result;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        Ptr<UniqueId>::Ref  uniqueId(new UniqueId(std::string(uniqueIdArray[i])));
        returnValue->push_back(uniqueId);
    }
    
    return returnValue;
}

