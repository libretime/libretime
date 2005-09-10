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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PlaylistEventContainerTest.h,v $

------------------------------------------------------------------------------*/
#ifndef PlaylistEventContainerTest_h
#define PlaylistEventContainerTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


#include <cppunit/extensions/HelperMacros.h>

#include "LiveSupport/Authentication/AuthenticationClientInterface.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Storage/StorageClientInterface.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"
#include "ScheduleInterface.h"
#include "PlayLogInterface.h"
#include "ScheduleFactory.h"
#include "BaseTestMethod.h"

namespace LiveSupport {
namespace Scheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::PlaylistExecutor;
using namespace LiveSupport::Scheduler;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::Storage;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the PlaylistEventContainer class
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see PlaylistEventContainer
 */
class PlaylistEventContainerTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(PlaylistEventContainerTest);
    CPPUNIT_TEST(simpleTest);
    CPPUNIT_TEST(scheduleTest);
    CPPUNIT_TEST_SUITE_END();

    private:
        /**
         *  The audio player used by the test.
         */
        Ptr<AudioPlayerInterface>::Ref      audioPlayer;

        /**
         *  The storage used by the container.
         */
        Ptr<StorageClientInterface>::Ref    storage;

        /**
         *  The schedule used by the container.
         */
        Ptr<ScheduleInterface>::Ref         schedule;

        /**
         *  An authentication client.
         */
        Ptr<AuthenticationClientInterface>::Ref authentication;

        /**
         *  A playlog interface.
         */
        Ptr<PlayLogInterface>::Ref              playLog;

        /**
         *  A session ID from the authentication client login() method.
         */
        Ptr<SessionId>::Ref                     sessionId;


    protected:

        /**
         *  Simple smoke test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simpleTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Test to see if we can get back a scheduled event.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        scheduleTest(void)                  throw (CPPUNIT_NS::Exception);


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

#endif // PlaylistEventContainerTest_h

