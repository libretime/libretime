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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/LocalizedConfigurableTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <iostream>
#include <unicode/resbund.h>

#include "LiveSupport/Core/LocalizedConfigurable.h"
#include "LocalizedConfigurableTest.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(LocalizedConfigurableTest);

/**
 *  The name of the configuration file for the resource bundle.
 */
static const std::string configFileName = "etc/resourceBundle.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
LocalizedConfigurableTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
LocalizedConfigurableTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple smoke test.
 *----------------------------------------------------------------------------*/
void
LocalizedConfigurableTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<LocalizedConfigurable>::Ref     locConf(new LocalizedConfigurable());

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        locConf->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        Ptr<LocalizedObject>::Ref   section1(new LocalizedObject(
                                               locConf->getBundle("section1")));
        Ptr<UnicodeString>::Ref     foo = section1->getResourceString("foo");
        CPPUNIT_ASSERT(foo->compare("fou") == 0);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  A test to see if chaning the locale works.
 *----------------------------------------------------------------------------*/
void
LocalizedConfigurableTest :: changeLocaleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<LocalizedConfigurable>::Ref     locConf(new LocalizedConfigurable());

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        locConf->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }

    // see if all is OK in english
    try {
        Ptr<LocalizedObject>::Ref   section1(new LocalizedObject(
                                               locConf->getBundle("section1")));
        Ptr<UnicodeString>::Ref     foo = section1->getResourceString("foo");
        CPPUNIT_ASSERT(foo->compare("fou") == 0);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    // see if all is OK when changing to hungarian.
    try {
        locConf->changeLocale("hu");

        Ptr<LocalizedObject>::Ref   section1(new LocalizedObject(
                                               locConf->getBundle("section1")));
        Ptr<UnicodeString>::Ref     foo = section1->getResourceString("foo");
        CPPUNIT_ASSERT(foo->charAt(0) == 0x0066);  // 'f'
        CPPUNIT_ASSERT(foo->charAt(1) == 0x00fa);  // 'u' with acute
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    // see if all is OK when changing to japanese.
    try {
        locConf->changeLocale("jp");

        Ptr<LocalizedObject>::Ref   section1(new LocalizedObject(
                                               locConf->getBundle("section1")));
        Ptr<UnicodeString>::Ref     foo = section1->getResourceString("foo");
        CPPUNIT_ASSERT(foo->charAt(0) == 0x3075);  // hiragana fu
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


