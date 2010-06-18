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

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif


#include <iostream>
#include <string>
#include <XmlRpcClient.h>
#include <XmlRpcValue.h>

#include "SchedulerDaemon.h"
#include "GetSchedulerTimeMethodTest.h"

using namespace std;
using namespace XmlRpc;
using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(GetSchedulerTimeMethodTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
GetSchedulerTimeMethodTest :: setUp(void)       throw (CPPUNIT_NS::Exception)
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
GetSchedulerTimeMethodTest :: tearDown(void)    throw (CPPUNIT_NS::Exception)
{
}


/*------------------------------------------------------------------------------
 *  Test a simple query, resulting in an empty result set.
 *----------------------------------------------------------------------------*/
void
GetSchedulerTimeMethodTest :: simpleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<GetSchedulerTimeMethod>::Ref 
                        getSchedulerTimeMethod(new GetSchedulerTimeMethod());

//    XmlRpcValue         parameters;
    XmlRpc::XmlRpcValue rootParameter;
//    rootParameter[0] = parameters;
    XmlRpcValue         result;
    struct tm           time1,
                        time2;
    result.clear();
    try {
        getSchedulerTimeMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("schedulerTime"));
    time1 = result["schedulerTime"];

    result.clear();
    try {
        getSchedulerTimeMethod->execute(rootParameter, result);
    } catch (XmlRpc::XmlRpcException &e) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method returned error: " << e.getCode()
             << " - " << e.getMessage();
        CPPUNIT_FAIL(eMsg.str());
    }
    CPPUNIT_ASSERT(result.hasMember("schedulerTime"));
    time2 = result["schedulerTime"];

    CPPUNIT_ASSERT(time1.tm_year == time2.tm_year);
    // could fail on New Year's Eve, but we don't work on New Year's Eve
    
    CPPUNIT_ASSERT(time1.tm_hour <= time2.tm_hour);
    CPPUNIT_ASSERT(time1.tm_min <= time2.tm_min);
    CPPUNIT_ASSERT(time1.tm_min + 1 >= time2.tm_min);
}

