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
#ifndef CreateBackupOpenMethod_h
#define CreateBackupOpenMethod_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcServerMethod.h>
#include <XmlRpcValue.h>
#include <XmlRpcException.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An XML-RPC method object to start a backup creation process.
 *
 *  The name of the method when called through XML-RPC is "createBackupOpen".
 *
 *  The expected parameter is an XML-RPC structure, with the following
 *  members:
 *  <ul>
 *      <li>sessionId  - string - the session ID obtained via the login()
 *                                method of the authentication client </li>
 *      <li>criteria   - struct - the criteria to use for backing up the
 *                                storage </li>
 *      <li>fromTime   - datetime - entries are included in the schedule export
 *                                  starting from this time </li>
 *      <li>toTime     - datetime - entries are included in the schedule export
 *                                  up to but not including this time </li>
 *  </ul>
 *
 *  For the format of the <code>criteria</code> parameter, see the
 *  documentation of <code>XR_LocStor::xr_searchMetadata()</code>.
 *
 *  On success, returns an XML-RPC struct with a single field:
 *  <ul>
 *      <li>token   - string -  a token, which can be used to query the 
 *                              backup process </li>
 *  </ul>
 *
 *  In case of an error, a standard XML-RPC fault response is generated, 
 *  and a {&nbsp;faultCode, faultString&nbsp;} structure is returned.  The
 *  possible errors are:
 *  <ul>
 *     <li>4001 - invalid argument format </li>
 *     <li>4002 - missing criteria argument </li>
 *     <li>4003 - missing fromTime argument </li>
 *     <li>4004 - missing toTime argument </li>
 *     <li>4010 - error reported by the scheduler daemon </li>
 *     <li>4020 - missing session ID argument </li>
 *  </ul>
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class CreateBackupOpenMethod : public XmlRpc::XmlRpcServerMethod
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
        CreateBackupOpenMethod(void)                                throw ()
                            : XmlRpc::XmlRpcServerMethod(methodName)
        {
        }

        /**
         *  Constuctor that registers the method with the server right away.
         *
         *  @param xmlRpcServer the XML-RPC server to register with.
         */
        CreateBackupOpenMethod(
                    Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)
                                                                    throw ();

        /**
         *  Execute the createBackupOpen command on the Scheduler daemon.
         *
         *  @param parameters XML-RPC function call parameters
         *  @param returnValue the return value of the call (out parameter)
         */
        void
        execute(XmlRpc::XmlRpcValue &   parameters,
                XmlRpc::XmlRpcValue &   returnValue)
                                            throw (XmlRpc::XmlRpcException);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // CreateBackupOpenMethod_h

