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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RevertEditedPlaylistMethod.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_TIME_H
#include <time.h>
#else
#error need time.h
#endif


#include <string>

#include "LiveSupport/Storage/StorageClientInterface.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "XmlRpcTools.h"

#include "RevertEditedPlaylistMethod.h"


using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of this XML-RPC method.
 *----------------------------------------------------------------------------*/
const std::string RevertEditedPlaylistMethod::methodName 
                                                = "revertEditedPlaylist";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int RevertEditedPlaylistMethod::errorId = 800;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
RevertEditedPlaylistMethod :: RevertEditedPlaylistMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}

 
/*------------------------------------------------------------------------------
 *  Execute the stop XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
RevertEditedPlaylistMethod :: execute(XmlRpc::XmlRpcValue  & rootParameter,
                                      XmlRpc::XmlRpcValue  & returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    if (!rootParameter.valid() || rootParameter.size() != 1) {
        XmlRpcTools::markError(errorId+1, "invalid argument format", 
                               returnValue);
        return;
    }
    XmlRpc::XmlRpcValue      parameters = rootParameter[0];

    Ptr<SessionId>::Ref      sessionId;
    try{
        sessionId = XmlRpcTools::extractSessionId(parameters);
    }
    catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+20, 
                               "missing session ID argument",
                                returnValue);
        return;
    }

    Ptr<UniqueId>::Ref id;
    try{
        id = XmlRpcTools::extractPlaylistId(parameters);
    }
    catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+2, "argument is not a playlist ID", 
                               returnValue);
        return;
    }

    Ptr<StorageClientFactory>::Ref      scf;
    Ptr<StorageClientInterface>::Ref    storage;

    scf     = StorageClientFactory::getInstance();
    storage = scf->getStorageClient();
 
    Ptr<Playlist>::Ref playlist;
    try {
        playlist = storage->getPlaylist(sessionId, id);
    }
    catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+3, "playlist not found", 
                               returnValue);
        return;
    }

    try {
        playlist->revertToSavedCopy();
    }
    catch (std::logic_error) {
        XmlRpcTools::markError(errorId+4, "could not revert playlist", 
                               returnValue);
        return;
    }
}
