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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/AudioPlayerTest.cxx,v $

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

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/Core/AudioClip.h"
#include "LiveSupport/Core/Playlist.h"

#include "AudioPlayerTest.h"


using namespace LiveSupport::PlaylistExecutor;
using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::gLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(AudioPlayerTest);

/**
 *  The name of the configuration file for the audio player.
 */
static const std::string audioPlayerConfigFileName 
                                            = "etc/audioPlayer.xml";

/**
 *  The name of the configuration file for the local storage.
 */
static const std::string storageClientConfigFileName
                                            = "storageClient.xml";

/**
 *  The name of the configuration file for the authentication client.
 */
static const std::string authenticationClientConfigFileName 
                                            = "authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AudioPlayerTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                new xmlpp::DomParser(audioPlayerConfigFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        Ptr<AudioPlayerFactory>::Ref        audioPlayerFactory;

        audioPlayerFactory = AudioPlayerFactory::getInstance();
        audioPlayerFactory->configure(*root);

        // initialize the audio player configured by the factory
        Ptr<AudioPlayerInterface>::Ref      audioPlayer;
        audioPlayer = audioPlayerFactory->getAudioPlayer();
        audioPlayer->initialize();

    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in audio player configuration file: " 
                  << e.what() << std::endl;
    } catch (xmlpp::exception &e) {
        std::cerr << "syntax error in audio player configuration file: " 
                  << e.what() << std::endl;
    }

    try {
        xmlpp::DomParser    parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                            storageClientConfigFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        Ptr<StorageClientFactory>::Ref      storageClientFactory;

        storageClientFactory = StorageClientFactory::getInstance();
        storageClientFactory->configure(*root);

        // initialize the storage client configured by the factory
        Ptr<StorageClientInterface>::Ref    storage;
        storage = storageClientFactory->getStorageClient();
        storage->reset();

    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in storage client configuration file: " 
                  << e.what() << std::endl;
    } catch (xmlpp::exception &e) {
        std::cerr << "syntax error in storage client configuration file: " 
                  << e.what() << std::endl;
    }

    try {
        xmlpp::DomParser    parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                            authenticationClientConfigFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        Ptr<AuthenticationClientFactory>::Ref   authentClientFactory;

        authentClientFactory = AuthenticationClientFactory::getInstance();
        authentClientFactory->configure(*root);

        // log in using the authentication client
        Ptr<AuthenticationClientInterface>::Ref authent;
        authent = authentClientFactory->getAuthenticationClient();
        sessionId = authent->login("root", "q");

    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in authentication client "
                                                        "configuration file: " 
                  << e.what() << std::endl;
    } catch (xmlpp::exception &e) {
        std::cerr << "syntax error in authentication client "
                                                        "configuration file: " 
                  << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
AudioPlayerTest :: tearDown(void)                      throw ()
{
    // de-initialize the audio player configured by the factory
    Ptr<AudioPlayerFactory>::Ref    audioPlayerFactory;
    audioPlayerFactory = AudioPlayerFactory::getInstance();
    Ptr<AudioPlayerInterface>::Ref      audioPlayer;
    audioPlayer = audioPlayerFactory->getAudioPlayer();
    audioPlayer->deInitialize();

    // log out using the authentication client    
    Ptr<AuthenticationClientFactory>::Ref   authentClientFactory;
    authentClientFactory = AuthenticationClientFactory::getInstance();
    Ptr<AuthenticationClientInterface>::Ref authent;
    authent = authentClientFactory->getAuthenticationClient();
    CPPUNIT_ASSERT_NO_THROW(authent->logout(sessionId));
}


/*------------------------------------------------------------------------------
 *  Test to see if the audio player engine can be started and stopped
 *----------------------------------------------------------------------------*/
void
AudioPlayerTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AudioPlayerFactory>::Ref        audioPlayerFactory;

    audioPlayerFactory = AudioPlayerFactory::getInstance();
    CPPUNIT_ASSERT(audioPlayerFactory.get());

    Ptr<AudioPlayerInterface>::Ref      audioPlayer;

    audioPlayer = audioPlayerFactory->getAudioPlayer();
    CPPUNIT_ASSERT(audioPlayer.get());
    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
}


/*------------------------------------------------------------------------------
 *  Play an audio clip from storage.
 *----------------------------------------------------------------------------*/
void
AudioPlayerTest :: playAudioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AudioPlayerFactory>::Ref 
                    audioPlayerFactory = AudioPlayerFactory::getInstance();
    Ptr<AudioPlayerInterface>::Ref
                    audioPlayer        = audioPlayerFactory->getAudioPlayer();

    Ptr<StorageClientFactory>::Ref 
                    storageClientFactory = StorageClientFactory::getInstance();
    Ptr<StorageClientInterface>::Ref 
                    storage = storageClientFactory->getStorageClient();

    Ptr<UniqueId>::Ref      audioClipId(new UniqueId(0x10001));
    Ptr<AudioClip>::Ref     audioClip;
    CPPUNIT_ASSERT_NO_THROW(
        audioClip = storage->acquireAudioClip(sessionId, audioClipId)
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->open(*audioClip->getUri())
    );

    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(audioPlayer->start());
    CPPUNIT_ASSERT(audioPlayer->isPlaying());

    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));
    while (audioPlayer->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!audioPlayer->isPlaying());

    CPPUNIT_ASSERT_NO_THROW(
        storage->releaseAudioClip(audioClip)
    );
    audioPlayer->close();
}


/*------------------------------------------------------------------------------
 *  Play a playlist from storage.
 *----------------------------------------------------------------------------*/
void
AudioPlayerTest :: playPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AudioPlayerFactory>::Ref 
                    audioPlayerFactory = AudioPlayerFactory::getInstance();
    Ptr<AudioPlayerInterface>::Ref
                    audioPlayer        = audioPlayerFactory->getAudioPlayer();

    Ptr<StorageClientFactory>::Ref 
                    storageClientFactory = StorageClientFactory::getInstance();
    Ptr<StorageClientInterface>::Ref 
                    storage = storageClientFactory->getStorageClient();

    Ptr<UniqueId>::Ref      audioClipId(new UniqueId(2));
    Ptr<Playlist>::Ref      playlist;
    CPPUNIT_ASSERT_NO_THROW(
        playlist = storage->acquirePlaylist(sessionId, audioClipId)
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->open(*playlist->getUri())
    );

    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(audioPlayer->start());
    CPPUNIT_ASSERT(audioPlayer->isPlaying());

    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));
    while (audioPlayer->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!audioPlayer->isPlaying());

    CPPUNIT_ASSERT_NO_THROW(
        storage->releasePlaylist(playlist)
    );
    audioPlayer->close();
}

