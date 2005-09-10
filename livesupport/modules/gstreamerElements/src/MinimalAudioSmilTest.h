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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/MinimalAudioSmilTest.h,v $

------------------------------------------------------------------------------*/
#ifndef MinimalAudioSmilTest_h
#define MinimalAudioSmilTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>


namespace LiveSupport {
namespace GstreamerElements {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the partialplay gstreamer element.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class MinimalAudioSmilTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(MinimalAudioSmilTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(simpleClipBeginTest);
    CPPUNIT_TEST(simpleClipBeginEndTest);
    CPPUNIT_TEST(parallelTest);
    CPPUNIT_TEST(parallelClipBeginEndTest);
    CPPUNIT_TEST(oggVorbisTest);
//    disabled because this test hangs on some systems
//    CPPUNIT_TEST(embeddedTest);
    CPPUNIT_TEST(soundAnimationTest);
    CPPUNIT_TEST(soundAnimationParallelTest);
    CPPUNIT_TEST(fadeInOutTest);
    CPPUNIT_TEST(fadeInOutParallelTest);
    CPPUNIT_TEST(sequentialSmilTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Play a smil file.
         *
         *  @param smilFile the name of the smil file to play.
         *  @return the number of milliseconds played.
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        gint64
        playSmilFile(const char   * smilFile)
                                                throw (CPPUNIT_NS::Exception);


    protected:

        /**
         *  A simple smoke test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  A simple test with clipBegin attribute.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simpleClipBeginTest(void)               throw (CPPUNIT_NS::Exception);

        /**
         *  A simple test with clipBegin and clipEnd attributes.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simpleClipBeginEndTest(void)            throw (CPPUNIT_NS::Exception);

        /**
         *  Test on <par> elements in a SMIL file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        parallelTest(void)                      throw (CPPUNIT_NS::Exception);

        /**
         *  Test on <par> elements in a SMIL file, with clipBegin and
         *  clipEnd attributes.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        parallelClipBeginEndTest(void)          throw (CPPUNIT_NS::Exception);

        /**
         *  A simple test with a SMIL file referring to an Ogg Vorbis file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  A test looking at an embedded SMIL file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        embeddedTest(void)                      throw (CPPUNIT_NS::Exception);

        /**
         *  Test sound level animation.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        soundAnimationTest(void)                throw (CPPUNIT_NS::Exception);

        /**
         *  Test sound level animation with two parallel elements.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        soundAnimationParallelTest(void)        throw (CPPUNIT_NS::Exception);

        /**
         *  Test fade in / fade out using sound level animation.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        fadeInOutTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Test fade in / fade out using sound level animation,
         *  with two parallel audio files.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        fadeInOutParallelTest(void)             throw (CPPUNIT_NS::Exception);

        /**
         *  A sequential par element test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        sequentialSmilTest(void)                throw (CPPUNIT_NS::Exception);


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


} // namespace GstreamerElements
} // namespace LiveSupport

#endif // MinimalAudioSmilTest_h

