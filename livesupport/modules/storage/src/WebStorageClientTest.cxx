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
    Version  : $Revision: 1.2 $
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
#include "WebStorageClientTest.h"


using namespace std;
using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(WebStorageClientTest);

/**
 *  The name of the configuration file for the storage client factory daemon.
 */
static const std::string configFileName = "etc/webStorage.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        wsc.reset(new WebStorageClient());
        wsc->configure(*root);
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
WebStorageClientTest :: tearDown(void)                      throw ()
{
    wsc.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id01(new UniqueId(10001));
    Ptr<UniqueId>::Ref      id77(new UniqueId(10077));

    CPPUNIT_ASSERT( wsc->existsAudioClip(id01));
    CPPUNIT_ASSERT(!wsc->existsAudioClip(id77));
}


/*------------------------------------------------------------------------------
 *  Testing the deletePlaylist method
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: deletePlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
        Ptr<UniqueId>::Ref      id1(new UniqueId(1));
        Ptr<UniqueId>::Ref      id2(new UniqueId(77));

        try {
            wsc->deletePlaylist(id2);
            CPPUNIT_FAIL("allowed to delete non-existent playlist");
        } catch (std::invalid_argument &e) {
        }
        try {
            wsc->deletePlaylist(id1);
        } catch (std::invalid_argument &e) {
            CPPUNIT_FAIL("cannot delete existing playlist");
        }
        try {
            wsc->deletePlaylist(id1);
            CPPUNIT_FAIL("allowed to delete non-existent playlist");
        } catch (std::invalid_argument &e) {
        }
}


/*------------------------------------------------------------------------------
 *  Testing the getAllPlaylists method
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: getAllPlaylistsTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref  playlistVector =
                                                wsc->getAllPlaylists();
    CPPUNIT_ASSERT(playlistVector->size() == 1);

    Ptr<Playlist>::Ref playlist = (*playlistVector)[0];
    CPPUNIT_ASSERT((int) (playlist->getId()->getId()) == 1);
}


/*------------------------------------------------------------------------------
 *  Testing the createPlaylist method
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: createPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<Playlist>::Ref playlist = wsc->createPlaylist();

    CPPUNIT_ASSERT(wsc->existsPlaylist(playlist->getId()));
}


/*------------------------------------------------------------------------------
 *  Testing the audio clip operations
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: audioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const UniqueId>::Ref  id2(new UniqueId(10002));
    Ptr<const UniqueId>::Ref  id77(new UniqueId(10077));

    CPPUNIT_ASSERT(wsc->existsAudioClip(id2));
    CPPUNIT_ASSERT(!wsc->existsAudioClip(id77));

    Ptr<AudioClip>::Ref       audioClip = wsc->getAudioClip(id2);
    CPPUNIT_ASSERT(audioClip->getId()->getId() == id2->getId());
    CPPUNIT_ASSERT(audioClip->getPlaylength()->total_seconds()
                                                   == 30*60);

    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref  audioClipVector =
                                                 wsc->getAllAudioClips();
    CPPUNIT_ASSERT(audioClipVector->size() == 2);

    audioClip = (*audioClipVector)[0];
    CPPUNIT_ASSERT((int) (audioClip->getId()->getId()) == 10001);

    wsc->deleteAudioClip(id2);
    CPPUNIT_ASSERT(!wsc->existsAudioClip(id2));
}


/*------------------------------------------------------------------------------
 *  Testing the acquire / release operations
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: acquireAudioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const UniqueId>::Ref    id2(new UniqueId(10002));
    Ptr<const UniqueId>::Ref    id77(new UniqueId(10077));
    Ptr<AudioClip>::Ref         audioClip;
    
    try {
        audioClip = wsc->acquireAudioClip(id2);
    }
    catch (std::logic_error &e) {
        std::string     eMsg = "could not acquire audio clip:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    string  audioClipUri("file://");
    audioClipUri += get_current_dir_name();
    audioClipUri += "/var/test2.mp3";
    CPPUNIT_ASSERT(*(audioClip->getUri()) == audioClipUri);
    
    try {
        wsc->releaseAudioClip(audioClip);
    }
    catch (std::logic_error &e) {
        std::string     eMsg = "could not release audio clip:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    try {
        audioClip = wsc->acquireAudioClip(id77);
        CPPUNIT_FAIL("allowed to acquire non-existent audio clip");
    }
    catch (std::logic_error &e) {
    }
}


/*------------------------------------------------------------------------------
 *  Testing the acquire / release operations
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: acquirePlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id1(new UniqueId(1));
    Ptr<UniqueId>::Ref      id77(new UniqueId(77));
    Ptr<Playlist>::Ref      playlist;
    
    try {
        playlist = wsc->acquirePlaylist(id1);
    }
    catch (std::logic_error &e) {
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
        wsc->releasePlaylist(playlist);
    }
    catch (std::logic_error &e) {
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
        playlist = wsc->acquirePlaylist(id77);
        CPPUNIT_FAIL("allowed to acquire non-existent playlist");
    }
    catch (std::logic_error &e) {
    }  
}
