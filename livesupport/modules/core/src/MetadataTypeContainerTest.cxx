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
    Version  : $Revision: 1.4 $
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
    Ptr<ResourceBundle>::Ref    rootBundle;
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                              new xmlpp::DomParser(bundleConfigFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        rootBundle = LocalizedObject::getBundle(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in bundle configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(std::string("XML error in bundle configuration file:\n")
                   + e.what());
    }
    CPPUNIT_ASSERT(rootBundle);

    UErrorCode      icuError = U_ZERO_ERROR;
    bundle.reset(new ResourceBundle(rootBundle->get("metadata", icuError)));
    CPPUNIT_ASSERT(U_SUCCESS(icuError));
    CPPUNIT_ASSERT(bundle);

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        container.reset(new MetadataTypeContainer(bundle));
        container->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(std::string("semantic error in metadata container"
                                 " configuration file:\n")
                   + e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(std::string("XML error in metadata container"
                                 " configuration file:\n")
                   + e.what());
    }

}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: tearDown(void)                          throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const MetadataType>::Ref        metadataType;
    bool                                gotException;

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
    CPPUNIT_ASSERT(*metadataType->getId3Tag() == "TPE1");
    CPPUNIT_ASSERT(*metadataType->getLocalizationKey() == "creator");

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
    CPPUNIT_ASSERT(container->existsById3Tag("TPE1"));
    metadataType = container->getById3Tag("TPE1");

    CPPUNIT_ASSERT(*metadataType->getDcName() == "dc:creator");
    CPPUNIT_ASSERT(*metadataType->getId3Tag() == "TPE1");
    CPPUNIT_ASSERT(*metadataType->getLocalizationKey() == "creator");

    // a negative check on the ID3v2 tag
    CPPUNIT_ASSERT(!container->existsById3Tag("NonExistentTag"));

    gotException = false;
    try {
        container->getById3Tag("NonExistentTag");
    } catch (std::invalid_argument &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);

    // two simple positive checks on the tab attribute
    CPPUNIT_ASSERT(container->existsByDcName("dc:title"));
    metadataType = container->getByDcName("dc:title");
    CPPUNIT_ASSERT(metadataType->getTab() == MetadataType::mainTab);

    CPPUNIT_ASSERT(container->existsByDcName("ls:buycdurl"));
    metadataType = container->getByDcName("ls:buycdurl");
    CPPUNIT_ASSERT(metadataType->getTab() == MetadataType::noTab);
}


/*------------------------------------------------------------------------------
 *  Test the iterator feature of the container.
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: iteratorTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const MetadataType>::Ref                    metadataType;
    MetadataTypeContainer::Vector::const_iterator   it;
    MetadataTypeContainer::Vector::const_iterator   end;

    // check the first two elements in the container
    it  = container->begin();
    end = container->end();

    CPPUNIT_ASSERT(it != end);
    metadataType = (Ptr<const MetadataType>::Ref) *it;
    CPPUNIT_ASSERT(*metadataType->getDcName() == "dc:title");
    CPPUNIT_ASSERT(*metadataType->getId3Tag() == "TIT2");
    CPPUNIT_ASSERT(*metadataType->getLocalizationKey() == "title");

    ++it;
    CPPUNIT_ASSERT(it != end);
    metadataType = (Ptr<const MetadataType>::Ref) *it;
    CPPUNIT_ASSERT(*metadataType->getDcName() == "dc:creator");
    CPPUNIT_ASSERT(*metadataType->getId3Tag() == "TPE1");
    CPPUNIT_ASSERT(*metadataType->getLocalizationKey() == "creator");

    // test on an empty container
    container.reset(new MetadataTypeContainer(bundle));
    it  = container->begin();
    end = container->end();
    CPPUNIT_ASSERT(it == end);
}


/*------------------------------------------------------------------------------
 *  Test localized metadata type names.
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainerTest :: localizedTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const MetadataType>::Ref           metadataType;

    CPPUNIT_ASSERT(container->existsByDcName("dc:creator"));
    metadataType = container->getByDcName("dc:creator");

    CPPUNIT_ASSERT(*metadataType->getLocalizedName() == "Creator");

    UErrorCode                      status = U_ZERO_ERROR;
    Ptr<ResourceBundle>::Ref        rootBundle;
    Ptr<ResourceBundle>::Ref        huBundle;
    Ptr<ResourceBundle>::Ref        jpBundle;
    Ptr<const Glib::ustring>::Ref   ustr;

    // test with hungarian
    rootBundle.reset(new ResourceBundle("./tmp/" PACKAGE_NAME, "hu", status));
    CPPUNIT_ASSERT(U_SUCCESS(status));
    huBundle.reset(new ResourceBundle(rootBundle->get("metadata", status)));
    CPPUNIT_ASSERT(U_SUCCESS(status));
    container->setBundle(huBundle);

    ustr = metadataType->getLocalizedName();
    CPPUNIT_ASSERT(ustr->length() == 6);
    CPPUNIT_ASSERT((*ustr)[0] == 0x0045);  // 'E'
    CPPUNIT_ASSERT((*ustr)[1] == 0x006C);  // 'l'
    CPPUNIT_ASSERT((*ustr)[2] == 0x0151);  // 'o' with double acute
    CPPUNIT_ASSERT((*ustr)[3] == 0x0061);  // 'a'
    CPPUNIT_ASSERT((*ustr)[4] == 0x0064);  // 'd'
    CPPUNIT_ASSERT((*ustr)[5] == 0x00F3);  // 'o' with acute

    // test with japanese
    rootBundle.reset(new ResourceBundle("./tmp/" PACKAGE_NAME, "jp", status));
    CPPUNIT_ASSERT(U_SUCCESS(status));
    jpBundle.reset(new ResourceBundle(rootBundle->get("metadata", status)));
    CPPUNIT_ASSERT(U_SUCCESS(status));
    container->setBundle(jpBundle);

    ustr = metadataType->getLocalizedName();
    CPPUNIT_ASSERT(ustr->length() == 6);
    CPPUNIT_ASSERT((*ustr)[0] == 0x30af);  // katakana ku
    CPPUNIT_ASSERT((*ustr)[1] == 0x30ea);  // katakana ri
    CPPUNIT_ASSERT((*ustr)[2] == 0x30a8);  // katakana e
    CPPUNIT_ASSERT((*ustr)[3] == 0x30fc);  // katakana '-'
    CPPUNIT_ASSERT((*ustr)[4] == 0x30bf);  // katakana ta
    CPPUNIT_ASSERT((*ustr)[5] == 0x30fc);  // katakana '-'
}

