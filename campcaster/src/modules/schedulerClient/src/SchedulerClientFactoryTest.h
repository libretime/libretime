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
#ifndef SchedulerClientFactoryTest_h
#define SchedulerClientFactoryTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>

#include "LiveSupport/Core/BaseTestMethod.h"
#include "LiveSupport/SchedulerClient/SchedulerClientFactory.h"

namespace LiveSupport {
namespace SchedulerClient {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the SchedulerClientFactoryTest class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see SchedulerClientFactoryTest
 */
class SchedulerClientFactoryTest : public BaseTestMethod
{
    CPPUNIT_TEST_SUITE(SchedulerClientFactoryTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST_SUITE_END();

    private:
        /**
         *  The SchedulerClientFactory instance to test.
         */
        Ptr<SchedulerClientFactory>::Ref        schedulerClientFactory;

        /**
         *  The SchedulerClientInterface returned by the factory.
         */
        Ptr<SchedulerClientInterface>::Ref      schedulerClient;

    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

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


} // namespace SchedulerClient
} // namespace LiveSupport

#endif // SchedulerClientFactoryTest_h

