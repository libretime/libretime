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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/DisplayPlaylistsMethod.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


#include <string>

#include "LiveSupport/Core/StorageClientInterface.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
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
 *  Convert a vector of Playlists into an XML-RPC value.
 *  This function returns an XML-RPC array of XML-RPC structures.
 *----------------------------------------------------------------------------*/
void
DisplayPlaylistsMethod :: playlistVectorToXmlRpcValue(
             const Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref playlistVector,
             XmlRpc::XmlRpcValue                            & returnValue)
                                                                      throw ()
{
    returnValue.setSize(playlistVector->size());
                            // a call to setSize() makes sure it's an XML-RPC
                            // array

    std::vector<Ptr<Playlist>::Ref>::const_iterator  it =
                                                     playlistVector->begin();
    int                     arraySize = 0;
    while (it != playlistVector->end()) {
        Ptr<Playlist>::Ref  playlist = *it;
        XmlRpc::XmlRpcValue returnStruct;
        returnStruct["id"]         = (int) (playlist->getId()->getId());
        returnStruct["playlength"] = playlist->getPlaylength()->total_seconds();
        returnValue[arraySize++]   = returnStruct;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Execute the stop XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
DisplayPlaylistsMethod :: execute(XmlRpc::XmlRpcValue  & parameters,
                                  XmlRpc::XmlRpcValue  & returnValue)
                                                                      throw ()
{
    Ptr<StorageClientFactory>::Ref      scf;
    Ptr<StorageClientInterface>::Ref    storage;

    scf     = StorageClientFactory::getInstance();
    storage = scf->getStorageClient();

    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref playlistVector = 
                                               storage->getAllPlaylists();

    playlistVectorToXmlRpcValue(playlistVector, returnValue);
}

