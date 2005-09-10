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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/src/AuthenticationClientFactoryTest.cxx,v $

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


#include <string>
#include <fstream>
#include <iostream>

#include "LiveSupport/Core/SessionId.h"
#include "AuthenticationClientFactoryTest.h"


using namespace std;
using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(AuthenticationClientFactoryTest);

/**
 *  The name of the configuration file for the authentication client factory.
 */
static const std::string configFileName = "authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AuthenticationClientFactoryTest :: setUp(void)                         throw ()
{
    Ptr<AuthenticationClientFactory>::Ref
                            acf = AuthenticationClientFactory::getInstance();
    try {
        xmlpp::DomParser        parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                             configFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        acf->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
AuthenticationClientFactoryTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if we can log on and off
 *----------------------------------------------------------------------------*/
void
AuthenticationClientFactoryTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AuthenticationClientFactory>::Ref
                            acf = AuthenticationClientFactory::getInstance();
    Ptr<AuthenticationClientInterface>::Ref
                            authentication = acf->getAuthenticationClient();

    Ptr<SessionId>::Ref     sessionId;
    
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    CPPUNIT_ASSERT(sessionId);

    try {
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test to see if we can save and load user preferences.
 *----------------------------------------------------------------------------*/
void
AuthenticationClientFactoryTest :: preferencesTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AuthenticationClientFactory>::Ref
                            acf = AuthenticationClientFactory::getInstance();
    Ptr<AuthenticationClientInterface>::Ref
                            authentication = acf->getAuthenticationClient();

    Ptr<SessionId>::Ref             sessionId;
    Ptr<const Glib::ustring>::Ref   prefValue;

    // check "please log in" error
    try {
        prefValue = authentication->loadPreferencesItem(sessionId, "something");
        CPPUNIT_FAIL("Allowed operation without login.");
    } catch (XmlRpcException &e) {
    }

    // log in
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    // check "no such key" error
    try {
        prefValue = authentication->loadPreferencesItem(sessionId, "eye_color");
        CPPUNIT_FAIL("Retrieved non-existent user preferences item.");
    } catch (std::invalid_argument &e) {
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    // check normal save and load
    prefValue.reset(new const Glib::ustring("chyornye"));
    try {
        authentication->savePreferencesItem(sessionId, "eye_color", prefValue);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<const Glib::ustring>::Ref   newPrefValue;
    try {
        newPrefValue = authentication->loadPreferencesItem(sessionId, "eye_color");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(*newPrefValue == *prefValue);
    
    // try some unicode characters
    prefValue.reset(new const Glib::ustring("страстные"));
    try {
        authentication->savePreferencesItem(sessionId, "eye_color", prefValue);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        newPrefValue = authentication->loadPreferencesItem(sessionId, "eye_color");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(*newPrefValue == "страстные");

    // check another normal save and load...
    prefValue.reset(new const Glib::ustring("ne dobryj"));
    try {
        authentication->savePreferencesItem(sessionId, "hour", prefValue);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

            // ... but now change session ID in the middle
            try {
                authentication->logout(sessionId);
                sessionId = authentication->login("root", "q");
            } catch (XmlRpcException &e) {
                CPPUNIT_FAIL(e.what());
            }

    try {
        newPrefValue = authentication->loadPreferencesItem(sessionId, "hour");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(*newPrefValue == *prefValue);
    
    // check the delete method
    try {
        authentication->deletePreferencesItem(sessionId, "hour");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        newPrefValue = authentication->loadPreferencesItem(sessionId, "hour");
        CPPUNIT_FAIL("Allowed to load preference after it was deleted");
    } catch (std::invalid_argument &e) {
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    // and log out
    try {
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}

