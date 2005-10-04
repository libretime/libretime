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
#ifndef TestScheduledEvent_h
#define TestScheduledEvent_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/EventScheduler/ScheduledEventInterface.h"


namespace LiveSupport {
namespace EventScheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A scheduled event for testing purposes.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class TestScheduledEvent : public virtual ScheduledEventInterface
{
    public:
        /**
         *  Enumeration describing the possible states of the event.
         */
        typedef enum {  created,
                        initializing,
                        initialized,
                        running,
                        stopped,
                        deInitialized } State;

    private:
        /**
         *  The time this event is scheduled for.
         */
        Ptr<ptime>::Ref             when;

        /**
         *  The maximum time this event should get initialized.
         */
        Ptr<time_duration>::Ref     initTime;

        /**
         *  The length of the event.
         */
        Ptr<time_duration>::Ref     length;

        /**
         *  The current state of the event.
         */
        State                       state;
 
    public:
        /**
         *  Constructor.
         *
         *  @param when the time this event is scheduled for.
         *  @param maxTimeToInitialize the maximum time for this event
         *         to initialize.
         *  @param eventLength the length of the event.
         */
        TestScheduledEvent(Ptr<ptime>::Ref          when,
                           Ptr<time_duration>::Ref  maxTimeToInitialize,
                           Ptr<time_duration>::Ref  eventLength)
                                                                    throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~TestScheduledEvent(void)                           throw ()
        {
        }

        /**
         *  Return the current state of the event.
         */
        virtual State
        getState(void) const                                throw ()
        {
            return state;
        }

        /**
         *  Tell the time this event is scheduled for.
         *
         *  @return the time this event is scheduled for.
         */
        virtual Ptr<const ptime>::Ref
        getScheduledTime(void)                              throw ()
        {
            return when;
        }

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
        initialize(void)                        throw (std::exception);

        /**
         *  The maximum time for the initalize() function to complete.
         *  It is the responsibility of the ScheduledEventInterface object to
         *  complete the initialization in that time.
         *
         *  @return the maximum time for the initialize() function to complete.
         *  @see #initialize
         */
        virtual Ptr<const time_duration>::Ref
        maxTimeToInitialize(void)                   throw ()
        {
            return initTime;
        }

        /**
         *  De-initialize the event object.
         */
        virtual void
        deInitialize(void)                          throw ();

        /**
         *  Start the event.
         *  This function call should start the execution of the event in
         *  a separate thread, and return immediately.
         */
        virtual void
        start(void)                                 throw ();

        /**
         *  The length of the event.
         *  The scheduler will call stop() when this much time has passed
         *  after calling start().
         *
         *  @return the length of the event, in time.
         */
        virtual Ptr<const time_duration>::Ref
        eventLength(void)                           throw ()
        {
            return length;
        }

        /**
         *  Stop the event.
         *  This function call should result in the event stopping, if
         *  this has not happened yet. The processing of this event should
         *  persue in a seperate thread, and the function itself should
         *  return immediately.
         */
        virtual void
        stop(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace EventScheduler
} // namespace LiveSupport


#endif // TestScheduledEvent_h

