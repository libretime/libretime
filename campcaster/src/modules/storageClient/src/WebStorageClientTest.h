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
#ifndef WebStorageClientTest_h
#define WebStorageClientTest_h

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
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"

namespace LiveSupport {
namespace StorageClient {

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the WebStorageClient class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see WebStorageClient
 */
class WebStorageClientTest : public BaseTestMethod
{
    CPPUNIT_TEST_SUITE(WebStorageClientTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(getVersionTest);
    CPPUNIT_TEST(simplePlaylistTest);
    CPPUNIT_TEST(playlistTest);
    CPPUNIT_TEST(embeddedPlaylistTest);
    CPPUNIT_TEST(audioClipTest);
    CPPUNIT_TEST(searchTest);
    CPPUNIT_TEST(searchUnicodeTest);
    CPPUNIT_TEST(getAllTest);
    CPPUNIT_TEST(browseTest);
    CPPUNIT_TEST(createBackupTest);
    CPPUNIT_TEST(restoreBackupTest);
    CPPUNIT_TEST(exportPlaylistTest);
    CPPUNIT_TEST(importPlaylistTest);
    CPPUNIT_TEST(remoteSearchTest);
    CPPUNIT_TEST_SUITE_END();

    private:
        /**
         *  An AuthenticationClient instance to login an logout.
         */
        Ptr<AuthenticationClientInterface>::Ref     authentication;

        /**
         *  The WebStorageClient instance to test.
         */
        Ptr<WebStorageClient>::Ref                  wsc;

        /**
         *  Auxiliary function for exportPlaylistTest().
         */
        void
        exportPlaylistHelper(
                        Ptr<UniqueId>::Ref                          playlistId,
                        StorageClientInterface::ExportFormatType    format)
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
         *  Test the getVersion() function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getVersionTest(void)                    throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the audio clip operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        audioClipTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the playlist operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        simplePlaylistTest(void)                throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the playlist operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        playlistTest(void)                      throw (CPPUNIT_NS::Exception);

        /**
         *  Testing an embedded playlist
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        embeddedPlaylistTest(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the search operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        searchTest(void)                        throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the search operations.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        searchUnicodeTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Testing getAllPlaylists() and getAllAudioClips().
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        getAllTest(void)                        throw (CPPUNIT_NS::Exception);

        /**
         *  Testing browse().
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        browseTest(void)                        throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the createBackupXxxx() functions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        createBackupTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the restoreBackupXxxx() functions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        restoreBackupTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the exportPlaylistXxxx() functions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        exportPlaylistTest(void)                throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the importPlaylist() function.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        importPlaylistTest(void)                throw (CPPUNIT_NS::Exception);

        /**
         *  Testing the remoteSearchXxxx() functions.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        remoteSearchTest(void)                  throw (CPPUNIT_NS::Exception);


    public:
        
        /**
         *  Set up the environment for the test case.
         */
        void
        setUp(void)                             throw (CPPUNIT_NS::Exception);

        /**
         *  Clean up the environment after the test case.
         */
        void
        tearDown(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace StorageClient
} // namespace LiveSupport

#endif // WebStorageClientTest_h

