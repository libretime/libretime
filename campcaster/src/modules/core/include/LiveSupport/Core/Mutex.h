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
#ifndef LiveSupport_Core_Mutex_h
#define LiveSupport_Core_Mutex_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <pthread.h>


namespace LiveSupport {
namespace Core {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A simple wrapper for pthread_mutex_t.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class Mutex
{
    private:
        /**
         *  The mutex object.
         */
        pthread_mutex_t *                   mutex;


    public:
        /**
         *  Default constructor.
         */
        Mutex(void)                                                 throw ()
        {
            mutex = new pthread_mutex_t;
            pthread_mutex_init(mutex, NULL);
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~Mutex(void)                                                throw ()
        {
            pthread_mutex_destroy(mutex);
        }

        /**
         *  Lock the mutex.
         *  If the mutex is already locked, it blocks until it becomes free.
         */
        void
        lock(void)                                                  throw ()
        {
            pthread_mutex_lock(mutex);
        }

        /**
         *  Unlock a mutex.
         */
        void
        unlock(void)                                                throw ()
        {
            pthread_mutex_unlock(mutex);
        }

        /**
         *  Try to lock a mutex.
         *  If the mutex is already locked, it returns false.
         *
         *  @return true if the mutex was successfully locked; false otherwise.
         */
        bool
        tryLock(void)                                               throw ()
        {
            return (pthread_mutex_trylock(mutex) == 0);
        }

        /**
         *  Try to lock a mutex.
         *  If the mutex is already locked, it returns false.
         *
         *` @param  errorCode   return parameter for the error code;
         *                      0 for no error.
         *  @return true if the mutex was successfully locked; false otherwise.
         */
        bool
        tryLock(int &   errorCode)                                  throw ()
        {
            errorCode = pthread_mutex_trylock(mutex);
            return (errorCode == 0);
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport


#endif // LiveSupport_Core_Mutex_h

