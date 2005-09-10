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
#include <iostream>

#include "LiveSupport/Core/Playlist.h"
#include "PlaylistTest.h"


using namespace std;
using namespace LiveSupport::Core;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PlaylistTest);

/**
 *  The name of the configuration file for the playlist.
 */
static const std::string configFileName = "etc/playlist.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: setUp(void)                         throw ()
{
    playlist.reset(new Playlist);
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        playlist->configure(*root);

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
PlaylistTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT(playlist->getId()->getId() == 0x1);
    Ptr<const boost::posix_time::time_duration>::Ref  duration
                                            = playlist->getPlaylength();
    CPPUNIT_ASSERT(duration->total_seconds() == 34);

    CPPUNIT_ASSERT(playlist->valid());
    CPPUNIT_ASSERT(*playlist->getXmlElementString() ==
"<playlist id=\"0000000000000001\" playlength=\"00:00:34.000000\" "
                                  "title=\"My First Playlist\">\n"
"<playlistElement id=\"0000000000000101\" relativeOffset=\"00:00:00.000000\">\n"
"<audioClip id=\"0000000000010001\" playlength=\"00:00:11.000000\" title=\"one\"/>\n"
"</playlistElement>\n"
"<playlistElement id=\"0000000000000102\" relativeOffset=\"00:00:11.000000\">\n"
"<audioClip id=\"0000000000010002\" playlength=\"00:00:12.000000\" title=\"two\"/>\n"
"<fadeInfo id=\"0000000000009901\" fadeIn=\"00:00:02.000000\" "
                                  "fadeOut=\"00:00:01.500000\"/>\n"
"</playlistElement>\n"
"<playlistElement id=\"0000000000000103\" relativeOffset=\"00:00:23.000000\">\n"
"<playlist id=\"0000000000000002\" playlength=\"00:00:11.000000\" title=\"\">\n"
"<playlistElement id=\"0000000000000111\" relativeOffset=\"00:00:00.000000\">\n"
"<audioClip id=\"0000000000010003\" playlength=\"00:00:11.000000\" title=\"three\"/>\n"
"</playlistElement>\n"
"</playlist>\n"
"</playlistElement>\n"
"</playlist>");

    Playlist::const_iterator        it = playlist->begin();
    CPPUNIT_ASSERT(it != playlist->end());
    Ptr<PlaylistElement>::Ref       playlistElement = it->second;
    CPPUNIT_ASSERT(playlistElement->getId()->getId() == 0x101);
    Ptr<const time_duration>::Ref   relativeOffset 
                                    = playlistElement->getRelativeOffset();
    CPPUNIT_ASSERT(relativeOffset->total_seconds()   == 0);
    CPPUNIT_ASSERT(playlistElement->getType() 
                                    == PlaylistElement::AudioClipType);
    CPPUNIT_ASSERT(playlistElement->getAudioClip()->getId()->getId() 
                                                     == 0x10001);
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    playlistElement  = it->second;
    CPPUNIT_ASSERT(playlistElement->getId()->getId() == 0x102);
    relativeOffset   = playlistElement->getRelativeOffset();
    CPPUNIT_ASSERT(relativeOffset->total_seconds()   == 11);
    CPPUNIT_ASSERT(playlistElement->getType() 
                                    == PlaylistElement::AudioClipType);
    CPPUNIT_ASSERT(playlistElement->getAudioClip()->getId()->getId() 
                                                     == 0x10002);
    
    ++it;
    ++it;
    CPPUNIT_ASSERT(it == playlist->end());
}


/*------------------------------------------------------------------------------
 *  Test to see if we can add or remove an audio clip
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: audioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref       clipId(new UniqueId("20001"));
    Ptr<time_duration>::Ref  clipLength(new time_duration(0,30,0,0));
    Ptr<AudioClip>::Ref      audioClip(new AudioClip(clipId, clipLength));

    Ptr<time_duration>::Ref  relativeOffset(new time_duration(0,10,0,0));
                                                // hour, min, sec, frac_sec
    try {
        playlist->addAudioClip(audioClip, relativeOffset);
    } catch (std::invalid_argument &e) {
        string eMsg = "addAudioClip returned with error: ";
        eMsg += e.what(); 
        CPPUNIT_FAIL(eMsg);
    }

    CPPUNIT_ASSERT(playlist->getPlaylength());
    CPPUNIT_ASSERT(*playlist->getPlaylength() == *relativeOffset + *clipLength);

    CPPUNIT_ASSERT(!playlist->valid());    // big gap in playlist

    Playlist::const_iterator       it = playlist->begin();
    CPPUNIT_ASSERT(it != playlist->end());

    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    
    Ptr<PlaylistElement>::Ref   playlistElement = it->second;
    CPPUNIT_ASSERT(playlistElement->getType() 
                                == PlaylistElement::AudioClipType);
    CPPUNIT_ASSERT(playlistElement->getAudioClip()->getId()->getId()
                                                             == 0x20001);

    Ptr<const time_duration>::Ref  otherRelativeOffset 
                                   = playlistElement->getRelativeOffset();
    CPPUNIT_ASSERT(otherRelativeOffset->total_seconds() == 10*60);

    ++it;
    CPPUNIT_ASSERT(it == playlist->end());

    try {
        playlist->removePlaylistElement(playlistElement->getId());
    } catch (std::invalid_argument &e) {
        string eMsg = "removePlaylistElement returned with error: ";
        eMsg += e.what(); 
        CPPUNIT_FAIL(eMsg);
    }

    it = playlist->begin();
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it == playlist->end());

    Ptr<UniqueId>::Ref  phonyPlaylistElementId(new UniqueId(9999));
    try {
        playlist->removePlaylistElement(phonyPlaylistElementId);
        CPPUNIT_FAIL("removePlaylistElement allowed to remove "
                     "non-existent audio clip");
    } catch (std::invalid_argument &e) {
    }
}


/*------------------------------------------------------------------------------
 *  Test the "save/revert to current state" mechanism
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: savedCopyTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        playlist->revertToSavedCopy();
        CPPUNIT_FAIL("allowed to revert to non-existent state");
    } catch (std::invalid_argument &e) {
    }

    playlist->createSavedCopy();
    playlist->removePlaylistElement(playlist->begin()->second->getId());
    playlist->removePlaylistElement(playlist->begin()->second->getId());
    playlist->removePlaylistElement(playlist->begin()->second->getId());
    CPPUNIT_ASSERT(playlist->begin() == playlist->end());

    try {
        playlist->revertToSavedCopy();
    } catch (std::logic_error &e) {
        CPPUNIT_FAIL("could not revert to saved state");
    }
    
    Playlist::const_iterator  it = playlist->begin();
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    Ptr<PlaylistElement>::Ref   playlistElement = it->second;
    CPPUNIT_ASSERT(playlistElement->getType() 
                                    == PlaylistElement::AudioClipType);
    CPPUNIT_ASSERT(playlistElement->getAudioClip()->getId()->getId() 
                                    == 0x10002);
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it == playlist->end());

    playlist->deleteSavedCopy();
    try {
        playlist->revertToSavedCopy();
        CPPUNIT_FAIL("allowed to revert to deleted state");
    } catch (std::logic_error &e) {
    }
}


/*------------------------------------------------------------------------------
 *  Test to see if we can add a fade info
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: fadeInfoTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<PlaylistElement>::Ref   playlistElementOne,
                                playlistElementTwo; 

    Playlist::const_iterator    it = playlist->begin();
    CPPUNIT_ASSERT(it != playlist->end());
    playlistElementOne = it->second;
    CPPUNIT_ASSERT(playlistElementOne->getFadeInfo().get() == 0);

    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    playlistElementTwo  = it->second;
    CPPUNIT_ASSERT(playlistElementTwo->getFadeInfo()->getFadeIn()
                                     ->total_milliseconds() == 2000);
    CPPUNIT_ASSERT(playlistElementTwo->getFadeInfo()->getFadeOut()
                                     ->total_milliseconds() == 1500);
    
    ++it;
    CPPUNIT_ASSERT(it != playlist->end());
    ++it;
    CPPUNIT_ASSERT(it == playlist->end());

    Ptr<time_duration>::Ref fadeIn (new time_duration(0,0,3,200000));
    Ptr<time_duration>::Ref fadeOut(new time_duration(0,0,4,0));
    Ptr<FadeInfo>::Ref      fadeInfo(new FadeInfo(fadeIn, fadeOut));

    try {
        playlist->setFadeInfo(playlistElementOne->getId(), fadeInfo);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("could not add new fade info");
    }

    try {
        playlist->setFadeInfo(playlistElementTwo->getId(), fadeInfo);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("could not update fade info");
    }

    it = playlist->begin();
    playlistElementOne  = it->second;
    CPPUNIT_ASSERT(playlistElementOne->getFadeInfo()->getFadeIn()
                                     ->total_milliseconds() == 3200);
    CPPUNIT_ASSERT(playlistElementOne->getFadeInfo()->getFadeOut()
                                     ->total_milliseconds() == 4000);
    ++it;
    playlistElementTwo  = it->second;
    CPPUNIT_ASSERT(playlistElementTwo->getFadeInfo()->getFadeIn()
                                     ->total_milliseconds() == 3200);
    CPPUNIT_ASSERT(playlistElementTwo->getFadeInfo()->getFadeOut()
                                     ->total_milliseconds() == 4000);

    Ptr<UniqueId>::Ref  phonyPlaylistElementId(new UniqueId(9999));
    try {
        playlist->setFadeInfo(phonyPlaylistElementId, fadeInfo);
        CPPUNIT_FAIL("allowed to set fade info for non-existent element");
    } catch (std::invalid_argument &e) {
    }
}


/*------------------------------------------------------------------------------
 *  Test conversion to and from Playable
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: conversionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{    
    CPPUNIT_ASSERT(playlist.use_count() == 1);

    Ptr<Playable>::Ref      playable = playlist;
    CPPUNIT_ASSERT(playable->getType() == Playable::PlaylistType);
    CPPUNIT_ASSERT(playlist.use_count() == 2);
    
    Ptr<Playlist>::Ref      otherPlaylist = playable->getPlaylist();
    CPPUNIT_ASSERT(otherPlaylist == playlist);
    CPPUNIT_ASSERT(playlist.use_count() == 3);

    Ptr<AudioClip>::Ref     audioClip = playable->getAudioClip();
    CPPUNIT_ASSERT(!audioClip);
}


/*------------------------------------------------------------------------------
 *  Marshalling test
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: marshallingTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue     xmlRpcValue = *playlist;
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("playlist"));

    Ptr<Playlist>::Ref     otherPlaylist;
    CPPUNIT_ASSERT_NO_THROW(otherPlaylist.reset(new Playlist(xmlRpcValue)));

    CPPUNIT_ASSERT(*playlist->getId() == *otherPlaylist->getId());
    CPPUNIT_ASSERT(*playlist->getTitle() 
                                       == *otherPlaylist->getTitle());
    CPPUNIT_ASSERT(*playlist->getPlaylength() 
                                       == *otherPlaylist->getPlaylength());
}


/*------------------------------------------------------------------------------
 *  Testing the addPlayable() method
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: addPlayableTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<Playlist>::Ref     newPlaylist(new Playlist(*playlist));
                                                // make a copy

    Ptr<UniqueId>::Ref       clipId(new UniqueId("20001"));
    Ptr<time_duration>::Ref  clipLength(new time_duration(0,0,10,0));
    Ptr<AudioClip>::Ref      audioClip(new AudioClip(clipId, clipLength));

    Ptr<time_duration>::Ref  firstOffset(new time_duration(0,0,30,0));
                                                // hour, min, sec, frac_sec
    try {
        newPlaylist->addPlayable(audioClip, firstOffset);
    } catch (std::invalid_argument &e) {
        string eMsg = "addPlayable returned with error: ";
        eMsg += e.what(); 
        CPPUNIT_FAIL(eMsg);
    }

    CPPUNIT_ASSERT(newPlaylist->getPlaylength());
    CPPUNIT_ASSERT(*newPlaylist->getPlaylength() == *firstOffset
                                                  + *audioClip->getPlaylength());

    Ptr<time_duration>::Ref  secondOffset(new time_duration(0,0,40,0));
    try {
        newPlaylist->addPlayable(playlist, secondOffset);
    } catch (std::invalid_argument &e) {
        string eMsg = "addPlayable returned with error: ";
        eMsg += e.what(); 
        CPPUNIT_FAIL(eMsg);
    }

    CPPUNIT_ASSERT(newPlaylist->getPlaylength());
    CPPUNIT_ASSERT(*newPlaylist->getPlaylength() == *secondOffset
                                                  + *playlist->getPlaylength());
}


/*------------------------------------------------------------------------------
 *  Testing the eliminateGaps() method
 *----------------------------------------------------------------------------*/
void
PlaylistTest :: eliminateGapsTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // a simple negative test
    bool    result = playlist->eliminateGaps();
    CPPUNIT_ASSERT(result == false);

    // a simple positive test
    Ptr<UniqueId>::Ref       secondElement(new UniqueId("101"));
    try {
        playlist->removePlaylistElement(secondElement);
    } catch (std::invalid_argument &e) {
        string eMsg = "removePlaylistElement() returned with error: ";
        eMsg += e.what(); 
        CPPUNIT_FAIL(eMsg);
    }
    CPPUNIT_ASSERT(!playlist->valid());         // big gap in playlist
    CPPUNIT_ASSERT(playlist->getPlaylength());
    CPPUNIT_ASSERT(*playlist->getPlaylength() == seconds(34));

    result = playlist->eliminateGaps();
    CPPUNIT_ASSERT(result == true);
    CPPUNIT_ASSERT(playlist->valid());          // the gap is gone
    CPPUNIT_ASSERT(playlist->getPlaylength());
    CPPUNIT_ASSERT(*playlist->getPlaylength() == seconds(23));
}   

