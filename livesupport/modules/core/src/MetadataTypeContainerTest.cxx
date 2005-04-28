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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/MetadataTypeContainerTest.cxx,v $

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

#include "LiveSupport/Core/MetadataTypeContainer.h"

#include "MetadataTypeContainerTest.h"

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(MetadataTypeContainerTest);

/**
 *  The name of the configuration file for the resource bundle.
 */
static const std::string bundleConfigFileName = "etc/resourceBundle.xml";

/**
 *  The name of the configuration file for the metadataType element.
 */
static const std::string metadataTypeConfigFileName = "etc/metadataType.xml";

/**
 *  The name of the configuration file for the metadataTypeContainer element.
 */
static const std::string configFileName = "etc/metadataTypeContainer.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: setUp(void)                             throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                              new xmlpp::DomParser(bundleConfigFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        bundle = LocalizedObject::getBundle(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(bundle.get());
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: tearDown(void)                          throw ()
{
    bundle.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const MetadataType>::Ref        metadataType;
    Ptr<MetadataTypeContainer>::Ref     container;
    bool                                gotException;

    // test configuration from a configuration file
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        container.reset(new MetadataTypeContainer(bundle));
        container->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(std::string("semantic error in configuration file:\n")
                   + e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(std::string("XML error in configuration file:\n")
                   + e.what());
    }

    // test double-configuration
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        gotException = false;
        try {
            container->configure(*root);
        } catch (std::invalid_argument &e) {
            gotException = true;
        }
        CPPUNIT_ASSERT(gotException);

    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(std::string("XML error in configuration file:\n")
                   + e.what());
    }

    // a simple positive check on the DC name
    CPPUNIT_ASSERT(container->existsByDcName("dc:creator"));
    metadataType = container->getByDcName("dc:creator");

    CPPUNIT_ASSERT(*metadataType->getDcName() == "dc:creator");
    CPPUNIT_ASSERT(*metadataType->getId3Tag() == "TPE2");
    CPPUNIT_ASSERT(*metadataType->getLocalizationKey() == "dc_creator");

    // a negative check on the DC name
    CPPUNIT_ASSERT(!container->existsByDcName("dc:nonExistent"));

    gotException = false;
    try {
        container->getByDcName("dc:nonExistent");
    } catch (std::invalid_argument &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);

    // a simple positive check on the ID3v2 tag
    CPPUNIT_ASSERT(container->existsById3Tag("TPE2"));
    metadataType = container->getById3Tag("TPE2");

    CPPUNIT_ASSERT(*metadataType->getDcName() == "dc:creator");
    CPPUNIT_ASSERT(*metadataType->getId3Tag() == "TPE2");
    CPPUNIT_ASSERT(*metadataType->getLocalizationKey() == "dc_creator");

    // a negative check on the DC name
    CPPUNIT_ASSERT(!container->existsByDcName("NonExistentTag"));

    gotException = false;
    try {
        container->getByDcName("NonExistentTag");
    } catch (std::invalid_argument &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);
}


/*------------------------------------------------------------------------------
 *  Test the iterator feature of the container.
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: iteratorTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const MetadataType>::Ref                    metadataType;
    Ptr<MetadataTypeContainer>::Ref                 container;
    MetadataTypeContainer::Vector::const_iterator   it;
    MetadataTypeContainer::Vector::const_iterator   end;

    // test on an empty container
    container.reset(new MetadataTypeContainer(bundle));
    it  = container->begin();
    end = container->end();
    CPPUNIT_ASSERT(it == end);
    container.reset();

    // test configuration from a configuration file
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        container.reset(new MetadataTypeContainer(bundle));
        container->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(std::string("semantic error in configuration file:\n")
                   + e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(std::string("XML error in configuration file:\n")
                   + e.what());
    }

    // cycle through the iterator, should be one element
    it  = container->begin();
    end = container->end();
    while (it != end) {
        metadataType = (Ptr<const MetadataType>::Ref) *it;
        CPPUNIT_ASSERT(*metadataType->getDcName() == "dc:creator");
        CPPUNIT_ASSERT(*metadataType->getId3Tag() == "TPE2");
        CPPUNIT_ASSERT(*metadataType->getLocalizationKey() == "dc_creator");

        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Test localized metadata type names.
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: localizedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const MetadataType>::Ref           metadataType;
    Ptr<MetadataTypeContainer>::Ref        container;

    // test configuration from a configuration file
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        container.reset(new MetadataTypeContainer(bundle));
        container->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(std::string("semantic error in configuration file:\n")
                   + e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(std::string("XML error in configuration file:\n")
                   + e.what());
    }

    CPPUNIT_ASSERT(container->existsByDcName("dc:creator"));
    metadataType = container->getByDcName("dc:creator");

    CPPUNIT_ASSERT(*metadataType->getLocalizedName() == "Creator");

    UErrorCode                      status = U_ZERO_ERROR;
    Ptr<ResourceBundle>::Ref        huBundle;
    Ptr<ResourceBundle>::Ref        jpBundle;
    Ptr<const Glib::ustring>::Ref   ustr;

    // test with hungarian
    huBundle.reset(new ResourceBundle("./tmp/" PACKAGE_NAME, "hu", status));
    CPPUNIT_ASSERT(U_SUCCESS(status));
    container->setBundle(huBundle);

    ustr = metadataType->getLocalizedName();
    CPPUNIT_ASSERT((*ustr)[0] == 0x004c);  // 'L'
    CPPUNIT_ASSERT((*ustr)[1] == 0x00e9);  // 'e' with acute
    CPPUNIT_ASSERT((*ustr)[2] == 0x0074);  // 't'
    CPPUNIT_ASSERT((*ustr)[3] == 0x0072);  // 'r'
    CPPUNIT_ASSERT((*ustr)[4] == 0x0065);  // 'e'
    CPPUNIT_ASSERT((*ustr)[5] == 0x0068);  // 'h'
    CPPUNIT_ASSERT((*ustr)[6] == 0x006f);  // 'o'
    CPPUNIT_ASSERT((*ustr)[7] == 0x007a);  // 'z'
    CPPUNIT_ASSERT((*ustr)[8] == 0x00f3);  // 'o' with acute

    // test with japanese
    jpBundle.reset(new ResourceBundle("./tmp/" PACKAGE_NAME, "jp", status));
    CPPUNIT_ASSERT(U_SUCCESS(status));
    container->setBundle(jpBundle);

    ustr = metadataType->getLocalizedName();
    CPPUNIT_ASSERT((*ustr)[0] == 0x30af);  // katakana ku
    CPPUNIT_ASSERT((*ustr)[1] == 0x30ea);  // katakana ri
    CPPUNIT_ASSERT((*ustr)[2] == 0x30a8);  // katakana e
    CPPUNIT_ASSERT((*ustr)[3] == 0x30fc);  // katakana '-'
    CPPUNIT_ASSERT((*ustr)[4] == 0x30bf);  // katakana ta
}

