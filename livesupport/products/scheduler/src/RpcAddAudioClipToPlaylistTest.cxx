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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RpcAddAudioClipToPlaylistTest.cxx,v $

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
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "XmlRpcTools.h"

#include "OpenPlaylistForEditingMethod.h"
#include "AddAudioClipToPlaylistMethod.h"
#include "RpcAddAudioClipToPlaylistTest.h"

using namespace std;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RpcAddAudioClipToPlaylistTest);

/**
 *  The name of the configuration file for the scheduler daemon.
 */
static const std::string schedulerDaemonConfig = 
                                            "etc/scheduler.xml";

/**
 *  The name of the configuration file for the storage client factory.
 */
static const std::string storageClientConfig =
                                            "etc/storageClient.xml";
/**
 *  The name of the configuration file for the authentication client factory.
 */
static const std::string authenticationClientConfig =
                                            "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
RpcAddAudioClipToPlaylistTest :: configure(
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
RpcAddAudioClipToPlaylistTest :: setUp(void)                         throw ()
{
    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
    if (!daemon->isConfigured()) {
        try {
            configure(daemon, schedulerDaemonConfig);
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("semantic error in scheduler configuration file");
        } catch (xmlpp::exception &e) {
            std::cerr << e.what() << std::endl;
            CPPUNIT_FAIL("error parsing scheduler configuration file");
        }
    }
    daemon->install();
//    daemon->start();
//    sleep(2);

    try {
        Ptr<StorageClientFactory>::Ref scf
                            = StorageClientFactory::getInstance();
        configure(scf, storageClientConfig);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in storage configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing storage configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    Ptr<AuthenticationClientFactory>::Ref acf;
    try {
        acf = AuthenticationClientFactory::getInstance();
        configure(acf, authenticationClientConfig);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in authentication configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing authentication configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    authentication = acf->getAuthenticationClient();
    try {
        sessionId = authentication->login("root", "q");
    }
    catch (AuthenticationException &e) {
        std::string eMsg = "could not log in:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
RpcAddAudioClipToPlaylistTest :: tearDown(void)                      throw ()
{
    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();

    Ptr<SchedulerDaemon>::Ref   daemon = SchedulerDaemon::getInstance();
//    daemon->stop();
//    sleep(2);
    daemon->uninstall();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
RpcAddAudioClipToPlaylistTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcClient xmlRpcClient("localhost", 3344, "/RPC2", false);
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;

    parameters["sessionId"]      = sessionId->getId();
    parameters["playlistId"]     = "0000000000000001";
    parameters["audioClipId"]    = "0000000000010001";
    parameters["relativeOffset"] = 0;

    result.clear();
    xmlRpcClient.execute("openPlaylistForEditing", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
    CPPUNIT_ASSERT(result.hasMember("id"));
    CPPUNIT_ASSERT(result["id"].getType() == XmlRpcValue::TypeString);
    CPPUNIT_ASSERT(std::string(result["id"]) == "0000000000000001");

    result.clear();
    xmlRpcClient.execute("addAudioClipToPlaylist", parameters, result);
    CPPUNIT_ASSERT(xmlRpcClient.isFault());
    
    parameters.clear();
    parameters["sessionId"]      = sessionId->getId();
    parameters["playlistId"]     = "0000000000000001";
    parameters["audioClipId"]    = "0000000000010001";
    parameters["relativeOffset"] = 90*60;

    result.clear();
    xmlRpcClient.execute("addAudioClipToPlaylist", parameters, result);
    CPPUNIT_ASSERT(!xmlRpcClient.isFault());
}
