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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/SavePlaylistMethod.h,v $

------------------------------------------------------------------------------*/
#ifndef SavePlaylistMethod_h
#define SavePlaylistMethod_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
#include <XmlRpcServerMethod.h>
#include <XmlRpcValue.h>
#include <XmlRpcException.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Playlist.h"


namespace LiveSupport {
namespace Scheduler {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An XML-RPC method object to save a playlist (specified by its playlist id) 
 *  after editing.  It releases the "locked for editing" lock and discards the
 *  copy of the playlist which was saved for reverting to.
 *
 *  The name of the method when called through XML-RPC is 
 *  "savePlaylist".
 *
 *  The expected parameter is an XML-RPC structure, with the following
 *  members:
 *  <ul>
 *      <li>sessionId  - string - the session ID obtained via the login()
 *                                method of the authentication client </li>
 *      <li>playlistId - int - the unique id of the playlist to save.</li>
 *  </ul>
 *
 *  In case of an error, a standard XML-RPC fault response is generated, 
 *  and a {&nbsp;faultCode, faultString&nbsp;} structure is returned.  The
 *  possible errors are:
 *  <ul>
 *     <li>701 - invalid argument format </li>
 *     <li>702 - argument is not a playlist ID </li>
 *     <li>703 - playlist not found </li>
 *     <li>704 - could not unlock playlist </li>
 *     <li>705 - could not save playlist </li>
 *     <li>720 - missing session ID argument </li>
 *  </ul>
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.5 $
 */
class SavePlaylistMethod : public XmlRpc::XmlRpcServerMethod
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
        SavePlaylistMethod(void)                                throw ()
                            : XmlRpc::XmlRpcServerMethod(methodName)
        {
        }

        /**
         *  Constuctor that registers the method with the server right away.
         *
         *  @param xmlRpcServer the XML-RPC server to register with.
         */
        SavePlaylistMethod(
                    Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)
                                                                throw ();

        /**
         *  Execute the display schedule command on the Scheduler daemon.
         *
         *  @param parameters XML-RPC function call parameters
         *  @param returnValue the return value of the call (out parameter)
         */
        void
        execute(XmlRpc::XmlRpcValue  & parameters,
                XmlRpc::XmlRpcValue  & returnValue)
                                            throw (XmlRpc::XmlRpcException);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // SavePlaylistMethod_h

