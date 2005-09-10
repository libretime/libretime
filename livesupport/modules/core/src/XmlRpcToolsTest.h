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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/XmlRpcToolsTest.h,v $

------------------------------------------------------------------------------*/
#ifndef XmlRpcToolsTest_h
#define XmlRpcToolsTest_h

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
 *  Unit test for the XmlRpcTools class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see XmlRpcTools
 */
class XmlRpcToolsTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(XmlRpcToolsTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(errorTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Configure a configurable with an XML file.
         *
         *  @param configurable configure this
         *  @param fileName the name of the XML file to configure with.
         *  @exception CPPUNIT_NS::Exception on XML parsing errors.
         */
        void
        configure(Ptr<Configurable>::Ref    configurable,
                  std::string               fileName)
                                                throw (CPPUNIT_NS::Exception);

    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the method for error message packaging.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        errorTest(void)                         throw (CPPUNIT_NS::Exception);


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


} // namespace Scheduler
} // namespace LiveSupport

#endif // XmlRpcToolsTest_h

