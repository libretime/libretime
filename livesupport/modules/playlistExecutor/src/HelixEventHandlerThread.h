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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/HelixEventHandlerThread.h,v $

------------------------------------------------------------------------------*/
#ifndef HelixEventHandlerThread_h
#define HelixEventHandlerThread_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/RunnableInterface.h"
#include "HelixPlayer.h"


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace boost::posix_time;

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A thread that checks on Helix events every once in a while.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class HelixEventHandlerThread : public virtual RunnableInterface
{
    private:
        /**
         *  The Helix client engine to check.
         */
        IHXClientEngine                   * clientEngine;

        /**
         *  The granularity of the thread: the time it will sleep
         *  between checking up on the state of things.
         */
        Ptr<time_duration>::Ref             granularity;

        /**
         *  Flag indicating whether the thread should still run, or
         *  actually terminate.
         */
        bool                                shouldRun;

        /**
         *  Default constructor.
         */
        HelixEventHandlerThread(void)                   throw ()
        {
        }


    public:
        /**
         *  Constructor.
         *
         *  @param clientEngine the Helix client engine to check.
         *  @param granularity the granularity of the thread: the time the
         *         thread will sleep between checking up on things.
         */
        HelixEventHandlerThread(IHXClientEngine       * clientEngine,
                                Ptr<time_duration>::Ref granularity)
                                                                    throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~HelixEventHandlerThread(void)                          throw ()
        {
        }

        /**
         *  The main execution loop for the thread.
         */
        virtual void
        run(void)                                       throw ();

        /**
         *  Signal the thread to stop, gracefully.
         */
        virtual void
        stop(void)                                      throw ()
        {
            shouldRun = false;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport


#endif // HelixEventHandlerThread_h

