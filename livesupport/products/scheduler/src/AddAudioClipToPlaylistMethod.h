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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/AddAudioClipToPlaylistMethod.h,v $

------------------------------------------------------------------------------*/
#ifndef AddAudioClipToPlaylistMethod_h
#define AddAudioClipToPlaylistMethod_h

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
 *  An XML-RPC method object to add an audio clip (specified by its ID) 
 *  to a playlist (also specified by its ID).
 *
 *  The name of the method when called through XML-RPC is 
 *  "addAudioClipToPlaylist".
 *  The expected parameter is an XML-RPC structure, with the following
 *  members:
 *  <ul>
 *      <li>playlistId - int - the unique id of the playlist.</li>
 *      <li>audioClipId - int - the unique id of the audio clip to
 *                              be added.</li>
 *      <li>relativeOffset - int - the number of seconds between the
 *                start of the playlist and the start of the audio clip.</li>
 *  </ul>
 *
 *  In case of an error, an XML-RPC structure is returned, with the following
 *  fields:
 *  <ul>
 *      <li>errorCode - int - the id of the error condition</li>
 *      <li>errorMessage - string - a description of the error</li>
 *  </ul>
 *  The possible error codes are:
 *  <ul>
 *     <li>301 - invalid argument format </li>
 *     <li>301 - missing playlist ID argument </li>
 *     <li>301 - missing audio clip ID argument </li>
 *     <li>301 - missing relative offset argument </li>
 *     <li>301 - playlist does not exist </li>
 *     <li>301 - could not load playlist </li>
 *     <li>301 - playlist has not been opened for editing </li>
 *     <li>301 - two audio clips at the same relative offset
 *               (from Playlist::addAudioClipToPlaylist()) </li>
 *  </ul>
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class AddAudioClipToPlaylistMethod : public XmlRpc::XmlRpcServerMethod
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
        AddAudioClipToPlaylistMethod(void)                          throw ()
                            : XmlRpc::XmlRpcServerMethod(methodName)
        {
        }

        /**
         *  Constuctor that registers the method with the server right away.
         *
         *  @param xmlRpcServer the XML-RPC server to register with.
         */
        AddAudioClipToPlaylistMethod(
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
                 XmlRpc::XmlRpcValue  & returnValue)                throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // AddAudioClipToPlaylistMethod_h

