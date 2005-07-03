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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/GstreamerPlayerTest.cxx,v $

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
    try {
        player->initialize();
        CPPUNIT_ASSERT(!player->isPlaying());
        player->deInitialize();
    } catch (std::exception &e) {
        CPPUNIT_FAIL("failed to initialize or de-initialize GstreamerPlayer");
    }
}


/*------------------------------------------------------------------------------
 *  Play something simple
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: simplePlayTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    player->initialize();
    try {
        player->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    player->start();
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
 *  Play a simple SMIL file
 *----------------------------------------------------------------------------*/
void
GstreamerPlayerTest :: simpleSmilTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    player->initialize();
    try {
        player->open("file:var/simpleSmil.smil");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
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

    player->initialize();
    try {
        player->open("file:var/sequentialSmil.smil");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
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

    player->initialize();

    try {
        player->open("file:var/animatedSmil.smil");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!player->isPlaying());
    player->start();
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
    player->initialize();

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
        player->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    player->close();
    gotException = false;
    try {
        player->open("totally/bad/URL");
    } catch (std::invalid_argument &e) {
        gotException = true;
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
        player->open("file:var/test.mp3");
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
        player->open("file:var/test.mp3");
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
        player->open("file:var/test.mp3");
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

