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
    Ptr<Playlist>::Ref         playlist = Ptr<Playlist>::Ref(new Playlist());
    Ptr<const AudioClip>::Ref  audioClip;

    // set up a playlist instance
    configure(playlist, configFileName);
    audioClip = playlist->begin()->second->getAudioClip();

    // run the packing methods
    XmlRpcTools :: playlistToXmlRpcValue(playlist, xmlRpcPlaylist);
    XmlRpcTools :: audioClipToXmlRpcValue(audioClip, xmlRpcAudioClip);

    CPPUNIT_ASSERT(xmlRpcPlaylist.hasMember("playlist"));
    CPPUNIT_ASSERT(xmlRpcPlaylist["playlist"].getType() 
                                                == XmlRpcValue::TypeString);

    CPPUNIT_ASSERT(xmlRpcAudioClip.hasMember("audioClip"));
    CPPUNIT_ASSERT(xmlRpcAudioClip["audioClip"].getType() 
                                                == XmlRpcValue::TypeString);

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
    Ptr<Playlist>::Ref       newPlaylist;
    Ptr<AudioClip>::Ref      newAudioClip;
    try {
        newPlaylistId   = XmlRpcTools::extractPlaylistId(xmlRpcPlaylistId);
        newAudioClipId  = XmlRpcTools::extractAudioClipId(xmlRpcPlaylistId);
        newRelativeOffset 
                        = XmlRpcTools::extractRelativeOffset(xmlRpcPlaylistId);
        newPlaylist     = XmlRpcTools::extractPlaylist(xmlRpcPlaylist);
        newAudioClip    = XmlRpcTools::extractAudioClip(xmlRpcAudioClip);
        
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    CPPUNIT_ASSERT(*playlistId     == *newPlaylistId);
    CPPUNIT_ASSERT(*audioClipId    == *newAudioClipId);
    CPPUNIT_ASSERT(*relativeOffset == *newRelativeOffset);

    CPPUNIT_ASSERT(*playlist->getId()     == *newPlaylist->getId());
    CPPUNIT_ASSERT(*playlist->getTitle()  == *newPlaylist->getTitle());
    CPPUNIT_ASSERT(*playlist->getPlaylength() 
                                          == *newPlaylist->getPlaylength());

    CPPUNIT_ASSERT(*audioClip->getId()     == *newAudioClip->getId());
    CPPUNIT_ASSERT(*audioClip->getTitle()  == *newAudioClip->getTitle());
    CPPUNIT_ASSERT(*audioClip->getPlaylength() 
                                           == *newAudioClip->getPlaylength());
}


/*------------------------------------------------------------------------------
 *  Testing the search criteria marshaling/demarshaling.
 *----------------------------------------------------------------------------*/
void
XmlRpcToolsTest :: secondTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring("this is a token"));
    XmlRpcValue                 xmlRpcToken;
    
    CPPUNIT_ASSERT_NO_THROW(
        XmlRpcTools::tokenToXmlRpcValue(token, xmlRpcToken)
    );
    
    Ptr<Glib::ustring>::Ref     otherToken;
    CPPUNIT_ASSERT_NO_THROW(
        otherToken = XmlRpcTools::extractToken(xmlRpcToken)
    );
    CPPUNIT_ASSERT(otherToken);
    
    CPPUNIT_ASSERT(*token == *otherToken);
    
    XmlRpcValue                 otherXmlRpcToken;
    CPPUNIT_ASSERT_NO_THROW(
        XmlRpcTools::tokenToXmlRpcValue(otherToken, otherXmlRpcToken)
    );
    
    CPPUNIT_ASSERT(xmlRpcToken == otherXmlRpcToken);
}


/*------------------------------------------------------------------------------
 *  Testing the search criteria marshaling/demarshaling.
 *----------------------------------------------------------------------------*/
void
XmlRpcToolsTest :: searchCriteriaTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    std::string         xmlStringCriteria =
"<value><struct>"
"<member>"
"<name>criteria</name>"
"<value><struct>"
"   <member>"
"       <name>filetype</name>"
"       <value>audioClip</value>"
"   </member>"
"   <member>"
"       <name>operator</name>"
"       <value>or</value>"
"   </member>"
"   <member>"
"       <name>limit</name>"
"       <value><int>5</int></value>"
"   </member>"
"   <member>"
"       <name>offset</name>"
"       <value><int>100</int></value>"
"   </member>"
"   <member>"
"       <name>conditions</name>"
"       <value><array><data>"
"           <value><struct>"
"               <member>"
"                   <name>cat</name>"
"                   <value><string>dc:title</string></value>"
"               </member>"
"               <member>"
"                   <name>op</name>"
"                   <value><string>partial</string></value>"
"               </member>"
"               <member>"
"                   <name>val</name>"
"                   <value>abcdef</value>"
"               </member>"
"           </struct></value>"
"           <value><struct>"
"               <member>"
"                   <name>cat</name>"
"                   <value><string>dc:creator</string></value>"
"               </member>"
"               <member>"
"                   <name>op</name>"
"                   <value><string>=</string></value>"
"               </member>"
"               <member>"
"                   <name>val</name>"
"                   <value>ABCDEF</value>"
"               </member>"
"           </struct></value>"
"       </data></array></value>"
"   </member>"
"</struct></value>"
"</member>"
"</struct></value>";

    XmlRpcValue         xmlRpcCriteria;
    int                 offset = 0;
    xmlRpcCriteria.fromXml(xmlStringCriteria, &offset);
    
    Ptr<SearchCriteria>::Ref    criteria;
    CPPUNIT_ASSERT_NO_THROW(
        criteria = XmlRpcTools::extractSearchCriteria(xmlRpcCriteria)
    );
    CPPUNIT_ASSERT(criteria);
    
    XmlRpcValue         otherXmlRpcCriteria;
    CPPUNIT_ASSERT_NO_THROW(
        XmlRpcTools::searchCriteriaToXmlRpcValue(criteria, otherXmlRpcCriteria)
    );
    
    CPPUNIT_ASSERT(xmlRpcCriteria == otherXmlRpcCriteria);
    
    Ptr<SearchCriteria>::Ref    otherCriteria;
    CPPUNIT_ASSERT_NO_THROW(
        otherCriteria = XmlRpcTools::extractSearchCriteria(otherXmlRpcCriteria)
    );
    CPPUNIT_ASSERT(otherCriteria);
    
    CPPUNIT_ASSERT(*criteria == *otherCriteria);
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
    } catch (XmlRpc::XmlRpcException &e) {
        CPPUNIT_ASSERT(e.getCode() == 42);
        CPPUNIT_ASSERT(e.getMessage() == "this is an error");
    }
}

