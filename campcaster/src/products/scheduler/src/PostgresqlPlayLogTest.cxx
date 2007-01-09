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


#include <string>
#include <iostream>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "SchedulerDaemon.h"
#include "PostgresqlPlayLog.h"
#include "PostgresqlPlayLogTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PostgresqlPlayLogTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
PostgresqlPlayLogTest :: setUp(void)            throw (CPPUNIT_NS::Exception)
{
    Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();
    try {
        cm = scheduler->getConnectionManager();

        playLog.reset(new PostgresqlPlayLog(cm));
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
PostgresqlPlayLogTest :: tearDown(void)         throw (CPPUNIT_NS::Exception)
{
    playLog.reset();
    cm.reset();
}


/*------------------------------------------------------------------------------
 *  Add a single item to the play log.
 *----------------------------------------------------------------------------*/
void
PostgresqlPlayLogTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      audioClipId = UniqueId::generateId();
    Ptr<ptime>::Ref         timestamp(new ptime(time_from_string(
                                                    "2004-10-25 16:09:00")));

    try {
        playLog->addPlayLogEntry(audioClipId, timestamp);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  See if getPlayLogEntries() returns correct lists
 *----------------------------------------------------------------------------*/
void
PostgresqlPlayLogTest :: getPlayLogEntriesTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      audioClipId;
    Ptr<ptime>::Ref         timestamp;
    Ptr<ptime>::Ref         fromTime;
    Ptr<ptime>::Ref         toTime;

    Ptr<std::vector<Ptr<PlayLogEntry>::Ref> >::Ref  entries;
    Ptr<PlayLogEntry>::Ref                          entry;

    try {
        audioClipId.reset(new UniqueId(10001));
        timestamp.reset(new ptime(time_from_string("2004-10-25 10:00:00")));
        playLog->addPlayLogEntry(audioClipId, timestamp);

        audioClipId.reset(new UniqueId(10002));
        timestamp.reset(new ptime(time_from_string("2004-10-25 10:12:00")));
        playLog->addPlayLogEntry(audioClipId, timestamp);

        audioClipId.reset(new UniqueId(10003));
        timestamp.reset(new ptime(time_from_string("2004-10-25 12:00:00")));
        playLog->addPlayLogEntry(audioClipId, timestamp);

        // first interval
        fromTime.reset(new ptime(time_from_string("2004-10-25 10:00:00")));
        toTime.reset(  new ptime(time_from_string("2004-10-25 12:00:00")));
        entries = playLog->getPlayLogEntries(fromTime, toTime);

        CPPUNIT_ASSERT(entries->size() == 2);
        entry = (*entries)[0];
        CPPUNIT_ASSERT(entry->getAudioClipId()->getId() == 10001);
        timestamp.reset(new ptime(time_from_string("2004-10-25 10:00:00")));
        CPPUNIT_ASSERT(*(entry->getTimestamp()) == *timestamp);
        entry = (*entries)[1];
        CPPUNIT_ASSERT(entry->getAudioClipId()->getId() == 10002);
        timestamp.reset(new ptime(time_from_string("2004-10-25 10:12:00")));
        CPPUNIT_ASSERT(*(entry->getTimestamp()) == *timestamp);

        // second interval
        fromTime.reset(new ptime(time_from_string("2004-10-25 10:10:00")));
        toTime.reset(  new ptime(time_from_string("2005-10-25 00:00:00")));
        entries = playLog->getPlayLogEntries(fromTime, toTime);

        CPPUNIT_ASSERT(entries->size() == 2);
        entry = (*entries)[0];
        CPPUNIT_ASSERT(entry->getAudioClipId()->getId() == 10002);
        timestamp.reset(new ptime(time_from_string("2004-10-25 10:12:00")));
        CPPUNIT_ASSERT(*(entry->getTimestamp()) == *timestamp);
        entry = (*entries)[1];
        CPPUNIT_ASSERT(entry->getAudioClipId()->getId() == 10003);
        timestamp.reset(new ptime(time_from_string("2004-10-25 12:00:00")));
        CPPUNIT_ASSERT(*(entry->getTimestamp()) == *timestamp);

        // third interval -- this one's empty
        fromTime.reset(new ptime(time_from_string("2004-10-25 13:00:00")));
        toTime.reset(  new ptime(time_from_string("2005-10-25 13:00:00")));
        entries = playLog->getPlayLogEntries(fromTime, toTime);

        CPPUNIT_ASSERT(entries->size() == 0);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}
