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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/HelixPlayerTest.h,v $

------------------------------------------------------------------------------*/
#ifndef HelixPlayerTest_h
#define HelixPlayerTest_h

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
 *  Unit test for the HelixPlayer class.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.7 $
 *  @see HelixPlayer
 */
class HelixPlayerTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(HelixPlayerTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(playlengthTest);
    CPPUNIT_TEST(simplePlayTest);
    CPPUNIT_TEST(checkErrorConditions);
    CPPUNIT_TEST(smilTest);
//    CPPUNIT_TEST(smilParallelTest0);
//    CPPUNIT_TEST(smilParallelTest1);
//    CPPUNIT_TEST(smilParallelTest2);
//    CPPUNIT_TEST(smilParallelTest3);
//    CPPUNIT_TEST(smilParallelTest4);
//    CPPUNIT_TEST(smilSoundAnimationTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  The helix player to use for the tests.
         */
        Ptr<HelixPlayer>::Ref       helixPlayer;

        /**
         *  Play a specific file.
         *
         *  @param fileName the name of the file to play.
         *  @exception CPPUNIT_NS::Exception on playing failures
         */
        void
        playFile(const std::string  & fileName)     
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
         *  Check the length of an audio file
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        playlengthTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Play something simple.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simplePlayTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Check for error conditions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        checkErrorConditions(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Test features of SMIL files.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilTest(void)                          throw (CPPUNIT_NS::Exception);

        /**
         *  Test SMIL files, when playing audio clips in parallel.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilParallelTest0(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Test SMIL files, when playing audio clips in parallel.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilParallelTest1(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Test SMIL files, when playing audio clips in parallel.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilParallelTest2(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Test SMIL files, when playing audio clips in parallel.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilParallelTest3(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Test SMIL files, when playing audio clips in parallel.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilParallelTest4(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Test SMIL files, when animating the sound of a played clip.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilSoundAnimationTest(void)            throw (CPPUNIT_NS::Exception);

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


} // namespace PlaylistExecutor
} // namespace LiveSupport

#endif // HelixPlayerTest_h

