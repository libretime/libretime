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
    Version  : $Revision: 1.43 $
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

#include "LiveSupport/Core/XmlRpcException.h"
#include "LiveSupport/Core/XmlRpcCommunicationException.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Core/XmlRpcMethodResponseException.h"
#include "LiveSupport/Core/XmlRpcInvalidArgumentException.h"
#include "LiveSupport/Core/XmlRpcIOException.h"

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
static const std::string storageConfigFileName = "webStorage.xml";

/**
 *  The name of the configuration file for the authentication factory.
 */
static const std::string authenticationFactoryConfigFileName 
                         = "webAuthenticationClient.xml";


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
        xmlpp::DomParser    parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                         authenticationFactoryConfigFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        acf->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in authentication configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing authentication configuration file");
    }

    authentication  = acf->getAuthenticationClient();
    
    try {
        xmlpp::DomParser    parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                        storageConfigFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        wsc.reset(new WebStorageClient());
        wsc->configure(*root);
        wsc->reset();
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

    try {
        authentication->logout(sessionId);
        CPPUNIT_FAIL("allowed logout operation without login");
    } catch (XmlRpcException &e) {
    }

    try {
        sessionId = authentication->login("noSuchUser", "incorrectPassword");
        CPPUNIT_FAIL("Allowed login with incorrect password.");
    } catch (XmlRpcException &e) {
    }

    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        std::string eMsg = "Login failed.";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    try {
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        std::string eMsg = "Login failed.";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Test the getVersion function
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: getVersionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const Glib::ustring>::Ref   version;

    try {
        version = wsc->getVersion();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    CPPUNIT_ASSERT(version.get());
}


/*------------------------------------------------------------------------------
 *  Testing the playlist operations
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: playlistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        wsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() >= 4);
    Ptr<UniqueId>::Ref  audioClipId = wsc->getAudioClipIds()->at(3);

    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(sessionId);


    // test createPlaylist()
    Ptr<UniqueId>::Ref  playlistIdxx;
    try{
        playlistIdxx = wsc->createPlaylist(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(playlistIdxx);
    

    // test existsPlaylist()
    bool exists = false;
    try {
        exists = wsc->existsPlaylist(sessionId, playlistIdxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(exists);

    Ptr<UniqueId>::Ref  playlistId77(new UniqueId(77));
    try {
        exists = wsc->existsPlaylist(sessionId, playlistId77);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!exists);


    // test editPlaylist()
    Ptr<Playlist>::Ref  playlist;
    try {
        playlist = wsc->editPlaylist(sessionId, playlistIdxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(playlist);
    
    try {
        playlist = wsc->editPlaylist(sessionId, playlistIdxx);
        CPPUNIT_FAIL("allowed to open playlist for editing twice");
    } catch (Core::XmlRpcInvalidArgumentException &e) {
    } catch (XmlRpcException &e) {
        std::string eMsg = "editPlaylist() threw unexpected exception:\n";
        CPPUNIT_FAIL(eMsg + e.what());
    }
    CPPUNIT_ASSERT(playlist);
    
    Ptr<AudioClip>::Ref     audioClip;
    try {
        audioClip = wsc->getAudioClip(sessionId, audioClipId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<time_duration>::Ref relativeOffset(new time_duration(0,0,0,0));    
    playlist->addAudioClip(audioClip, relativeOffset);
    relativeOffset.reset(new time_duration(0,0,3,0));    
    playlist->addAudioClip(audioClip, relativeOffset);
    relativeOffset.reset(new time_duration(0,0,6,0));    
    playlist->addAudioClip(audioClip, relativeOffset);

    CPPUNIT_ASSERT(playlist->valid());      // WARNING: side effect; fixes the
                                            //      playlength of the playlist
    try {
        Ptr<Playlist>::Ref  throwAwayPlaylist
                            = wsc->getPlaylist(sessionId, playlistIdxx);
        // we are editing it, get another working copy
        CPPUNIT_ASSERT(throwAwayPlaylist->getPlaylength()
                                        ->total_seconds() == 9);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        Ptr<SessionId>::Ref newSessionId = authentication->login("root", "q");
        Ptr<Playlist>::Ref  throwAwayPlaylist
                            = wsc->getPlaylist(newSessionId, playlistIdxx);
        // somebody else is editing it, get the old copy
        CPPUNIT_ASSERT(throwAwayPlaylist->getPlaylength()
                                        ->total_seconds() == 0);
        authentication->logout(newSessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        wsc->savePlaylist(sessionId, playlist);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }


    // test getPlaylist()
    Ptr<Playlist>::Ref      newPlaylist;
    try {
        newPlaylist = wsc->getPlaylist(sessionId, playlistIdxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(newPlaylist);
    CPPUNIT_ASSERT(newPlaylist->getPlaylength()->total_seconds() 
                   == playlist->getPlaylength()->total_seconds());
    // NOTE: we really ought to define == for playlists...


    // test acquirePlaylist() and releasePlaylist()
    try {
        newPlaylist = wsc->acquirePlaylist(sessionId, playlistIdxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(newPlaylist);
    CPPUNIT_ASSERT(newPlaylist->getUri());
//  std::cerr << "url:\n" << *newPlaylist->getUri() << "\n";
//  sleep(30);

    std::ifstream ifs(newPlaylist->getUri()->substr(7).c_str());
    if (!ifs) {                                            // cut off "file://"
        ifs.close();
        CPPUNIT_FAIL("playlist temp file not found");
    }
//  std::stringstream   playlistSmilFile;
//  std::string         tempString;
//  while (ifs) {
//      std::getline(ifs, tempString);
//      playlistSmilFile << tempString << "\n";
//  }
//  std::cerr << "smil:\n" << playlistSmilFile.str() << "\n";
//  sleep(60);
    ifs.close();

    try {
        wsc->releasePlaylist(newPlaylist);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!newPlaylist->getUri());

/*
    // test deletePlaylist()
    try {
        wsc->deletePlaylist(sessionId, playlistIdxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        exists = wsc->existsPlaylist(sessionId, playlistIdxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!exists);
*/
}


/*------------------------------------------------------------------------------
 *  Testing the audio clip operations
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: audioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        wsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() >= 2);
    Ptr<UniqueId>::Ref  id01 = wsc->getAudioClipIds()->at(1);
    
//    std::cout << "\nReset storage result:\n";
//    for (unsigned i=0; i < wsc->getAudioClipIds()->size(); i++) {
//        std::cout << std::hex << std::string(*wsc->getAudioClipIds()->at(i))
//                  << std::endl;
//    } 

    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(sessionId);


    // test existsAudioClip(), deleteAudioClip() and getAudioClip()
    bool exists = false;;
    try {
        exists = wsc->existsAudioClip(sessionId, id01);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(exists);

    Ptr<AudioClip>::Ref audioClip;
    try {
        audioClip = wsc->getAudioClip(sessionId, id01);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
/*
    try {
        wsc->deleteAudioClip(sessionId, id01);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        exists = wsc->existsAudioClip(sessionId, id01);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!exists);
*/
    Ptr<UniqueId>::Ref  id77(new UniqueId(10077));
    try {
        exists = wsc->existsAudioClip(sessionId, id77);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!exists);


    // test storeAudioClip() and getAudioClip()
    Ptr<const Glib::ustring>::Ref title(new Glib::ustring(
                                                    "Muppet Show theme"));
    Ptr<time_duration>::Ref playlength(new time_duration(0,0,11,0));
    Ptr<const std::string>::Ref   uri(new std::string(
                                                    "file:var/test10001.mp3"));
    audioClip.reset(new AudioClip(title, playlength, uri));

    try {    
        wsc->storeAudioClip(sessionId, audioClip);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    CPPUNIT_ASSERT(audioClip->getId());
    Ptr<UniqueId>::Ref  idxx = audioClip->getId();

    try {
        CPPUNIT_ASSERT( wsc->existsAudioClip(sessionId, idxx));
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    Ptr<AudioClip>::Ref     newAudioClip;
    try {
        newAudioClip = wsc->getAudioClip(sessionId, idxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    CPPUNIT_ASSERT(std::string(*newAudioClip->getId()) == std::string(*idxx));
    CPPUNIT_ASSERT(newAudioClip->getPlaylength()->total_seconds()
                   == audioClip->getPlaylength()->total_seconds());


    // test acquireAudioClip() and releaseAudioClip()
    try {
        newAudioClip = wsc->acquireAudioClip(sessionId, idxx);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(newAudioClip->getUri());
//  std::cerr << *newAudioClip->getUri() << std::endl;
//  sleep(30);

    try {
        wsc->releaseAudioClip(newAudioClip);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!newAudioClip->getUri());

    try{
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Simple playlist saving test.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: simplePlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        wsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() >= 1);
    Ptr<UniqueId>::Ref  audioClipId = wsc->getAudioClipIds()->at(0);

    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(sessionId);


    // test createPlaylist()
    Ptr<UniqueId>::Ref  playlistId;
    try{
        playlistId = wsc->createPlaylist(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(playlistId);
    
    // test editPlaylist()
    Ptr<Playlist>::Ref  playlist;
    try {
        playlist = wsc->editPlaylist(sessionId, playlistId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(playlist);
    
    Ptr<AudioClip>::Ref     audioClip;
    try {
        audioClip = wsc->getAudioClip(sessionId, audioClipId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<Glib::ustring>::Ref     title(new Glib::ustring("simple playlist"));

    playlist->addAudioClip(audioClip, playlist->getPlaylength());
    playlist->setTitle(title);

    try {
        wsc->savePlaylist(sessionId, playlist);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    // test getPlaylist()
    Ptr<Playlist>::Ref      newPlaylist;
    try {
        newPlaylist = wsc->getPlaylist(sessionId, playlistId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(newPlaylist);
    CPPUNIT_ASSERT(newPlaylist->getPlaylength()->total_seconds() 
                   == playlist->getPlaylength()->total_seconds());
    CPPUNIT_ASSERT(newPlaylist->getTitle().get());
    CPPUNIT_ASSERT(*newPlaylist->getTitle() == *title);

    try{
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Search test.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: searchTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        wsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() >= 6);
    Ptr<UniqueId>::Ref  audioClip0 = wsc->getAudioClipIds()->at(0);
    Ptr<UniqueId>::Ref  audioClip1 = wsc->getAudioClipIds()->at(1);
    Ptr<UniqueId>::Ref  audioClip2 = wsc->getAudioClipIds()->at(2);
    Ptr<UniqueId>::Ref  audioClip3 = wsc->getAudioClipIds()->at(3);
    Ptr<UniqueId>::Ref  audioClip4 = wsc->getAudioClipIds()->at(4);
    Ptr<UniqueId>::Ref  audioClip5 = wsc->getAudioClipIds()->at(5);
    CPPUNIT_ASSERT(wsc->getPlaylistIds()->size() >= 1);
    Ptr<UniqueId>::Ref  playlist0  = wsc->getPlaylistIds()->at(0);
    Ptr<UniqueId>::Ref  playlist1  = wsc->getPlaylistIds()->at(1);

    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(sessionId);

    try {
        Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria(
                                            "audioClip",
                                            "dc:title", "prefix", "File "));
        int numberFound = wsc->search(sessionId, criteria);
        CPPUNIT_ASSERT(numberFound == 1);
        CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() == 1);
        CPPUNIT_ASSERT(*wsc->getAudioClipIds()->at(0) == *audioClip3);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    
    try {
        Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria(
                                                            "playlist", "or"));
        criteria->addCondition("dcterms:extent", ">=", "0");
        criteria->setLimit(10);
        int numberFound = wsc->search(sessionId, criteria);
        CPPUNIT_ASSERT(numberFound >= 2);
        CPPUNIT_ASSERT(wsc->getPlaylistIds()->size() >= 2);
        CPPUNIT_ASSERT(*wsc->getPlaylistIds()->at(0) == *playlist0);
        CPPUNIT_ASSERT(*wsc->getPlaylistIds()->at(1) == *playlist1);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria(
                                    "audioClip", 
                                    "dcterms:extent", ">=", "00:01:00.00000"));
        criteria->setLogicalOperator("and");
        criteria->addCondition("dc:title", "partial",  "Title ");
        int numberFound = wsc->search(sessionId, criteria);
        CPPUNIT_ASSERT(numberFound == 1);
        CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() == 1);
        CPPUNIT_ASSERT(*wsc->getAudioClipIds()->at(0) == *audioClip4);
        CPPUNIT_ASSERT(wsc->getPlaylistIds()->size() == 0);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria("all", "or"));
        criteria->addCondition("dcterms:extent", ">",  "00:00:15.000000");
        criteria->addCondition("dc:title", "prefix", "My");
        int numberFound = wsc->search(sessionId, criteria);
        CPPUNIT_ASSERT(numberFound >= 4);
        CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() >= 2);
        CPPUNIT_ASSERT(*wsc->getAudioClipIds()->at(0) == *audioClip4);
        CPPUNIT_ASSERT(*wsc->getAudioClipIds()->at(1) == *audioClip5);
        CPPUNIT_ASSERT(wsc->getPlaylistIds()->size() >= 2);
        CPPUNIT_ASSERT(*wsc->getPlaylistIds()->at(0)  == *playlist0);
        CPPUNIT_ASSERT(*wsc->getPlaylistIds()->at(1)  == *playlist1);

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
        criteria->setOffset(3);
        int numberFound = wsc->search(sessionId, criteria);
        CPPUNIT_ASSERT(numberFound >= 5);
        CPPUNIT_ASSERT(wsc->getAudioClipIds()->size() == 2);
        CPPUNIT_ASSERT(*wsc->getAudioClipIds()->at(0)  == *audioClip4);
        CPPUNIT_ASSERT(*wsc->getAudioClipIds()->at(1)  == *audioClip5);
        CPPUNIT_ASSERT(wsc->getPlaylistIds()->size() == 0);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (Core::XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try{
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Browse test.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: browseTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        wsc->reset()
    );

    Ptr<SessionId>::Ref sessionId;
    CPPUNIT_ASSERT_NO_THROW(
        sessionId = authentication->login("root", "q")
    );
    CPPUNIT_ASSERT(sessionId);

    Ptr<Glib::ustring>::Ref     metadata(new Glib::ustring("dc:title"));
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria());
    Ptr<std::vector<Glib::ustring> >::Ref   values;
    CPPUNIT_ASSERT_NO_THROW(
        values = wsc->browse(sessionId, metadata, criteria)
    );
    CPPUNIT_ASSERT(values);
    CPPUNIT_ASSERT(values->size() >= 6);

    metadata.reset(new Glib::ustring("dcterms:extent"));
    criteria.reset(new SearchCriteria("all", "dc:title", "=", "one"));
    CPPUNIT_ASSERT_NO_THROW(
        values = wsc->browse(sessionId, metadata, criteria)
    );
    CPPUNIT_ASSERT(values);
    CPPUNIT_ASSERT(values->size() >= 1);
    CPPUNIT_ASSERT((*values)[0] == Glib::ustring("00:00:11.000000"));
}


/*------------------------------------------------------------------------------
 *  Testing getAllPlaylists() and getAllAudioClips().
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: getAllTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(sessionId);

    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref 
                playlists = wsc->getAllPlaylists(sessionId);
    CPPUNIT_ASSERT(playlists);
    CPPUNIT_ASSERT(playlists->size() >= 1);
    
    Ptr<Playlist>::Ref  playlist = playlists->at(0);
    CPPUNIT_ASSERT(playlist);
    CPPUNIT_ASSERT(playlist->getId());
    CPPUNIT_ASSERT(playlist->getId()->getId() == 1);
    
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref 
                audioClips = wsc->getAllAudioClips(sessionId);
    CPPUNIT_ASSERT(audioClips);
    CPPUNIT_ASSERT(audioClips->size() >= 5);
    
    audioClips = wsc->getAllAudioClips(sessionId, 2, 1);
    CPPUNIT_ASSERT(audioClips);
    CPPUNIT_ASSERT(audioClips->size() == 2);

    Ptr<AudioClip>::Ref audioClip = audioClips->at(0);
    CPPUNIT_ASSERT(audioClip);
    CPPUNIT_ASSERT(audioClip->getId());
    CPPUNIT_ASSERT(audioClip->getId()->getId() == 0x10002);

    try{
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}

