/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

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

#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "TestStorageClient.h"
#include "TestStorageClientTest.h"


using namespace std;
using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(TestStorageClientTest);

/**
 *  The name of the configuration file for the storage client factory daemon.
 */
static const std::string configFileName = "etc/testStorage.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: setUp(void)            throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        tsc.reset(new TestStorageClient());
        tsc->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }
    
    dummySessionId.reset(new SessionId("dummy"));
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: tearDown(void)                      throw ()
{
    tsc.reset();
}


/*------------------------------------------------------------------------------
 *  Some very simple smoke tests
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref  id1(new UniqueId(1));
    Ptr<UniqueId>::Ref  id2(new UniqueId(77));

    try {
        CPPUNIT_ASSERT(tsc->existsPlaylist(dummySessionId, id1));
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        CPPUNIT_ASSERT(!tsc->existsPlaylist(dummySessionId, id2));
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<Playlist>::Ref  playlist;
    try {
        playlist = tsc->getPlaylist(dummySessionId, id1);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(playlist->getId());
    CPPUNIT_ASSERT(playlist->getId()->getId() == id1->getId());
}


/*------------------------------------------------------------------------------
 *  Test the getVersion function
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: getVersionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const Glib::ustring>::Ref   version;

    try {
        version = tsc->getVersion();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    CPPUNIT_ASSERT(version.get());
    CPPUNIT_ASSERT(*version == "TestStorage");
}


/*------------------------------------------------------------------------------
 *  Testing the reset method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: resetTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        tsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref searchResults
                                               = tsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults);
    CPPUNIT_ASSERT(searchResults->size() >= 4);

    Ptr<Playable>::Ref  playable = searchResults->at(0);
    CPPUNIT_ASSERT(playable);

    playable = searchResults->at(1);
    CPPUNIT_ASSERT(playable);

    playable = searchResults->at(2);
    CPPUNIT_ASSERT(playable);

    playable = searchResults->at(3);
    CPPUNIT_ASSERT(playable);
}


/*------------------------------------------------------------------------------
 *  Testing the createPlaylist method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: createPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref  playlistId;
    try {
        playlistId = tsc->createPlaylist(dummySessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        CPPUNIT_ASSERT(tsc->existsPlaylist(dummySessionId, playlistId));
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Testing the audio clip operations
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: audioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref    id02(new UniqueId(0x10002));
    Ptr<UniqueId>::Ref    id77(new UniqueId(0x10077));

    try {
        CPPUNIT_ASSERT( tsc->existsAudioClip(dummySessionId, id02));
        CPPUNIT_ASSERT(!tsc->existsAudioClip(dummySessionId, id77));
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<AudioClip>::Ref     audioClip;
    try {
        audioClip = tsc->getAudioClip(dummySessionId, id02);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(audioClip->getId()->getId() == id02->getId());
    CPPUNIT_ASSERT(audioClip->getPlaylength()->total_seconds()
                                                   == 12);

    try {
        tsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref searchResults
                                               = tsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults);
    CPPUNIT_ASSERT(searchResults->size() >= 3);

    Ptr<const Glib::ustring>::Ref   title(new Glib::ustring("New Title"));
    Ptr<time_duration>::Ref         playlength(new time_duration(0,0,13,0));
    Ptr<const std::string>::Ref     uri;

    Ptr<AudioClip>::Ref     newAudioClip(new AudioClip(title, playlength, uri));

    try {
        tsc->storeAudioClip(dummySessionId, newAudioClip);
        CPPUNIT_FAIL("Allowed to store audio clip without binary sound file.");
    } catch (XmlRpcException &e) {
    }
    
    uri.reset(new std::string("file:var/test10001.mp3"));
    newAudioClip->setUri(uri);
    try {
        CPPUNIT_ASSERT(!newAudioClip->getId());
        tsc->storeAudioClip(dummySessionId, newAudioClip);
        CPPUNIT_ASSERT(newAudioClip->getId());
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        CPPUNIT_ASSERT(tsc->existsAudioClip(dummySessionId, 
                                            newAudioClip->getId()));
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Testing the acquire / release operations
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: acquireAudioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref    id2(new UniqueId(0x10002));
    Ptr<UniqueId>::Ref    id77(new UniqueId(0x10077));
    Ptr<AudioClip>::Ref   audioClip;
    
    try {
        audioClip = tsc->acquireAudioClip(dummySessionId, id2);
    } catch (XmlRpcException &e) {
        std::string     eMsg = "could not acquire audio clip:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    string  audioClipUri("file://");
    char *  currentDirName = get_current_dir_name();
    audioClipUri += currentDirName;
    audioClipUri += "/var/test10002.mp3";
    free(currentDirName);
    CPPUNIT_ASSERT(*(audioClip->getUri()) == audioClipUri);
    
    try {
        tsc->releaseAudioClip(audioClip);
    } catch (XmlRpcException &e) {
        std::string     eMsg = "could not release audio clip:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    try {
        audioClip = tsc->acquireAudioClip(dummySessionId, id77);
        CPPUNIT_FAIL("allowed to acquire non-existent audio clip");
    } catch (XmlRpcException &e) {
    }
}


/*------------------------------------------------------------------------------
 *  Testing the acquire / release operations
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: acquirePlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id1(new UniqueId(0x1));
    Ptr<UniqueId>::Ref      id77(new UniqueId(0x77));
    Ptr<Playlist>::Ref      playlist;
    
    try {
        playlist = tsc->acquirePlaylist(dummySessionId, id1);
    } catch (XmlRpcException &e) {
        std::string     eMsg = "could not acquire playlist:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    CPPUNIT_ASSERT(playlist->getUri());
    CPPUNIT_ASSERT(playlist->getUri()->substr(0,7) == "file://");
    
    std::ifstream ifs1(playlist->getUri()->substr(7).c_str());
    if (!ifs1) {
        ifs1.close();
        CPPUNIT_FAIL("temp file not created correctly");
    }
//  std::cerr << "url:\n" << *playlist->getUri() << "\n";
//  sleep(30);
    ifs1.close();

    string  savedTempFilePath = playlist->getUri()->substr(7);
    try {
        tsc->releasePlaylist(playlist);
    } catch (XmlRpcException &e) {
        std::string     eMsg = "could not release playlist:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    CPPUNIT_ASSERT(!playlist->getUri());
    std::ifstream ifs2(savedTempFilePath.c_str());
    if (ifs2) {
        ifs2.close();
        CPPUNIT_FAIL("temp file not destroyed correctly");
    }
    ifs2.close();

    try {
        playlist = tsc->acquirePlaylist(dummySessionId, id77);
        CPPUNIT_FAIL("allowed to acquire non-existent playlist");
    } catch (XmlRpcException &e) {
    }  
}


/*------------------------------------------------------------------------------
 *  Search test.
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: searchTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria(
                                    "audioClip", 
                                    "dcterms:extent", "=", "00:00:11.000000"));

        int numberFound = tsc->search(dummySessionId, criteria);
        CPPUNIT_ASSERT(numberFound == 2);
        Ptr<std::vector<Ptr<Playable>::Ref> >::Ref
                                searchResults = tsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() == 2);
        CPPUNIT_ASSERT(searchResults->at(0)->getId()->getId() == 0x10001);
        CPPUNIT_ASSERT(searchResults->at(1)->getId()->getId() == 0x10003);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria("all", "or"));
        criteria->addCondition("dcterms:extent", ">",  "00:00:11.000000");
        criteria->addCondition("dc:title", "prefix", "Playlist");
        int numberFound = tsc->search(dummySessionId, criteria);
        CPPUNIT_ASSERT(numberFound == 3);
        Ptr<std::vector<Ptr<Playable>::Ref> >::Ref
                                searchResults = tsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() == 3);
        CPPUNIT_ASSERT(searchResults->at(0)->getId()->getId() == 0x10002);
        CPPUNIT_ASSERT(searchResults->at(1)->getId()->getId() == 1);
        CPPUNIT_ASSERT(searchResults->at(2)->getId()->getId() == 2);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria);
        criteria->setType("all");
        criteria->addCondition("dc:title", "partial",  "t");
        criteria->setLimit(2);
        criteria->setOffset(1);
        int numberFound = tsc->search(dummySessionId, criteria);
        CPPUNIT_ASSERT(numberFound == 4);
        Ptr<std::vector<Ptr<Playable>::Ref> >::Ref
                                searchResults = tsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() == 2);
        CPPUNIT_ASSERT(searchResults->at(0)->getId()->getId() == 0x10003);
        CPPUNIT_ASSERT(searchResults->at(1)->getId()->getId() == 1);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Testing getAllPlaylists() and getAllAudioClips().
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: getAllTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref 
                playlists = tsc->getAllPlaylists(dummySessionId);
    CPPUNIT_ASSERT(playlists);
    CPPUNIT_ASSERT(playlists->size() >= 2);
    
    Ptr<Playlist>::Ref  playlist = playlists->at(0);
    CPPUNIT_ASSERT(playlist);
    CPPUNIT_ASSERT(playlist->getId());
    CPPUNIT_ASSERT(playlist->getId()->getId() == 1);
    
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref 
                audioClips = tsc->getAllAudioClips(dummySessionId);
    CPPUNIT_ASSERT(audioClips);
    CPPUNIT_ASSERT(audioClips->size() >= 3);
    
    audioClips = tsc->getAllAudioClips(dummySessionId, 1, 1);
    CPPUNIT_ASSERT(audioClips);
    CPPUNIT_ASSERT(audioClips->size() == 1);

    Ptr<AudioClip>::Ref audioClip = audioClips->at(0);
    CPPUNIT_ASSERT(audioClip);
    CPPUNIT_ASSERT(audioClip->getId());
    CPPUNIT_ASSERT(audioClip->getId()->getId() == 0x10002);
}


/*------------------------------------------------------------------------------
 *  Testing the createBackupXxxx() functions.
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: createBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria);
    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = tsc->createBackupOpen(dummySessionId, criteria);
    );
    CPPUNIT_ASSERT(token);
    
    Ptr<const Glib::ustring>::Ref       url;
    Ptr<const Glib::ustring>::Ref       path;
    Ptr<const Glib::ustring>::Ref       errorMessage;
    AsyncState                          state;
    CPPUNIT_ASSERT_NO_THROW(
        state = tsc->createBackupCheck(*token, url, path, errorMessage);
    );
    CPPUNIT_ASSERT_EQUAL(AsyncState::pendingState, state);
    
    CPPUNIT_ASSERT_NO_THROW(
        tsc->createBackupClose(*token);
    );
}


/*------------------------------------------------------------------------------
 *  Testing the restoreBackupXxxx() functions.
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: restoreBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<Glib::ustring>::Ref     path(new Glib::ustring(
                                                "var/cowbell_backup.tar"));
    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = tsc->restoreBackupOpen(dummySessionId, path);
    );
    CPPUNIT_ASSERT(token);
    
    Ptr<const Glib::ustring>::Ref       errorMessage;
    AsyncState                          state;
    CPPUNIT_ASSERT_NO_THROW(
        state = tsc->restoreBackupCheck(*token, errorMessage);
    );
    CPPUNIT_ASSERT_EQUAL(AsyncState::pendingState, state);
    
    CPPUNIT_ASSERT_NO_THROW(
        tsc->restoreBackupClose(*token);
    );
}


/*------------------------------------------------------------------------------
 *  Testing the exportPlaylistXxxx() functions.
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: exportPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    exportPlaylistHelper(StorageClientInterface::internalFormat);
    exportPlaylistHelper(StorageClientInterface::smilFormat);
}


/*------------------------------------------------------------------------------
 *  Auxiliary function for exportPlaylistTest().
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: exportPlaylistHelper(
                            StorageClientInterface::ExportFormatType    format)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref          playlistId(new UniqueId(1));
    Ptr<Glib::ustring>::Ref     url(new Glib::ustring(""));
    Ptr<Glib::ustring>::Ref     token;
    
    CPPUNIT_ASSERT_NO_THROW(
        token = tsc->exportPlaylistOpen(
                                dummySessionId, playlistId, format, url);
    );
    CPPUNIT_ASSERT(token);
    CPPUNIT_ASSERT(url);
    CPPUNIT_ASSERT(*url != "");
    
    CPPUNIT_ASSERT_NO_THROW(
        tsc->exportPlaylistClose(token);
    );
}


/*------------------------------------------------------------------------------
 *  Testing the remoteSearchXxxx() functions.
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: remoteSearchTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria);
    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = tsc->remoteSearchOpen(dummySessionId, criteria);
    );
    CPPUNIT_ASSERT(token);
    
    Ptr<Glib::ustring>::Ref                 errorMessage(new Glib::ustring);
    AsyncState                              state;
    CPPUNIT_ASSERT_NO_THROW(
        state = tsc->checkTransport(token, errorMessage);
    );
    CPPUNIT_ASSERT_EQUAL(AsyncState::pendingState, state);
    
    CPPUNIT_ASSERT_THROW(
        tsc->remoteSearchClose(token), XmlRpcMethodFaultException
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        tsc->cancelTransport(dummySessionId, token);
    );
}

