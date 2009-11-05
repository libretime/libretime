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

#include <glib.h>
#include <string>
#include <iostream>

#include "LiveSupport/Core/TimeConversion.h"

#include "AudioPlayerFactoryGstreamerTest.h"


using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(AudioPlayerFactoryGstreamerTest);

/**
 *  The name of the configuration file for the audio player.
 */
static const std::string configFileName = "etc/audioPlayerGstreamer.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AudioPlayerFactoryGstreamerTest :: setUp(void)                         throw ()
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
AudioPlayerFactoryGstreamerTest :: tearDown(void)                      throw ()
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
 *  Test to see if the audio player engine can be started and stopped
 *----------------------------------------------------------------------------*/
void
AudioPlayerFactoryGstreamerTest :: firstTest(void)
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
AudioPlayerFactoryGstreamerTest :: simplePlayTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<AudioPlayerFactory>::Ref        audioPlayerFactory;
    Ptr<AudioPlayerInterface>::Ref      audioPlayer;
    Ptr<time_duration>::Ref             sleepT;


    GMainLoop *loop=g_main_loop_new(NULL, FALSE);

    audioPlayerFactory = AudioPlayerFactory::getInstance();
    audioPlayer        = audioPlayerFactory->getAudioPlayer();

//    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->open("file:///tmp/campcaster/simple.smil", 0L, 0L);
//    );
//    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
//    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->start();
    

    g_main_loop_run(loop);


//    );
//    CPPUNIT_ASSERT(
        audioPlayer->isPlaying();
//    );
/*    
    sleepT.reset(new time_duration(seconds(5)));
    TimeConversion::sleep(sleepT);
    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->pause();
    );
    sleepT.reset(new time_duration(seconds(1)));
    TimeConversion::sleep(sleepT);
    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->start();
    );
    sleepT.reset(new time_duration(seconds(2)));
    TimeConversion::sleep(sleepT);
    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->pause();
    );
    sleepT.reset(new time_duration(seconds(1)));
    TimeConversion::sleep(sleepT);
    CPPUNIT_ASSERT_NO_THROW(
        audioPlayer->start();
    );
    
    sleepT.reset(new time_duration(microseconds(10)));
    while (audioPlayer->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!audioPlayer->isPlaying());
*/    audioPlayer->close();
}

