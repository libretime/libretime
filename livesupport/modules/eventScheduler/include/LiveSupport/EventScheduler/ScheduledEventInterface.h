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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/eventScheduler/include/LiveSupport/EventScheduler/ScheduledEventInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_EventScheduler_ScheduledEventInterface_h
#define LiveSupport_EventScheduler_ScheduledEventInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace EventScheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The base class for all events scheduled by the EventScheduler.
 *  Subclass this one, and implement the necessary functions to have
 *  a class that can be scheduled.
 *
 *  The lifetime of the scheduled event is as follows, if it is scheduled
 *  to start at time S and end at time E, where E = S + event.eventLength():
 *  <ul>
 *      <li>latest at S - event.maxTimeToInitialize(): event.initialize()</li>
 *      <li>at S: event.start()</li>
 *      <li>at E: event.stop()</li>
 *      <li>after E: event.deInitialize()</li>
 *  </ul>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class ScheduledEventInterface
{
    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~ScheduledEventInterface(void)                        throw ()
        {
        }

        /**
         *  Tell the time this event is scheduled for.
         *
         *  @return the time this event is scheduled for.
         */
        virtual Ptr<const ptime>::Ref
        getScheduledTime(void)                              throw () = 0;

        /**
         *  Initialize the event object.
         *  This should finishin at most maxTimeToInitialize() time.
         *  Use this call to allocate any resources that will be needed
         *  by the event itself.
         *
         *  @exception std::exception on initialization problems.
         *             a raised exception will result in the cancellation
         *             of the event.
         *  @see #maxTimeToInitialize
         */
        virtual void
        initialize(void)                        throw (std::exception)    = 0;

        /**
         *  The maximum time for the initalize() function to complete.
         *  It is the responsibility of the ScheduledEventInterface object to
         *  complete the initialization in that time.
         *
         *  @return the maximum time for the initialize() function to complete.
         *  @see #initialize
         */
        virtual Ptr<const time_duration>::Ref
        maxTimeToInitialize(void)                   throw ()              = 0;

        /**
         *  De-initialize the event object.
         */
        virtual void
        deInitialize(void)                          throw ()              = 0;

        /**
         *  Start the event.
         *  This function call should start the execution of the event in
         *  a separate thread, and return immediately.
         */
        virtual void
        start(void)                                 throw ()              = 0;

        /**
         *  The length of the event.
         *  The scheduler will call stop() when this much time has passed
         *  after calling start().
         *
         *  @return the length of the event, in time.
         */
        virtual Ptr<const time_duration>::Ref
        eventLength(void)                           throw ()              = 0;

        /**
         *  Stop the event.
         *  This function call should result in the event stopping, if
         *  this has not happened yet. The event execution should be stopped
         *  and ready to be de-initialized after this call returns.
         */
        virtual void
        stop(void)                                  throw ()              = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace EventScheduler
} // namespace LiveSupport


#endif // LiveSupport_EventScheduler_ScheduledEventInterface_h

