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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RescheduleMethodTest.cxx,v $

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
#include <XmlRpcValue.h>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "ScheduleFactory.h"
#include "UploadPlaylistMethod.h"
#include "RescheduleMethod.h"
#include "RescheduleMethodTest.h"


using namespace LiveSupport::Db;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RescheduleMethodTest);

/**
 *  The name of the configuration file for the schedule factory.
 */
const std::string RescheduleMethodTest::scheduleConfig =
                                            "etc/scheduleFactory.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
RescheduleMethodTest :: configure(
            Ptr<Configurable>::Ref      configurable,
            const std::string           fileName)
                                                throw (std::invalid_argument,
                                                       xmlpp::exception)
{
    Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser(fileName, true));
    const xmlpp::Document * document = parser->get_document();
    const xmlpp::Element  * root     = document->get_root_node();

    configurable->configure(*root);
}

                                                        
/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
RescheduleMethodTest :: setUp(void)                         throw ()
{
    try {
        Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
        configure(sf, scheduleConfig);

        schedule = sf->getSchedule();
        schedule->install();
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
RescheduleMethodTest :: tearDown(void)                      throw ()
{
    schedule->uninstall();
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
RescheduleMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  uploadMethod(new UploadPlaylistMethod());
    Ptr<RescheduleMethod>::Ref      rescheduleMethod(new RescheduleMethod());
    XmlRpc::XmlRpcValue             rootParameter;
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;
    Ptr<UniqueId>::Ref              entryId;

    // let's upload something so we can reschedule it
    parameters["playlistId"] = 1;
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 18;
    time.tm_min  = 31;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    uploadMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(result.valid());
    entryId.reset(new UniqueId((int) result));

    // now let's reschedule it
    parameters.clear();
    result.clear();
    parameters["scheduleEntryId"] = (int) entryId->getId();
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 12;
    time.tm_min  = 31;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    rescheduleMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(result.valid());
    CPPUNIT_ASSERT((bool) result);

    // now let's reschedule unto itself, should fail
    parameters.clear();
    result.clear();
    parameters["scheduleEntryId"] = (int) entryId->getId();
    time.tm_year = 2001;
    time.tm_mon  = 11;
    time.tm_mday = 12;
    time.tm_hour = 12;
    time.tm_min  = 51;
    time.tm_sec  = 1;
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    rescheduleMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(result.valid());
    CPPUNIT_ASSERT(!((bool) result));
}

