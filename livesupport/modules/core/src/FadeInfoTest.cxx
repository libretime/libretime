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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/FadeInfoTest.cxx,v $

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

#include "LiveSupport/Core/FadeInfo.h"
#include "FadeInfoTest.h"


using namespace std;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(FadeInfoTest);

/**
 *  The name of the configuration file for the audio clip.
 */
static const std::string configFileName = "etc/fadeInfo.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
FadeInfoTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
FadeInfoTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
FadeInfoTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();
        Ptr<FadeInfo>::Ref     fadeInfo(new FadeInfo());

        fadeInfo->configure(*root);

        CPPUNIT_ASSERT(fadeInfo->getId()->getId() == 9901);

        Ptr<const boost::posix_time::time_duration>::Ref
                                            fadeIn = fadeInfo->getFadeIn();
        CPPUNIT_ASSERT(fadeIn->hours()   == 0);
        CPPUNIT_ASSERT(fadeIn->minutes() == 0);
        CPPUNIT_ASSERT(fadeIn->seconds() == 2);

        Ptr<const boost::posix_time::time_duration>::Ref
                                            fadeOut = fadeInfo->getFadeOut();
        CPPUNIT_ASSERT(fadeOut->hours()   == 0);
        CPPUNIT_ASSERT(fadeOut->minutes() == 0);
        CPPUNIT_ASSERT(fadeOut->seconds() == 1);
        CPPUNIT_ASSERT(fadeOut->fractional_seconds() == 500);

    } catch (std::invalid_argument &e) {
        std::string eMsg = "semantic error in configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    } catch (xmlpp::exception &e) {
        std::string eMsg = "error parsing configuration file:\n";
        eMsg += e.what();
        CPPUNIT_FAIL(eMsg);
    }
}
