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
    Version  : $Revision: 1.7 $
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

#include <iostream>             // for testing only, REMOVE THIS later
#include <fstream>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

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
static const std::string    resetStorageMethodResultParamName = "gunids";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: existsAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the exists audio clip method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipMethodName 
                            = "locstor.existsAudioClip";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipMethodSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipMethodAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    existsAudioClipMethodResultParamName = "exists";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: getAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the get audio clip method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipMethodName 
                            = "locstor.getAudioClip";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipMethodSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipMethodAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
static const std::string    getAudioClipMethodResultParamName = "metadata";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: storeAudioClip */

// TODO: fix; this method does not exist any more

/*------------------------------------------------------------------------------
 *  The name of the store audio clip method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipMethodName 
                            = "locstor.storeAudioClip";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipMethodSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipMethodAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the binary file name parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipMethodBinaryFileParamName 
                            = "mediaFileLP";

/*------------------------------------------------------------------------------
 *  The name of the metadata file name parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    storeAudioClipMethodMetadataFileParamName 
                            = "mdataFileLP";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the web storage client.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
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
                                                throw (std::logic_error)
{
    return false;
}
 

/*------------------------------------------------------------------------------
 *  Return a playlist to be displayed.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: getPlaylist(Ptr<SessionId>::Ref sessionId,
                                Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error)
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
                                                throw (std::logic_error)
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
                                                throw (std::logic_error)
{
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: acquirePlaylist(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error)
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
                                                throw (std::logic_error)
{

}


/*------------------------------------------------------------------------------
 *  Delete a playlist.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: deletePlaylist(Ptr<SessionId>::Ref sessionId,
                                   Ptr<UniqueId>::Ref  id)
                                                throw (std::logic_error)
{

}


/*------------------------------------------------------------------------------
 *  Return a listing of all the playlists in the playlist store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
WebStorageClient :: getAllPlaylists(Ptr<SessionId>::Ref sessionId) const
                                                throw (std::logic_error)
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
                                                throw (std::logic_error)
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
                                                throw (std::logic_error)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters[existsAudioClipMethodSessionIdParamName] 
            = sessionId->getId().c_str();
    parameters[existsAudioClipMethodAudioClipIdParamName] 
            = int(id->getId());
    
    if (!xmlRpcClient.execute(existsAudioClipMethodName.c_str(),
                              parameters, result)) {
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += existsAudioClipMethodName;
        eMsg += "'";
        throw std::logic_error(eMsg);
    }
    
    if (xmlRpcClient.isFault()
                || ! result.hasMember(existsAudioClipMethodResultParamName) 
                || result[existsAudioClipMethodResultParamName].getType() 
                                                != XmlRpcValue::TypeBoolean) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << existsAudioClipMethodName
             << "' returned error message:\n"
             << result;
        throw std::logic_error(eMsg.str());
    }
    
    return bool(result[existsAudioClipMethodResultParamName]);
}
 

/*------------------------------------------------------------------------------
 *  Return an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: getAudioClip(Ptr<SessionId>::Ref sessionId,
                                 Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters[getAudioClipMethodSessionIdParamName] 
            = sessionId->getId().c_str();
    parameters[getAudioClipMethodAudioClipIdParamName] 
            = int(id->getId());
    
    if (!xmlRpcClient.execute(getAudioClipMethodName.c_str(),
                              parameters, result)) {
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += getAudioClipMethodName;
        eMsg += "'";
        throw std::logic_error(eMsg);
    }

    if (xmlRpcClient.isFault() 
                || ! result.hasMember(getAudioClipMethodResultParamName)
                || result[getAudioClipMethodResultParamName].getType() 
                                                != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getAudioClipMethodName
             << "' returned error message:\n"
             << result;
        throw std::logic_error(eMsg.str());
    }
    
    std::string         xmlAudioClip(result[getAudioClipMethodResultParamName]);
    Ptr<AudioClip>::Ref audioClip;

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_memory(xmlAudioClip);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        audioClip.reset(new AudioClip);
        audioClip->configure(*root);
    } catch (std::invalid_argument &e) {
        throw std::logic_error("semantic error in audio clip metafile");
    } catch (xmlpp::exception &e) {
        throw std::logic_error("error parsing audio clip metafile");
    }

    return audioClip;
}


/*------------------------------------------------------------------------------
 *  Store an audio clip.
 *----------------------------------------------------------------------------*/
bool
WebStorageClient :: storeAudioClip(Ptr<SessionId>::Ref sessionId,
                                   Ptr<AudioClip>::Ref audioClip)
                                                throw (std::logic_error)
{
    if (!audioClip || !audioClip->getUri()) {
        throw std::logic_error("binary audio clip file not found");
    }
    
    Ptr<xmlpp::Document>::Ref   metadata = audioClip->getMetadata();
    
    std::stringstream metadataFileName;
    metadataFileName << localTempStorage << "-audioClipMetadata-" 
                     << audioClip->getId()->getId()
                     << "-" << rand() << ".xml";

    metadata->write_to_file(metadataFileName.str(), "UTF-8");

    // temporary hack; we will expect an absolute file name from getUri()
    //   in the final version
    std::string     binaryFileName = audioClip->getUri()->substr(5);
    std::ifstream   ifs(binaryFileName.c_str());
    if (!ifs) {
        ifs.close();
        throw std::logic_error("could not read audio clip");
    }
    ifs.close();
    std::string     binaryFilePrefix("file://");
    binaryFilePrefix += get_current_dir_name();     // doesn't work if current
    binaryFilePrefix += "/";                        // dir = /, but OK for now
    binaryFileName = binaryFilePrefix + binaryFileName;

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters[storeAudioClipMethodSessionIdParamName] 
            = sessionId->getId();
    parameters[storeAudioClipMethodAudioClipIdParamName] 
            = int(audioClip->getId()->getId());
    parameters[storeAudioClipMethodBinaryFileParamName] 
            = binaryFileName;
    parameters[storeAudioClipMethodMetadataFileParamName] 
            = metadataFileName.str();

    bool clientStatus = xmlRpcClient.execute(storeAudioClipMethodName.c_str(),
                                             parameters, result);
    std::remove(metadataFileName.str().substr(7).c_str());

    if (!clientStatus) {
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += storeAudioClipMethodName;
        eMsg += "'";
        throw std::logic_error(eMsg);
    }

    if (xmlRpcClient.isFault() 
                || result.getType() != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << getAudioClipMethodName
             << "' returned error message:\n"
             << result;
        throw std::logic_error(eMsg.str());
    }
    
    return true;
}


/*------------------------------------------------------------------------------
 *  Acquire resources for an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: acquireAudioClip(Ptr<SessionId>::Ref sessionId,
                                     Ptr<UniqueId>::Ref  id) const
                                                throw (std::logic_error)
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
                                                throw (std::logic_error)
{

}


/*------------------------------------------------------------------------------
 *  Delete an audio clip.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: deleteAudioClip(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id)
                                                throw (std::logic_error)
{

}


/*------------------------------------------------------------------------------
 *  Return a listing of all the audio clips in the audio clip store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
WebStorageClient :: getAllAudioClips(Ptr<SessionId>::Ref sessionId)
                                                                        const
                                                throw (std::logic_error)
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
                                                throw (std::logic_error)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters["dummy_param"] = "dummy_value"; 
    
    if (!xmlRpcClient.execute(resetStorageMethodName.c_str(),
                              parameters, result)) {
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += resetStorageMethodName;
        eMsg += "'";
        throw std::logic_error(eMsg);
    }

    if (xmlRpcClient.isFault() 
                || ! result.hasMember(resetStorageMethodResultParamName)
                || result[resetStorageMethodResultParamName].getType() 
                                                != XmlRpcValue::TypeArray) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << resetStorageMethodName
             << "' returned error message:\n"
             << result;
        throw std::logic_error(eMsg.str());
    }
    
    XmlRpcValue uniqueIdArray = result[resetStorageMethodResultParamName];
    Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref returnValue(
                                        new std::vector<Ptr<UniqueId>::Ref>);
    
    for (int i=0; i < uniqueIdArray.size(); i++) {
        if (uniqueIdArray[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string gunid returned by XML-RPC method '" 
                 << resetStorageMethodName
                 << "':\n"
                 << result;
            throw std::logic_error(eMsg.str());
        }            
        Ptr<UniqueId>::Ref  uniqueId(new UniqueId(10001 + i));  // TODO: fix!!!
        returnValue->push_back(uniqueId);
    }
    
    return returnValue;
}

