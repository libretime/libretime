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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/AudioPlayerTest.h,v $

------------------------------------------------------------------------------*/
#ifndef AudioPlayerTest_h
#define AudioPlayerTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/SessionId.h"


namespace LiveSupport {
namespace gLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Testing the AudioPlayerInterface::openAndStart() method.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 *  @see AudioPlayerFactory
 */
class AudioPlayerTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(AudioPlayerTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(playAudioClipTest);
    CPPUNIT_TEST(playPlaylistTest);
    CPPUNIT_TEST_SUITE_END();

    private:
    
        /**
         *  The session ID returned by the authentication client login.
         */
        Ptr<SessionId>::Ref     sessionId;

    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Play an audio clip from storage.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        playAudioClipTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Play a playlist from storage.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        playPlaylistTest(void)                  throw (CPPUNIT_NS::Exception);

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


} // namespace gLiveSupport
} // namespace LiveSupport

#endif // AudioPlayerTest_h

