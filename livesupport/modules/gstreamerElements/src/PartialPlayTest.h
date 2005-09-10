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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/PartialPlayTest.h,v $

------------------------------------------------------------------------------*/
#ifndef PartialPlayTest_h
#define PartialPlayTest_h

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
 *  @author  $Author$
 *  @version $Revision$
 */
class PartialPlayTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(PartialPlayTest);
    CPPUNIT_TEST(mp3Test);
    CPPUNIT_TEST(mp3OpenEndedTest);
    CPPUNIT_TEST(oggVorbisTest);
    CPPUNIT_TEST(oggVorbisOpenEndedTest);
    CPPUNIT_TEST(smilTest);
    CPPUNIT_TEST(smilOpenEndedTest);
    CPPUNIT_TEST_SUITE_END();

    private:

        /**
         *  Play a file, with a specific partial play config.
         *
         *  @param audioFile the file to play
         *  @param config the partial play config to use when playing the file
         *  @return the number of milliseconds played.
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        gint64
        playFile(const char   * audioFile,
                 const char   * config)
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
         *  An open ended mp3 play test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        mp3OpenEndedTest(void)                  throw (CPPUNIT_NS::Exception);

        /**
         *  A simple ogg vorbis smoke test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisTest(void)                     throw (CPPUNIT_NS::Exception);

        /**
         *  An open ended ogg vorbis play test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        oggVorbisOpenEndedTest(void)            throw (CPPUNIT_NS::Exception);

        /**
         *  A simple SMIL smoke test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        smilTest(void)                          throw (CPPUNIT_NS::Exception);

        /**
         *  An open ended SMIL play test.
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

#endif // PartialPlayTest_h

