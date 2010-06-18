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

#ifdef HAVE_UNISTD_H
#include <unistd.h>
#else
#error need unistd.h
#endif

#include "LiveSupport/Core/TimeConversion.h"

#include "TestScheduledEvent.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::EventScheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
TestScheduledEvent :: TestScheduledEvent(
                        Ptr<ptime>::Ref             when,
                        Ptr<time_duration>::Ref     maxTimeToInitialize,
                        Ptr<time_duration>::Ref     eventLength)
                                                                    throw ()
{
    this->when        = when;
    this->initTime    = maxTimeToInitialize;
    this->length      = eventLength;
    state             = created;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
TestScheduledEvent :: initialize(void)                  throw (std::exception)
{
    state = initializing;
    TimeConversion::sleep(initTime);
    state = initialized;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
TestScheduledEvent :: deInitialize(void)                throw ()
{
    state = deInitialized;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
TestScheduledEvent :: start(void)                       throw ()
{
    state = running;
}


/*------------------------------------------------------------------------------
 *  Initialize the event object.
 *----------------------------------------------------------------------------*/
void
TestScheduledEvent :: stop(void)                        throw ()
{
    state = stopped;
}

