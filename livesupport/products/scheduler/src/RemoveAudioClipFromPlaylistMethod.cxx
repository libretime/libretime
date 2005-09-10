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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RemoveAudioClipFromPlaylistMethod.cxx,v $

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
#include "ScheduleInterface.h"
#include "ScheduleFactory.h"
#include "LiveSupport/Core/XmlRpcTools.h"

#include "RemoveAudioClipFromPlaylistMethod.h"


using namespace boost;
using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of this XML-RPC method.
 *----------------------------------------------------------------------------*/
const std::string RemoveAudioClipFromPlaylistMethod::methodName 
                                                = "removeAudioClipFromPlaylist";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int RemoveAudioClipFromPlaylistMethod::errorId = 400;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
RemoveAudioClipFromPlaylistMethod :: RemoveAudioClipFromPlaylistMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}

 
/*------------------------------------------------------------------------------
 *  Execute the stop XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
RemoveAudioClipFromPlaylistMethod :: execute(
                                XmlRpc::XmlRpcValue  & rootParameter,
                                XmlRpc::XmlRpcValue  & returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    if (!rootParameter.valid() || rootParameter.size() != 1
                               || !rootParameter[0].valid()) {
        XmlRpcTools::markError(errorId+1, "invalid argument format", 
                               returnValue);
        return;
    }
    XmlRpc::XmlRpcValue     parameters = rootParameter[0];

    Ptr<SessionId>::Ref     sessionId;
    try{
        sessionId = XmlRpcTools::extractSessionId(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+20, 
                               "missing session ID argument",
                                returnValue);
        return;
    }

    Ptr<UniqueId>::Ref      playlistId;
    try{
        playlistId = XmlRpcTools::extractPlaylistId(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+2, 
                               "missing playlist ID argument",
                                returnValue);
        return;
    }

    Ptr<UniqueId>::Ref      playlistElementId;
    try{
        playlistElementId = XmlRpcTools::extractPlaylistElementId(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+3, 
                               "missing playlist element ID argument",
                               returnValue);
        return;
    }

    Ptr<StorageClientFactory>::Ref     scf;
    Ptr<StorageClientInterface>::Ref   storage;
    scf     = StorageClientFactory::getInstance();
    storage = scf->getStorageClient();
 
    Ptr<Playlist>::Ref playlist;
    try {
        playlist = storage->getPlaylist(sessionId, playlistId);
    } catch (Core::XmlRpcException &e) {
        std::string eMsg = "playlist does not exist:\n";
        eMsg += e.what();
        XmlRpcTools::markError(errorId+4, eMsg, returnValue);
        return;
    }

    if (!playlist->isLocked()) {
        XmlRpcTools::markError(errorId+5, 
                               "playlist has not been opened for editing", 
                               returnValue);
        return;
    }

    try {                                        // and finally, the beef
        playlist->removePlaylistElement(playlistElementId);
    } catch(std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+6,
                               "no audio clip at the specified "
                               "relative offset",
                               returnValue);
        return;
    }
}
