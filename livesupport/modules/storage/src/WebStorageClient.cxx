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
    Version  : $Revision: 1.2 $
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

/*------------------------------------------------------------------------------
 *  The name of the config child element for the storage server login
 *----------------------------------------------------------------------------*/
static const std::string    identityConfigElementName = "identity";

/*------------------------------------------------------------------------------
 *  The name of the config child element for the storage server login name
 *----------------------------------------------------------------------------*/
static const std::string    identityLoginAttrName = "login";

/*------------------------------------------------------------------------------
 *  The name of the config child element for the storage server login password
 *----------------------------------------------------------------------------*/
static const std::string    identityPasswordAttrName = "pass";


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


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: login */

/*------------------------------------------------------------------------------
 *  The name of the login method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    loginMethodName = "locstor.login";

/*------------------------------------------------------------------------------
 *  The name of the login parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    loginMethodLoginParamName = "login";

/*------------------------------------------------------------------------------
 *  The name of the password parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    loginMethodPasswordParamName = "pass";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: logout */

/*------------------------------------------------------------------------------
 *  The name of the logout method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    logoutMethodName = "locstor.logout";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    logoutMethodSessionIdParamName = "sessid";


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

    // read the login and password to the storage server
    childNodes  = element.get_children(identityConfigElementName);
    it          = childNodes.begin();

    if (it == childNodes.end()) {
        std::string eMsg = "missing ";
        eMsg += identityConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Element      * identityConfigElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
    if (!(attribute = identityConfigElement
                      ->get_attribute(identityLoginAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += identityLoginAttrName;
        throw std::invalid_argument(eMsg);
    }
    storageServerLogin = attribute->get_value();

    if (!(attribute = identityConfigElement
                      ->get_attribute(identityPasswordAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += identityPasswordAttrName;
        throw std::invalid_argument(eMsg);
    }
    storageServerPassword = attribute->get_value();
    
    ++it;
    if (it != childNodes.end()) {
        std::string eMsg = "more than one ";
        eMsg += identityConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Login to the storage server.
 *----------------------------------------------------------------------------*/
std::string
WebStorageClient :: loginToStorageServer(void) const
                                                throw ()
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters[loginMethodLoginParamName] = storageServerLogin.c_str();
    parameters[loginMethodPasswordParamName] = storageServerPassword.c_str();
    
    if (!xmlRpcClient.execute(loginMethodName.c_str(), parameters, result)) {
        // throw exception;
    }

    if (result.getType() != XmlRpcValue::TypeString) {
        return std::string("");             // change to throw exception
    }
    
    return std::string(result);
}


/*------------------------------------------------------------------------------
 *  Logout from the storage server.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: logoutFromStorageServer(std::string sessionId) const
                                                throw ()
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters[logoutMethodSessionIdParamName] = sessionId.c_str();
    
    if (!xmlRpcClient.execute(logoutMethodName.c_str(), parameters, result)) {
        //throw exception;
    }

    if (xmlRpcClient.isFault()) {
        // throw exception
    }
}


/*------------------------------------------------------------------------------
 *  Tell if a playlist exists.
 *----------------------------------------------------------------------------*/
const bool
WebStorageClient :: existsPlaylist(Ptr<const UniqueId>::Ref id) const
                                                throw ()
{
    return false;
}
 

/*------------------------------------------------------------------------------
 *  Return a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: getPlaylist(Ptr<const UniqueId>::Ref id) const
                                                throw (std::invalid_argument)
{
    Ptr<Playlist>::Ref  playlist(new Playlist);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: acquirePlaylist(Ptr<const UniqueId>::Ref id) const
                                                throw (std::logic_error)
{
    Ptr<Playlist>::Ref  playlist(new Playlist);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Release a playlist.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releasePlaylist(Ptr<Playlist>::Ref playlist) const
                                                throw (std::logic_error)
{

}


/*------------------------------------------------------------------------------
 *  Delete a playlist.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: deletePlaylist(Ptr<const UniqueId>::Ref id)
                                                throw (std::invalid_argument)
{

}


/*------------------------------------------------------------------------------
 *  Return a listing of all the playlists in the playlist store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
WebStorageClient :: getAllPlaylists(void) const
                                                throw ()
{
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref  playlistVector(
                                        new std::vector<Ptr<Playlist>::Ref>);
    return playlistVector;
}


/*------------------------------------------------------------------------------
 *  Create a new playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: createPlaylist()                                throw ()
{
    Ptr<Playlist>::Ref  playlist(new Playlist);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Tell if an audio clip exists.
 *----------------------------------------------------------------------------*/
const bool
WebStorageClient :: existsAudioClip(Ptr<const UniqueId>::Ref id) const
                                                                throw ()
{
    std::string     sessionId = loginToStorageServer();
    
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters[existsAudioClipMethodSessionIdParamName] = sessionId.c_str();
    parameters[existsAudioClipMethodAudioClipIdParamName] = int(id->getId());
    
    if (!xmlRpcClient.execute(existsAudioClipMethodName.c_str(),
                              parameters, result)) {
        // throw exception
    }

    logoutFromStorageServer(sessionId);
    
    if (result.getType() != XmlRpcValue::TypeBoolean) {
        return false;                       // change to throw exception
    }
    
    return bool(result);
}
 

/*------------------------------------------------------------------------------
 *  Return an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: getAudioClip(Ptr<const UniqueId>::Ref id) const
                                                throw (std::invalid_argument)
{
    Ptr<AudioClip>::Ref  playlist(new AudioClip);
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Acquire resources for an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: acquireAudioClip(Ptr<const UniqueId>::Ref id) const
                                                throw (std::logic_error)
{
    Ptr<AudioClip>::Ref  playlist(new AudioClip);
    return playlist;

}


/*------------------------------------------------------------------------------
 *  Release an audio clip.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releaseAudioClip(Ptr<AudioClip>::Ref audioClip) const
                                                throw (std::logic_error)
{

}


/*------------------------------------------------------------------------------
 *  Delete an audio clip.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: deleteAudioClip(Ptr<const UniqueId>::Ref id)
                                                throw (std::invalid_argument)
{

}


/*------------------------------------------------------------------------------
 *  Return a listing of all the audio clips in the audio clip store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
WebStorageClient :: getAllAudioClips(void) const
                                                throw ()
{
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref  audioClipVector(
                                        new std::vector<Ptr<AudioClip>::Ref>);
    return audioClipVector;
}

