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
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/DisplayAudioClipsMethod.h,v $

------------------------------------------------------------------------------*/
#ifndef DisplayAudioClipsMethod_h
#define DisplayAudioClipsMethod_h

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
#include <XmlRpcServerMethod.h>
#include <XmlRpcValue.h>
#include <XmlRpcException.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/AudioClip.h"


namespace LiveSupport {
namespace Scheduler {

using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An XML-RPC method object to return a listing of the audio clips contained
 *  in the audio clip store.
 *
 *  The name of the method when called through XML-RPC is "displayAudioClips".
 *
 *  The expected parameter is an XML-RPC structure, with the following
 *  members:
 *  <ul>
 *      <li>sessionId  - string - the session ID obtained via the login()
 *                                method of the authentication client </li>
 *  </ul>
 *
 *  The XML-RPC function returns an XML-RPC array, containing a structure
 *  for each audio clip in the audio clip store.  An array of size 0 means the
 *  audio clip store is empty.  Each structure is as follows:
 *  <ul>
 *      <li>audioClip - string - an XML representation of the audio clip; this
 *          XML element can be used as argument to AudioClip::configure()</li>
 *  </ul>
 *
 *  In case of an error, a standard XML-RPC fault response is generated, 
 *  and a {&nbsp;faultCode, faultString&nbsp;} structure is returned.  The
 *  possible errors are:
 *  <ul>
 *     <li>1801 - invalid argument format </li>
 *     <li>1802 - XML-RPC error </li>
 *     <li>1820 - missing session ID argument </li>
 *  </ul>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.6 $
 */
class DisplayAudioClipsMethod : public XmlRpc::XmlRpcServerMethod
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
        DisplayAudioClipsMethod(void)                                 throw ()
                            : XmlRpc::XmlRpcServerMethod(methodName)
        {
        }

        /**
         *  Constuctor that registers the method with the server right away.
         *
         *  @param xmlRpcServer the XML-RPC server to register with.
         */
        DisplayAudioClipsMethod(
                    Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)
                                                                    throw ();

        /**
         *  Execute the displayAudioClips command on the Scheduler daemon.
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

#endif // DisplayAudioClipsMethod_h

