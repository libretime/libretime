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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/TestStorageClientTest.cxx,v $

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

#include "TestStorageClient.h"
#include "TestStorageClientTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(TestStorageClientTest);

/**
 *  The name of the configuration file for the storage client factory daemon.
 */
static const std::string configFileName = "etc/testStorage.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        tsc.reset(new TestStorageClient());
        tsc->configure(*root);
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
TestStorageClientTest :: tearDown(void)                      throw ()
{
    tsc.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
        Ptr<UniqueId>::Ref      id1(new UniqueId(1));
        Ptr<UniqueId>::Ref      id2(new UniqueId(2));

        CPPUNIT_ASSERT(tsc->existsPlaylist(id1));
        CPPUNIT_ASSERT(!tsc->existsPlaylist(id2));

        Ptr<Playlist>::Ref      playlist = tsc->getPlaylist(id1);
        CPPUNIT_ASSERT(playlist->getId()->getId() == id1->getId());
}


/*------------------------------------------------------------------------------
 *  Testing the deletePlaylist method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: deletePlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
        Ptr<UniqueId>::Ref      id1(new UniqueId(1));
        Ptr<UniqueId>::Ref      id2(new UniqueId(2));

        try {
            tsc->deletePlaylist(id2);
            CPPUNIT_FAIL("allowed to delete non-existent playlist");
        } catch (std::invalid_argument &e) {
        }
        try {
            tsc->deletePlaylist(id1);
        } catch (std::invalid_argument &e) {
            CPPUNIT_FAIL("cannot delete existing playlist");
        }
        try {
            tsc->deletePlaylist(id1);
            CPPUNIT_FAIL("allowed to delete non-existent playlist");
        } catch (std::invalid_argument &e) {
        }
}


/*------------------------------------------------------------------------------
 *  Testing the getAllPlaylists method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: getAllPlaylistsTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref  playlistVector =
                                                tsc->getAllPlaylists();
    CPPUNIT_ASSERT(playlistVector->size() == 1);

    Ptr<Playlist>::Ref playlist = (*playlistVector)[0];
    CPPUNIT_ASSERT((int) (playlist->getId()->getId()) == 1);
}


/*------------------------------------------------------------------------------
 *  Testing the createPlaylist method
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: createPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<Playlist>::Ref playlist = tsc->createPlaylist();

    CPPUNIT_ASSERT(tsc->existsPlaylist(playlist->getId()));
}


/*------------------------------------------------------------------------------
 *  Test to see if the fake audio clips are correctly counterfeited
 *----------------------------------------------------------------------------*/
void
TestStorageClientTest :: audioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
        Ptr<const UniqueId>::Ref  id(new UniqueId(rand()));

        CPPUNIT_ASSERT(tsc->existsAudioClip(id));

        Ptr<AudioClip>::Ref       audioClip = tsc->getAudioClip(id);
        CPPUNIT_ASSERT(audioClip->getId()->getId() == id->getId());
        CPPUNIT_ASSERT(audioClip->getPlaylength()->total_seconds()
                                                   == 30*60);
}
