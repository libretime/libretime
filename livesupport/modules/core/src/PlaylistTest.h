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
    Version  : $Revision: 1.13 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/PlaylistTest.h,v $

------------------------------------------------------------------------------*/
#ifndef PlaylistTest_h
#define PlaylistTest_h

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
 *  Unit test for the UploadPlaylistMetohd class.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.13 $
 *  @see Playlist
 */
class PlaylistTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(PlaylistTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(audioClipTest);
    CPPUNIT_TEST(savedCopyTest);
    CPPUNIT_TEST(fadeInfoTest);
    CPPUNIT_TEST(conversionTest);
    CPPUNIT_TEST(marshallingTest);
    CPPUNIT_TEST(addPlayableTest);
    CPPUNIT_TEST(eliminateGapsTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  A playlist to play with.
         */
        Ptr<Playlist>::Ref  playlist;

    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Trying to add a new audio clip.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        audioClipTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the "save/revert to current state" mechanism.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        savedCopyTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Trying to add a new fade info.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        fadeInfoTest(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Testing conversion to and from Playable.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        conversionTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Testing conversion to and from XmlRpcValue.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        marshallingTest(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the addPlayable() method.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        addPlayableTest(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the eliminateGaps() method.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        eliminateGapsTest(void)                 throw (CPPUNIT_NS::Exception);


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

#endif // PlaylistTest_h

