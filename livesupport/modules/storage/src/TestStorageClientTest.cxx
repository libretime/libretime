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
    Version  : $Revision: 1.19 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/TestStorageClientTest.cxx,v $

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

#include "TestStorageClient.h"
#include "TestStorageClientTest.h"


using namespace std;
using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

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
TestStorageClientTest :: setUp(void)                         throw ()
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
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
        Ptr<UniqueId>::Ref  id1(new UniqueId(1));
        Ptr<UniqueId>::Ref  id2(new UniqueId(77));

        CPPUNIT_ASSERT(tsc->existsPlaylist(dummySessionId, id1));
        CPPUNIT_ASSERT(!tsc->existsPlaylist(dummySessionId, id2));

        Ptr<Playlist>::Ref  playlist = tsc->getPlaylist(dummySessionId, id1);
        CPPUNIT_ASSERT(playlist->getId()->getId() == id1->getId());
}


/*------------------------------------------------------------------------------
 *  Testing the deletePlaylist method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: deletePlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
        Ptr<UniqueId>::Ref      id1(new UniqueId(0x1));
        Ptr<UniqueId>::Ref      id2(new UniqueId(0x77));

        try {
            tsc->deletePlaylist(dummySessionId, id2);
            CPPUNIT_FAIL("allowed to delete non-existent playlist");
        } catch (StorageException &e) {
        }
        try {
            tsc->deletePlaylist(dummySessionId, id1);
        } catch (StorageException &e) {
            CPPUNIT_FAIL("cannot delete existing playlist");
        }
        try {
            tsc->deletePlaylist(dummySessionId, id1);
            CPPUNIT_FAIL("allowed to delete non-existent playlist");
        } catch (StorageException &e) {
        }
}


/*------------------------------------------------------------------------------
 *  Testing the getAllPlaylists method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: getAllPlaylistsTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
                        playlistVector = tsc->getAllPlaylists(dummySessionId);
    CPPUNIT_ASSERT(playlistVector->size() == 2);

    Ptr<Playlist>::Ref  playlist = (*playlistVector)[0];
    CPPUNIT_ASSERT((int) (playlist->getId()->getId()) == 0x1);
}


/*------------------------------------------------------------------------------
 *  Testing the createPlaylist method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: createPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<Playlist>::Ref playlist = tsc->createPlaylist(dummySessionId);

    CPPUNIT_ASSERT(tsc->existsPlaylist(dummySessionId, playlist->getId()));
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

    CPPUNIT_ASSERT(tsc->existsAudioClip(dummySessionId, id02));
    CPPUNIT_ASSERT(!tsc->existsAudioClip(dummySessionId, id77));

    Ptr<AudioClip>::Ref     audioClip = tsc->getAudioClip(dummySessionId, id02);
    CPPUNIT_ASSERT(audioClip->getId()->getId() == id02->getId());
    CPPUNIT_ASSERT(audioClip->getPlaylength()->total_seconds()
                                                   == 30*60);

    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
                            audioClipVector 
                            = tsc->getAllAudioClips(dummySessionId);
    CPPUNIT_ASSERT(audioClipVector->size() == 3);

    audioClip = (*audioClipVector)[0];
    CPPUNIT_ASSERT((int) (audioClip->getId()->getId()) == 0x10001);

    tsc->deleteAudioClip(dummySessionId, id02);
    CPPUNIT_ASSERT(!tsc->existsAudioClip(dummySessionId, id02));

    Ptr<const Glib::ustring>::Ref   title(new Glib::ustring("New Title"));
    Ptr<time_duration>::Ref         playlength(new time_duration(0,0,13,0));
    Ptr<const std::string>::Ref     uri;

    Ptr<AudioClip>::Ref     newAudioClip(new AudioClip(title, playlength, uri));
    CPPUNIT_ASSERT(!newAudioClip->getId());
    tsc->storeAudioClip(dummySessionId, newAudioClip);
    Ptr<UniqueId>::Ref      newId = newAudioClip->getId();
    CPPUNIT_ASSERT(newId);
    CPPUNIT_ASSERT(tsc->existsAudioClip(dummySessionId, newId));    
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
    }
    catch (StorageException &e) {
        std::string     eMsg = "could not acquire audio clip:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    string  audioClipUri("file://");
    audioClipUri += get_current_dir_name();
    audioClipUri += "/var/test10002.mp3";
    CPPUNIT_ASSERT(*(audioClip->getUri()) == audioClipUri);
    
    try {
        tsc->releaseAudioClip(dummySessionId, audioClip);
    }
    catch (StorageException &e) {
        std::string     eMsg = "could not release audio clip:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    try {
        audioClip = tsc->acquireAudioClip(dummySessionId, id77);
        CPPUNIT_FAIL("allowed to acquire non-existent audio clip");
    }
    catch (StorageException &e) {
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
    }
    catch (StorageException &e) {
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
    ifs1.close();

    string  savedTempFilePath = playlist->getUri()->substr(7);
    try {
        tsc->releasePlaylist(dummySessionId, playlist);
    }
    catch (StorageException &e) {
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
    }
    catch (StorageException &e) {
    }  
}
