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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/GLiveSupportTest.h,v $

------------------------------------------------------------------------------*/
#ifndef GLiveSupportTest_h
#define GLiveSupportTest_h

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
#include "LiveSupport/Core/BaseTestMethod.h"

#include "GLiveSupport.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Testing the GLiveSupport class
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 *  @see GLiveSupport
 */
class GLiveSupportTest : public BaseTestMethod
{
    CPPUNIT_TEST_SUITE(GLiveSupportTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(openAudioClipTest);
    CPPUNIT_TEST(acquireAudioClipTest);
    CPPUNIT_TEST(openPlaylistTest);
    CPPUNIT_TEST(acquirePlaylistTest);
    CPPUNIT_TEST_SUITE_END();

    private:
    
        /**
         *  The GLiveSupport object we're testing
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;


    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Open an audio clip.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        openAudioClipTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Acquire an audio clip.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        acquireAudioClipTest(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Open a playlist.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        openPlaylistTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Acquire a playlist.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        acquirePlaylistTest(void)               throw (CPPUNIT_NS::Exception);


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


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // GLiveSupportTest_h

