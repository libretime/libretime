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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/src/WebAuthenticationClientTest.cxx,v $

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
#include <fstream>
#include <iostream>

#include "LiveSupport/Core/SessionId.h"
#include "WebAuthenticationClientTest.h"


using namespace std;
using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(WebAuthenticationClientTest);

/**
 *  The name of the configuration file for the authentication client factory daemon.
 */
static const std::string configFileName = "etc/webAuthentication.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
WebAuthenticationClientTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        wac.reset(new WebAuthenticationClient());
        wac->configure(*root);
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
WebAuthenticationClientTest :: tearDown(void)                      throw ()
{
    wac.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if we can log on and off
 *----------------------------------------------------------------------------*/
void
WebAuthenticationClientTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SessionId>::Ref     sessionId;

    CPPUNIT_ASSERT(!(sessionId = wac->login("Piszkos Fred", "malnaszor")));

    // TODO: this call writes some garbage to cerr; it should be told not to
    sessionId.reset(new SessionId("ceci n'est pas un session ID"));
    CPPUNIT_ASSERT(!wac->logout(sessionId));
    
    CPPUNIT_ASSERT( sessionId = wac->login("root", "q"));
    CPPUNIT_ASSERT( wac->logout(sessionId));
//    this does not work due to a bug in the storage server
//    CPPUNIT_ASSERT(!wac->logout(sessionId));
}

