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
#ifndef DisplayScheduleMethod_h
#define DisplayScheduleMethod_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
#include <vector>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcServerMethod.h>
#include <XmlRpcValue.h>
#include <XmlRpcException.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/ScheduleEntry.h"


namespace LiveSupport {
namespace Scheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An XML-RPC method object to return the schedule for a specified
 *  time interval.
 *
 *  The name of the method when called through XML-RPC is "displaySchedule".
 *
 *  The expected parameter is an XML-RPC structure, with the following
 *  members:
 *  <ul>
 *      <li>sessionId  - string - the session ID obtained via the login()
 *                                method of the authentication client </li>
 *      <li>from - datetime - the start of the interval to list schedule from,
 *          inclusive.</li>
 *      <li>to  - datetime - the end of the interval to list schedule from,
 *          non-inclusive.</li>
 *  </ul>
 *
 *  The XML-RPC function returns an XML-RPC array, containing a structure
 *  for each scheduled item in the interval. An array of size 0 means there
 *  are not scheduled entries. Each structure is as follows:
 *  <ul>
 *      <li>id - int - the id of the scheduled entry</li>
 *      <li>playlistId - string - the id of the playlist associated with the entry
 *      </li>
 *      <li>start - datetime - the start of the scheduled item</li>
 *      <li>end - datetime - the end of the scheduled item</li>
 *  </ul>
 *
 *  The playlistId parameter is a string containing the hexadecimal value of 
 *  the playlist ID.
 *  It is up to 8 characters long, representing a value between 0 and 2^31-1,
 *  inclusive.
 *
 *  In case of an error, a standard XML-RPC fault response is generated, 
 *  and a {&nbsp;faultCode, faultString&nbsp;} structure is returned.  The
 *  possible errors are:
 *  <ul>
 *     <li>1101 - invalid argument format </li>
 *     <li>1102 - missing or invalid 'from' argument </li>
 *     <li>1103 - missing or invalid 'to' argument </li>
 *     <li>1120 - missing session ID argument </li>
 *  </ul>
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class DisplayScheduleMethod : public XmlRpc::XmlRpcServerMethod
{
    private:
        /**
         *  The name of this method, as it will be registered into the
         *  XML-RPC server.
         */
        static const std::string        methodName;

        /**
         *  The ID of this method for error reporting purposes.
         */
        static const int                errorId;


    public:
        /**
         *  A default constructor, for testing purposes.
         */
        DisplayScheduleMethod(void)                                 throw ()
                            : XmlRpc::XmlRpcServerMethod(methodName)
        {
        }

        /**
         *  Constuctor that registers the method with the server right away.
         *
         *  @param xmlRpcServer the XML-RPC server to register with.
         */
        DisplayScheduleMethod(
                    Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)
                                                                    throw ();

        /**
         *  Execute the display schedule command on the Scheduler daemon.
         *
         *  @param parameters XML-RPC function call parameters
         *  @param returnValue the return value of the call (out parameter)
         */
        void
        execute( XmlRpc::XmlRpcValue  & parameters,
                 XmlRpc::XmlRpcValue  & returnValue)
                                            throw (XmlRpc::XmlRpcException);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // DisplayScheduleMethod_h

