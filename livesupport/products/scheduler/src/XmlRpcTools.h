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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/Attic/XmlRpcTools.h,v $

------------------------------------------------------------------------------*/
#ifndef XmlRpcTools_h
#define XmlRpcTools_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
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
 *  A toolbox for converting between inner representations of classes
 *  and XmlRpcValues.  Used by almost all XmlRpcServerMethod subclasses
 *  in the Scheduler.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.2 $
 */
class XmlRpcTools
{
    public:
        /**
         *  The name of the playlistId member in the XML-RPC parameter
         *  structure given as the input to an XmlRpcServerMethod.
         */
        static const std::string        playlistIdName;

        /**
         *  The name of the playlistId member in the XML-RPC parameter
         *  structure given as the input to an XmlRpcServerMethod.
         */
        static const std::string        audioClipIdName;

        /**
         *  The name of the playlistId member in the XML-RPC parameter
         *  structure given as the input to an XmlRpcServerMethod.
         */
        static const std::string        relativeOffsetName;

        /**
         *  Extract the playlist id from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a UniqueId that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no playlistId
         *             member in xmlRpcValue
         */
        static Ptr<UniqueId>::Ref
        extractPlaylistId(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the audio clip id from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a UniqueId that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no audioClipId
         *             member in xmlRpcValue
         */
        static Ptr<UniqueId>::Ref
        extractAudioClipId(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the relative offset from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a time_duration that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no relativeOffset
         *             member in xmlRpcValue
         */
        static Ptr<time_duration>::Ref
        extractRelativeOffset(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Convert a Playlist to an XmlRpcValue
         *
         *  @param playlist the Playlist to convert.
         *  @param xmlRpcValue the output parameter holding the value of
         *         the conversion.
         */
        static void
        playlistToXmlRpcValue(Ptr<const Playlist>::Ref playlist,
                              XmlRpc::XmlRpcValue    & xmlRpcValue)
                                                                     throw ();

        /**
         *  Convert an error code, message pair to an XmlRpcValue
         *
         *  @param playlist the Playlist to convert.
         *  @param xmlRpcValue the output parameter holding the value of
         *         the conversion.
         */
        static void
        markError(int errorCode, const std::string errorMessage,
                  XmlRpc::XmlRpcValue            & xmlRpcValue)
                                                                     throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // XmlRpcTools_h

