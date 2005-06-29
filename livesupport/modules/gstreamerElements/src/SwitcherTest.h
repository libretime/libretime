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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/SwitcherTest.h,v $

------------------------------------------------------------------------------*/
#ifndef SwitcherTest_h
#define SwitcherTest_h

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
 *  @version $Revision: 1.5 $
 */
class SwitcherTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(SwitcherTest);
    CPPUNIT_TEST(mp3Test);
    CPPUNIT_TEST(mp3OpenEndedTest);
    CPPUNIT_TEST(mp3MultipleTest);
    CPPUNIT_TEST(mp3MultipleOpenEndedTest);
    CPPUNIT_TEST(oggVorbisTest);
    CPPUNIT_TEST(oggVorbisOpenEndedTest);
    CPPUNIT_TEST(oggVorbisMultipleTest);
    CPPUNIT_TEST(oggVorbisMultipleOpenEndedTest);
    CPPUNIT_TEST(smilTest);
    CPPUNIT_TEST(smilOpenEndedTest);
    CPPUNIT_TEST(smilMultipleTest);
    CPPUNIT_TEST(smilMultipleOpenEndedTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Play audio files, with a specific switcher configuration.
         *
         *  @param audioFiles an array of file names to play
         *  @param noFiles the size of the audioFiles array.
         *  @param sourceConfig the source config to use.
         *  @return the number of milliseconds played.
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        gint64
        playFiles(const char     ** audioFiles,
                  unsigned int      noFiles,
                  const char      * sourceConfig)
                                                throw (CPPUNIT_NS::Exception);


    protected:

        /**
         *  A simple smoke test with an mp3 file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3Test(void)                           throw (CPPUNIT_NS::Exception);

        /**
         *  A test to play an mp3 file until its end.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3OpenEndedTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Test the switcher with multiple mp3 inputs.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3MultipleTest(void)                   throw (CPPUNIT_NS::Exception);

        /**
         *  Test the switcher with multiple mp3 inputs,
         *  including open-ended ones.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3MultipleOpenEndedTest(void)          throw (CPPUNIT_NS::Exception);

        /**
         *  A simple smoke test with an ogg vorbis file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  A test to play an ogg vorbis file until its end.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisOpenEndedTest(void)            throw (CPPUNIT_NS::Exception);

        /**
         *  Test the switcher with multiple ogg vorbis inputs.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisMultipleTest(void)             throw (CPPUNIT_NS::Exception);

        /**
         *  Test the switcher with multiple ogg vorbis inputs,
         *  including open-ended ones.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisMultipleOpenEndedTest(void)    throw (CPPUNIT_NS::Exception);

        /**
         *  A simple smoke test with a SMIL file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilTest(void)                          throw (CPPUNIT_NS::Exception);

        /**
         *  A test to play a SMIL file until its end.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilOpenEndedTest(void)                 throw (CPPUNIT_NS::Exception);

        /**
         *  Test the switcher with multiple SMIL inputs.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilMultipleTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  Test the switcher with multiple SMIL inputs,
         *  including open-ended ones.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilMultipleOpenEndedTest(void)         throw (CPPUNIT_NS::Exception);


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

#endif // SwitcherTest_h

