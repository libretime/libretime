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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/Attic/TagConversionTest.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <iostream>

#include "LiveSupport/Core/TagConversion.h"
#include "TagConversionTest.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(TagConversionTest);

/**
 *  The name of the configuration file.
 */
static const std::string configFileName = "etc/tagConversionTable.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
TagConversionTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
TagConversionTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple test
 *----------------------------------------------------------------------------*/
void
TagConversionTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        TagConversion::existsId3Tag("Title");
        CPPUNIT_FAIL("allowed to use class before configuration");
    } catch (std::invalid_argument &e) {
    }

    try {
        TagConversion::id3ToDublinCore("Title");
        CPPUNIT_FAIL("allowed to use class before configuration");
    } catch (std::invalid_argument &e) {
    }

    CPPUNIT_ASSERT(!TagConversion::isConfigured());
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                new xmlpp::DomParser(configFileName, false));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();
        TagConversion::configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT( TagConversion::isConfigured());

    try {
        CPPUNIT_ASSERT( TagConversion::existsId3Tag("Title"));
        CPPUNIT_ASSERT(!TagConversion::existsId3Tag("Groovicity"));
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        CPPUNIT_ASSERT(TagConversion::id3ToDublinCore("Title") == "dc:title");
        std::string    dcTag = TagConversion::id3ToDublinCore("Artist");
        CPPUNIT_ASSERT(dcTag == "dc:creator");
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    try {
        std::string    dcTag = TagConversion::id3ToDublinCore("Boringness");
        CPPUNIT_FAIL("allowed to convert non-existent tag");
    } catch (std::invalid_argument &e) {
    }
}

