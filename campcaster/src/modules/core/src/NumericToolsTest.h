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
#ifndef NumericToolsTest_h
#define NumericToolsTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>


namespace LiveSupport {
namespace Core {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the NumericTools class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see NumericTools
 */
class NumericToolsTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(NumericToolsTest);
    CPPUNIT_TEST(itoaTest);
    CPPUNIT_TEST(addIndexTest);
    CPPUNIT_TEST_SUITE_END();

    protected:

        /**
         *  Test the itoa() function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        itoaTest(void)                          throw (CPPUNIT_NS::Exception);

        /**
         *  Test the addIndex() function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        addIndexTest(void)                      throw (CPPUNIT_NS::Exception);


    public:
        
        /**
         *  Set up the environment for the test case.
         */
        void
        setUp(void)                                                 throw ();

        /**
         *  Clean up the environment after the test case.
         */
        void
        tearDown(void)                                              throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // NumericToolsTest_h

