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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/SeekPackTest.h,v $

------------------------------------------------------------------------------*/
#ifndef SeekPackTest_h
#define SeekPackTest_h

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
 *  Unit test for the SeekPack structure.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class SeekPackTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(SeekPackTest);
    CPPUNIT_TEST(mp3Test);
    CPPUNIT_TEST(mp3NoSilenceTest);
    CPPUNIT_TEST(mp3OpenEndedTest);
    CPPUNIT_TEST(oggVorbisTest);
    CPPUNIT_TEST(oggVorbisNoSilenceTest);
    CPPUNIT_TEST(oggVorbisOpenEndedTest);
    CPPUNIT_TEST(smilTest);
    CPPUNIT_TEST(smilNoSilenceTest);
    CPPUNIT_TEST(smilOpenEndedTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Play a file, with a specific partial play config.
         *
         *  @param audioFile the file to play
         *  @param silenceDuration the amount of silence before playing
         *  @param playFrom play the audio file from this offset
         *  @param playTo play the audio file until this offset,
         *         or -1LL if until the end.
         *  @return the number of milliseconds played.
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        gint64
        playFile(const char   * audioFile,
                 gint64         silenceDuration,
                 gint64         playFrom,
                 gint64         playTo)
                                                throw (CPPUNIT_NS::Exception);


    protected:

        /**
         *  A simple mp3 smoke test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3Test(void)                           throw (CPPUNIT_NS::Exception);

        /**
         *  An mp3 test with no silence.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3NoSilenceTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  An open ended mp3 play test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3OpenEndedTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Try SeekPack on an ogg vorbis file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Try SeekPack on an ogg vorbis file, without playing silence.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisNoSilenceTest(void)            throw (CPPUNIT_NS::Exception);

        /**
         *  Try SeekPack on an ogg vorbis file, playing until EOS.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisOpenEndedTest(void)            throw (CPPUNIT_NS::Exception);

        /**
         *  Try SeekPack on a SMIL file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilTest(void)                          throw (CPPUNIT_NS::Exception);

        /**
         *  Try SeekPack on a SMIL file, with no silence in the front.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilNoSilenceTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Try SeekPack on a SMIL file, playing until EOS.
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

#endif // SeekPackTest_h

