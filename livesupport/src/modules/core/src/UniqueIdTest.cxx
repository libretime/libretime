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

#include "LiveSupport/Core/UniqueId.h"
#include "UniqueIdTest.h"


using namespace std;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(UniqueIdTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
UniqueIdTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
UniqueIdTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
UniqueIdTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    UniqueId::IdType    idNumeric = 51966;
    std::string         idAsString = "000000000000cafe";
    Ptr<UniqueId>::Ref  id;

    id.reset(new UniqueId(idNumeric));
    CPPUNIT_ASSERT(id->getId() == idNumeric);
    CPPUNIT_ASSERT(UniqueId::IdType(*id)    == idNumeric);
    CPPUNIT_ASSERT(std::string(*id)         == idAsString);

    id.reset(new UniqueId(idAsString));
    CPPUNIT_ASSERT(id->getId() == idNumeric);
    CPPUNIT_ASSERT(UniqueId::IdType(*id)    == idNumeric);
    CPPUNIT_ASSERT(std::string(*id)         == idAsString);
    
    id = UniqueId::generateId();
    idNumeric       = UniqueId::IdType(*id);
    idAsString      = std::string(*id);
    std::stringstream   idReader(idAsString);
    UniqueId::IdType    idNumericCheck;
    idReader >> std::hex >> idNumericCheck;
    CPPUNIT_ASSERT(idNumeric == idNumericCheck);  
    
    // OK if initialized with bad strings, but the integral value is bogus
    std::string     idAsVeryLongString = "123456789abcdef0123456789abcdef0";
    id.reset(new UniqueId(idAsVeryLongString));
    CPPUNIT_ASSERT(std::string(*id)         == idAsVeryLongString);

    std::string     idAsSillyString = "this is not a number";
    id.reset(new UniqueId(idAsSillyString));
    CPPUNIT_ASSERT(std::string(*id)         == idAsSillyString);

/*  // this works fine, but please don't use
    UniqueId::IdType    idSillyNumeric = -3;
    id.reset(new UniqueId(idSillyNumeric));
    CPPUNIT_ASSERT(UniqueId::IdType(*id)    == idSillyNumeric);
    CPPUNIT_ASSERT(std::string(*id)         == "fffffffffffffffd"); */

    // this is used in Postgresql classes, because Long does not get properly
    // typedef'd to long long -- can be removed after this bug is fixed
    std::string     idAsDecimalString = "65546";
    id = UniqueId::fromDecimalString(idAsDecimalString);
    CPPUNIT_ASSERT(id->getId()              == 65546);
    CPPUNIT_ASSERT(*id->toDecimalString()   == idAsDecimalString);
}

