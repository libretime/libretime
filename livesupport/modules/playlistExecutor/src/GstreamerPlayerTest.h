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
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef GstreamerPlayerTest_h
#define GstreamerPlayerTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>


namespace LiveSupport {
namespace PlaylistExecutor {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the GstreamerPlayer class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see GstreamerPlayer
 */
class GstreamerPlayerTest : public CPPUNIT_NS::TestFixture,
                            public AudioPlayerEventListener
{
    CPPUNIT_TEST_SUITE(GstreamerPlayerTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(simplePlayTest);
    CPPUNIT_TEST(getPositionTest);
    CPPUNIT_TEST(setDeviceTest);
    CPPUNIT_TEST(simpleSmilTest);
    CPPUNIT_TEST(secondSmilTest);
    CPPUNIT_TEST(animatedSmilTest);
    CPPUNIT_TEST(checkErrorConditions);
    CPPUNIT_TEST(eventListenerAttachTest);
    CPPUNIT_TEST(eventListenerTest);
    CPPUNIT_TEST(eventListenerOnStopTest);
    CPPUNIT_TEST(openTimeTest);
    CPPUNIT_TEST(pauseResumeTest);
    CPPUNIT_TEST(openSoundcardTwiceTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  The player to use for the tests.
         */
        Ptr<GstreamerPlayer>::Ref       player;

        /**
         *  Time how long it takes to open, play, stop and close files.
         *
         *  @param fileName the name of the file to take a look at.
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        timeSteps(const std::string     fileName)
                                                throw (CPPUNIT_NS::Exception);

        /**
         *  A flag set to true if onStop() should start playing a new clip.
         */
        bool                            startNewClipFlag;


    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  A simple play test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simplePlayTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Test the getPosition() function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getPositionTest(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Test the setDevice() function, with ALSA and OSS devices.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        setDeviceTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Play a simple SMIL file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simpleSmilTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Play a more complicated SMIL file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        secondSmilTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Play a SMIL file with sound animation.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        animatedSmilTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Check for error conditions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        checkErrorConditions(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Test to see if attaching and detaching event listeners works.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        eventListenerAttachTest(void)           throw (CPPUNIT_NS::Exception);

        /**
         *  Test to see if the player event listener mechanism works.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        eventListenerTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Another, more realistic test of the event listener mechanism.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        eventListenerOnStopTest(void)           throw (CPPUNIT_NS::Exception);

        /**
         *  Test how long it takes to open and play files.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        openTimeTest(void)                      throw (CPPUNIT_NS::Exception);

        /**
         *  Test pausing and resuming.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        pauseResumeTest(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Test for opening the same sound card twice
         *  (thus forcing an error)
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        openSoundcardTwiceTest(void)            throw (CPPUNIT_NS::Exception);


    public:
        
        /**
         *  A virtual destructor, necessary because of onStop().
         */
        virtual
        ~GstreamerPlayerTest(void)                      throw ()
        {
        }

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

        /**
         *  Event handler for the "player has stopped" event.
         */
        virtual void
        onStop(void)                                    throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport

#endif // GstreamerPlayerTest_h

