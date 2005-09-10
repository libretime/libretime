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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/src/WebAuthenticationClient.cxx,v $

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

#include <fstream>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "WebAuthenticationClient.h"

using namespace boost::posix_time;
using namespace XmlRpc;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  configuration file constants */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string WebAuthenticationClient::configElementNameStr 
                                           = "webAuthentication";

/*------------------------------------------------------------------------------
 *  The name of the config child element for the authentication server location
 *----------------------------------------------------------------------------*/
static const std::string    locationConfigElementName = "location";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the server name
 *----------------------------------------------------------------------------*/
static const std::string    locationServerAttrName = "server";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the server port
 *----------------------------------------------------------------------------*/
static const std::string    locationPortAttrName = "port";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the server php page
 *----------------------------------------------------------------------------*/
static const std::string    locationPathAttrName = "path";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  authentication server constants: login */

/*------------------------------------------------------------------------------
 *  The name of the get version method on the storage server
 *----------------------------------------------------------------------------*/
static const std::string    getVersionMethodName = "locstor.getVersion";

/*------------------------------------------------------------------------------
 *  The name of version return parameter for getVersion
 *----------------------------------------------------------------------------*/
static const std::string    getVersionResultParamName = "version";

/*------------------------------------------------------------------------------
 *  The name of the login method on the server
 *----------------------------------------------------------------------------*/
static const std::string    loginMethodName = "locstor.login";

/*------------------------------------------------------------------------------
 *  The name of the login parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    loginParamName = "login";

/*------------------------------------------------------------------------------
 *  The name of the password parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    passwordParamName = "pass";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the output structure
 *----------------------------------------------------------------------------*/
static const std::string    outputSessionIdParamName = "sessid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  authentication server constants: logout */

/*------------------------------------------------------------------------------
 *  The name of the logout method on the server
 *----------------------------------------------------------------------------*/
static const std::string    logoutMethodName = "locstor.logout";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    inputSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the status parameter in the output structure
 *----------------------------------------------------------------------------*/
static const std::string    statusParamName = "status";


/* ~~~~~~~~~~~~~~~~~~  authentication server constants: load/save preferences */

/*------------------------------------------------------------------------------
 *  The name of the load preferences method on the server
 *----------------------------------------------------------------------------*/
static const std::string    loadPreferencesMethodName = "locstor.loadPref";

/*------------------------------------------------------------------------------
 *  The name of the save preferences method on the server
 *----------------------------------------------------------------------------*/
static const std::string    savePreferencesMethodName = "locstor.savePref";

/*------------------------------------------------------------------------------
 *  The name of the delete preferences method on the server
 *----------------------------------------------------------------------------*/
static const std::string    deletePreferencesMethodName = "locstor.delPref";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    preferencesSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the key parameter in the input structure
 *----------------------------------------------------------------------------*/
static const std::string    preferencesKeyParamName = "key";

/*------------------------------------------------------------------------------
 *  The name of the value parameter for both save and load methods
 *----------------------------------------------------------------------------*/
static const std::string    preferencesValueParamName = "value";

/*------------------------------------------------------------------------------
 *  The name of the return parameter for the save method
 *----------------------------------------------------------------------------*/
static const std::string    preferencesStatusParamName = "status";

/*------------------------------------------------------------------------------
 *  The name of the fault code parameter
 *----------------------------------------------------------------------------*/
static const std::string    faultCodeParamName = "faultCode";

/*------------------------------------------------------------------------------
 *  The fault code for the "invalid preference key" error
 *----------------------------------------------------------------------------*/
static const int            invalidPreferenceKeyFaultCode = 849;


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~  authentication server constants: resetStorage */

/*------------------------------------------------------------------------------
 *  The name of the reset storage method on the server
 *----------------------------------------------------------------------------*/
static const std::string    resetStorageMethodName = "locstor.resetStorage";

/*------------------------------------------------------------------------------
 *  The name of the list of audio clips parameter returned (ignored here)
 *----------------------------------------------------------------------------*/
static const std::string    resetStorageAudioClipResultParamName = "audioclips";

/*------------------------------------------------------------------------------
 *  The name of the list of playlists parameter returned (ignored here)
 *----------------------------------------------------------------------------*/
static const std::string    resetStoragePlaylistResultParamName = "playlists";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the web storage client.
 *----------------------------------------------------------------------------*/
void
WebAuthenticationClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

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
WebAuthenticationClient :: getVersion(void)
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
 *  Login to the authentication server.
 *----------------------------------------------------------------------------*/
Ptr<SessionId>::Ref
WebAuthenticationClient :: login(const std::string & login,
                                 const std::string & password)
                                                throw (XmlRpcException)
{
    XmlRpcValue             parameters;
    XmlRpcValue             result;
    Ptr<SessionId>::Ref     sessionId;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[loginParamName]      = login;
    parameters[passwordParamName]   = password;
    
    result.clear();
    if (!xmlRpcClient.execute(loginMethodName.c_str(), parameters, result)) {
        xmlRpcClient.close();
        throw Authentication::XmlRpcCommunicationException("Login failed.");
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "Login method returned fault response:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    if (! result.hasMember(outputSessionIdParamName)) {
        std::stringstream eMsg;
        eMsg << "Login method returned unexpected response:\n"
             << result;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }

    if (result[outputSessionIdParamName].getType() != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "Login method returned unexpected response:\n"
             << result;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }

    sessionId.reset(new SessionId(result[outputSessionIdParamName]));
    return sessionId;
}


/*------------------------------------------------------------------------------
 *  Logout from the authentication server.
 *----------------------------------------------------------------------------*/
void
WebAuthenticationClient :: logout(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw Core::XmlRpcInvalidArgumentException("Missing session ID.");
    }

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[inputSessionIdParamName] = sessionId->getId();
    
    result.clear();
    if (!xmlRpcClient.execute(logoutMethodName.c_str(), parameters, result)) {
        xmlRpcClient.close();
        throw Core::XmlRpcCommunicationException("Logout failed.");
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "Logout method returned fault response:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    if (! result.hasMember(statusParamName)
            || result[statusParamName].getType() != XmlRpcValue::TypeBoolean
            || ! bool(result[statusParamName])) {
        std::stringstream eMsg;
        eMsg << "Logout method returned unexpected response:\n"
             << result;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  Load a `user preferences' item from the server.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
WebAuthenticationClient :: loadPreferencesItem(
                                Ptr<SessionId>::Ref             sessionId,
                                const Glib::ustring &           key)
                                                throw (XmlRpcException,
                                                       std::invalid_argument)
{
    if (!sessionId) {
        throw Core::XmlRpcInvalidArgumentException("Missing session ID.");
    }

    XmlRpcValue             parameters;
    XmlRpcValue             result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[preferencesSessionIdParamName]   = sessionId->getId();
    parameters[preferencesKeyParamName]         = std::string(key);
    
    result.clear();
    if (!xmlRpcClient.execute(loadPreferencesMethodName.c_str(),
                                                        parameters, result)) {
        xmlRpcClient.close();
        throw Core::XmlRpcCommunicationException(
                                          "Could not execute XML-RPC method.");
    }
    xmlRpcClient.close();
    
    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method "
                << loadPreferencesMethodName 
                << " returned fault response:\n"
                << result;
        if (result.hasMember(faultCodeParamName) 
                && result[faultCodeParamName].getType() 
                                            == XmlRpcValue::TypeInt
                && int(result[faultCodeParamName]) 
                                            == invalidPreferenceKeyFaultCode) {
            throw std::invalid_argument(eMsg.str());
        } else {
            throw Core::XmlRpcMethodFaultException(eMsg.str());
        }
    }

    if (! result.hasMember(preferencesValueParamName)
        || result[preferencesValueParamName].getType() 
                                                != XmlRpcValue::TypeString) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method "
             << loadPreferencesMethodName 
             << " returned unexpected response:\n"
             << result;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }

    Ptr<Glib::ustring>::Ref     value(new Glib::ustring(std::string(
                                        result[preferencesValueParamName] )));
    return value;
}


/*------------------------------------------------------------------------------
 *  Store a `user preferences' item on the server.
 *----------------------------------------------------------------------------*/
void
WebAuthenticationClient :: savePreferencesItem(
                                Ptr<SessionId>::Ref             sessionId,
                                const Glib::ustring &           key,
                                Ptr<const Glib::ustring>::Ref   value)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw Core::XmlRpcInvalidArgumentException("Missing session ID.");
    }

    if (!value) {
        throw Core::XmlRpcInvalidArgumentException("Missing value argument.");
    }

    XmlRpcValue             parameters;
    XmlRpcValue             result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[preferencesSessionIdParamName]   = sessionId->getId();
    parameters[preferencesKeyParamName]         = std::string(key);
    parameters[preferencesValueParamName]       = std::string(*value);
    
    result.clear();
    if (!xmlRpcClient.execute(savePreferencesMethodName.c_str(),
                                                        parameters, result)) {
        xmlRpcClient.close();
        throw Core::XmlRpcCommunicationException(
                                          "Could not execute XML-RPC method.");
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method "
             << savePreferencesMethodName 
             << " returned fault response:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    if (! result.hasMember(preferencesStatusParamName)
        || result[preferencesStatusParamName].getType() 
                                                != XmlRpcValue::TypeBoolean
        || ! bool(result[preferencesStatusParamName])) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method "
             << savePreferencesMethodName 
             << " returned unexpected response:\n"
             << result;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  Delete a `user preferences' item from the server.
 *----------------------------------------------------------------------------*/
void
WebAuthenticationClient :: deletePreferencesItem(
                                Ptr<SessionId>::Ref             sessionId,
                                const Glib::ustring &           key)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw Core::XmlRpcInvalidArgumentException("Missing session ID.");
    }

    XmlRpcValue             parameters;
    XmlRpcValue             result;

    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);

    parameters.clear();
    parameters[preferencesSessionIdParamName]   = sessionId->getId();
    parameters[preferencesKeyParamName]         = std::string(key);
    
    result.clear();
    if (!xmlRpcClient.execute(deletePreferencesMethodName.c_str(),
                                                        parameters, result)) {
        xmlRpcClient.close();
        throw Core::XmlRpcCommunicationException(
                                          "Could not execute XML-RPC method.");
    }
    xmlRpcClient.close();

    if (xmlRpcClient.isFault()) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method "
             << deletePreferencesMethodName 
             << " returned fault response:\n"
             << result;
        throw Core::XmlRpcMethodFaultException(eMsg.str());
    }

    if (! result.hasMember(preferencesStatusParamName)
        || result[preferencesStatusParamName].getType() 
                                                != XmlRpcValue::TypeBoolean
        || ! bool(result[preferencesStatusParamName])) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method "
             << deletePreferencesMethodName 
             << " returned unexpected response:\n"
             << result;
        throw Core::XmlRpcMethodResponseException(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  Reset the list of preferences to its initial (empty) state.
 *----------------------------------------------------------------------------*/
void
WebAuthenticationClient :: reset(void)
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
        throw Core::XmlRpcCommunicationException(
                                          "Could not execute XML-RPC method.");
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
}

