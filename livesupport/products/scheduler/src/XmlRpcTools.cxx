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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/Attic/XmlRpcTools.cxx,v $

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

#include "XmlRpcTools.h"


using namespace boost;
using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */

/*------------------------------------------------------------------------------
 *  The name of the playlist ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string XmlRpcTools::playlistIdName = "playlistId";

/*------------------------------------------------------------------------------
 *  The name of the audio clip ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string XmlRpcTools::audioClipIdName = "audioClipId";

/*------------------------------------------------------------------------------
 *  The name of the relative offset member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string XmlRpcTools::relativeOffsetName = "relativeOffset";


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Extract the playlist ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractPlaylistId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(playlistIdName)) {
        throw std::invalid_argument("missing playlist ID");
    }

    Ptr<UniqueId>::Ref id(new UniqueId((int) xmlRpcValue[playlistIdName]));
    return id;
}


/*------------------------------------------------------------------------------
 *  Extract the audio clip ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractAudioClipId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(audioClipIdName)) {
        throw std::invalid_argument("missing audio clip ID");
    }

    Ptr<UniqueId>::Ref id(new UniqueId((int) xmlRpcValue[audioClipIdName]));
    return id;
}


/*------------------------------------------------------------------------------
 *  Extract the relative offset from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
XmlRpcTools :: extractRelativeOffset(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(relativeOffsetName)) {
        throw std::invalid_argument("missing relative offset");
    }

    Ptr<time_duration>::Ref relativeOffset(new time_duration(0,0,
                               (int) xmlRpcValue[relativeOffsetName], 0));
    return relativeOffset;
}


/*------------------------------------------------------------------------------
 *  Convert a Playlist to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: playlistToXmlRpcValue(
                            Ptr<const Playlist>::Ref    playlist,
                            XmlRpc::XmlRpcValue       & xmlRpcValue)
                                                throw ()
{
    xmlRpcValue["id"]         = (int) (playlist->getId()->getId());
    xmlRpcValue["playlength"] = playlist->getPlaylength()->total_seconds();
}


/*------------------------------------------------------------------------------
 *  Convert an error code, error message pair to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: markError(int errorCode, const std::string errorMessage,
                         XmlRpc::XmlRpcValue            & xmlRpcValue)
                                                throw ()
{
    xmlRpcValue["errorCode"]    = errorCode;
    xmlRpcValue["errorMessage"] = errorMessage;
}
