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
#ifndef TestEventContainer_h
#define TestEventContainer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/EventScheduler/EventContainerInterface.h"


namespace LiveSupport {
namespace EventScheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An event container for testing purposes.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class TestEventContainer : public virtual EventContainerInterface
{
    private:
        /**
         *  The only event that this container holds.
         */
        Ptr<ScheduledEventInterface>::Ref   event;


    public:
        /**
         *  Constructor.
         *
         *  @param event the only event this container will hold.
         */
        TestEventContainer(Ptr<ScheduledEventInterface>::Ref    event)
                                                                    throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~TestEventContainer(void)                           throw ()
        {
        }

        /**
         *  Return the first event after the specified timepoint.
         *
         *  @param when return the first event after this timepoint,
         *  @return the first event to schedule after the specified
         *          timepoint. may be a reference to 0, if currently
         *          there are no known events after the specified time.
         */
        virtual Ptr<ScheduledEventInterface>::Ref
        getNextEvent(Ptr<ptime>::Ref    when)               throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace EventScheduler
} // namespace LiveSupport


#endif // TestEventContainer_h

