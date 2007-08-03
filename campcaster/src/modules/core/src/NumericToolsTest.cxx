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

#include "LiveSupport/Core/NumericTools.h"
#include "NumericToolsTest.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(NumericToolsTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
NumericToolsTest :: setUp(void)                                     throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
NumericToolsTest :: tearDown(void)                                  throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test the itoa() function.
 *----------------------------------------------------------------------------*/
void
NumericToolsTest :: itoaTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    int             i = 3142874;
    Glib::ustring   a = NumericTools::itoa(i);
    CPPUNIT_ASSERT(a == "3142874");
}


/*------------------------------------------------------------------------------
 *  Test the addIndex() function.
 *----------------------------------------------------------------------------*/
void
NumericToolsTest :: addIndexTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Glib::ustring       base = "itemLabel";
    int                 index = 123;
    Glib::ustring       result = NumericTools::addIndex(base, index);
    CPPUNIT_ASSERT(result == "itemLabel124");

    Glib::ustring       second = NumericTools::addIndex("second", 0);
    CPPUNIT_ASSERT(second == "second1");
}

