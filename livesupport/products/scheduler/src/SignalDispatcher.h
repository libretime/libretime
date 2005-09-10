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
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef SignalDispatcher_h
#define SignalDispatcher_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_SIGNAL_H
#include <signal.h>
#else
#error "Need signal.h"
#endif

#include <string>
#include <stdexcept>

#include "SignalHandler.h"


namespace LiveSupport {
namespace Scheduler {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class to dispatch signals.
 *  See http://www.cs.wustl.edu/~schmidt/signal-patterns.html for details.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class SignalDispatcher
{
    private:

        /**
         *  The singleton instance of this class.
         */
        static SignalDispatcher       * instance;

        /**
         *  An array of registered signal handlers,
         *  of size NSIG defined in signal.h
         */
        static SignalHandler          * handlers[NSIG];

        /**
         *  Default constructor.
         */
        SignalDispatcher(void)                      throw ()
        {
        }

        /**
         *  The function registered to handle signals.
         *
         *  @param signal the signal being handled.
         */
        static void
        dispatcher(int signal)                      throw ();

    public:

        /**
         *  Return the singleton instance of SignalDispatcher.
         *
         *  @return the singleton instance of SignalDispatcher.
         */
        static SignalDispatcher  * 
        getInstance(void)                           throw ();

        /**
         *  Register a signal handler for a specific signal.
         *
         *  @param signal the signal to register for.
         *  @param signalHandler the signal handler to register.
         *  @exception std::invalid_argument if signal is out of range,
         *             or if signalHandler is 0.
         */
        void
        registerHandler(int             signal,
                        SignalHandler * signalHandler)
                                                throw (std::invalid_argument);

        /**
         *  Remove a signal handler for a specific signal.
         *  Restores the original system signal handling.
         *
         *  @param signal the signal to remove the handler for.
         *  @exception std::invalid_argument if signal is out of range.
         */
        void
        removeHandler(int       signal)         throw (std::invalid_argument);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // SignalDispatcher_h

