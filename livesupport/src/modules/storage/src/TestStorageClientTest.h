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
#ifndef TestStorageClientTest_h
#define TestStorageClientTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>


namespace LiveSupport {
namespace Storage {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the UploadPlaylistMetohd class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see TestStorageClient
 */
class TestStorageClientTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(TestStorageClientTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(getVersionTest);
    CPPUNIT_TEST(resetTest);
    CPPUNIT_TEST(createPlaylistTest);
    CPPUNIT_TEST(audioClipTest);
    CPPUNIT_TEST(acquireAudioClipTest);
    CPPUNIT_TEST(acquirePlaylistTest);
    CPPUNIT_TEST(searchTest);
    CPPUNIT_TEST(getAllTest);
    CPPUNIT_TEST_SUITE_END();

    private:
        /**
         *  The TestStorageClient instance to test.
         */
        Ptr<TestStorageClient>::Ref     tsc;

        /**
         *  A dummy session ID to pass to the storage client.
         */
        Ptr<SessionId>::Ref             dummySessionId;

    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /*
         *  Test the getVersion() function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getVersionTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Testing getAllPlaylists().
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        resetTest(void)
                                                throw (CPPUNIT_NS::Exception);
        /**
         *  Testing createPlaylist().
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        createPlaylistTest(void)                throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the audio clip operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        audioClipTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the acquire / release operations on audio clips.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        acquireAudioClipTest(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the acquire / release operations on playlists.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        acquirePlaylistTest(void)               throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the search operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        searchTest(void)                        throw (CPPUNIT_NS::Exception);

        /**
         *  Testing getAllPlaylists() and getAllAudioClips().
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getAllTest(void)                        throw (CPPUNIT_NS::Exception);


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


} // namespace Storage
} // namespace LiveSupport

#endif // TestStorageClientTest_h

