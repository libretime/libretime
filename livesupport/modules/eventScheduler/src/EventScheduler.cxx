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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/eventScheduler/src/EventScheduler.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/EventScheduler/EventScheduler.h"

#include "SchedulerThread.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::EventScheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
LiveSupport::EventScheduler::EventScheduler :: EventScheduler(
                        Ptr<EventContainerInterface>::Ref   eventContainer,
                        Ptr<time_duration>::Ref             granularity)
                                                                    throw ()
{
    runnable.reset(new SchedulerThread(eventContainer, granularity));
    thread.reset(new Thread(runnable));
}


/*------------------------------------------------------------------------------
 *  Start the event scheduler.
 *----------------------------------------------------------------------------*/
void
LiveSupport::EventScheduler::EventScheduler :: start(void)      throw ()
{
    thread->start();
}


/*------------------------------------------------------------------------------
 *  Update the events.
 *----------------------------------------------------------------------------*/
void
LiveSupport::EventScheduler::EventScheduler :: update(void)     throw ()
{
    thread->signal(SchedulerThread::UpdateSignal);
}


/*------------------------------------------------------------------------------
 *  Stop the event scheduler.
 *----------------------------------------------------------------------------*/
void
LiveSupport::EventScheduler::EventScheduler :: stop(void)       throw ()
{
    thread->stop();
    thread->join();
}

