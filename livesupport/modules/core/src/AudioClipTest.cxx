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
    Version  : $Revision: 1.11 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/AudioClipTest.cxx,v $

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

#include "LiveSupport/Core/AudioClip.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/TagConversion.h"
#include "AudioClipTest.h"


using namespace std;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(AudioClipTest);

/**
 *  The name of the configuration file for the audio clip.
 */
static const std::string configFileName = "etc/audioClip.xml";

/**
 *  The name of the configuration file for the tag conversion table.
 */
static const std::string tagConversionConfig = "etc/tagConversionTable.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                new xmlpp::DomParser(configFileName, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();
        Ptr<AudioClip>::Ref     audioClip(new AudioClip());

        audioClip->configure(*root);

        CPPUNIT_ASSERT(audioClip->getId()->getId() == 0x1);
        Ptr<const boost::posix_time::time_duration>::Ref  duration
                                                = audioClip->getPlaylength();
        CPPUNIT_ASSERT(duration->hours() == 0);
        CPPUNIT_ASSERT(duration->minutes() == 18);
        CPPUNIT_ASSERT(duration->seconds() == 30);

        Ptr<const Glib::ustring>::Ref     title = audioClip->getTitle();
        CPPUNIT_ASSERT(title);
        CPPUNIT_ASSERT(*title == "File Title txt");

        Ptr<const Glib::ustring>::Ref     subject = audioClip
                                        ->getMetadata("dc:subject");
        CPPUNIT_ASSERT(subject);
        CPPUNIT_ASSERT(*subject == "Keywords: qwe, asd, zcx");

        Ptr<const Glib::ustring>::Ref     alternativeTitle = audioClip
                                        ->getMetadata("dcterms:alternative");
        CPPUNIT_ASSERT(alternativeTitle);
        CPPUNIT_ASSERT(*alternativeTitle ==
                            "Alternative File Title ín sőmé %$#@* LÁNGŰAGÉ");

        CPPUNIT_ASSERT(*audioClip->getXmlElementString() ==
                                            "<audioClip id=\"0000000000000001\" "
                                            "playlength=\"00:18:30\" "
                                            "title=\"File Title txt\"/>");

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        std::string  eMsg = "error parsing configuration file\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Test conversion to and from Playable
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: conversionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AudioClip>::Ref     audioClip(new AudioClip());
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                new xmlpp::DomParser(configFileName, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        audioClip->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        std::string  eMsg = "error parsing configuration file\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    
    Ptr<Playable>::Ref      playable = audioClip;
    CPPUNIT_ASSERT(playable->getType() == Playable::AudioClipType);
    
    Ptr<AudioClip>::Ref     otherAudioClip = playable->getAudioClip();
    CPPUNIT_ASSERT(otherAudioClip == audioClip);

    Ptr<Playlist>::Ref      playlist = playable->getPlaylist();
    CPPUNIT_ASSERT(!playlist);
}


/*------------------------------------------------------------------------------
 *  Test id3v2 tag extraction
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: tagTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                            new xmlpp::DomParser(tagConversionConfig, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();
        TagConversion::configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<AudioClip>::Ref     audioClip(new AudioClip());
    
    Ptr<std::string>::Ref   uri(new std::string("var/test10001.mp3"));
    audioClip->setUri(uri);
    try {
        audioClip->readTag();
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<const Glib::ustring>::Ref   title
                                    = audioClip->getMetadata("dc:title");
    CPPUNIT_ASSERT(*title == "Theme Song");

    Ptr<const Glib::ustring>::Ref   artist 
                                    = audioClip->getMetadata("dc:creator");
    CPPUNIT_ASSERT(*artist == "The Muppets");
}


/*------------------------------------------------------------------------------
 *  Marshalling test
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: marshallingTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AudioClip>::Ref     audioClip(new AudioClip());
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                new xmlpp::DomParser(configFileName, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        audioClip->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(e.what());
    }

    XmlRpc::XmlRpcValue     xmlRpcValue = *audioClip;
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("audioClip"));

    Ptr<AudioClip>::Ref     otherAudioClip;
    CPPUNIT_ASSERT_NO_THROW(otherAudioClip.reset(new AudioClip(xmlRpcValue)));

    CPPUNIT_ASSERT(*audioClip->getId() == *otherAudioClip->getId());
    CPPUNIT_ASSERT(*audioClip->getTitle() 
                                       == *otherAudioClip->getTitle());
    CPPUNIT_ASSERT(*audioClip->getPlaylength() 
                                       == *otherAudioClip->getPlaylength());
}

