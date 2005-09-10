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

#include "LiveSupport/Core/AudioClip.h"
#include "LiveSupport/Core/Playlist.h"
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
 *  The name of the configuration file for the resource bundle.
 */
static const std::string bundleConfigFileName = "etc/resourceBundle.xml";

/**
 *  The name of the configuration file for the metadata type container.
 */
static const std::string metadataConfigFileName 
                         = "etc/metadataTypeContainer.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                        new xmlpp::DomParser(configFileName, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();
        
        audioClip.reset(new AudioClip());
        audioClip->configure(*root);
        
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in audio clip configuration file");
    } catch (xmlpp::exception &e) {
        std::string  eMsg = "error parsing audio clip configuration file\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }

    Ptr<ResourceBundle>::Ref    bundle;
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                        new xmlpp::DomParser(bundleConfigFileName, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();
        
        bundle = LocalizedObject::getBundle(*root);
        
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (std::exception &e) {
        std::string  eMsg = "error parsing audio clip configuration file\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
    CPPUNIT_ASSERT(bundle);

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                        new xmlpp::DomParser(metadataConfigFileName, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();
        
        metadataTypes.reset(new MetadataTypeContainer(bundle));
        metadataTypes->configure(*root);
        
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in metadata configuration file");
    } catch (xmlpp::exception &e) {
        std::string  eMsg = "error parsing metadata configuration file\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
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
                                        "playlength=\"00:18:30.000000\" "
                                        "title=\"File Title txt\"/>");
}


/*------------------------------------------------------------------------------
 *  Test conversion to and from Playable
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: conversionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
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
    // should work with either plain file path...
    Ptr<std::string>::Ref   uri(new std::string("var/test10001.mp3"));
    audioClip->setUri(uri);
    try {
        audioClip->readTag(metadataTypes);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    Ptr<const Glib::ustring>::Ref   title
                                    = audioClip->getMetadata("dc:title");
    CPPUNIT_ASSERT(*title == "Theme Song");

    Ptr<const Glib::ustring>::Ref   artist 
                                    = audioClip->getMetadata("dc:creator");
    CPPUNIT_ASSERT(*artist == "The Muppets");

    Ptr<const Glib::ustring>::Ref   album 
                                    = audioClip->getMetadata("dc:source");
    CPPUNIT_ASSERT(*album == "מוישה אופניק");

    // ... or with URI
    uri.reset(new std::string("file:var/test10001.mp3"));
    audioClip->setUri(uri);
    try {
        audioClip->readTag(metadataTypes);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    title = audioClip->getMetadata("dc:title");
    CPPUNIT_ASSERT(*title == "Theme Song");

    artist = audioClip->getMetadata("dc:creator");
    CPPUNIT_ASSERT(*artist == "The Muppets");

    album = audioClip->getMetadata("dc:source");
    CPPUNIT_ASSERT(*album == "מוישה אופניק");
    // Moshe Offnik is the Israeli/Palestinian version of Oscar The Grouch
}


/*------------------------------------------------------------------------------
 *  Marshalling test
 *----------------------------------------------------------------------------*/
void
AudioClipTest :: marshallingTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
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

