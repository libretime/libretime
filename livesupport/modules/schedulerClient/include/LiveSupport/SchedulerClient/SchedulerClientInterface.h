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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/include/LiveSupport/SchedulerClient/SchedulerClientInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_SchedulerClient_SchedulerClientInterface_h
#define LiveSupport_SchedulerClient_SchedulerClientInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include "boost/date_time/posix_time/posix_time.hpp"

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/SessionId.h"

namespace LiveSupport {
namespace SchedulerClient {

using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface to access the scheduler daemon as a client.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.3 $
 */
class SchedulerClientInterface
{
    public:
        /**
         *  Return the version string for the scheduler this client
         *  is connected to.
         *
         *  @return the version string of the scheduler daemon.
         */
        virtual Ptr<const std::string>::Ref
        getVersion(void)                            throw ()
                                                                    = 0;

        /**
         *  Return the current time at the scheduler server.
         *
         *  @return the current time at the scheduler server.
         */
        virtual Ptr<const boost::posix_time::ptime>::Ref
        getSchedulerTime(void)                      throw ()
                                                                    = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace SchedulerClient
} // namespace LiveSupport

#endif // LiveSupport_SchedulerClient_SchedulerClientInterface_h

