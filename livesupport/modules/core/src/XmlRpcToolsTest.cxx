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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/XmlRpcToolsTest.cxx,v $

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
#include <XmlRpcValue.h>

#include "LiveSupport/Core/XmlRpcTools.h"
#include "XmlRpcToolsTest.h"


using namespace LiveSupport::Core;

using namespace std;
using namespace XmlRpc;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(XmlRpcToolsTest);

/**
 *  The name of the configuration file for the playlist.
 */
const std::string configFileName = "etc/playlist.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
XmlRpcToolsTest :: configure(
            Ptr<Configurable>::Ref      configurable,
            const std::string           fileName)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        configurable->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }
}

                      
/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
XmlRpcToolsTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
XmlRpcToolsTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
XmlRpcToolsTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue                xmlRpcPlaylist;
    XmlRpcValue                xmlRpcAudioClip;
    Ptr<Playlist>::Ref         playlist = Ptr<Playlist>::Ref(new Playlist);
    Ptr<const AudioClip>::Ref  audioClip;

    // set up a playlist instance
    configure(playlist, configFileName);
    audioClip = playlist->begin()->second->getAudioClip();

    // run the packing methods
    XmlRpcTools :: playlistToXmlRpcValue(playlist, xmlRpcPlaylist);
    XmlRpcTools :: audioClipToXmlRpcValue(audioClip, xmlRpcAudioClip);

    CPPUNIT_ASSERT(xmlRpcPlaylist.hasMember("id"));
    CPPUNIT_ASSERT(xmlRpcPlaylist["id"].getType() == XmlRpcValue::TypeString);
    CPPUNIT_ASSERT(std::string(xmlRpcPlaylist["id"]) == "0000000000000001");

    CPPUNIT_ASSERT(xmlRpcPlaylist.hasMember("playlength"));
    CPPUNIT_ASSERT(xmlRpcPlaylist["playlength"].getType() 
                                                     == XmlRpcValue::TypeInt);
    CPPUNIT_ASSERT(int(xmlRpcPlaylist["playlength"]) == 34);

    CPPUNIT_ASSERT(xmlRpcAudioClip.hasMember("id"));
    CPPUNIT_ASSERT(xmlRpcAudioClip["id"].getType() == XmlRpcValue::TypeString);
    CPPUNIT_ASSERT(std::string(xmlRpcAudioClip["id"]) == "0000000000010001");

    CPPUNIT_ASSERT(xmlRpcAudioClip.hasMember("playlength"));
    CPPUNIT_ASSERT(xmlRpcAudioClip["playlength"].getType() 
                                                      == XmlRpcValue::TypeInt);
    CPPUNIT_ASSERT(int(xmlRpcAudioClip["playlength"]) == 11);

    XmlRpcValue              xmlRpcPlaylistId;
    Ptr<UniqueId>::Ref       playlistId(new UniqueId(rand()));
    Ptr<UniqueId>::Ref       audioClipId(new UniqueId(rand()));
    Ptr<time_duration>::Ref  relativeOffset(new time_duration(0,0,rand(),0));

    xmlRpcPlaylistId["playlistId"]      = std::string(*playlistId);
    xmlRpcPlaylistId["audioClipId"]     = std::string(*audioClipId);
    xmlRpcPlaylistId["relativeOffset"]  = relativeOffset->total_seconds();

    // run the unpacking methods
    Ptr<UniqueId>::Ref       newPlaylistId;
    Ptr<UniqueId>::Ref       newAudioClipId;
    Ptr<time_duration>::Ref  newRelativeOffset;
    try {
       newPlaylistId     = XmlRpcTools::extractPlaylistId(xmlRpcPlaylistId);
       newAudioClipId    = XmlRpcTools::extractAudioClipId(xmlRpcPlaylistId);
       newRelativeOffset = XmlRpcTools::extractRelativeOffset(xmlRpcPlaylistId);
    }
    catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    CPPUNIT_ASSERT(*playlistId     == *newPlaylistId);
    CPPUNIT_ASSERT(*audioClipId    == *newAudioClipId);
    CPPUNIT_ASSERT(*relativeOffset == *newRelativeOffset);
}


/*------------------------------------------------------------------------------
 *  Testing markError()
 *----------------------------------------------------------------------------*/
void
XmlRpcToolsTest :: errorTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpcValue  xmlRpcValue;

    try {
        XmlRpcTools :: markError(42, "this is an error", xmlRpcValue);
        CPPUNIT_FAIL("did not throw exception in markError()");
    }
    catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 42);
        CPPUNIT_ASSERT(e.getMessage() == "this is an error");
    }
}

