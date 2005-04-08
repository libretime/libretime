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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PostgresqlScheduleTest.cxx,v $

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
#include "PostgresqlSchedule.h"
#include "PostgresqlScheduleTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(PostgresqlScheduleTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: setUp(void)                         throw ()
{
    Ptr<SchedulerDaemon>::Ref   scheduler = SchedulerDaemon::getInstance();
    try {
        cm = scheduler->getConnectionManager();
        
        schedule.reset(new PostgresqlSchedule(cm));
        schedule->install();

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
PostgresqlScheduleTest :: tearDown(void)                      throw ()
{
    schedule->uninstall();
    schedule.reset();
    cm.reset();
}


/*------------------------------------------------------------------------------
 *  Test for an available timeframe in an empty schedule database.
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // check with any two arbitary dates, the timeframe should be available
    Ptr<ptime>::Ref from(new ptime(time_from_string("2004-07-23 10:00:00")));
    Ptr<ptime>::Ref to(new ptime(time_from_string("2004-07-23 11:00:00")));

    CPPUNIT_ASSERT(schedule->isTimeframeAvailable(from, to));
}


/*------------------------------------------------------------------------------
 *  Schedule a single playlist.
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: simpleScheduleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // create a 1 hour long playlist, from 10 o'clock 2004-07-23
    Ptr<UniqueId>::Ref      id = UniqueId::generateId();
    Ptr<time_duration>::Ref playlength(new time_duration(1, 0, 0));
    Ptr<Playlist>::Ref      playlist(new Playlist(id, playlength));
    Ptr<ptime>::Ref         from(new ptime(time_from_string(
                                                    "2004-07-23 10:00:00")));

    try {
        schedule->schedulePlaylist(playlist, from);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Schedule a single playlist, and then query for available timeframes
 *  around it.
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: scheduleAndQueryTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // create a 1 hour long playlist, from 10 o'clock 2004-07-23
    Ptr<UniqueId>::Ref      id = UniqueId::generateId();
    Ptr<time_duration>::Ref playlength(new time_duration(1, 0, 0));
    Ptr<Playlist>::Ref      playlist(new Playlist(id, playlength));
    Ptr<ptime>::Ref         from(new ptime(time_from_string(
                                                    "2004-07-23 10:00:00")));

    try {
        schedule->schedulePlaylist(playlist, from);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }

    // check for available timeframes around the inserted one
    Ptr<ptime>::Ref     to;

    // this is the exact same timeframe as the scheduled playlist
    from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
    to.reset(new ptime(time_from_string("2004-07-23 11:00:00")));

    CPPUNIT_ASSERT(!schedule->isTimeframeAvailable(from, to));

    // a timeframe before our playlist
    from.reset(new ptime(time_from_string("2004-07-23 09:00:00")));
    to.reset(new ptime(time_from_string("2004-07-23 09:50:00")));

    CPPUNIT_ASSERT(schedule->isTimeframeAvailable(from, to));

    // a timeframe after our playlist
    from.reset(new ptime(time_from_string("2004-07-23 11:10:00")));
    to.reset(new ptime(time_from_string("2004-07-23 12:00:00")));

    CPPUNIT_ASSERT(schedule->isTimeframeAvailable(from, to));

    // a timeframe inside ours
    from.reset(new ptime(time_from_string("2004-07-23 10:10:00")));
    to.reset(new ptime(time_from_string("2004-07-23 10:50:00")));

    CPPUNIT_ASSERT(!schedule->isTimeframeAvailable(from, to));

    // a timeframe encapsulating ours
    from.reset(new ptime(time_from_string("2004-07-23 09:50:00")));
    to.reset(new ptime(time_from_string("2004-07-23 11:10:00")));

    CPPUNIT_ASSERT(!schedule->isTimeframeAvailable(from, to));

    // a timeframe starting earlier, but flowing into ours
    from.reset(new ptime(time_from_string("2004-07-23 09:00:00")));
    to.reset(new ptime(time_from_string("2004-07-23 10:10:00")));

    CPPUNIT_ASSERT(!schedule->isTimeframeAvailable(from, to));

    // a timeframe starting inside ours, and continuing afterwards
    from.reset(new ptime(time_from_string("2004-07-23 10:50:00")));
    to.reset(new ptime(time_from_string("2004-07-23 11:50:00")));

    CPPUNIT_ASSERT(!schedule->isTimeframeAvailable(from, to));

    // a timeframe ending exaclty when ours starts, which is OK
    from.reset(new ptime(time_from_string("2004-07-23 09:00:00")));
    to.reset(new ptime(time_from_string("2004-07-23 10:00:00")));

    CPPUNIT_ASSERT(schedule->isTimeframeAvailable(from, to));

    // a timeframe starting exactly when ours ends, which is OK
    from.reset(new ptime(time_from_string("2004-07-23 11:00:00")));
    to.reset(new ptime(time_from_string("2004-07-23 12:00:00")));

    CPPUNIT_ASSERT(schedule->isTimeframeAvailable(from, to));
}


/*------------------------------------------------------------------------------
 *  See if getScheduleEntries() returns correct lists
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: getScheduleEntriesTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // create a 1 hour long playlist
    Ptr<UniqueId>::Ref      playlistId = UniqueId::generateId();
    Ptr<time_duration>::Ref playlength(new time_duration(1, 0, 0));
    Ptr<Playlist>::Ref      playlist(new Playlist(playlistId, playlength));

    Ptr<ptime>::Ref         from;
    Ptr<ptime>::Ref         to;

    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref  entries;
    Ptr<ScheduleEntry>::Ref                          entry;

    try {
        // schedule our playlist for 2004-07-23, 10 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        schedule->schedulePlaylist(playlist, from);

        // schedule our playlist for 2004-07-23, 12 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 12:00:00")));
        schedule->schedulePlaylist(playlist, from);

        // schedule our playlist for 2004-07-23, 14 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 14:00:00")));
        schedule->schedulePlaylist(playlist, from);

        // and now let's see what's scheduled for 2004-07-23 between
        // 9:00 and 11:00
        from.reset(new ptime(time_from_string("2004-07-23 09:00:00")));
        to.reset(new ptime(time_from_string("2004-07-23 11:00:00")));
        entries = schedule->getScheduleEntries(from, to);
        // see that it is a single entry starting from 10 to 11 o'clock
        CPPUNIT_ASSERT(entries->size() == 1);
        entry = (*entries)[0];
        CPPUNIT_ASSERT(*(entry->getPlaylistId()) == *(playlist->getId()));
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        CPPUNIT_ASSERT(*(entry->getStartTime()) == *from);
        to.reset(new ptime(time_from_string("2004-07-23 11:00:00")));
        CPPUNIT_ASSERT(*(entry->getEndTime()) == *to);

        // let's see what's scheduled for 2004-07-23 between
        // 9:00 and 13:00
        from.reset(new ptime(time_from_string("2004-07-23 09:00:00")));
        to.reset(new ptime(time_from_string("2004-07-23 13:00:00")));
        entries = schedule->getScheduleEntries(from, to);
        // see that it is 2 entries, the one at 10 and the other at 12 o'clock
        CPPUNIT_ASSERT(entries->size() == 2);
        // see the one at 10 o'clock
        entry = (*entries)[0];
        CPPUNIT_ASSERT(*(entry->getPlaylistId()) == *(playlist->getId()));
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        CPPUNIT_ASSERT(*(entry->getStartTime()) == *from);
        to.reset(new ptime(time_from_string("2004-07-23 11:00:00")));
        CPPUNIT_ASSERT(*(entry->getEndTime()) == *to);
        // see the other at 12 o'clock
        entry = (*entries)[1];
        CPPUNIT_ASSERT(*(entry->getPlaylistId()) == *(playlist->getId()));
        from.reset(new ptime(time_from_string("2004-07-23 12:00:00")));
        CPPUNIT_ASSERT(*(entry->getStartTime()) == *from);
        to.reset(new ptime(time_from_string("2004-07-23 13:00:00")));
        CPPUNIT_ASSERT(*(entry->getEndTime()) == *to);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  See if getNextEntry() returns correct entry
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: getNextEntryTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // create a 1 hour long playlist
    Ptr<UniqueId>::Ref      playlistId = UniqueId::generateId();
    Ptr<time_duration>::Ref playlength(new time_duration(1, 0, 0));
    Ptr<Playlist>::Ref      playlist(new Playlist(playlistId, playlength));

    Ptr<ptime>::Ref             from;
    Ptr<ScheduleEntry>::Ref     entry;

    try {
        // schedule our playlist for 2004-07-23, 10 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        schedule->schedulePlaylist(playlist, from);

        // schedule our playlist for 2004-07-23, 12 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 12:00:00")));
        schedule->schedulePlaylist(playlist, from);

        // schedule our playlist for 2004-07-23, 14 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 14:00:00")));
        schedule->schedulePlaylist(playlist, from);

        // see what gives after 2004-07-23 09:00:00
        from.reset(new ptime(time_from_string("2004-07-23 09:00:00")));
        entry = schedule->getNextEntry(from);
        CPPUNIT_ASSERT(entry.get());
        // see that it is a single entry starting from 10 to 11 o'clock
        CPPUNIT_ASSERT(*(entry->getPlaylistId()) == *(playlist->getId()));
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        CPPUNIT_ASSERT(*(entry->getStartTime()) == *from);

        // see what gives after 2004-07-23 10:00:00
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        entry = schedule->getNextEntry(from);
        CPPUNIT_ASSERT(entry.get());
        // see that it is a single entry starting from 10 to 11 o'clock
        CPPUNIT_ASSERT(*(entry->getPlaylistId()) == *(playlist->getId()));
        from.reset(new ptime(time_from_string("2004-07-23 12:00:00")));
        CPPUNIT_ASSERT(*(entry->getStartTime()) == *from);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  See if scheduleEntryExists() returns correct results
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: scheduleEntryExistsTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // create a 1 hour long playlist
    Ptr<UniqueId>::Ref      playlistId = UniqueId::generateId();
    Ptr<time_duration>::Ref playlength(new time_duration(1, 0, 0));
    Ptr<Playlist>::Ref      playlist(new Playlist(playlistId, playlength));

    Ptr<ptime>::Ref         from;
    Ptr<ptime>::Ref         to;

    Ptr<UniqueId>::Ref      entryId1;
    Ptr<UniqueId>::Ref      entryId2;

    // at the very first, check for a nonexistent entry
    entryId1.reset(new UniqueId(9999));
    CPPUNIT_ASSERT(!schedule->scheduleEntryExists(entryId1));

    try {
        // schedule our playlist for 2004-07-23, 10 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        entryId1 = schedule->schedulePlaylist(playlist, from);

        // schedule our playlist for 2004-07-23, 12 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 12:00:00")));
        entryId2 = schedule->schedulePlaylist(playlist, from);

        // now let's check if our entries exist
        CPPUNIT_ASSERT(schedule->scheduleEntryExists(entryId1));
        CPPUNIT_ASSERT(schedule->scheduleEntryExists(entryId2));

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  See if removeFromSchedule() really removes
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: removeFromScheduleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // create a 1 hour long playlist
    Ptr<UniqueId>::Ref      playlistId = UniqueId::generateId();
    Ptr<time_duration>::Ref playlength(new time_duration(1, 0, 0));
    Ptr<Playlist>::Ref      playlist(new Playlist(playlistId, playlength));

    Ptr<ptime>::Ref         from;
    Ptr<ptime>::Ref         to;

    Ptr<UniqueId>::Ref      entryId1;
    Ptr<UniqueId>::Ref      entryId2;

    // at the very first, try to remove something not scheduled
    bool                    gotException = false;
    try {
        entryId1.reset(new UniqueId(9999));
        schedule->removeFromSchedule(entryId1);
    } catch (std::invalid_argument &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);

    try {
        // schedule our playlist for 2004-07-23, 10 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        entryId1 = schedule->schedulePlaylist(playlist, from);

        // schedule our playlist for 2004-07-23, 12 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 12:00:00")));
        entryId2 = schedule->schedulePlaylist(playlist, from);

        // now let's remove one of them, and see that it's not there anymore
        CPPUNIT_ASSERT(schedule->scheduleEntryExists(entryId1));
        schedule->removeFromSchedule(entryId1);
        CPPUNIT_ASSERT(!schedule->scheduleEntryExists(entryId1));

        // now let's remove the other, and see that it's not there anymore
        CPPUNIT_ASSERT(schedule->scheduleEntryExists(entryId2));
        schedule->removeFromSchedule(entryId2);
        CPPUNIT_ASSERT(!schedule->scheduleEntryExists(entryId2));

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test rescheduling.
 *----------------------------------------------------------------------------*/
void
PostgresqlScheduleTest :: rescheduleTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    // create a 1 hour long playlist
    Ptr<UniqueId>::Ref      playlistId = UniqueId::generateId();
    Ptr<time_duration>::Ref playlength(new time_duration(1, 0, 0));
    Ptr<Playlist>::Ref      playlist(new Playlist(playlistId, playlength));

    Ptr<ptime>::Ref         from;
    Ptr<ptime>::Ref         to;

    Ptr<UniqueId>::Ref      entryId1;
    Ptr<UniqueId>::Ref      entryId2;

    Ptr<ScheduleEntry>::Ref entry;

    // at the very first, try to reschedule something not scheduled
    bool                    gotException = false;
    try {
        entryId1.reset(new UniqueId(9999));
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        schedule->reschedule(entryId1, from);
    } catch (std::invalid_argument &e) {
        gotException = true;
    }
    CPPUNIT_ASSERT(gotException);

    try {
        // schedule our playlist for 2004-07-23, 10 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 10:00:00")));
        entryId1 = schedule->schedulePlaylist(playlist, from);

        // schedule our playlist for 2004-07-23, 12 o'clock
        from.reset(new ptime(time_from_string("2004-07-23 12:00:00")));
        entryId2 = schedule->schedulePlaylist(playlist, from);

        // now let's reschedule the first to a valid timepoint
        from.reset(new ptime(time_from_string("2004-07-23 08:00:00")));
        schedule->reschedule(entryId1, from);
        entry = schedule->getScheduleEntry(entryId1);
        CPPUNIT_ASSERT((bool) entry);
        CPPUNIT_ASSERT(*(entry->getStartTime()) == *from);

        // try to reschedule the second one into the first, should fail
        gotException = false;
        try {
            from.reset(new ptime(time_from_string("2004-07-23 08:30:00")));
            schedule->reschedule(entryId1, from);
        } catch (std::invalid_argument &e) {
            gotException = true;
        }
        CPPUNIT_ASSERT(gotException);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
}


