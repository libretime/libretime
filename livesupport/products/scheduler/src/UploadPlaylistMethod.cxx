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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/UploadPlaylistMethod.cxx,v $

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

#include "LiveSupport/Core/StorageClientInterface.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "ScheduleInterface.h"
#include "ScheduleFactory.h"
#include "UploadPlaylistMethod.h"


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
const std::string UploadPlaylistMethod::methodName = "stop";

/*------------------------------------------------------------------------------
 *  The name of the playlist id member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string UploadPlaylistMethod::playlistIdName = "playlistId";

/*------------------------------------------------------------------------------
 *  The name of the playlength member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string UploadPlaylistMethod::playlengthName = "playlength";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the StopXmlRpcMethod and register it right away.
 *----------------------------------------------------------------------------*/
UploadPlaylistMethod :: UploadPlaylistMethod (
                        Ptr<XmlRpc::XmlRpcServer>::Ref xmlRpcServer)   throw()
    : XmlRpc::XmlRpcServerMethod(methodName, xmlRpcServer.get())
{
}


/*------------------------------------------------------------------------------
 *  Extract the UniqueId from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
UploadPlaylistMethod :: extractPlaylistId(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(playlistIdName)) {
        throw std::invalid_argument("no playlist id in parameter structure");
    }

    Ptr<UniqueId>::Ref id(new UniqueId((int) xmlRpcValue[playlistIdName]));
    return id;
}


/*------------------------------------------------------------------------------
 *  Extract the playtime from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
UploadPlaylistMethod :: extractPlayschedule(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(playlengthName)) {
        throw std::invalid_argument("no playlength in parameter structure");
    }

    struct tm     & tm   = (struct tm &) xmlRpcValue[playlengthName];
    time_t          time = mktime(&tm);
    Ptr<ptime>::Ref ptime(new ptime(from_time_t(time)));

    return ptime;
}


/*------------------------------------------------------------------------------
 *  Execute the stop XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethod :: execute( XmlRpc::XmlRpcValue  & parameters,
                                 XmlRpc::XmlRpcValue  & returnValue)
                                                                    throw ()
{
    try {
        Ptr<UniqueId>::Ref  id           = extractPlaylistId(parameters);
        Ptr<ptime>::Ref     playschedule = extractPlayschedule(parameters);

        Ptr<StorageClientFactory>::Ref      scf;
        Ptr<StorageClientInterface>::Ref    storage;

        scf     = StorageClientFactory::getInstance();
        storage = scf->getStorageClient();
 
        if (!storage->existsPlaylist(id)) {
            // TODO: mark error
            returnValue = XmlRpc::XmlRpcValue(false);
            return;
        }

        Ptr<Playlist>::Ref  playlist = storage->getPlaylist(id);
        Ptr<ptime>::Ref     until(new ptime(*playschedule
                                          + *(playlist->getPlaylength())));

        Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
        Ptr<ScheduleInterface>::Ref schedule = sf->getSchedule();

        if (!schedule->isTimeframeAvailable(playschedule, until)) {
            // TODO: mark error;
            returnValue = XmlRpc::XmlRpcValue(false);
            return;
        }

        schedule->schedulePlaylist(playlist, playschedule);

    } catch (std::invalid_argument &e) {
        // TODO: mark error
        returnValue = XmlRpc::XmlRpcValue(false);
        return;
    }

    // TODO
    returnValue = XmlRpc::XmlRpcValue(true);
}

