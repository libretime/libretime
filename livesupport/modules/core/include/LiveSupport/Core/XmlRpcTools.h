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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/XmlRpcTools.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_XmlRpcTools_h
#define LiveSupport_Core_XmlRpcTools_h

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
#include <XmlRpcException.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/ScheduleEntry.h"
#include "LiveSupport/Core/PlayLogEntry.h"


namespace LiveSupport {
namespace Core {

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
 *  @author  $Author$
 *  @version $Revision$
 */
class XmlRpcTools
{
    private:

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

        /**
         *  Convert a PlayLogEntry to an XmlRpcValue
         *
         *  @param playLogEntry the PlayLogEntry to convert.
         *  @param xmlRpcValue the output parameter holding the result of
         *         the conversion.
         */
        static void
        playLogEntryToXmlRpcValue(Ptr<const PlayLogEntry>::Ref playLogEntry,
                                  XmlRpc::XmlRpcValue        & returnValue)
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
         *  Extract the generic 'id' from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a UniqueId that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no playlistId
         *             member in xmlRpcValue
         */
        static Ptr<UniqueId>::Ref
        extractId(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the playlist ID from the XML-RPC parameters.
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
         *  Extract the playlist element ID from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a UniqueId that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no playlistElementId
         *             member in xmlRpcValue
         */
        static Ptr<UniqueId>::Ref
        extractPlaylistElementId(XmlRpc::XmlRpcValue  & xmlRpcValue)
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
         *         representation of the list of AudioClips.
         */
        static void
        audioClipVectorToXmlRpcValue(
            const Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref audioClipVector,
            XmlRpc::XmlRpcValue                             & returnValue)
                                                                     throw ();

        /**
         *  Extract a Playlist from an XML-RPC parameter.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the extracted Playlist.
         */
        static Ptr<Playlist>::Ref
        extractPlaylist(XmlRpc::XmlRpcValue    & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract a vector of Playlists from an XML-RPC parameter.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a list of Playlists.
         */
        static Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        extractPlaylistVector(XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract an AudioClip from an XML-RPC parameter.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the extracted AudioClip.
         */
        static Ptr<AudioClip>::Ref
        extractAudioClip(XmlRpc::XmlRpcValue    & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract a vector of AudioClips from an XML-RPC parameter.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a list of AudioClips.
         */
        static Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        extractAudioClipVector(XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Convert an error code, message pair to an XML-RPC fault response.
         *  This is done by throwing an XmlRpc::XmlRpcException.  The client
         *  receives a fault response, and the return value is set to a
         *  { faultCode, faultString } structure holding the error code and 
         *  message.
         *
         *  @param errorCode    the numerical code of the error.
         *  @param errorMessage a short English description of the error.
         *  @param xmlRpcValue  remains here from an earlier version
         *                      TODO: remove this later.
         */
        static void
        markError(int errorCode, const std::string errorMessage,
                  XmlRpc::XmlRpcValue            & xmlRpcValue)
                                                throw (XmlRpc::XmlRpcException);

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
         *  Extract the 'from' time parameter from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the time value for the 'from' parameter
         *  @exception std::invalid_argument if there was no from parameter
         *             in xmlRpcValue
         */
        static Ptr<boost::posix_time::ptime>::Ref
        extractFromTime(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the 'to' parameter from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the time value for the 'to' parameter
         *  @exception std::invalid_argument if there was no to parameter
         *             in xmlRpcValue
         */
        static Ptr<boost::posix_time::ptime>::Ref
        extractToTime(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the 'start' parameter from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the time value for the 'start' parameter
         *  @exception std::invalid_argument if there was no to parameter
         *             in xmlRpcValue
         */
        static Ptr<boost::posix_time::ptime>::Ref
        extractStartTime(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the 'end' parameter from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return the time value for the 'end' parameter
         *  @exception std::invalid_argument if there was no to parameter
         *             in xmlRpcValue
         */
        static Ptr<boost::posix_time::ptime>::Ref
        extractEndTime(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Add a 'from' time value to an XmlRpcValue.
         *
         *  @param from the 'from' time value to add.
         *  @param returnValue an output parameter, which has the
         *         'from' time added after the function returns.
         */
        static void
        fromTimeToXmlRpcValue(
                Ptr<const boost::posix_time::ptime>::Ref    from,
                XmlRpc::XmlRpcValue                       & xmlRpcValue)
                                                                    throw ();

        /**
         *  Add a 'to' time value to an XmlRpcValue.
         *
         *  @param to the 'to' time value to add.
         *  @param returnValue an output parameter, which has the
         *         'to' time added after the function returns.
         */
        static void
        toTimeToXmlRpcValue(
                Ptr<const boost::posix_time::ptime>::Ref    to,
                XmlRpc::XmlRpcValue                       & xmlRpcValue)
                                                                    throw ();

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
         *  Extract the fade in time from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a time_duration that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no fadeIn
         *             member in xmlRpcValue
         */
        static Ptr<time_duration>::Ref
        extractFadeIn(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the fade out time from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a time_duration that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no fadeOut
         *             member in xmlRpcValue
         */
        static Ptr<time_duration>::Ref
        extractFadeOut(XmlRpc::XmlRpcValue  & xmlRpcValue)
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

        /**
         *  Convert an XmlRpcValue array, holding schedule entries,
         *  to a vector of ScheduleEntry object references.
         *
         *  @param xmlRpcValue the XML-RPC array holding the schedule entry
         *         data
         *  @return a vector of ScheduleEntry object references, holding
         *          the same data.
         */
        static Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
        extractScheduleEntries(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                                    throw ();

        /**
         *  Convert a schedule entry ID (a UniqueId) to an XmlRpcValue
         *
         *  @param scheduleEntryId the UniqueId to convert.
         *  @param returnValue the output parameter holding the result of
         *         the conversion.
         */
        static void
        scheduleEntryIdToXmlRpcValue(
                Ptr<const UniqueId>::Ref scheduleEntryId,
                XmlRpc::XmlRpcValue    & returnValue)               throw ();

        /**
         *  Add a session id to an XmlRpcValue
         *
         *  @param sessionId the session id to add to the XmlRpcValue
         *  @param returnValue an output parameter, which has the 
         *         session id added after the function returns.
         */
        static void
        sessionIdToXmlRpcValue(
                Ptr<const SessionId>::Ref sessionId,
                XmlRpc::XmlRpcValue     & returnValue)              throw ();

        /**
         *  Add a playlist ID to an XmlRpcValue
         *
         *  @param playlistId the playlist ID to add to the XmlRpcValue
         *  @param returnValue an output parameter, which has the 
         *         playlist ID added after the function returns.
         */
        static void
        playlistIdToXmlRpcValue(
                Ptr<const UniqueId>::Ref  playlistId,
                XmlRpc::XmlRpcValue     & returnValue)              throw ();

        /**
         *  Add an audio clip ID to an XmlRpcValue
         *
         *  @param audioClipId the audio clip ID to add to the XmlRpcValue
         *  @param returnValue an output parameter, which has the 
         *         audio clip ID added after the function returns.
         */
        static void
        audioClipIdToXmlRpcValue(
                Ptr<const UniqueId>::Ref  audioClipId,
                XmlRpc::XmlRpcValue     & returnValue)              throw ();

        /**
         *  Add a playlist element ID to an XmlRpcValue
         *
         *  @param playlistElementId the playlist element ID 
         *                           to add to the XmlRpcValue
         *  @param returnValue an output parameter, which has the 
         *         playlist element ID added after the function returns.
         */
        static void
        playlistElementIdToXmlRpcValue(
                Ptr<const UniqueId>::Ref  playlistElementId,
                XmlRpc::XmlRpcValue     & returnValue)              throw ();

        /**
         *  Add a playtime value to an XmlRpcValue.
         *
         *  @param playtime the playtime to add to the XmlRpcValue
         *  @param returnValue an output parameter, which has the 
         *         playtime added after the function returns.
         */
        static void
        playtimeToXmlRpcValue(
                Ptr<const boost::posix_time::ptime>::Ref   playtime,
                XmlRpc::XmlRpcValue                      & returnValue)
                                                                    throw ();

        /**
         *  Convert a vector of PlayLogEntries to an XML-RPC return value.
         *
         *  @param playLogVector a list of PlayLogEntries.
         *  @param returnValue the output parameter holding an XML-RPC
         *         representation of the list of PlayLogEntries.
         */
        static void
        playLogVectorToXmlRpcValue(
            Ptr<const std::vector<Ptr<PlayLogEntry>::Ref> >::Ref
                                    playLogVector,
            XmlRpc::XmlRpcValue   & returnValue)
                                                                     throw ();

        /**
         *  Extract the session ID from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a SessionId that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no sessionId
         *             member in xmlRpcValue
         */
        static Ptr<SessionId>::Ref
        extractSessionId(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the login name from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a std::string that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no login
         *             member in xmlRpcValue
         */
        static Ptr<std::string>::Ref
        extractLoginName(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

        /**
         *  Extract the password from the XML-RPC parameters.
         *
         *  @param xmlRpcValue the XML-RPC parameter to extract from.
         *  @return a std::string that was found in the XML-RPC parameter.
         *  @exception std::invalid_argument if there was no sessionId
         *             member in xmlRpcValue
         */
        static Ptr<std::string>::Ref
        extractPassword(XmlRpc::XmlRpcValue  & xmlRpcValue)
                                                throw (std::invalid_argument);

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_XmlRpcTools_h

