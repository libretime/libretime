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
#ifndef LiveSupport_Core_Thread_h
#define LiveSupport_Core_Thread_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <pthread.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/RunnableInterface.h"


namespace LiveSupport {
namespace Core {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A generic thread executor class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see RunnableInterface
 */
class Thread
{
    private:
        /**
         *  The POSIX thread for this object.
         */
        pthread_t                           thread;

        /**
         *  The Runnable object, that constitutes the main running body
         *  of the thread.
         */
        Ptr<RunnableInterface>::Ref         runnable;

        /**
         *  Default constructor.
         */
        Thread(void)                                    throw ()
        {
        }

        /**
         *  The thread function for the POSIX thread interface.
         *
         *  @param thread pointer to this thread instance.
         *  @return always 0
         */
        static void *
        posixThreadFunction(void * thread)              throw ();

    public:
        /**
         *  Constructor.
         *
         *  @param runnable the Runnable object making up the execution
         *         part of the thread.
         */
        Thread(Ptr<RunnableInterface>::Ref      runnable)   throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~Thread(void)                                   throw ()
        {
        }

        /**
         *  Start the execution of the thread.
         *  This funcion will create a new thread and starts executing
         *  it by the run() function of the Runnable object. Start will
         *  return immediately.
         *
         *  @exception std::exception if the thread could not be started.
         */
        virtual void
        start(void)                                     throw (std::exception);

        /**
         *  Signal the thread to stop, gracefully.
         *  This is just a call to signal the execution to stop, eventually.
         *  The thread still has to be joined after calling stop().
         *
         *  @see #join
         */
        virtual void
        stop(void)                                      throw ()
        {
            runnable->stop();
        }

        /**
         *  Force the current thread to relinquish use of its processor.
         *  So that other threads get a chance to run.
         */
        static void
        yield(void)                                     throw ()
        {
            pthread_yield();
        }

        /**
         *  Join the thread.
         *  Wait for the thread to terminate and free up all its resources.
         */
        virtual void
        join(void)                                      throw ();

        /**
         *  Send a signal to the runnable object inside this thread.
         *
         *  @param userData user-specific parameter for the signal.
         */
        virtual void
        signal(int userData)                            throw ()
        {
            runnable->signal(userData);
        }

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport


#endif // LiveSupport_Core_Thread_h

