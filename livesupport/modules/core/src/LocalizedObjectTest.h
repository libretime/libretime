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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/LocalizedObjectTest.h,v $

------------------------------------------------------------------------------*/
#ifndef LocalizedObjectTest_h
#define LocalizedObjectTest_h

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

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the LocalizedObject class.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.3 $
 *  @see LocalizedObject
 */
class LocalizedObjectTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(LocalizedObjectTest);
    CPPUNIT_TEST(simpleTest);
    CPPUNIT_TEST(fallbackTest);
    CPPUNIT_TEST(unicodeTest);
    CPPUNIT_TEST(formatMessageTest);
    CPPUNIT_TEST(loadFromConfig);
    CPPUNIT_TEST_SUITE_END();

    protected:

        /**
         *  A simple smoke test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simpleTest(void)                        throw (CPPUNIT_NS::Exception);

        /**
         *  Test to see if multiple locales work, and they fall back to
         *  more generic values.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        fallbackTest(void)                      throw (CPPUNIT_NS::Exception);

        /**
         *  Test to see if funny unicode characters work properly.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        unicodeTest(void)                       throw (CPPUNIT_NS::Exception);

        /**
         *  A test to see if message formatting works all right.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        formatMessageTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  A test to see if a resource bundle can be loaded based on a
         *  configuration file
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        loadFromConfig(void)                    throw (CPPUNIT_NS::Exception);


    public:
        
        /**
         *  Set up the environment for the test case.
         */
        void
        setUp(void)                                     throw ();

        /**
         *  Clean up the environment after the test case.
         */
        void
        tearDown(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LocalizedObjectTest_h

