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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/AudioPlayerFactoryTest.cxx,v $

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

#include "AudioPlayerFactoryTest.h"


using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(AudioPlayerFactoryTest);

/**
 *  The name of the configuration file for the Helix player.
 */
static const std::string configFileName = "etc/audioPlayer.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AudioPlayerFactoryTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        Ptr<AudioPlayerFactory>::Ref    audioPlayerFactory;

        audioPlayerFactory = AudioPlayerFactory::getInstance();
        audioPlayerFactory->configure(*root);

        // initialize the audio player configured by the factory
        Ptr<AudioPlayerInterface>::Ref      audioPlayer;
        audioPlayer = audioPlayerFactory->getAudioPlayer();
        audioPlayer->initialize();

    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in configuration file" << std::endl;
    } catch (xmlpp::exception &e) {
        std::cerr << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
AudioPlayerFactoryTest :: tearDown(void)                      throw ()
{
    try {
        Ptr<AudioPlayerFactory>::Ref    audioPlayerFactory;
        audioPlayerFactory = AudioPlayerFactory::getInstance();

        // de-initialize the audio player configured by the factory
        Ptr<AudioPlayerInterface>::Ref      audioPlayer;
        audioPlayer = audioPlayerFactory->getAudioPlayer();
        audioPlayer->deInitialize();
    } catch (xmlpp::exception &e) {
        std::cerr << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Test to see if the HelixPlayer engine can be started and stopped
 *----------------------------------------------------------------------------*/
void
AudioPlayerFactoryTest :: firstTest(void)
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
 *  Play something simple
 *----------------------------------------------------------------------------*/
void
AudioPlayerFactoryTest :: simplePlayTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AudioPlayerFactory>::Ref        audioPlayerFactory;
    Ptr<AudioPlayerInterface>::Ref      audioPlayer;
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    audioPlayerFactory = AudioPlayerFactory::getInstance();
    audioPlayer        = audioPlayerFactory->getAudioPlayer();

    audioPlayer->open("file:var/test.mp3");
    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
    audioPlayer->start();
    CPPUNIT_ASSERT(audioPlayer->isPlaying());
    while (audioPlayer->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
    audioPlayer->close();
}

