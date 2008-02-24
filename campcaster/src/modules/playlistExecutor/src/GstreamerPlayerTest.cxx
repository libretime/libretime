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
#include <iostream>

#include "LiveSupport/Core/TimeConversion.h"

#include "GstreamerPlayer.h"
#include "TestEventListener.h"
#include "GstreamerPlayerTest.h"


using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(GstreamerPlayerTest);

/**
 *  The name of the configuration file for the audio player.
 */
static const std::string configFileName = "etc/gstreamerPlayer.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        player.reset(new GstreamerPlayer());
        player->configure(*root);

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
GstreamerPlayerTest :: tearDown(void)                      throw ()
{
    player.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the GstreamerPlayer engine can be started and stopped
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play something simple
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: simplePlayTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }

    Ptr<time_duration>::Ref     playlength = player->getPlaylength();
    CPPUNIT_ASSERT(playlength.get());
    CPPUNIT_ASSERT(playlength->seconds() == 14);
    CPPUNIT_ASSERT(playlength->fractional_seconds() == 785187);

    CPPUNIT_ASSERT(!player->isPlaying());
    player->close();
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Check the getPosition() function
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: getPositionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(100)));
    Ptr<ptime>::Ref             start;

    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    start = TimeConversion::now();
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        Ptr<ptime>::Ref         now = TimeConversion::now();
        Ptr<time_duration>::Ref offset(new time_duration(*now - *start));
        Ptr<time_duration>::Ref position;
        CPPUNIT_ASSERT_NO_THROW(
            position = player->getPosition()
        );

        TimeConversion::sleep(sleepT);
        // TODO: check here for abs(position - offset) < epsilon
        //       but unforunately seeking / position reporting with the mad
        //       plugin is quite off the scale
    }

    Ptr<time_duration>::Ref     playlength = player->getPlaylength();
    CPPUNIT_ASSERT(playlength.get());
    CPPUNIT_ASSERT(playlength->seconds() == 14);
    CPPUNIT_ASSERT(playlength->fractional_seconds() == 785187);

    CPPUNIT_ASSERT(!player->isPlaying());
    player->close();
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Check if the setDevice() function works are advertized.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: setDeviceTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));
    Ptr<time_duration>::Ref     playlength;

    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );

    // check on an ALSA device
    CPPUNIT_ASSERT(player->setAudioDevice("plughw:0,0"));
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test-short.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT_NO_THROW(
        playlength = player->getPlaylength();
    );
    CPPUNIT_ASSERT(playlength.get());
    CPPUNIT_ASSERT(playlength->seconds() == 2);
    CPPUNIT_ASSERT(!player->isPlaying());

    // check on an OSS DSP device
    CPPUNIT_ASSERT(player->setAudioDevice("/dev/dsp"));
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test-short.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    playlength = player->getPlaylength();
    CPPUNIT_ASSERT(playlength.get());
    CPPUNIT_ASSERT(playlength->seconds() == 2);
    CPPUNIT_ASSERT(!player->isPlaying());

    // check changing from ALSA to OSS after opening
    CPPUNIT_ASSERT(player->setAudioDevice("plughw:0,0"));
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test-short.mp3");
    );
    CPPUNIT_ASSERT(player->setAudioDevice("/dev/dsp"));
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT_NO_THROW(
        playlength = player->getPlaylength();
    );
    CPPUNIT_ASSERT(playlength.get());
    CPPUNIT_ASSERT(playlength->seconds() == 2);
    CPPUNIT_ASSERT(!player->isPlaying());

    // test playing on ALSA after playing on OSS
    player->close();
    CPPUNIT_ASSERT(player->setAudioDevice("plughw:0,0"));
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test-short.mp3")
    );
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player->isPlaying());

    player->close();
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play a simple SMIL file
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: simpleSmilTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/simpleSmil.smil");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    player->close();
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play a more complicated SMIL file
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: secondSmilTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/sequentialSmil.smil");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    player->close();
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play a SMIL file with sound animation
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: animatedSmilTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/animatedSmil.smil");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    player->close();

    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Check for error conditions
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: checkErrorConditions(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );

    bool    gotException;

    CPPUNIT_ASSERT(!player->isPlaying());

    gotException = false;
    try {
        player->start();
    } catch (std::logic_error &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);

    gotException = false;
    try {
        player->stop();
    } catch (std::logic_error &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);

    gotException = false;
    try {
        player->open("totally/bad/URL");
    } catch (std::invalid_argument &e) {
        gotException = true;
    } catch (std::runtime_error &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(gotException);

    gotException = false;
    try {
        player->start();
    } catch (std::logic_error &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);

    // check for opening a wrong URL after opening a proper one
    try {
        player->open("file:///tmp/campcaster/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (std::runtime_error &e) {
        CPPUNIT_FAIL(e.what());
    }
    player->close();
    gotException = false;
    try {
        player->open("totally/bad/URL");
    } catch (std::invalid_argument &e) {
        gotException = true;
    } catch (std::runtime_error &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(gotException);

    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Test to see if attaching and detaching event listeners works.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: eventListenerAttachTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(player->initialize());

    Ptr<TestEventListener>::Ref     listener1(new TestEventListener());
    Ptr<TestEventListener>::Ref     listener2(new TestEventListener());

    // try with one listener
    player->attachListener(listener1.get());
    CPPUNIT_ASSERT_NO_THROW(
        player->detachListener(listener1.get())
    );
    CPPUNIT_ASSERT_THROW(
        player->detachListener(listener1.get()),
        std::invalid_argument
    );

    // try with two listeners
    player->attachListener(listener1.get());
    CPPUNIT_ASSERT_THROW(
        player->detachListener(listener2.get()),
        std::invalid_argument
    );
    player->attachListener(listener2.get());
    CPPUNIT_ASSERT_NO_THROW(
        player->detachListener(listener1.get());
    );
    
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Test to see if the player event listener mechanism works.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: eventListenerTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(player->initialize());

    Ptr<time_duration>::Ref         sleepT(new time_duration(microseconds(10)));
    Ptr<TestEventListener>::Ref     listener1(new TestEventListener());
    player->attachListener(listener1.get());

    // try with one listener
    CPPUNIT_ASSERT(!listener1->stopFlag);
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT(!listener1->stopFlag);
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    CPPUNIT_ASSERT(!listener1->stopFlag);
    while (player->isPlaying()) {
        CPPUNIT_ASSERT(!listener1->stopFlag);
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT(listener1->stopFlag);
    listener1->stopFlag = false;
    player->close();

    // try with two listeners
    Ptr<TestEventListener>::Ref     listener2(new TestEventListener());
    player->attachListener(listener2.get());

    CPPUNIT_ASSERT(!listener1->stopFlag);
    CPPUNIT_ASSERT(!listener2->stopFlag);
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT(!listener1->stopFlag);
    CPPUNIT_ASSERT(!listener2->stopFlag);
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    CPPUNIT_ASSERT(!listener1->stopFlag);
    CPPUNIT_ASSERT(!listener2->stopFlag);
    while (player->isPlaying()) {
        CPPUNIT_ASSERT(!listener1->stopFlag);
        CPPUNIT_ASSERT(!listener2->stopFlag);
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT(listener1->stopFlag);
    CPPUNIT_ASSERT(listener2->stopFlag);
    listener1->stopFlag = false;
    listener2->stopFlag = false;
    player->close();

    // try with only the second listener
    CPPUNIT_ASSERT_NO_THROW(
        player->detachListener(listener1.get());
    );
    CPPUNIT_ASSERT(!listener2->stopFlag);
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT(!listener2->stopFlag);
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    CPPUNIT_ASSERT(!listener2->stopFlag);
    while (player->isPlaying()) {
        CPPUNIT_ASSERT(!listener2->stopFlag);
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT(listener2->stopFlag);
    listener2->stopFlag = false;
    player->close();

    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Another, more realistic test of the event listener mechanism.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: eventListenerOnStopTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(player->initialize());
    player->attachListener(this);

    // start the first clip
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test-short.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    startNewClipFlag = true;

    // sleep for a while; in the meantime, onStop() starts the second clip
    Ptr<time_duration>::Ref         sleepT(new time_duration(seconds(7)));
    TimeConversion::sleep(sleepT);

    // the second clip should be over by now
    CPPUNIT_ASSERT(!player->isPlaying());
    player->close();

    CPPUNIT_ASSERT_NO_THROW(
        player->detachListener(this);
    );
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Another, more realistic test of the event listener mechanism.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: onStop(Ptr<const Glib::ustring>::Ref  errorMessage)
                                                throw ()
{
    if (!startNewClipFlag) {
        return;
    }

    try {
        CPPUNIT_ASSERT_NO_THROW(
            player->close();
        );

        CPPUNIT_ASSERT_NO_THROW(
            player->open("file:///tmp/campcaster/test-short.mp3");
        );
        CPPUNIT_ASSERT(!player->isPlaying());

        CPPUNIT_ASSERT_NO_THROW(
            player->start();
        );
        CPPUNIT_ASSERT(player->isPlaying());
    } catch (CPPUNIT_NS::Exception &e) {
        std::cerr << "Exception in onStop(): " << e.what() << std::endl;
    }

    startNewClipFlag = false;
}


/*------------------------------------------------------------------------------
 *  Time how long it takes to open, play and close files.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: timeSteps(const std::string  fileName)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<ptime>::Ref             start;
    Ptr<ptime>::Ref             end;
    Ptr<time_duration>::Ref     openTime;
    Ptr<time_duration>::Ref     startTime;
    Ptr<time_duration>::Ref     stopTime;
    Ptr<time_duration>::Ref     closeTime;

    start = TimeConversion::now();
    CPPUNIT_ASSERT_NO_THROW(
        player->open(fileName);
    );
    end = TimeConversion::now();
    openTime.reset(new time_duration(*end - *start));

    CPPUNIT_ASSERT(!player->isPlaying());
    start = TimeConversion::now();
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    end = TimeConversion::now();
    startTime.reset(new time_duration(*end - *start));

    CPPUNIT_ASSERT(player->isPlaying());

    start = TimeConversion::now();
    CPPUNIT_ASSERT_NO_THROW(
        player->stop();
    );
    end = TimeConversion::now();
    stopTime.reset(new time_duration(*end - *start));

    CPPUNIT_ASSERT(!player->isPlaying());

    start = TimeConversion::now();
    player->close();
    end = TimeConversion::now();
    closeTime.reset(new time_duration(*end - *start));

    // TODO: somehow assert on the time values
}


/*------------------------------------------------------------------------------
 *  Test how long it takes to open and play files.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: openTimeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );

    timeSteps("file:///tmp/campcaster/test.mp3");

    timeSteps("file:///tmp/campcaster/simpleSmil.smil");

    timeSteps("file:///tmp/campcaster/sequentialSmil.smil");

    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Test pausing and resuming.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: pauseResumeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT;

    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test10001.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    
    sleepT.reset(new time_duration(seconds(2)));
    TimeConversion::sleep(sleepT);
    CPPUNIT_ASSERT_NO_THROW(
        player->pause();
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    
    sleepT.reset(new time_duration(seconds(10)));
    TimeConversion::sleep(sleepT);
    CPPUNIT_ASSERT(!player->isPlaying());

    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());
    
    sleepT.reset(new time_duration(seconds(1)));
    TimeConversion::sleep(sleepT);
    CPPUNIT_ASSERT(player->isPlaying());
    
    sleepT.reset(new time_duration(microseconds(10)));
    while (player->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    
    CPPUNIT_ASSERT(!player->isPlaying());
    player->close();
    player->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Open the same soundcard twice, thus force an error
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: openSoundcardTwiceTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));
    Ptr<GstreamerPlayer>::Ref   player2;

    // create a second player, with the same config as our usual player
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        player2.reset(new GstreamerPlayer());
        player2->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(e.what());
    }

    // initialize & start playing on the first player
    CPPUNIT_ASSERT_NO_THROW(
        player->initialize();
    );
    CPPUNIT_ASSERT_NO_THROW(
        player->open("file:///tmp/campcaster/test.mp3");
    );
    CPPUNIT_ASSERT(!player->isPlaying());
    CPPUNIT_ASSERT_NO_THROW(
        player->start();
    );
    CPPUNIT_ASSERT(player->isPlaying());

    // now open the same again in the second one
    CPPUNIT_ASSERT_NO_THROW(
        player2->initialize();
    );
    try {
        player2->open("file:///tmp/campcaster/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (std::runtime_error &e) {
        // this is what we're expecting, if open failed for the reason of
        // the soundcard being blocked. this doesn't always happen with
        // ALSA drivers (with dmix, for example)
    }
    CPPUNIT_ASSERT(!player2->isPlaying());

    // close everything
    player2->close();
    player2->deInitialize();
    player->close();
    player->deInitialize();
}

