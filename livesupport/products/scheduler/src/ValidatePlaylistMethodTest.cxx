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
    Version  : $Revision: 1.11 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/ValidatePlaylistMethodTest.cxx,v $

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
#include <iostream>
#include <XmlRpcValue.h>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/Core/XmlRpcTools.h"

#include "OpenPlaylistForEditingMethod.h"
#include "RemoveAudioClipFromPlaylistMethod.h"
#include "ValidatePlaylistMethod.h"

#include "ValidatePlaylistMethodTest.h"

using namespace std;
using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(ValidatePlaylistMethodTest);

/**
 *  The name of the configuration file for the storage client factory.
 */
const std::string ValidatePlaylistMethodTest::storageClientConfig =
                                                    "etc/storageClient.xml";

/**
 *  The name of the configuration file for the connection manager factory.
 */
const std::string ValidatePlaylistMethodTest::connectionManagerConfig =
                                          "etc/connectionManagerFactory.xml";

/**
 *  The name of the configuration file for the authentication client factory.
 */
const std::string ValidatePlaylistMethodTest::authenticationClientConfig =
                                          "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
ValidatePlaylistMethodTest :: configure(
            Ptr<Configurable>::Ref      configurable,
            const std::string           fileName)
                                                throw (std::invalid_argument,
                                                       xmlpp::exception)
{
    Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser(fileName, true));
    const xmlpp::Document * document = parser->get_document();
    const xmlpp::Element  * root     = document->get_root_node();

    configurable->configure(*root);
}

                                                        
/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
ValidatePlaylistMethodTest :: setUp(void)                         throw ()
{
    Ptr<AuthenticationClientFactory>::Ref acf;
    Ptr<StorageClientFactory>::Ref scf;
    try {
        scf = StorageClientFactory::getInstance();
        configure(scf, storageClientConfig);
        Ptr<StorageClientInterface>::Ref    storage = scf->getStorageClient();
        storage->reset();

        Ptr<ConnectionManagerFactory>::Ref  cmf
                                    = ConnectionManagerFactory::getInstance();
        configure(cmf, connectionManagerConfig);

        acf = AuthenticationClientFactory::getInstance();
        configure(acf, authenticationClientConfig);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    authentication = acf->getAuthenticationClient();
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        std::string eMsg = "could not log in:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
ValidatePlaylistMethodTest :: tearDown(void)                      throw ()
{
    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
ValidatePlaylistMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<OpenPlaylistForEditingMethod>::Ref 
               openPlaylistMethod(new OpenPlaylistForEditingMethod());
    Ptr<RemoveAudioClipFromPlaylistMethod>::Ref 
               removeAudioClipMethod(new RemoveAudioClipFromPlaylistMethod());
    Ptr<ValidatePlaylistMethod>::Ref 
               validatePlaylistMethod(new ValidatePlaylistMethod());
    XmlRpc::XmlRpcValue             parameter;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;

    result.clear();
    parameter["sessionId"]  = sessionId->getId();
    parameter["playlistId"] = "0000000000009999";
    rootParameter[0]        = parameter;
    try {
        validatePlaylistMethod->execute(rootParameter, result);
        CPPUNIT_FAIL("allowed to validate non-existent playlist");
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 503);  // no such playlist
    }

    result.clear();
    parameter.clear();
    parameter["sessionId"]  = sessionId->getId();
    parameter["playlistId"] = "0000000000000001";
    rootParameter[0]        = parameter;    
    try {
        openPlaylistMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }

    result.clear();
    try {
        validatePlaylistMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("valid"));
    CPPUNIT_ASSERT(bool(result["valid"]));

    result.clear();
    parameter["playlistElementId"]  = "0000000000000101";
    rootParameter[0]                = parameter;    
    try {
        removeAudioClipMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }

    result.clear();
    try {
        validatePlaylistMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("valid"));
    CPPUNIT_ASSERT(result["valid"].getType() 
                                          == XmlRpc::XmlRpcValue::TypeBoolean);
    CPPUNIT_ASSERT(!bool(result["valid"]));  // has a gap at the beginning
}
