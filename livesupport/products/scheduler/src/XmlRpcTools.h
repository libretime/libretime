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
#include <vector>
#include <XmlRpcValue.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Playlist.h"
#include "ScheduleEntry.h"


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
 *  @version $Revision: 1.5 $
 */
class XmlRpcTools
{
    private:
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
         *  The name of the from member in the XML-RPC parameter
         *  structure.
         */
        static const std::string        fromTimeName;

        /**
         *  The name of the to member in the XML-RPC parameter
         *  structure.
         */
        static const std::string        toTimeName;

        /**
         *  The name of the entry id member in the XML-RPC parameter
         *  structure.
         */
        static const std::string        scheduleEntryIdName;

        /**
         *  The name of the playtime member in the XML-RPC parameter
         *  structure.
         */
        static const std::string        playtimeName;

        /**
         *  Convert a boost::posix_time::ptime to an XmlRpcValue
         *
         *  @param ptime the ptime to convert
         *  @param xmlRpcValue the output parameter holding the value of
         *         the conversion.
         */
        static void
        ptimeToXmlRpcValue(Ptr<const ptime>::Ref   ptime,
                           XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                                    throw ();


    public:
        /**
         *  Extract the schedule entry id from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a UniqueId that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no UniqueId
         *             in xmlRpcValue
         */
        static Ptr<UniqueId>::Ref
        extractScheduleEntryId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

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
         *  @param xmlRpcValue the output parameter holding the result of
         *         the conversion.
         */
        static void
        playlistToXmlRpcValue(Ptr<const Playlist>::Ref playlist,
                              XmlRpc::XmlRpcValue    & xmlRpcValue)
                                                                     throw ();

        /**
         *  Convert a vector of Playlists to an XML-RPC return value.
         *
         *  @param playlistVector a list of Playlists.
         *  @param returnValue the output parameter holding an XML-RPC
         *         representation of the list of Playlists.
         */
        static void
        playlistVectorToXmlRpcValue(
            const Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref playlistVector,
            XmlRpc::XmlRpcValue                            & returnValue)
                                                                     throw ();

        /**
         *  Convert an AudioClip to an XmlRpcValue
         *
         *  @param audioClip the AudioClip to convert.
         *  @param xmlRpcValue the output parameter holding the result of
         *         the conversion.
         */
        static void
        audioClipToXmlRpcValue(Ptr<const AudioClip>::Ref audioClip,
                               XmlRpc::XmlRpcValue     & xmlRpcValue)
                                                                     throw ();

        /**
         *  Convert a vector of AudioClips to an XML-RPC return value.
         *
         *  @param audioClipVector a list of AudioClips.
         *  @param returnValue the output parameter holding an XML-RPC
         *         representation of the list of Playlists.
         */
        static void
        audioClipVectorToXmlRpcValue(
            const Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref audioClipVector,
            XmlRpc::XmlRpcValue                             & returnValue)
                                                                     throw ();

        /**
         *  Convert an error code, message pair to an XmlRpcValue
         *
         *  @param playlist the Playlist to convert.
         *  @param xmlRpcValue the output parameter holding the result of
         *         the conversion.
         */
        static void
        markError(int errorCode, const std::string errorMessage,
                  XmlRpc::XmlRpcValue            & xmlRpcValue)
                                                                     throw ();

        /**
         *  Convert the valid status of a playlist to an XmlRpcValue
         *
         *  @param validStatus true if the playlist is valid, false otherwise.
         *  @param xmlRpcValue the output parameter holding the result of
         *         the conversion.
         */
        static void
        validStatusToXmlRpcValue(bool validStatus,
                                 XmlRpc::XmlRpcValue    & xmlRpcValue)
                                                                     throw ();

        /**
         *  Extract the from time parameter from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the time value for the from parameter
         *  @exception std::invalid_argument if there was no from parameter
         *             in xmlRpcValue
         */
        static Ptr<boost::posix_time::ptime>::Ref
        extractFromTime(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the to parameter from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the time value for the to parameter
         *  @exception std::invalid_argument if there was no to parameter
         *             in xmlRpcValue
         */
        static Ptr<boost::posix_time::ptime>::Ref
        extractToTime(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the playtime from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the playing time, as stored in the XML-RPC parameter
         *  @exception std::invalid_argument if there was no playtime
         *             in xmlRpcValue
         */
        static Ptr<boost::posix_time::ptime>::Ref
        extractPlayschedule(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Convert a vector of ScheduleEntries to an XML-RPC return value.
         *
         *  @param scheduleEntries a list of ScheduleEntries.
         *  @param returnValue the output parameter holding an XML-RPC
         *         representation of the suppied schedule entires.
         */
        static void
        scheduleEntriesToXmlRpcValue(
                Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref scheduleEntries,
                XmlRpc::XmlRpcValue                           & returnValue)
                                                                    throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // XmlRpcTools_h

