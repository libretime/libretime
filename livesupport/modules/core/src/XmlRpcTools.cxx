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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/XmlRpcTools.cxx,v $

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
#include "LiveSupport/Core/TimeConversion.h"

#include "LiveSupport/Core/XmlRpcTools.h"


using namespace boost;
using namespace boost::posix_time;

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */

/*------------------------------------------------------------------------------
 *  The name of the playlist ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
static const std::string playlistIdName = "playlistId";

/*------------------------------------------------------------------------------
 *  The name of the audio clip ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
static const std::string audioClipIdName = "audioClipId";

/*------------------------------------------------------------------------------
 *  The name of the relative offset member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
static const std::string relativeOffsetName = "relativeOffset";

/*------------------------------------------------------------------------------
 *  The name of the from member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
static const std::string fromTimeName = "from";

/*------------------------------------------------------------------------------
 *  The name of the to member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
static const std::string toTimeName = "to";

/*------------------------------------------------------------------------------
 *  The name of the playlist id member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
static const std::string scheduleEntryIdName = "scheduleEntryId";

/*------------------------------------------------------------------------------
 *  The name of the playtime member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
static const std::string playtimeName = "playtime";

/*------------------------------------------------------------------------------
 *  The name of the fade in member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
static const std::string fadeInName = "fadeIn";

/*------------------------------------------------------------------------------
 *  The name of the fade out member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
static const std::string fadeOutName = "fadeOut";

/*------------------------------------------------------------------------------
 *  The name of the session ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
static const std::string sessionIdName = "sessionId";


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Extract the schedule entry ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractScheduleEntryId(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(scheduleEntryIdName)
        || xmlRpcValue[scheduleEntryIdName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad schedule entry ID "
                                        "argument");
    }

    Ptr<UniqueId>::Ref id(new UniqueId(std::string(
                                        xmlRpcValue[scheduleEntryIdName] )));
    return id;
}


/*------------------------------------------------------------------------------
 *  Extract the playlist ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractPlaylistId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(playlistIdName)
        || xmlRpcValue[playlistIdName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad playlist ID argument");
    }

    Ptr<UniqueId>::Ref id(new UniqueId(std::string(
                                        xmlRpcValue[playlistIdName] )));
    return id;
}


/*------------------------------------------------------------------------------
 *  Extract the audio clip ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractAudioClipId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(audioClipIdName)
        || xmlRpcValue[audioClipIdName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad audio clip ID argument");
    }

    Ptr<UniqueId>::Ref id(new UniqueId(std::string(
                                        xmlRpcValue[audioClipIdName] )));
    return id;
}


/*------------------------------------------------------------------------------
 *  Extract the relative offset from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
XmlRpcTools :: extractRelativeOffset(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(relativeOffsetName)
        || xmlRpcValue[relativeOffsetName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeInt) {
        throw std::invalid_argument("missing relative offset argument");
    }

    Ptr<time_duration>::Ref relativeOffset(new time_duration(0,0,
                               int(xmlRpcValue[relativeOffsetName]), 0));
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
    xmlRpcValue["id"]         = std::string(*playlist->getId());
    xmlRpcValue["playlength"] = int(playlist->getPlaylength()->total_seconds());
}


/*------------------------------------------------------------------------------
 *  Convert a vector of Playlists into an XML-RPC value.
 *  This function returns an XML-RPC array of XML-RPC structures.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: playlistVectorToXmlRpcValue(
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
        Ptr<Playlist>::Ref    playlist = *it;
        XmlRpc::XmlRpcValue   returnStruct;
        playlistToXmlRpcValue(playlist, returnStruct);
        returnValue[arraySize++]      = returnStruct;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Convert an AudioClip to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: audioClipToXmlRpcValue(
                            Ptr<const AudioClip>::Ref    audioClip,
                            XmlRpc::XmlRpcValue        & xmlRpcValue)
                                                throw ()
{
    xmlRpcValue["id"]         = std::string(*audioClip->getId());
    xmlRpcValue["playlength"] = int(audioClip->getPlaylength()
                                             ->total_seconds());
}


/*------------------------------------------------------------------------------
 *  Convert a vector of AudioClips into an XML-RPC value.
 *  This function returns an XML-RPC array of XML-RPC structures.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: audioClipVectorToXmlRpcValue(
             const Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref audioClipVector,
             XmlRpc::XmlRpcValue                             & returnValue)
                                                throw ()
{
    returnValue.setSize(audioClipVector->size());
                            // a call to setSize() makes sure it's an XML-RPC
                            // array

    std::vector<Ptr<AudioClip>::Ref>::const_iterator  it =
                                                     audioClipVector->begin();
    int                     arraySize = 0;
    while (it != audioClipVector->end()) {
        Ptr<AudioClip>::Ref    audioClip = *it;
        XmlRpc::XmlRpcValue    returnStruct;
        audioClipToXmlRpcValue(audioClip, returnStruct);
        returnValue[arraySize++]        = returnStruct;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Convert an error code, error message pair to an XML-RPC fault response
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: markError(int errorCode, const std::string errorMessage,
                         XmlRpc::XmlRpcValue            & xmlRpcValue)
                                                throw (XmlRpc::XmlRpcException)
{
    throw XmlRpc::XmlRpcException(errorMessage, errorCode);
}


/*------------------------------------------------------------------------------
 *  Convert the valid status of a playlist to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: validStatusToXmlRpcValue(
                            bool validStatus,
                            XmlRpc::XmlRpcValue    & xmlRpcValue)
                                                throw ()
{
    xmlRpcValue["valid"] = XmlRpc::XmlRpcValue(validStatus);
}


/*------------------------------------------------------------------------------
 *  Extract the from time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
XmlRpcTools :: extractFromTime(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(fromTimeName)
        || xmlRpcValue[fromTimeName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeDateTime) {
        throw std::invalid_argument("missing or bad 'from' time in "
                                        "parameter structure");
    }

    struct tm       time = (struct tm) xmlRpcValue[fromTimeName];
    return TimeConversion::tmToPtime(&time);
}


/*------------------------------------------------------------------------------
 *  Extract the to time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
XmlRpcTools :: extractToTime(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(toTimeName)
        || xmlRpcValue[toTimeName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeDateTime) {
        throw std::invalid_argument("missing or bad 'to' time in "
                                        "parameter structure");
    }

    struct tm       time = (struct tm) xmlRpcValue[toTimeName];
    return TimeConversion::tmToPtime(&time);
}


/*------------------------------------------------------------------------------
 *  Convert a boost::posix_time::ptime to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: ptimeToXmlRpcValue(
                            Ptr<const ptime>::Ref   ptimeParam,
                            XmlRpc::XmlRpcValue&    xmlRpcValue)
                                                                throw ()
{
    struct tm           time;
    Ptr<ptime>::Ref     myPtime(new ptime(*ptimeParam));    // get rid of const
    
    TimeConversion::ptimeToTm(myPtime, time);
    xmlRpcValue = XmlRpc::XmlRpcValue(&time);
}

 
/*------------------------------------------------------------------------------
 *  Convert a vector of ScheduleEntries into an XML-RPC value.
 *  This function returns an XML-RPC array of XML-RPC structures.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: scheduleEntriesToXmlRpcValue(
                Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref scheduleEntries,
                XmlRpc::XmlRpcValue                           & returnValue)
                                                                        throw ()
{
    returnValue.setSize(scheduleEntries->size());
                            // a call to setSize() makes sure it's an XML-RPC
                            // array

    std::vector<Ptr<ScheduleEntry>::Ref>::iterator   it
                                                = scheduleEntries->begin();
    int                     arraySize = 0;
    while (it != scheduleEntries->end()) {
        Ptr<ScheduleEntry>::Ref     entry = *it;
        XmlRpc::XmlRpcValue         returnStruct;
        returnStruct["id"]         = std::string(*entry->getId());
        returnStruct["playlistId"] = std::string(*entry->getPlaylistId());

        XmlRpc::XmlRpcValue         time;
        ptimeToXmlRpcValue(entry->getStartTime(), time);
        returnStruct["start"]      = time;

        ptimeToXmlRpcValue(entry->getEndTime(), time);
        returnStruct["end"]        = time;

        returnValue[arraySize++] = returnStruct;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Extract the playtime from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
XmlRpcTools :: extractPlayschedule(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(playtimeName)
        || xmlRpcValue[playtimeName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeDateTime) {
        throw std::invalid_argument("missing or bad playtime in "
                                        "parameter structure");
    }

    struct tm       time = (struct tm) xmlRpcValue[playtimeName];
    return TimeConversion::tmToPtime(&time);
}


/*------------------------------------------------------------------------------
 *  Extract the fade in time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
XmlRpcTools :: extractFadeIn(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(fadeInName)
        || xmlRpcValue[fadeInName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeInt) {
        throw std::invalid_argument("missing or bad 'fade in' argument");
    }

    Ptr<time_duration>::Ref     fadeIn(new time_duration(0,0,
                                        int(xmlRpcValue[fadeInName]), 0));
    return fadeIn;
}


/*------------------------------------------------------------------------------
 *  Extract the fade out time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
XmlRpcTools :: extractFadeOut(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(fadeOutName)
        || xmlRpcValue[fadeOutName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeInt) {
        throw std::invalid_argument("missing or bad 'fade out' argument");
    }

    Ptr<time_duration>::Ref     fadeOut(new time_duration(0,0,
                                        int(xmlRpcValue[fadeOutName]), 0));
    return fadeOut;
}


/*------------------------------------------------------------------------------
 *  Convert a schedule entry ID (a UniqueId) to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: scheduleEntryIdToXmlRpcValue(
                            Ptr<const UniqueId>::Ref    scheduleEntryId,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    returnValue[scheduleEntryIdName] = std::string(*scheduleEntryId);
}


/*------------------------------------------------------------------------------
 *  Convert a PlayLogEntry to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: playLogEntryToXmlRpcValue(
                    Ptr<const PlayLogEntry>::Ref    playLogEntry,
                    XmlRpc::XmlRpcValue           & returnValue)
                                                throw ()
{
    returnValue["audioClipId"]  = std::string(*playLogEntry->getAudioClipId());

    XmlRpc::XmlRpcValue         timestamp;
    ptimeToXmlRpcValue(playLogEntry->getTimestamp(), timestamp);
    returnValue["timestamp"]    =  timestamp;
}


/*------------------------------------------------------------------------------
 *  Convert a vector of PlayLogEntries into an XML-RPC value.
 *  This function returns an XML-RPC array of XML-RPC structures.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: playLogVectorToXmlRpcValue(
             Ptr<const std::vector<Ptr<PlayLogEntry>::Ref> >::Ref
                                    playLogVector,
             XmlRpc::XmlRpcValue  & returnValue)
                                                throw ()
{
    returnValue.setSize(playLogVector->size());
                            // a call to setSize() makes sure it's an XML-RPC
                            // array

    std::vector<Ptr<PlayLogEntry>::Ref>::const_iterator it =
                                                        playLogVector->begin();
    int  arraySize = 0;
    while (it != playLogVector->end()) {
        Ptr<PlayLogEntry>::Ref          playLog = *it;
        XmlRpc::XmlRpcValue             returnStruct;
        playLogEntryToXmlRpcValue(playLog, returnStruct);
        returnValue[arraySize++] =      returnStruct;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Extract the session ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<SessionId>::Ref
XmlRpcTools :: extractSessionId(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(sessionIdName)
        || xmlRpcValue[sessionIdName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad session ID argument");
    }

    Ptr<SessionId>::Ref id(new SessionId(std::string(
                                        xmlRpcValue[sessionIdName] )));
    return id;
}

