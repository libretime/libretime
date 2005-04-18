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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/src/TestAuthenticationClient.cxx,v $

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

#include "TestAuthenticationClient.h"

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
const std::string TestAuthenticationClient::configElementNameStr 
                                           = "testAuthentication";

/*------------------------------------------------------------------------------
 *  The name of the config child element for the login and password
 *----------------------------------------------------------------------------*/
static const std::string    userConfigElementName = "user";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the login
 *----------------------------------------------------------------------------*/
static const std::string    userLoginAttrName = "login";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the password
 *----------------------------------------------------------------------------*/
static const std::string    userPasswordAttrName = "password";

/*------------------------------------------------------------------------------
 *  The dummy sessionId string returned by this authentication client
 *----------------------------------------------------------------------------*/
static const std::string    dummySessionIdString = "dummySessionId";

/*------------------------------------------------------------------------------
 *  The version string, returned by getVersion
 *----------------------------------------------------------------------------*/
static const std::string    versionStr = "TestAuthentication";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the test storage client.
 *----------------------------------------------------------------------------*/
void
TestAuthenticationClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    // read the user data
    xmlpp::Node::NodeList   childNodes 
                            = element.get_children(userConfigElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it == childNodes.end()) {
        std::string eMsg = "missing ";
        eMsg += userConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Element      * userConfigElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
    if (!(attribute = userConfigElement
                      ->get_attribute(userLoginAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += userLoginAttrName;
        throw std::invalid_argument(eMsg);
    }
    userLogin = attribute->get_value();

    if (!(attribute = userConfigElement
                      ->get_attribute(userPasswordAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += userPasswordAttrName;
        throw std::invalid_argument(eMsg);
    }
    userPassword = attribute->get_value();

    ++it;
    if (it != childNodes.end()) {
        std::string eMsg = "more than one ";
        eMsg += userConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }
    
    versionString.reset(new Glib::ustring(versionStr));

    sessionIdList.clear();
    sessionCounter = 0;
}


/*------------------------------------------------------------------------------
 *  Return the version string of the test storage.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
TestAuthenticationClient :: getVersion(void)
                                                throw (Core::XmlRpcException)
{
    return versionString;
}


/*------------------------------------------------------------------------------
 *  Login to the authentication server.
 *----------------------------------------------------------------------------*/
Ptr<SessionId>::Ref
TestAuthenticationClient :: login(const std::string & login,
                                  const std::string & password)
                                                throw (XmlRpcException)
{
    Ptr<SessionId>::Ref     sessionId;

    if (login == userLogin && password == userPassword) {
        std::stringstream   sessionIdStream;
        sessionIdStream << dummySessionIdString
                        << sessionCounter++
                        << '-'
                        << rand();
        sessionIdList.insert(sessionIdStream.str());
        sessionId.reset(new SessionId(sessionIdStream.str()));
        return sessionId;
    } else {
        throw XmlRpcException("incorrect login or password");
    }
}


/*------------------------------------------------------------------------------
 *  Logout from the authentication server.
 *----------------------------------------------------------------------------*/
void
TestAuthenticationClient :: logout(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException)
{
    // this returns the number of entries found and erased
    if (!sessionId || sessionIdList.erase(sessionId->getId())) {
        return;
    } else {
        throw XmlRpcException("logout() called without previous login()");
    }
}


/*------------------------------------------------------------------------------
 *  Load a `user preferences' item from the server.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
TestAuthenticationClient :: loadPreferencesItem(
                                Ptr<SessionId>::Ref     sessionId,
                                const Glib::ustring &   key)
                                                throw (XmlRpcException)
{
    if (!sessionId 
        || sessionIdList.find(sessionId->getId()) == sessionIdList.end()) {
        throw XmlRpcException("bad session ID");
    }
    
    PreferencesType::iterator   it;

    if ((it = preferences.find(key)) == preferences.end()) {
        throw XmlRpcException("no such user preferences item");
    }
    Ptr<Glib::ustring>::Ref     value(new Glib::ustring(*it->second));

    return value;
}


/*------------------------------------------------------------------------------
 *  Store a `user preferences' item on the server.
 *----------------------------------------------------------------------------*/
void
TestAuthenticationClient :: savePreferencesItem(
                                Ptr<SessionId>::Ref             sessionId,
                                const Glib::ustring &           key,
                                Ptr<const Glib::ustring>::Ref   value)
                                                throw (XmlRpcException)
{
    if (!sessionId 
        || sessionIdList.find(sessionId->getId()) == sessionIdList.end()) {
        throw XmlRpcException("bad session ID");
    }
    
    if (sessionIdList.find(sessionId->getId()) == sessionIdList.end()) {
        throw XmlRpcException("loadPreferences() called before login()");
    }
    
    preferences[key] = value;
}


/*------------------------------------------------------------------------------
 *  Delete a `user preferences' item from the server.
 *----------------------------------------------------------------------------*/
void
TestAuthenticationClient :: deletePreferencesItem(
                                Ptr<SessionId>::Ref             sessionId,
                                const Glib::ustring &           key)
                                                throw (XmlRpcException)
{
    if (!sessionId 
        || sessionIdList.find(sessionId->getId()) == sessionIdList.end()) {
        throw XmlRpcException("bad session ID");
    }

    PreferencesType::iterator   it;

    if ((it = preferences.find(key)) == preferences.end()) {
        throw XmlRpcException("no such user preferences item");
    }
    
    preferences.erase(it);
}

