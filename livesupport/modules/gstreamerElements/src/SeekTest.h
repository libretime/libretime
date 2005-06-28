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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/SeekTest.h,v $

------------------------------------------------------------------------------*/
#ifndef SeekTest_h
#define SeekTest_h

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
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class SeekTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(SeekTest);
//    CPPUNIT_TEST(mp3Test);
//    CPPUNIT_TEST(mp3OpenEndedTest);
//    CPPUNIT_TEST(oggVorbisTest);
//    CPPUNIT_TEST(oggVorbisOpenEndedTest);
    CPPUNIT_TEST(smilTest);
    CPPUNIT_TEST(smilOpenEndedTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Play a specific file, from and until a specific timepoint.
         *
         *  @param audioFile the audio file to play.
         *  @param seekTo before playing, seek to this position.
         *  @param playTo play until this position, or -1LL if play until
         *         the end.
         *  @return the number of milliseconds played.
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        gint64
        playFile(const char   * audioFile,
                 gint64         seekTo,
                 gint64         playTo)
                                                throw (CPPUNIT_NS::Exception);


    protected:

        /**
         *  A simple mp3 test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3Test(void)                           throw (CPPUNIT_NS::Exception);

        /**
         *  A test where an mp3 file is played until its end.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3OpenEndedTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  A simple ogg vorbis test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  A test where an ogg vorbis file is played until its end.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisOpenEndedTest(void)            throw (CPPUNIT_NS::Exception);

        /**
         *  A simple SMIL test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilTest(void)                          throw (CPPUNIT_NS::Exception);

        /**
         *  A test where an SMIL file is played until its end.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilOpenEndedTest(void)                 throw (CPPUNIT_NS::Exception);


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

#endif // SeekTest_h

