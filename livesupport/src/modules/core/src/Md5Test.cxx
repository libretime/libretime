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
    Location : $URL$

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

#include "LiveSupport/Core/Md5.h"
#include "Md5Test.h"


using namespace std;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(Md5Test);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
Md5Test :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
Md5Test :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if we can construct some simple md5 sums
 *----------------------------------------------------------------------------*/
void
Md5Test :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // test the construction from string
    Md5     emptyString("");
    CPPUNIT_ASSERT(emptyString.hexDigest() 
                   == "d41d8cd98f00b204e9800998ecf8427e");

    Md5     someString("Some other random string.");
    CPPUNIT_ASSERT(someString.hexDigest() 
                   == "9007a3599f5d3ae2ac11a29308f964eb");

    std::string s = someString;
    CPPUNIT_ASSERT(s == "9007a3599f5d3ae2ac11a29308f964eb");
    CPPUNIT_ASSERT(someString.low64bits() == 0xac11a29308f964ebLL);
    CPPUNIT_ASSERT(someString.high64bits() == 0x9007a3599f5d3ae2LL);

    // test the construction from a FILE*
    FILE    *f = fopen("var/md5test.data", "r");
    Md5     testFile(f);
    CPPUNIT_ASSERT(testFile.hexDigest()
                   == "fc359d2b366cc110db86c3d68bdf39c4");
    fclose(f);

    // test the construction from an istream
    std::ifstream   ifs("var/md5test.data");
    Md5             testFileStream(ifs);
    CPPUNIT_ASSERT(testFileStream.hexDigest()
                   == "fc359d2b366cc110db86c3d68bdf39c4");
    ifs.close();
}

