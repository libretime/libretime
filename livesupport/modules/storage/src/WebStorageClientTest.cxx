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
    Version  : $Revision: 1.9 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/WebStorageClientTest.cxx,v $

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

#include "WebStorageClient.h"
#include "LiveSupport/Core/SessionId.h"

#include "WebStorageClientTest.h"

using namespace std;
using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::Storage;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(WebStorageClientTest);

/**
 *  The name of the configuration file for the web storage client.
 */
static const std::string storageConfigFileName = "etc/webStorage.xml";

/**
 *  The name of the configuration file for the authentication factory.
 */
static const std::string authenticationFactoryConfigFileName 
                         = "etc/webAuthenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: setUp(void)                         throw ()
{
    Ptr<AuthenticationClientFactory>::Ref   acf;
    acf             = AuthenticationClientFactory::getInstance();
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
            new xmlpp::DomParser(authenticationFactoryConfigFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        acf->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in authentication configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing authentication configuration file");
    }

    authentication  = acf->getAuthenticationClient();
    
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
            new xmlpp::DomParser(storageConfigFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        wsc.reset(new WebStorageClient());
        wsc->configure(*root);
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
WebStorageClientTest :: tearDown(void)                      throw ()
{
    authentication.reset();
    wsc.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if we can log in to the storage server
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref         sessionId(new SessionId("bad ID"));

// this does not currently work due to a bug in the storage server
//    try {
//        wsc->logout(sessionId);
//        CPPUNIT_FAIL("allowed logout operation without login");
//    }
//    catch (std::logic_error &e) {
//    }

    CPPUNIT_ASSERT(!(sessionId = authentication->login("noSuchUser", 
                                                      "incorrectPassword")));
    CPPUNIT_ASSERT( (sessionId = authentication->login("root", "q")));
    CPPUNIT_ASSERT(  authentication->logout(sessionId));
}


/*------------------------------------------------------------------------------
 *  Testing the audio clip operations
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: audioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref  uniqueIdVector = wsc->reset();
    CPPUNIT_ASSERT(uniqueIdVector->size() > 0);
    Ptr<UniqueId>::Ref  id01 = uniqueIdVector->at(0);
    
/*    std::cout << "\nReset storage result:\n";
    for (unsigned i=0; i<uniqueIdVector->size(); i++) {
        std::cout << std::hex << std::string(*uniqueIdVector->at(i)) << std::endl;
    } */

    Ptr<SessionId>::Ref sessionId = authentication->login("root", "q");
    CPPUNIT_ASSERT(sessionId);

    bool exists;
    try {
        exists = wsc->existsAudioClip(sessionId, id01);
    }
    catch (std::logic_error &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(exists);

    Ptr<AudioClip>::Ref audioClip;
    try {
        audioClip = wsc->getAudioClip(sessionId, id01);
    }
    catch (std::logic_error &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<UniqueId>::Ref  id77(new UniqueId(10077));
    try {
        exists = wsc->existsAudioClip(sessionId, id77);
    }
    catch (std::logic_error &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!exists);

/*    
    Ptr<time_duration>::Ref playlength(new time_duration(0,0,11,0));
    Ptr<std::string>::Ref   uri(new std::string("file:var/test10001.mp3"));
    Ptr<AudioClip>::Ref     audioClip(new AudioClip(id01, playlength, uri));

    try {    
        wsc->storeAudioClip(sessionId, audioClip);
    }
    catch (std::logic_error &e) {
        CPPUNIT_FAIL(e.what());
    }

    CPPUNIT_ASSERT( wsc->existsAudioClip(sessionId, id01));   

    Ptr<AudioClip>::Ref     newAudioClip 
                            = wsc->getAudioClip(sessionId, id01);
    CPPUNIT_ASSERT(newAudioClip->getId()->getId() == id01->getId());
    CPPUNIT_ASSERT(newAudioClip->getPlaylength()->total_seconds()
                   == audioClip->getPlaylength()->total_seconds());
*/
    CPPUNIT_ASSERT( authentication->logout(sessionId));
/*
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref  audioClipVector =
                                                 wsc->getAllAudioClips();
    CPPUNIT_ASSERT(audioClipVector->size() == 2);

    audioClip = (*audioClipVector)[0];
    CPPUNIT_ASSERT((int) (audioClip->getId()->getId()) == 10001);

    wsc->deleteAudioClip(id2);
    CPPUNIT_ASSERT(!wsc->existsAudioClip(id2));
*/
}

