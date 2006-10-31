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

#include "WebStorageClient.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Core/XmlRpcException.h"
#include "LiveSupport/Core/XmlRpcCommunicationException.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Core/XmlRpcMethodResponseException.h"
#include "LiveSupport/Core/XmlRpcInvalidArgumentException.h"
#include "LiveSupport/Core/XmlRpcIOException.h"
#include "LiveSupport/Core/FileTools.h"

#include "WebStorageClientTest.h"

using namespace std;
using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::StorageClient;

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
WebStorageClientTest :: setUp(void)             throw (CPPUNIT_NS::Exception)
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
        std::string     eMsg = "semantic error in "
                               "authentication configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    } catch (xmlpp::exception &e) {
        std::string     eMsg = "error parsing "
                               "authentication configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    authentication  = acf->getAuthenticationClient();
    
    try {
        xmlpp::DomParser    parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                        storageConfigFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        wsc.reset(new WebStorageClient());
        wsc->configure(*root);
    } catch (std::invalid_argument &e) {
        std::string     eMsg = "semantic error in "
                               "authentication configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    } catch (xmlpp::exception &e) {
        std::string     eMsg = "error parsing "
                               "authentication configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    
    CPPUNIT_ASSERT_NO_THROW(
        wsc->reset()
    );
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
    
    // localSearchResults was filled by reset() with a list of all items
    // in the storage
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref 
                            searchResults = wsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults->size() >= 7);
    Ptr<AudioClip>::Ref     audioClip = searchResults->at(4)->getAudioClip();
    CPPUNIT_ASSERT(audioClip);

    Ptr<time_duration>::Ref relativeOffset(new time_duration(0,0,0,0));    
    playlist->addAudioClip(audioClip, relativeOffset);
    relativeOffset.reset(new time_duration(0,0,3,0));    
    playlist->addAudioClip(audioClip, relativeOffset);
    relativeOffset.reset(new time_duration(0,0,6,0));    
    playlist->addAudioClip(audioClip, relativeOffset);

    CPPUNIT_ASSERT(playlist->valid());
    
    try {
        Ptr<Playlist>::Ref  throwAwayPlaylist
                            = wsc->getPlaylist(sessionId, playlistIdxx);
        // we are editing it, get another pointer to the edited object
        CPPUNIT_ASSERT(throwAwayPlaylist->getPlaylength()
                                        ->total_seconds() == 9);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        Ptr<SessionId>::Ref newSessionId = authentication->login("root", "q");
        Ptr<Playlist>::Ref  throwAwayPlaylist
                            = wsc->getPlaylist(newSessionId, playlistIdxx);
        // somebody else is editing it, get a new object with the old data
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

    std::ifstream ifs(newPlaylist->getUri()->substr(7).c_str());
    if (!ifs) {                                            // cut off "file://"
        ifs.close();
        CPPUNIT_FAIL("playlist temp file not found");
    }
    ifs.close();

    try {
        wsc->releasePlaylist(newPlaylist);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!newPlaylist->getUri());
}


/*------------------------------------------------------------------------------
 *  Test on an embedded playlist
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: embeddedPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        wsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref  searchResults
                                                = wsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults->size() >= 7);
    Ptr<AudioClip>::Ref     audioClip = searchResults->at(6)->getAudioClip();
    CPPUNIT_ASSERT(audioClip);
    Ptr<Playlist>::Ref      playlist  = searchResults->at(2)->getPlaylist();
    CPPUNIT_ASSERT(playlist);

    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(sessionId);


    // test acquirePlaylist()
    try {
        playlist = wsc->acquirePlaylist(sessionId, playlist->getId());
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(playlist);
    CPPUNIT_ASSERT(playlist->getUri());

    std::ifstream ifs(playlist->getUri()->substr(7).c_str());
    if (!ifs) {                                            // cut off "file://"
        ifs.close();
        CPPUNIT_FAIL("playlist temp file not found");
    }
    ifs.close();

    // test releasePlaylist()
    try {
        wsc->releasePlaylist(playlist);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!playlist->getUri());
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
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref  searchResults
                                                = wsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults->size() >= 7);
    Ptr<AudioClip>::Ref     audioClip = searchResults->at(6)->getAudioClip();
    CPPUNIT_ASSERT(audioClip);

    Ptr<SessionId>::Ref sessionId;
    try {
        sessionId = authentication->login("root", "q");
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(sessionId);


    // test existsAudioClip() and getAudioClip()
    bool exists = false;;
    try {
        exists = wsc->existsAudioClip(sessionId, audioClip->getId());
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(exists);

    try {
        audioClip = wsc->getAudioClip(sessionId, audioClip->getId());
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }

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
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref  searchResults
                                                = wsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults->size() >= 7);
    Ptr<AudioClip>::Ref     audioClip = searchResults->at(6)->getAudioClip();
    CPPUNIT_ASSERT(audioClip);

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
    
    try {
        audioClip = wsc->getAudioClip(sessionId, audioClip->getId());
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
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref  searchResults
                                                = wsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults->size() >= 9);
    Ptr<Playlist>::Ref      playlist0  = searchResults->at(0)->getPlaylist();
    Ptr<Playlist>::Ref      playlist1  = searchResults->at(1)->getPlaylist();
    Ptr<Playlist>::Ref      playlist2  = searchResults->at(2)->getPlaylist();
    Ptr<AudioClip>::Ref     audioClip0 = searchResults->at(3)->getAudioClip();
    Ptr<AudioClip>::Ref     audioClip1 = searchResults->at(4)->getAudioClip();
    Ptr<AudioClip>::Ref     audioClip2 = searchResults->at(5)->getAudioClip();
    Ptr<AudioClip>::Ref     audioClip3 = searchResults->at(6)->getAudioClip();
    Ptr<AudioClip>::Ref     audioClip4 = searchResults->at(7)->getAudioClip();
    Ptr<AudioClip>::Ref     audioClip5 = searchResults->at(8)->getAudioClip();
    
    CPPUNIT_ASSERT(playlist0);
    CPPUNIT_ASSERT(playlist1);
    CPPUNIT_ASSERT(playlist2);
    CPPUNIT_ASSERT(audioClip0);
    CPPUNIT_ASSERT(audioClip1);
    CPPUNIT_ASSERT(audioClip2);
    CPPUNIT_ASSERT(audioClip3);
    CPPUNIT_ASSERT(audioClip4);
    CPPUNIT_ASSERT(audioClip5);
    
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
        searchResults = wsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() == 1);
        CPPUNIT_ASSERT(*searchResults->at(0)->getId() == *audioClip1->getId());

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
        searchResults = wsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() >= 2);
        CPPUNIT_ASSERT(*searchResults->at(0)->getId() == *playlist0->getId());
        CPPUNIT_ASSERT(*searchResults->at(1)->getId() == *playlist1->getId());

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
        searchResults = wsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() == 1);
        CPPUNIT_ASSERT(*searchResults->at(0)->getId() == *audioClip0->getId());

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
        CPPUNIT_ASSERT(numberFound >= 5);
        searchResults = wsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() >= 5);
        CPPUNIT_ASSERT(*searchResults->at(0)->getId() == *playlist0->getId());
        CPPUNIT_ASSERT(*searchResults->at(3)->getId() == *audioClip0->getId());

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
        searchResults = wsc->getLocalSearchResults();
        CPPUNIT_ASSERT(searchResults->size() == 2);
        CPPUNIT_ASSERT(*searchResults->at(0)->getId() == *audioClip4->getId());
        CPPUNIT_ASSERT(*searchResults->at(1)->getId() == *audioClip5->getId());

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
 *  Another search test.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: searchUnicodeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        wsc->reset();
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
    Ptr<std::vector<Ptr<Playable>::Ref> >::Ref  searchResults
                                                = wsc->getLocalSearchResults();
    CPPUNIT_ASSERT(searchResults->size() >= 9);
    Ptr<AudioClip>::Ref     audioClip1 = searchResults->at(4)->getAudioClip();
    
    CPPUNIT_ASSERT(audioClip1);

    Ptr<Glib::ustring>::Ref     creator;
    CPPUNIT_ASSERT_NO_THROW(
        creator = audioClip1->getMetadata("dc:creator")
    );
    CPPUNIT_ASSERT(
        *creator == "János Kőbor"
    );
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

    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref playlists;
    CPPUNIT_ASSERT_NO_THROW(
        playlists = wsc->getAllPlaylists(sessionId)
    );
    CPPUNIT_ASSERT(playlists);
    CPPUNIT_ASSERT(playlists->size() >= 1);
    
    Ptr<Playlist>::Ref  playlist = playlists->at(0);
    CPPUNIT_ASSERT(playlist);
    CPPUNIT_ASSERT(playlist->getId());
    CPPUNIT_ASSERT(playlist->getId()->getId() == 3);
    
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref 
                audioClips = wsc->getAllAudioClips(sessionId);
    CPPUNIT_ASSERT(audioClips);
    CPPUNIT_ASSERT(audioClips->size() >= 5);
    
    audioClips = wsc->getAllAudioClips(sessionId, 2, 4);
    CPPUNIT_ASSERT(audioClips);
    CPPUNIT_ASSERT(audioClips->size() == 2);

    Ptr<AudioClip>::Ref audioClip = audioClips->at(0);
    CPPUNIT_ASSERT(audioClip);
    CPPUNIT_ASSERT(audioClip->getId());
    CPPUNIT_ASSERT(audioClip->getId()->getId() == 0x10003);

    try{
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Testing the createBackupXxxx() functions.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: createBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref         sessionId;
    CPPUNIT_ASSERT_NO_THROW(
        sessionId = authentication->login("root", "q");
    );
    CPPUNIT_ASSERT(sessionId);
    
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria);
    criteria->setLimit(10);
    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = wsc->createBackupOpen(sessionId, criteria);
    );
    CPPUNIT_ASSERT(token);
    
    Ptr<const Glib::ustring>::Ref           url;
    Ptr<const Glib::ustring>::Ref           path;
    Ptr<const Glib::ustring>::Ref           errorMessage;
    AsyncState                              state;
    
    int     iterations = 20;
    do {
        std::cerr << "-/|\\"[iterations%4] << '\b';
        sleep(1);
        CPPUNIT_ASSERT_NO_THROW(
            state = wsc->createBackupCheck(*token, url, path, errorMessage);
        );
        CPPUNIT_ASSERT(state == AsyncState::pendingState
                         || state == AsyncState::finishedState
                         || state == AsyncState::failedState);
    } while (--iterations && state == AsyncState::pendingState);
    
    CPPUNIT_ASSERT_EQUAL(AsyncState::finishedState, state);
    // TODO: test accessibility of the URL?
    
    CPPUNIT_ASSERT_NO_THROW(
        wsc->createBackupClose(*token);
    );
    // TODO: test non-accessibility of the URL?
    
    CPPUNIT_ASSERT_NO_THROW(
        authentication->logout(sessionId);
    );
}


/*------------------------------------------------------------------------------
 *  Testing the restoreBackupXxxx() functions.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: restoreBackupTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref         sessionId;
    CPPUNIT_ASSERT_NO_THROW(
        sessionId = authentication->login("root", "q");
    );
    CPPUNIT_ASSERT(sessionId);
    
    Ptr<UniqueId>::Ref          audioClipId(new UniqueId("7c215b48a9c827e6"));
    CPPUNIT_ASSERT(
        !wsc->existsAudioClip(sessionId, audioClipId)
    );
    
    Ptr<Glib::ustring>::Ref     path(new Glib::ustring());
    char *                      currentDirName = get_current_dir_name();
    path->append(currentDirName);
    path->append("/var/cowbell_backup.tar");
    free(currentDirName);
    
    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = wsc->restoreBackupOpen(sessionId, path);
    );
    CPPUNIT_ASSERT(token);
    
    Ptr<const Glib::ustring>::Ref           errorMessage;
    AsyncState                              state;
    
    int     iterations = 20;
    do {
        std::cerr << "-/|\\"[iterations%4] << '\b';
        sleep(1);
        CPPUNIT_ASSERT_NO_THROW(
            state = wsc->restoreBackupCheck(*token, errorMessage);
        );
        CPPUNIT_ASSERT(state == AsyncState::pendingState
                         || state == AsyncState::finishedState
                         || state == AsyncState::failedState);
    } while (--iterations && state == AsyncState::pendingState);
    
    CPPUNIT_ASSERT_EQUAL(AsyncState::finishedState, state);
    
    CPPUNIT_ASSERT_NO_THROW(
        wsc->createBackupClose(*token);
    );
    
    CPPUNIT_ASSERT(
        wsc->existsAudioClip(sessionId, audioClipId)
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        authentication->logout(sessionId);
    );
}


/*------------------------------------------------------------------------------
 *  Testing the exportPlaylistXxxx() functions.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: exportPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref          playlistId(new UniqueId(1));
    
    exportPlaylistHelper(playlistId, StorageClientInterface::internalFormat);
    exportPlaylistHelper(playlistId, StorageClientInterface::smilFormat);
}


/*------------------------------------------------------------------------------
 *  Auxiliary function for exportPlaylistTest().
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: exportPlaylistHelper(
                    Ptr<UniqueId>::Ref                          playlistId,
                    StorageClientInterface::ExportFormatType    format)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref         sessionId;
    CPPUNIT_ASSERT_NO_THROW(
        sessionId = authentication->login("root", "q");
    );
    CPPUNIT_ASSERT(sessionId);
    
    Ptr<Glib::ustring>::Ref     url(new Glib::ustring(""));
    Ptr<Glib::ustring>::Ref     token;
    
    CPPUNIT_ASSERT_NO_THROW(
        token = wsc->exportPlaylistOpen(sessionId, playlistId, format, url);
    );
    CPPUNIT_ASSERT(token);
    CPPUNIT_ASSERT(url);
    CPPUNIT_ASSERT_NO_THROW(
        FileTools::copyUrlToFile(*url, "tmp/testExportedPlaylist.tar")
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        wsc->exportPlaylistClose(token);
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        authentication->logout(sessionId);
    );
}


/*------------------------------------------------------------------------------
 *  Testing the importPlaylist() function.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: importPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref         sessionId;
    CPPUNIT_ASSERT_NO_THROW(
        sessionId = authentication->login("root", "q")
    );
    CPPUNIT_ASSERT(sessionId);
        
    Ptr<UniqueId>::Ref          playlistId;
    CPPUNIT_ASSERT_NO_THROW(
        playlistId = wsc->createPlaylist(sessionId)
    );
    
    Ptr<Playlist>::Ref          playlist;
    CPPUNIT_ASSERT_NO_THROW(
        playlist = wsc->editPlaylist(sessionId, playlistId)
    );
    
    Ptr<UniqueId>::Ref          audioClipId(new UniqueId(0x10001));
    Ptr<AudioClip>::Ref         audioClip;
    CPPUNIT_ASSERT_NO_THROW(
        audioClip = wsc->getAudioClip(sessionId, audioClipId)
    );
    
    Ptr<time_duration>::Ref     relativeOffset(new time_duration(0,0,0,0));
    CPPUNIT_ASSERT_NO_THROW(
        playlist->addAudioClip(audioClip, relativeOffset)
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        wsc->savePlaylist(sessionId, playlist)
    );
    
    exportPlaylistHelper(playlistId, StorageClientInterface::internalFormat);
    
    Ptr<Glib::ustring>::Ref     path(new Glib::ustring(
                                            "tmp/testExportedPlaylist.tar"));
    CPPUNIT_ASSERT_NO_THROW(
        wsc->importPlaylist(sessionId, path)
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        authentication->logout(sessionId);
    );
}


/*------------------------------------------------------------------------------
 *  Testing the remoteSearchXxxx() functions.
 *----------------------------------------------------------------------------*/
void
WebStorageClientTest :: remoteSearchTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref         sessionId;
    CPPUNIT_ASSERT_NO_THROW(
        sessionId = authentication->login("root", "q");
    );
    CPPUNIT_ASSERT(sessionId);
    
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria);
    Ptr<Glib::ustring>::Ref     token;
    CPPUNIT_ASSERT_NO_THROW(
        token = wsc->remoteSearchOpen(sessionId, criteria);
    );
    CPPUNIT_ASSERT(token);
    
    CPPUNIT_ASSERT_NO_THROW(
        wsc->cancelTransport(sessionId, token)
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        token = wsc->remoteSearchOpen(sessionId, criteria);
    );
    CPPUNIT_ASSERT(token);
    
    Ptr<Glib::ustring>::Ref                 errorMessage(new Glib::ustring);
    AsyncState                              state;
    
    int     iterations = 20;
    do {
        std::cerr << "-/|\\"[iterations%4] << '\b';
        sleep(1);
        CPPUNIT_ASSERT_NO_THROW(
            state = wsc->checkTransport(token, errorMessage);
        );
        CPPUNIT_ASSERT(state == AsyncState::initState
                         || state == AsyncState::pendingState
                         || state == AsyncState::finishedState);
    } while (--iterations && state != AsyncState::finishedState);
    
    CPPUNIT_ASSERT_EQUAL(AsyncState::finishedState, state);
    
    CPPUNIT_ASSERT_NO_THROW(
        wsc->remoteSearchClose(token);
    );
    
    CPPUNIT_ASSERT_THROW(
        wsc->cancelTransport(sessionId, token), XmlRpcMethodFaultException
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        authentication->logout(sessionId);
    );
}

