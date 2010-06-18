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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <string>
#include <iostream>

#include "LiveSupport/Core/AsyncState.h"
#include "AsyncStateTest.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(AsyncStateTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
AsyncStateTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
AsyncStateTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test the basic conversions.
 *----------------------------------------------------------------------------*/
void
AsyncStateTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    AsyncState              state = AsyncState::invalidState;
    Ptr<std::string>::Ref   transportString;
    Ptr<std::string>::Ref   backupString;
    
    state = AsyncState::initState;
    transportString = state.toTransportString();
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromTransportString(*transportString),
                         state);

    state = AsyncState::pendingState;
    transportString = state.toTransportString();
    backupString    = state.toBackupString();
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromTransportString(*transportString),
                         state);
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromBackupString(*backupString),
                         state);

    state = AsyncState::finishedState;
    transportString = state.toTransportString();
    backupString    = state.toBackupString();
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromTransportString(*transportString),
                         state);
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromBackupString(*backupString),
                         state);

    state = AsyncState::closedState;
    transportString = state.toTransportString();
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromTransportString(*transportString),
                         state);

    state = AsyncState::failedState;
    transportString = state.toTransportString();
    backupString    = state.toBackupString();
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromTransportString(*transportString),
                         state);
    CPPUNIT_ASSERT_EQUAL(AsyncState::fromBackupString(*backupString),
                         state);

}


/*------------------------------------------------------------------------------
 *  Test the printing to an ostream.
 *----------------------------------------------------------------------------*/
void
AsyncStateTest :: ostreamTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    std::ostringstream      stream;
    AsyncState              state = AsyncState::finishedState;
    
    stream << state;
    CPPUNIT_ASSERT(stream.str() == "finished");
}

