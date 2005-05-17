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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/TwoHelixPlayersTest.h,v $

------------------------------------------------------------------------------*/
#ifndef TwoHelixPlayersTest_h
#define TwoHelixPlayersTest_h

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
 *  Unit test for the HelixPlayer class, two see if two helix players,
 *  playing on two different sound cards work correctly.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 *  @see HelixPlayer
 */
class TwoHelixPlayersTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(TwoHelixPlayersTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(simplePlay1Test);
    CPPUNIT_TEST(simplePlay2Test);
    CPPUNIT_TEST(playBothTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Helix player #1 to use for the tests.
         */
        Ptr<HelixPlayer>::Ref       helixPlayer1;

        /**
         *  Helix player #2 to use for the tests.
         */
        Ptr<HelixPlayer>::Ref       helixPlayer2;

        /**
         *  Play a specific file.
         *
         *  @param fileName the name of the file to play.
         *  @param player the player to use for playing the file.
         *  @exception CPPUNIT_NS::Exception on playing failures
         */
        void
        playFile(const std::string    & fileName,
                 Ptr<HelixPlayer>::Ref  player)     
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
         *  Play something on player #1.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simplePlay1Test(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Play something on player #2.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simplePlay2Test(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Play something on both players, at the same time.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        playBothTest(void)                      throw (CPPUNIT_NS::Exception);


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

#endif // TwoHelixPlayersTest_h

