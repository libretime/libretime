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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/SignalDispatcher.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_SIGNAL_H
#include <signal.h>
#else
#error "Need signal.h"
#endif

#if HAVE_SYS_STAT_H
#include <sys/stat.h>
#else
#error "Need sys/stat.h"
#endif


#include <iostream>
#include <sstream>
#include <fstream>
#include <cstdio>

#include "SignalDispatcher.h"


using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The singleton isntnace of SignalDispatcher.
 *----------------------------------------------------------------------------*/
SignalDispatcher      * SignalDispatcher::instance = 0;

/*------------------------------------------------------------------------------
 *  The signal handlers.
 *----------------------------------------------------------------------------*/
SignalHandler         * SignalDispatcher::handlers[NSIG];


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instnace.
 *----------------------------------------------------------------------------*/
class SignalDispatcher *
SignalDispatcher :: getInstance (void)                      throw ()
{
    if (!instance) {
        instance = new SignalDispatcher();
    }

    return instance;
}


/*------------------------------------------------------------------------------
 *  Register a signal handler
 *----------------------------------------------------------------------------*/
void
SignalDispatcher :: registerHandler(
                        int                 signal,
                        SignalHandler     * signalHandler)
                                                throw (std::invalid_argument)
{
    if (signal < 0 || signal >= NSIG) {
        throw std::invalid_argument("invalid signal value");
    }
    if (!signalHandler) {
        throw std::invalid_argument("signalHandler is 0");
    }

    handlers[signal] = signalHandler;

    // register our dispatcher for this signal
    ::signal(signal, dispatcher);
}


/*------------------------------------------------------------------------------
 *  Remove a signal handler
 *----------------------------------------------------------------------------*/
void
SignalDispatcher :: removeHandler(
                        int                 signal)
                                                throw (std::invalid_argument)
{
    if (signal < 0 || signal >= NSIG) {
        throw std::invalid_argument("invalid signal value");
    }

    handlers[signal] = 0;

    ::signal(signal, SIG_DFL);
}


/*------------------------------------------------------------------------------
 *  Our signal dispatcher
 *----------------------------------------------------------------------------*/
void
SignalDispatcher :: dispatcher(int  signal)             throw ()
{
    handlers[signal]->handleSignal(signal);
}

