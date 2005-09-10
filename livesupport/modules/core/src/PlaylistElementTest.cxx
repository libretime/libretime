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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/PlaylistElementTest.cxx,v $

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
#include "LiveSupport/Core/PlaylistElement.h"

#include "PlaylistElementTest.h"

using namespace std;
using namespace LiveSupport::Core;
using namespace boost::posix_time;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PlaylistElementTest);

/**
 *  The name of the configuration file for the playlist element.
 */
static const std::string configFileName = "etc/playlistElement.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistElementTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
PlaylistElementTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
PlaylistElementTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<PlaylistElement>::Ref      playlistElement(new PlaylistElement);
    try {

        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        playlistElement->configure(*root);

        // the playlist element
        CPPUNIT_ASSERT(playlistElement->getId()->getId() == 0x103);
        Ptr<const time_duration>::Ref  relativeOffset
                                       = playlistElement->getRelativeOffset();
        CPPUNIT_ASSERT(relativeOffset->total_seconds()   == 11);

        CPPUNIT_ASSERT(playlistElement->getFadeInfo()->getId()->getId()
                                                         == 0x9901);
        Ptr<const time_duration>::Ref   fadeIn
                                        = playlistElement->getFadeInfo()
                                                         ->getFadeIn();
        CPPUNIT_ASSERT(fadeIn->total_milliseconds()  == 2000);
        Ptr<const time_duration>::Ref   fadeOut
                                        = playlistElement->getFadeInfo()
                                                         ->getFadeOut();
        CPPUNIT_ASSERT(fadeOut->total_milliseconds() == 1500);

        CPPUNIT_ASSERT(playlistElement->getType() 
                                        == PlaylistElement::PlaylistType);

        // check that we can access the playlist inside the playlist element
        //   as a Playable instance
        CPPUNIT_ASSERT(playlistElement->getPlaylist()
                    == playlistElement->getPlayable()); 

        // the playlist inside the playlist element
        CPPUNIT_ASSERT(playlistElement->getPlaylist()->getId()->getId()
                                                         == 0x2);
        Ptr<Playlist>::Ref       playlist   = playlistElement->getPlaylist();
        Playlist::const_iterator it         = playlist->begin();
        CPPUNIT_ASSERT(it != playlist->end());
        playlistElement = it->second;
        ++it;
        CPPUNIT_ASSERT(it == playlist->end());

        // the playlist element inside the playlist
        CPPUNIT_ASSERT(playlistElement->getId()->getId() == 0x111);
        relativeOffset = playlistElement->getRelativeOffset();
        CPPUNIT_ASSERT(relativeOffset->total_seconds()   == 0);
        
        CPPUNIT_ASSERT(playlistElement->getType() 
                                        == PlaylistElement::AudioClipType);
                                        
        CPPUNIT_ASSERT(*playlistElement->getXmlElementString() ==
                                "<playlistElement id=\"0000000000000111\" "
                                "relativeOffset=\"00:00:00.000000\">\n"
                                        "<audioClip id=\"0000000000010003\" "
                                        "playlength=\"00:00:11.000000\" "
                                        "title=\"three\"/>\n"
                                "</playlistElement>");

        // and the audio clip inside the playlist element
        CPPUNIT_ASSERT(playlistElement->getAudioClip()->getId()->getId()
                                                         == 0x10003);

        // check that we can access this audio clip as a Playable instance
        CPPUNIT_ASSERT(playlistElement->getAudioClip()
                    == playlistElement->getPlayable()); 

    } catch (std::invalid_argument &e) {
        std::string eMsg = "semantic error in configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    } catch (xmlpp::exception &e) {
        std::string eMsg = "error parsing configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}
