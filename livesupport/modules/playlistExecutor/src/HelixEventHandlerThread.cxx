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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/HelixEventHandlerThread.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TimeConversion.h"

#include "HelixEventHandlerThread.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::PlaylistExecutor;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
HelixEventHandlerThread :: HelixEventHandlerThread(
                        IHXClientEngine           * clientEngine,
                        Ptr<time_duration>::Ref     granularity)
                                                                    throw ()
{
    this->clientEngine  = clientEngine;
    this->granularity   = granularity;
    this->shouldRun     = false;
}


/*------------------------------------------------------------------------------
 *  The main execution body of the thread.
 *----------------------------------------------------------------------------*/
void
HelixEventHandlerThread :: run(void)                                    throw ()
{
    shouldRun = true;

    while (shouldRun) {
        struct _HXxEvent  * event = 0;
        clientEngine->EventOccurred(event);
        TimeConversion::sleep(granularity);
    }
}

