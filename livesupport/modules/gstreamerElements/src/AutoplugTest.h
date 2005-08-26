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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/AutoplugTest.h,v $

------------------------------------------------------------------------------*/
#ifndef AutoplugTest_h
#define AutoplugTest_h

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
class AutoplugTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(AutoplugTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(mp3_48kHzTest);
    CPPUNIT_TEST(oggVorbisTest);
    CPPUNIT_TEST(oggVorbis160kbpsTest);
    CPPUNIT_TEST(smilTest);
    CPPUNIT_TEST(negativeTest);
    CPPUNIT_TEST(shortTest);
    CPPUNIT_TEST(shortSmilTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Play a specific file, from and until a specific timepoint.
         *
         *  @param audioFile the audio file to play.
         *  @return the number of milliseconds played.
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        gint64
        playFile(const char   * audioFile)
                                                throw (CPPUNIT_NS::Exception);


    protected:

        /**
         *  A simple smoke test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Test a 48kHz mp3 file
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3_48kHzTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Test an Ogg Vorbis file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  Test a 160 kb/s Ogg Vorbis file.
         *  See http://bugs.campware.org/view.php?id=1421 for details.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbis160kbpsTest(void)              throw (CPPUNIT_NS::Exception);

        /**
         *  Test a SMIL file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilTest(void)                          throw (CPPUNIT_NS::Exception);

        /**
         *  A negative test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        negativeTest(void)                      throw (CPPUNIT_NS::Exception);

        /**
         *  A test on a very short file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        shortTest(void)                        throw (CPPUNIT_NS::Exception);

        /**
         *  A test on a SMIL file referring to a very short file.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        shortSmilTest(void)                    throw (CPPUNIT_NS::Exception);


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

#endif // AutoplugTest_h

