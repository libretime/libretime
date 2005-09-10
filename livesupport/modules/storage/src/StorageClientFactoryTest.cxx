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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/StorageClientFactoryTest.cxx,v $

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

#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientInterface.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Storage/StorageClientInterface.h"
#include "StorageClientFactoryTest.h"


using namespace std;
using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::Storage;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(StorageClientFactoryTest);

/**
 *  The name of the configuration file for the storage client factory.
 */
static const std::string storageConfig = "etc/storageClient.xml";

/**
 *  The name of the configuration file for the authentication client factory.
 */
static const std::string authenticationConfig = "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
StorageClientFactoryTest :: setUp(void)                         throw ()
{
    Ptr<AuthenticationClientFactory>::Ref 
                        acf = AuthenticationClientFactory::getInstance();
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                        new xmlpp::DomParser(authenticationConfig, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        acf->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in authentication configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing authentication configuration file");
    }

    Ptr<StorageClientFactory>::Ref 
                        scf = StorageClientFactory::getInstance();
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                        new xmlpp::DomParser(storageConfig, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        scf->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in storage configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing storage configuration file");
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
StorageClientFactoryTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if we can do some simple operations
 *----------------------------------------------------------------------------*/
void
StorageClientFactoryTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<StorageClientFactory>::Ref 
                scf = StorageClientFactory::getInstance();
    Ptr<StorageClientInterface>::Ref 
                storage = scf->getStorageClient();

    Ptr<AuthenticationClientFactory>::Ref 
                acf = AuthenticationClientFactory::getInstance();
    Ptr<AuthenticationClientInterface>::Ref 
                authentication = acf->getAuthenticationClient();
    Ptr<SessionId>::Ref 
                sessionId = authentication->login("root", "q");
    CPPUNIT_ASSERT(sessionId);    
    
    Ptr<UniqueId>::Ref  id01(new UniqueId(1));
    Ptr<UniqueId>::Ref  id77(new UniqueId(77));

    try {
        CPPUNIT_ASSERT( storage->existsPlaylist(sessionId, id01));
    } catch (XmlRpcException &e) {
        std::string eMsg = "existsPlaylist returned error:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    
    try {
        CPPUNIT_ASSERT(!storage->existsPlaylist(sessionId, id77));
    } catch (XmlRpcException &e) {
        std::string eMsg = "existsPlaylist returned error:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    try {
        Ptr<Playlist>::Ref  playlist = storage->getPlaylist(sessionId, id01);
        CPPUNIT_ASSERT(playlist->getId()->getId() == id01->getId());
    } catch (XmlRpcException &e) {
        std::string eMsg = "getPlaylist returned error:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}

