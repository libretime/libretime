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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_UNISTD_H
#include <unistd.h>
#else
#error need unistd.h
#endif


#include "LiveSupport/Core/Thread.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
Thread :: Thread(Ptr<RunnableInterface>::Ref    runnable)       throw ()
{
    this->runnable = runnable;
}


/*------------------------------------------------------------------------------
 *  The POSIX thread function for this thread.
 *----------------------------------------------------------------------------*/
void *
Thread :: posixThreadFunction(void * thread)                    throw ()
{
    Thread   * pThread = (Thread *) thread;

    pThread->runnable->run();

    return 0;
}


/*------------------------------------------------------------------------------
 *  Start the thread.
 *----------------------------------------------------------------------------*/
void
Thread :: start(void)                               throw (std::exception)
{
    int             ret;
    pthread_attr_t  attr;

    pthread_attr_init(&attr);
    pthread_attr_setdetachstate(&attr, PTHREAD_CREATE_JOINABLE);
    ret = pthread_create(&thread, &attr, posixThreadFunction, this);
    pthread_attr_destroy(&attr);

    yield();

    if (ret) {
        // TODO: signal return code
        throw std::exception();
    }
}


/*------------------------------------------------------------------------------
 *  Join the thread.
 *----------------------------------------------------------------------------*/
void
Thread :: join(void)                                        throw ()
{
    int     ret;
    if ((ret = pthread_join(thread, 0))) {
        // TODO: signal return code
    }
}

