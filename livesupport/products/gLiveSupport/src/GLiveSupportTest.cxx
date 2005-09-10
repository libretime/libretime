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

#if HAVE_SYS_TYPES_H
#include <sys/types.h>
#else
#error "Need sys/types.h"
#endif

#if HAVE_PWD_H
#include <pwd.h>
#else
#error "Need pwd.h"
#endif


#include <string>
#include <iostream>
#include <fstream>

#include <gtkmm/main.h>

#include "LiveSupport/Core/TimeConversion.h"

#include "GLiveSupportTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(GLiveSupportTest);

/**
 *  The name of the generic configuration file for the GLiveSupport object.
 */
static const std::string gLiveSupportEtcConfigFileName 
                                            = "etc/gLiveSupport.xml";

/**
 *  The name of the user-specific configuration file for the
 *  GLiveSupport object, relative to the user's home directory.
 */
static const std::string gLiveSupportUserConfigFileName 
                                            = "/.livesupport/gLiveSupport.xml";

/**
 *  The login name.
 */
static const std::string login = "root";

/**
 *  The password.
 */
static const std::string password = "q";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
GLiveSupportTest :: setUp(void)                         throw ()
{
    Gtk::Main kit(0, 0);

    gLiveSupport.reset(new GLiveSupport());

    uid_t           uid = getuid();
    struct passwd * pwd = getpwuid(uid);
    std::string     configFileName;
    std::ifstream   ifs;

    configFileName  = pwd->pw_dir;
    configFileName += gLiveSupportUserConfigFileName;
    ifs.open(configFileName.c_str());
    if (!ifs.is_open() || ifs.bad()) {
        ifs.close();
        ifs.clear();
        ifs.open(gLiveSupportEtcConfigFileName.c_str());
    }

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_stream(ifs);
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        gLiveSupport->configure(*root);

    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in audio player configuration file: " 
                  << e.what() << std::endl;
    } catch (xmlpp::exception &e) {
        std::cerr << "syntax error in audio player configuration file: " 
                  << e.what() << std::endl;
    }
    ifs.close();

    gLiveSupport->resetStorage();

    if (!gLiveSupport->login(login, password)) {
        std::cerr << "gLiveSupport unable to log in" << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
GLiveSupportTest :: tearDown(void)                      throw ()
{
    gLiveSupport->logout();

    gLiveSupport.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the audio player engine can be started and stopped
 *----------------------------------------------------------------------------*/
void
GLiveSupportTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref     sessionId;

    sessionId = gLiveSupport->getSessionId();
    CPPUNIT_ASSERT(sessionId.get());
}


/*------------------------------------------------------------------------------
 *  Open an audio clip object.
 *----------------------------------------------------------------------------*/
void
GLiveSupportTest :: openAudioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id;
    Ptr<Playable>::Ref      clip;

    CPPUNIT_ASSERT(gLiveSupport->getAudioClipIds()->size() >= 4);
    id = gLiveSupport->getAudioClipIds()->at(3);

    try {
        clip = gLiveSupport->getAudioClip(id);
        CPPUNIT_ASSERT(clip.get());
        CPPUNIT_ASSERT(!clip->getToken().get());
        clip = gLiveSupport->getAudioClip(id);
        CPPUNIT_ASSERT(clip.get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }

    gLiveSupport->releaseOpennedAudioClips();

    try {
        clip = gLiveSupport->getAudioClip(id);
        CPPUNIT_ASSERT(clip.get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Acquire an audio clip object.
 *----------------------------------------------------------------------------*/
void
GLiveSupportTest :: acquireAudioClipTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id;
    Ptr<Playable>::Ref      clip;

    CPPUNIT_ASSERT(gLiveSupport->getAudioClipIds()->size() >= 4);
    id = gLiveSupport->getAudioClipIds()->at(3);

    try {
        clip = gLiveSupport->acquireAudioClip(id);
        CPPUNIT_ASSERT(clip.get());
        CPPUNIT_ASSERT(clip->getToken().get());
        // for a subsequent open call, returned the acquired clip again
        clip = gLiveSupport->getAudioClip(id);
        CPPUNIT_ASSERT(clip.get());
        CPPUNIT_ASSERT(clip->getToken().get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }

    gLiveSupport->releaseOpennedAudioClips();

    try {
        clip = gLiveSupport->acquireAudioClip(id);
        CPPUNIT_ASSERT(clip.get());
        CPPUNIT_ASSERT(clip->getToken().get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Open a playlist object.
 *----------------------------------------------------------------------------*/
void
GLiveSupportTest :: openPlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id;
    Ptr<Playable>::Ref      playlist;

    CPPUNIT_ASSERT(gLiveSupport->getPlaylistIds()->size() >= 2);
    id = gLiveSupport->getPlaylistIds()->at(1);

    try {
        playlist = gLiveSupport->getPlaylist(id);
        CPPUNIT_ASSERT(playlist.get());
        CPPUNIT_ASSERT(!playlist->getUri().get());
        playlist = gLiveSupport->getPlaylist(id);
        CPPUNIT_ASSERT(playlist.get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }

    gLiveSupport->releaseOpennedPlaylists();

    try {
        playlist = gLiveSupport->getPlaylist(id);
        CPPUNIT_ASSERT(playlist.get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Acquire a playlist object.
 *----------------------------------------------------------------------------*/
void
GLiveSupportTest :: acquirePlaylistTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id;
    Ptr<Playable>::Ref      playlist;

    CPPUNIT_ASSERT(gLiveSupport->getPlaylistIds()->size() >= 2);
    id = gLiveSupport->getPlaylistIds()->at(1);

    try {
        playlist = gLiveSupport->acquirePlaylist(id);
        CPPUNIT_ASSERT(playlist.get());
        CPPUNIT_ASSERT(playlist->getUri().get());
        // for a subsequent open call, returned the acquired playlist again
        playlist = gLiveSupport->getPlaylist(id);
        CPPUNIT_ASSERT(playlist.get());
        CPPUNIT_ASSERT(playlist->getUri().get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }

    gLiveSupport->releaseOpennedPlaylists();

    try {
        playlist = gLiveSupport->acquirePlaylist(id);
        CPPUNIT_ASSERT(playlist.get());
        CPPUNIT_ASSERT(playlist->getUri().get());
    } catch (XmlRpcException  &e) {
        CPPUNIT_FAIL(e.what());
    }
}

