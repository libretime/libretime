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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/TwoGstreamerPlayersTest.cxx,v $

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
#include "TwoGstreamerPlayersTest.h"


using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(TwoGstreamerPlayersTest);

/**
 *  The name of the configuration file for the audio player.
 */
static const std::string configFileName = "etc/twoGstreamerPlayers.xml";

/**
 *  The name of the root XML element in the configuration file.
 */
static const std::string rootElementName = "twoGstreamerPlayers";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
TwoGstreamerPlayersTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        xmlpp::Node::NodeList       children;
        const xmlpp::Element      * element;

        children = root->get_children(GstreamerPlayer::getConfigElementName());

        element = dynamic_cast<const xmlpp::Element*> (*(children.begin()));
        player1.reset(new GstreamerPlayer());
        player1->configure(*element);
        
        children.pop_front();
        element = dynamic_cast<const xmlpp::Element*> (*(children.begin()));
        player2.reset(new GstreamerPlayer());
        player2->configure(*element);

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
TwoGstreamerPlayersTest :: tearDown(void)                      throw ()
{
    player2.reset();
    player1.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the GstreamerPlayer engine can be started and stopped
 *----------------------------------------------------------------------------*/
void
TwoGstreamerPlayersTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        player1->initialize();
        player2->initialize();
        CPPUNIT_ASSERT(!player1->isPlaying());
        CPPUNIT_ASSERT(!player2->isPlaying());
        player2->deInitialize();
        player1->deInitialize();
    } catch (std::exception &e) {
        CPPUNIT_FAIL("failed to initialize or de-initialize GstreamerPlayer");
    }
}


/*------------------------------------------------------------------------------
 *  Play something simple on player #1
 *----------------------------------------------------------------------------*/
void
TwoGstreamerPlayersTest :: simplePlay1Test(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    player1->initialize();
    try {
        player1->open("file:var/test10001.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!player1->isPlaying());
    player1->start();
    CPPUNIT_ASSERT(player1->isPlaying());
    while (player1->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player1->isPlaying());
    player1->close();
    player1->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play something simple on player #2
 *----------------------------------------------------------------------------*/
void
TwoGstreamerPlayersTest :: simplePlay2Test(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    player2->initialize();
    try {
        player2->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!player2->isPlaying());
    player2->start();
    CPPUNIT_ASSERT(player2->isPlaying());
    while (player2->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player2->isPlaying());
    player2->close();
    player2->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play something simple on both players
 *----------------------------------------------------------------------------*/
void
TwoGstreamerPlayersTest :: playBothTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    player1->initialize();
    player2->initialize();

    // start playing on player1
    try {
        player1->open("file:var/test10001.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!player1->isPlaying());
    player1->start();
    CPPUNIT_ASSERT(player1->isPlaying());

    // sleep some time
    for (unsigned i = 0; i < 100; ++i) {
        TimeConversion::sleep(sleepT);
    }

    // start playing on player2
    try {
        player2->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!player2->isPlaying());
    player2->start();
    CPPUNIT_ASSERT(player2->isPlaying());

    // wait for both players to finish
    while (player1->isPlaying() || player2->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!player1->isPlaying());
    CPPUNIT_ASSERT(!player2->isPlaying());

    player2->close();
    player1->close();
    player2->deInitialize();
    player1->deInitialize();
}


