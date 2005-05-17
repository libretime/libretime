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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/TwoHelixPlayersTest.cxx,v $

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

#include "HelixPlayer.h"
#include "TestEventListener.h"
#include "TwoHelixPlayersTest.h"


using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(TwoHelixPlayersTest);

/**
 *  The name of the configuration file for the Helix player.
 */
static const std::string configFileName = "etc/twoHelixPlayers.xml";

/**
 *  The name of the root XML element in the configuration file.
 */
static const std::string rootElementName = "twoHelixPlayers";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
TwoHelixPlayersTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        xmlpp::Node::NodeList       children;
        const xmlpp::Element      * element;

        children = root->get_children(HelixPlayer::getConfigElementName());

        element = dynamic_cast<const xmlpp::Element*> (*(children.begin()));
        helixPlayer1.reset(new HelixPlayer());
        helixPlayer1->configure(*element);
        
        children.pop_front();
        element = dynamic_cast<const xmlpp::Element*> (*(children.begin()));
        helixPlayer2.reset(new HelixPlayer());
        helixPlayer2->configure(*element);

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
TwoHelixPlayersTest :: tearDown(void)                      throw ()
{
    helixPlayer2.reset();
    helixPlayer1.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the HelixPlayer engine can be started and stopped
 *----------------------------------------------------------------------------*/
void
TwoHelixPlayersTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        helixPlayer1->initialize();
        helixPlayer2->initialize();
        CPPUNIT_ASSERT(!helixPlayer1->isPlaying());
        CPPUNIT_ASSERT(!helixPlayer2->isPlaying());
        helixPlayer2->deInitialize();
        helixPlayer1->deInitialize();
    } catch (std::exception &e) {
        CPPUNIT_FAIL("failed to initialize or de-initialize HelixPlayer");
    }
}


/*------------------------------------------------------------------------------
 *  Play something simple on player #1
 *----------------------------------------------------------------------------*/
void
TwoHelixPlayersTest :: simplePlay1Test(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    helixPlayer1->initialize();
    try {
        helixPlayer1->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!helixPlayer1->isPlaying());
    helixPlayer1->start();
    CPPUNIT_ASSERT(helixPlayer1->isPlaying());
    while (helixPlayer1->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!helixPlayer1->isPlaying());
    helixPlayer1->close();
    helixPlayer1->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play something simple on player #2
 *----------------------------------------------------------------------------*/
void
TwoHelixPlayersTest :: simplePlay2Test(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    helixPlayer2->initialize();
    try {
        helixPlayer2->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!helixPlayer2->isPlaying());
    helixPlayer2->start();
    CPPUNIT_ASSERT(helixPlayer2->isPlaying());
    while (helixPlayer2->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!helixPlayer2->isPlaying());
    helixPlayer2->close();
    helixPlayer2->deInitialize();
}


/*------------------------------------------------------------------------------
 *  Play something simple on both players
 *----------------------------------------------------------------------------*/
void
TwoHelixPlayersTest :: playBothTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));

    helixPlayer1->initialize();
    helixPlayer2->initialize();

    // start playing on player1
    try {
        helixPlayer1->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!helixPlayer1->isPlaying());
    helixPlayer1->start();
    CPPUNIT_ASSERT(helixPlayer1->isPlaying());

    // sleep some time
    for (unsigned i = 0; i < 100; ++i) {
        TimeConversion::sleep(sleepT);
    }
    helixPlayer2->setAudioDevice();

    // start playing on player2
    try {
        helixPlayer2->open("file:var/test.mp3");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(!helixPlayer2->isPlaying());
    helixPlayer2->start();
    CPPUNIT_ASSERT(helixPlayer2->isPlaying());

    // wait for both players to finish
    while (helixPlayer1->isPlaying() || helixPlayer2->isPlaying()) {
        TimeConversion::sleep(sleepT);
    }
    CPPUNIT_ASSERT(!helixPlayer1->isPlaying());
    CPPUNIT_ASSERT(!helixPlayer2->isPlaying());

    helixPlayer2->close();
    helixPlayer1->close();
    helixPlayer2->deInitialize();
    helixPlayer1->deInitialize();
}


