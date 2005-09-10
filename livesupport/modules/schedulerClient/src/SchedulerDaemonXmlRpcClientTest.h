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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/src/SchedulerDaemonXmlRpcClientTest.h,v $

------------------------------------------------------------------------------*/
#ifndef SchedulerDaemonXmlRpcClientTest_h
#define SchedulerDaemonXmlRpcClientTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>

#include "LiveSupport/Core/BaseTestMethod.h"
#include "LiveSupport/Authentication/AuthenticationClientInterface.h"
#include "SchedulerDaemonXmlRpcClient.h"

namespace LiveSupport {
namespace SchedulerClient {

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the SchedulerDaemonXmlRpcClient class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see SchedulerDaemonXmlRpcClient
 */
class SchedulerDaemonXmlRpcClientTest : public BaseTestMethod
{
    CPPUNIT_TEST_SUITE(SchedulerDaemonXmlRpcClientTest);
    CPPUNIT_TEST(getVersionTest);
    CPPUNIT_TEST(getSchedulerTimeTest);
    CPPUNIT_TEST(displayScheduleEmptyTest);
    CPPUNIT_TEST(displayPlaylistTest);
    CPPUNIT_TEST(playlistMgmtTest);
    CPPUNIT_TEST(xmlRpcErrorTest);
    CPPUNIT_TEST_SUITE_END();

    private:
        /**
         *  The SchedulerDaemonXmlRpcClient instance to test.
         */
        Ptr<SchedulerDaemonXmlRpcClient>::Ref   schedulerClient;

        /**
         *  A session ID from the authentication client login() method.
         */
        Ptr<SessionId>::Ref                     sessionId;


    protected:

        /**
         *  A simple test, just to get the version string from the scheduler.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getVersionTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  A test to check the getSchedulerTime XML-RPC method.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getSchedulerTimeTest(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  A test to check the displaySchedule XML-RPC method, when
         *  the schedule is empty.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        displayScheduleEmptyTest(void)          throw (CPPUNIT_NS::Exception);

        /**
         *  Test some simple playlist operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        displayPlaylistTest(void)               throw (CPPUNIT_NS::Exception);

        /**
         *  Test playlist management.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        playlistMgmtTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Test for some XML-RPC error conditions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        xmlRpcErrorTest(void)                   throw (CPPUNIT_NS::Exception);


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

#endif // SchedulerDaemonXmlRpcClientTest_h

