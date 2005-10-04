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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


#include <string>

#include "LiveSupport/Storage/StorageClientInterface.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/Core/XmlRpcTools.h"

#include "DisplayPlaylistsMethod.h"

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
const std::string DisplayPlaylistsMethod::methodName = "displayPlaylists";

/*------------------------------------------------------------------------------
 *  The ID of this method for error reporting purposes.
 *----------------------------------------------------------------------------*/
const int DisplayPlaylistsMethod::errorId = 1700;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the method and register it right away.
 *----------------------------------------------------------------------------*/
DisplayPlaylistsMethod :: DisplayPlaylistsMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}


/*------------------------------------------------------------------------------
 *  Execute the stop XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
DisplayPlaylistsMethod :: execute(XmlRpc::XmlRpcValue  & rootParameter,
                                  XmlRpc::XmlRpcValue  & returnValue)
                                                throw (XmlRpc::XmlRpcException)
{
    if (!rootParameter.valid() || rootParameter.size() != 1
                               || !rootParameter[0].valid()) {
        XmlRpcTools::markError(errorId+1, "invalid argument format", 
                               returnValue);
        return;
    }
    XmlRpc::XmlRpcValue      parameters = rootParameter[0];

    Ptr<SessionId>::Ref      sessionId;
    try{
        sessionId = XmlRpcTools::extractSessionId(parameters);
    } catch (std::invalid_argument &e) {
        XmlRpcTools::markError(errorId+20, 
                               "missing session ID argument",
                                returnValue);
        return;
    }

    Ptr<StorageClientFactory>::Ref      scf;
    Ptr<StorageClientInterface>::Ref    storage;

    scf     = StorageClientFactory::getInstance();
    storage = scf->getStorageClient();

    Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref playlistIds;
    try {
        playlistIds = storage->getPlaylistIds();
//std::cerr << "\nplaylistIds: " << playlistIds << "\n"
//          << "size: " << playlistIds->size() << "n";
    } catch (Core::XmlRpcException &e) {
        std::string eMsg = "getPlaylistsIds() returned error:\n";
        eMsg += e.what();
        XmlRpcTools::markError(errorId+2, eMsg, returnValue);
        return;
    }
    
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref 
                               playlists(new std::vector<Ptr<Playlist>::Ref>);
    std::vector<Ptr<UniqueId>::Ref>::const_iterator it = playlistIds->begin();
    while (it != playlistIds->end()) {
        try {
            playlists->push_back(storage->getPlaylist(sessionId, *it));
        } catch (Core::XmlRpcException &e) {
            std::string eMsg = "audio clip not found:\n";
            eMsg += e.what();
            XmlRpcTools::markError(errorId+3, eMsg, returnValue);
            return;
        }
        ++it;
    }

    XmlRpcTools::playlistVectorToXmlRpcValue(playlists, returnValue);
}

