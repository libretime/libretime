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

#include <fstream>

#include "KeyboardShortcutContainer.h"
#include "KeyboardShortcutTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(KeyboardShortcutTest);

namespace {

/**
 *  The name of the test keyboard shortcut config file.
 */
const std::string   configFileName  = "etc/keyboardShortcut.xml";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
KeyboardShortcutTest :: setUp(void)             throw (CPPUNIT_NS::Exception)
{
    std::ifstream   ifs;
    ifs.open(configFileName.c_str());
    
    if (!ifs.is_open() || ifs.bad()) {
        ifs.close();
        CPPUNIT_FAIL("could not open keyboard shortcut config file "
                     + configFileName);
    }

    ksc.reset(new KeyboardShortcutContainer);
    
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_stream(ifs);
        parser->set_validate();
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        ksc->configure(*root);

    } catch (std::invalid_argument &e) {
        ifs.close();
        CPPUNIT_FAIL("semantic error in keyboard shortcuts configuration file: "
                     + std::string(e.what()));
    } catch (xmlpp::exception &e) {
        ifs.close();
        CPPUNIT_FAIL("syntax error in keyboard shortcuts configuration file: "
                     + std::string(e.what()));
    }
    ifs.close();
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
KeyboardShortcutTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  A simple test.
 *----------------------------------------------------------------------------*/
void
KeyboardShortcutTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT(ksc->findAction(Gdk::ModifierType(0), GDK_p)
                    == KeyboardShortcut::playAudio);
    
    CPPUNIT_ASSERT(ksc->findAction(Gdk::MOD2_MASK | Gdk::LOCK_MASK, GDK_p)
                    == KeyboardShortcut::playAudio);
    
    CPPUNIT_ASSERT(ksc->findAction(Gdk::CONTROL_MASK, GDK_p)
                    == KeyboardShortcut::pauseAudio);
    
    CPPUNIT_ASSERT(ksc->findAction(Gdk::ModifierType(0), GDK_space)
                    == KeyboardShortcut::stopAudio);
    
    CPPUNIT_ASSERT(ksc->findAction(Gdk::ModifierType(0), GDK_q)
                    == KeyboardShortcut::noAction);
    
    CPPUNIT_ASSERT(ksc->findAction(Gdk::CONTROL_MASK, GDK_w)
                    == KeyboardShortcut::noAction);
    
    CPPUNIT_ASSERT(ksc->findAction(Gdk::ModifierType(0xffffff), 0xffffff)
                    == KeyboardShortcut::noAction);
}

