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

#include "LiveSupport/Core/ScheduleEntry.h"
#include "ScheduleEntryTest.h"


using namespace std;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(ScheduleEntryTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
ScheduleEntryTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
ScheduleEntryTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
ScheduleEntryTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UniqueId>::Ref      id(new UniqueId(1));
    Ptr<UniqueId>::Ref      playlistId(new UniqueId(2));
    Ptr<ptime>::Ref         startTime(new ptime(
                                    time_from_string("2006-05-10 10:00:00")));
    Ptr<ptime>::Ref         endTime(new ptime(
                                    time_from_string("2006-05-10 11:00:00")));

    Ptr<ScheduleEntry>::Ref     se(new ScheduleEntry(id,
                                                     playlistId,
                                                     startTime,
                                                     endTime));

    xmlpp::Document   * document = new xmlpp::Document();
    document->create_root_node("root");

    se->toDom(document->get_root_node());

    xmlpp::Node::NodeList::iterator   it = document->get_root_node()
                                            ->get_children().begin();
    xmlpp::Node               * node = *it;
    xmlpp::Element            * element = dynamic_cast<xmlpp::Element*> (node);
    Ptr<ScheduleEntry>::Ref     see(new ScheduleEntry(element));

    CPPUNIT_ASSERT(*se == *see);
}

