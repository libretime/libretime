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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/UploadPlaylistMethodTest.h,v $

------------------------------------------------------------------------------*/
#ifndef UploadPlaylistMethodTest_h
#define UploadPlaylistMethodTest_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Unit test for the UploadPlaylistMetohd class.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 *  @see UploadPlaylistMethod
 */
class UploadPlaylistMethodTest : public CPPUNIT_NS::TestFixture
{
    CPPUNIT_TEST_SUITE(UploadPlaylistMethodTest);
    CPPUNIT_TEST(firstTest);
    CPPUNIT_TEST(overlappingPlaylists);
    CPPUNIT_TEST_SUITE_END();

        /**
         *  The name of the configuration file for the storage client factory.
         */
        static const std::string storageClientConfig;

        /**
         *  The name of the configuration file for the connection manager
         *  factory.
         */
        static const std::string connectionManagerConfig;

        /**
         *  The name of the configuration file for the schedule factory.
         */
        static const std::string scheduleConfig;

        /**
         *  The schedule used during the test.
         */
        Ptr<ScheduleInterface>::Ref     schedule;

        /**
         *  Configure a configurable with an XML file.
         *
         *  @param configurable configure this
         *  @param fileName the name of the XML file to configure with.
         *  @exception std::invalid_argument on configuration errors.
         *  @exception xmlpp::exception on XML parsing errors.
         */
        void
        configure(Ptr<Configurable>::Ref    configurable,
                  std::string               fileName)
                                                throw (std::invalid_argument,
                                                       xmlpp::exception);


    protected:

        /**
         *  A simple test.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        firstTest(void)                         throw (CPPUNIT_NS::Exception);

        /**
         *  Try to upload overlapping playlists, and see them fail.
         *
         *  @exception CPPUNIT_NS::Exception on test failures.
         */
        void
        overlappingPlaylists(void)              throw (CPPUNIT_NS::Exception);

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


} // namespace Scheduler
} // namespace LiveSupport

#endif // UploadPlaylistMethodTest_h

