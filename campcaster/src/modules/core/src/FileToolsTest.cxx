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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1871 $
    Location : $URL: svn+ssh://maroy@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/core/src/FileToolsTest.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif


#include <string>
#include <iostream>

#include "LiveSupport/Core/FileTools.h"
#include "LiveSupport/Core/Playlist.h"
#include "FileToolsTest.h"


using namespace std;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(FileToolsTest);

namespace {

/**
 *  The name of the test tar file
 */
const std::string tarFileName = "var/hello.tar";

/**
 *  The name of the test file in the tar file
 */
const std::string fileInTarName = "hello";

/**
 *  The name of the test file after extraction
 */
const std::string fileExtracted = "tmp/hello.txt";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
FileToolsTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
FileToolsTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the sample hello tarfile is accessible
 *----------------------------------------------------------------------------*/
void
FileToolsTest :: existsInTarTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT(FileTools::existsInTarball(tarFileName, fileInTarName));
    CPPUNIT_ASSERT(!FileTools::existsInTarball(tarFileName, "foobar"));
}


/*------------------------------------------------------------------------------
 *  Test to see if the sample hello tarfile is accessible
 *----------------------------------------------------------------------------*/
void
FileToolsTest :: extractFileFromTarballTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    FILE *      file;
    
    remove(fileExtracted.c_str());
    file = fopen(fileExtracted.c_str(), "r");
    CPPUNIT_ASSERT(file == 0);

    CPPUNIT_ASSERT_NO_THROW(
        FileTools::extractFileFromTarball(tarFileName,
                                          fileInTarName,
                                          fileExtracted)
    );
    
    file = fopen(fileExtracted.c_str(), "r");
    CPPUNIT_ASSERT(file != 0);
    CPPUNIT_ASSERT(fclose(file) == 0);
    
    CPPUNIT_ASSERT(remove(fileExtracted.c_str()) == 0);
    file = fopen(fileExtracted.c_str(), "r");
    CPPUNIT_ASSERT(file == 0);
    
    CPPUNIT_ASSERT_THROW(
        FileTools::extractFileFromTarball(tarFileName,
                                          "foobar",
                                          fileExtracted),
        std::runtime_error
    );
}

