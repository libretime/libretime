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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/GeneratePlayReportMethod.h,v $

------------------------------------------------------------------------------*/
#ifndef GeneratePlayReportMethod_h
#define GeneratePlayReportMethod_h

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

#include "LiveSupport/Core/Ptr.h"
#include "PlayLogEntry.h"


namespace LiveSupport {
namespace Scheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An XML-RPC method object to return the list of audio clips which were
 *  played during a specified time interval.
 *
 *  The name of the method when called through XML-RPC is "generatePlayReport".
 *
 *  The expected parameter is an XML-RPC structure, with the following
 *  member:
 *  <ul>
 *      <li>from - datetime - the start of the interval to give the playlog
 *          from, inclusive.</li>
 *      <li>to   - datetime - the end of the interval to give the playlog
 *          to, non-inclusive.</li>
 *  </ul>
 *
 *  The XML-RPC function returns an XML-RPC array, containing a structure
 *  for each play log item in the interval.  An array of size 0 means there
 *  are no play log entries. Each structure is as follows:
 *  <ul>
 *      <li>audioClipId - int -      the id of the audio clip played </li>
 *      <li>timestamp   - datetime - the time the clip was played (started) </li>
 *  </ul>
 *
 *  If there is an error, an XML-RPC structure is returned, with the following
 *  fields:
 *  <ul>
 *      <li>errorCode - int - a numerical code for the error</li>
 *      <li>errorMessage - string - a description of the error</li>
 *  </ul>
 *  The possible error codes are:
 *  <ul>
 *     <li>1501 - invalid argument format </li>
 *     <li>1502 - missing or invalid 'from' argument </li>
 *     <li>1503 - missing or invalid 'to' argument </li>
 *  </ul>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.2 $
 */
class GeneratePlayReportMethod : public XmlRpc::XmlRpcServerMethod
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
        GeneratePlayReportMethod(void)                                  throw ()
                            : XmlRpc::XmlRpcServerMethod(methodName)
        {
        }

        /**
         *  Constuctor that registers the method with the server right away.
         *
         *  @param xmlRpcServer the XML-RPC server to register with.
         */
        GeneratePlayReportMethod(
                    Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)
                                                                        throw ();

        /**
         *  Execute the generatePlayReport command on the Scheduler daemon.
         *
         *  @param parameters XML-RPC function call parameters
         *  @param returnValue the return value of the call (out parameter)
         */
        void
        execute(XmlRpc::XmlRpcValue  & parameters,
                XmlRpc::XmlRpcValue  & returnValue)                     throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // GeneratePlayReportMethod_h

