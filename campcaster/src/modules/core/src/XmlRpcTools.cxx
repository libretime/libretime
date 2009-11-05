/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

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

namespace {

/*------------------------------------------------------------------------------
 *  The name of the generic ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string idName = "id";

/*------------------------------------------------------------------------------
 *  The name of the playlist ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string playlistIdName = "playlistId";

/*------------------------------------------------------------------------------
 *  The name of the audio clip ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string audioClipIdName = "audioClipId";

/*------------------------------------------------------------------------------
 *  The name of the playlist element ID member in the XML-RPC param structure
 *----------------------------------------------------------------------------*/
const std::string playlistElementIdName = "playlistElementId";

/*------------------------------------------------------------------------------
 *  The name of the relative offset member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string relativeOffsetName = "relativeOffset";

/*------------------------------------------------------------------------------
 *  The name of the relative offset member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string clipStartName = "clipStart";

/*------------------------------------------------------------------------------
 *  The name of the relative offset member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string clipEndName = "clipEnd";

/*------------------------------------------------------------------------------
 *  The name of the relative offset member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string clipLengthName = "clipLength";

/*------------------------------------------------------------------------------
 *  The name of the from member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string fromTimeName = "from";

/*------------------------------------------------------------------------------
 *  The name of the to member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string toTimeName = "to";

/*------------------------------------------------------------------------------
 *  The name of the start member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string startTimeName = "start";

/*------------------------------------------------------------------------------
 *  The name of the end member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string endTimeName = "end";

/*------------------------------------------------------------------------------
 *  The name of the schedule entry id member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string scheduleEntryIdName = "scheduleEntryId";

/*------------------------------------------------------------------------------
 *  The name of the playtime member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string playtimeName = "playtime";

/*------------------------------------------------------------------------------
 *  The name of the fade in member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string fadeInName = "fadeIn";

/*------------------------------------------------------------------------------
 *  The name of the fade out member in the XML-RPC parameter structure.
 *----------------------------------------------------------------------------*/
const std::string fadeOutName = "fadeOut";

/*------------------------------------------------------------------------------
 *  The name of the session ID member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string sessionIdName = "sessionId";

/*------------------------------------------------------------------------------
 *  The name of the login name member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string loginName = "login";

/*------------------------------------------------------------------------------
 *  The name of the password member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string passwordName = "password";

/*------------------------------------------------------------------------------
 *  The name of the search criteria member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string searchCriteriaName = "criteria";

/*------------------------------------------------------------------------------
 *  The name of the token member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string tokenName = "token";

/*------------------------------------------------------------------------------
 *  The name of the backup status member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string backupStatusName = "status";

/*------------------------------------------------------------------------------
 *  The name of the URL member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string urlName = "url";

/*------------------------------------------------------------------------------
 *  The name of the path member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string pathName = "path";

/*------------------------------------------------------------------------------
 *  The name of the fault string member in the XML-RPC parameter structure
 *----------------------------------------------------------------------------*/
const std::string faultStringName = "faultString";

}

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
 *  Extract the generic ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(idName)
        || xmlRpcValue[idName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad ID argument");
    }

    Ptr<UniqueId>::Ref id(new UniqueId(std::string(xmlRpcValue[idName])));
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
 *  Extract the playlist element ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractPlaylistElementId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(playlistElementIdName)
        || xmlRpcValue[playlistElementIdName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad playlist element ID "
                                    "argument");
    }

    Ptr<UniqueId>::Ref id(new UniqueId(std::string(
                                        xmlRpcValue[playlistElementIdName] )));
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
XmlRpcTools :: extractClipStart(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(clipStartName)
        || xmlRpcValue[clipStartName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeInt) {
        throw std::invalid_argument("missing clip start argument");
    }

    Ptr<time_duration>::Ref clipStart(new time_duration(0,0,
                               int(xmlRpcValue[clipStartName]), 0));
    return clipStart;
}

/*------------------------------------------------------------------------------
 *  Extract the relative offset from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
XmlRpcTools :: extractClipEnd(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(clipEndName)
        || xmlRpcValue[clipEndName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeInt) {
        throw std::invalid_argument("missing clip end argument");
    }

    Ptr<time_duration>::Ref clipEnd(new time_duration(0,0,
                               int(xmlRpcValue[clipEndName]), 0));
    return clipEnd;
}

/*------------------------------------------------------------------------------
 *  Extract the relative offset from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
XmlRpcTools :: extractClipLength(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(clipLengthName)
        || xmlRpcValue[clipLengthName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeInt) {
        throw std::invalid_argument("missing clip length argument");
    }

    Ptr<time_duration>::Ref clipLength(new time_duration(0,0,
                               int(xmlRpcValue[clipLengthName]), 0));
    return clipLength;
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
    xmlRpcValue["playlist"] = std::string(*playlist->getXmlDocumentString());
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
    xmlRpcValue["audioClip"] = std::string(*audioClip->getXmlDocumentString());
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
 *  Extract a Playlist from an XML-RPC parameter.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
XmlRpcTools :: extractPlaylist(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    Ptr<Playlist>::Ref  playlist(new Playlist(xmlRpcValue));
                                         // may throw std::invalid_argument
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Extract a vector of Playlists from an XML-RPC parameter.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
XmlRpcTools :: extractPlaylistVector(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (xmlRpcValue.getType() != XmlRpc::XmlRpcValue::TypeArray) {
        throw std::invalid_argument("argument to extractPlaylistVector "
                                    "is not an array");
    }

    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref  playlistVector;
    for (int i=0; i < xmlRpcValue.size(); i++) {
        Ptr<Playlist>::Ref  playlist(new Playlist(xmlRpcValue[i]));
                                         // may throw std::invalid_argument
        playlistVector->push_back(playlist);
    }
    return playlistVector;
}


/*------------------------------------------------------------------------------
 *  Extract an AudioClip from an XML-RPC parameter.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
XmlRpcTools :: extractAudioClip(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    Ptr<AudioClip>::Ref  audioClip(new AudioClip(xmlRpcValue));
                                         // may throw std::invalid_argument
    return audioClip;
}


/*------------------------------------------------------------------------------
 *  Extract a vector of AudioClips from an XML-RPC parameter.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
XmlRpcTools :: extractAudioClipVector(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (xmlRpcValue.getType() != XmlRpc::XmlRpcValue::TypeArray) {
        throw std::invalid_argument("argument to extractAudioClipVector "
                                    "is not an array");
    }

    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref  audioClipVector;
    for (int i=0; i < xmlRpcValue.size(); i++) {
        Ptr<AudioClip>::Ref  audioClip(new AudioClip(xmlRpcValue[i]));
                                         // may throw std::invalid_argument
        audioClipVector->push_back(audioClip);
    }
    return audioClipVector;
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
 *  Extract the 'from' time from an XML-RPC function call parameter
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
 *  Extract the 'to' time from an XML-RPC function call parameter
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
 *  Extract the 'start' time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
XmlRpcTools :: extractStartTime(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(startTimeName)
        || xmlRpcValue[startTimeName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeDateTime) {
        throw std::invalid_argument("missing or bad 'start' time in "
                                        "parameter structure");
    }

    struct tm   time = (struct tm) xmlRpcValue[startTimeName];
    return TimeConversion::tmToPtime(&time);
}


/*------------------------------------------------------------------------------
 *  Extract the 'end' time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
XmlRpcTools :: extractEndTime(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(endTimeName)
        || xmlRpcValue[endTimeName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeDateTime) {
        throw std::invalid_argument("missing or bad 'end' time in "
                                        "parameter structure");
    }

    struct tm   time = (struct tm) xmlRpcValue[endTimeName];
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
        returnStruct[idName]         = std::string(*entry->getId());
        returnStruct[playlistIdName] = std::string(*entry->getPlaylistId());

        XmlRpc::XmlRpcValue         time;
        ptimeToXmlRpcValue(entry->getStartTime(), time);
        returnStruct[startTimeName] = time;

        ptimeToXmlRpcValue(entry->getEndTime(), time);
        returnStruct[endTimeName]   = time;

        returnValue[arraySize++] = returnStruct;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Convert an XML-RPC value, holding an array of schedule entries
 *  to a vector holding the same ScheduleEntry object.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
XmlRpcTools :: extractScheduleEntries(
                                XmlRpc::XmlRpcValue        & xmlRpcValue)
                                                                        throw ()
{
    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
    entries.reset(new std::vector<Ptr<ScheduleEntry>::Ref>());

    int nEntries = xmlRpcValue.size();
    for (int i = 0; i < nEntries; ++i) {
        XmlRpc::XmlRpcValue   & entryValue = xmlRpcValue[i];

        Ptr<UniqueId>::Ref  entryId    = extractId(entryValue);
        Ptr<UniqueId>::Ref  playlistId = extractPlaylistId(entryValue);
        Ptr<ptime>::Ref     start      = extractStartTime(entryValue);
        Ptr<ptime>::Ref     end        = extractEndTime(entryValue);
    
        Ptr<ScheduleEntry>::Ref     entry(new ScheduleEntry(entryId,
                                                            playlistId,
                                                            start,
                                                            end));

        entries->push_back(entry);
    }
    
    return entries;
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
 *  Add a session ID to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: sessionIdToXmlRpcValue(
                            Ptr<const SessionId>::Ref   sessionId,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    returnValue[sessionIdName] = sessionId->getId();
}


/*------------------------------------------------------------------------------
 *  Add a playlist ID to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: playlistIdToXmlRpcValue(
                            Ptr<const UniqueId>::Ref    playlistId,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    returnValue[playlistIdName] = std::string(*playlistId);
}


/*------------------------------------------------------------------------------
 *  Add an audio clip ID to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: audioClipIdToXmlRpcValue(
                            Ptr<const UniqueId>::Ref    audioClipId,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    returnValue[audioClipIdName] = std::string(*audioClipId);
}


/*------------------------------------------------------------------------------
 *  Add a playlist element ID to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: playlistElementIdToXmlRpcValue(
                            Ptr<const UniqueId>::Ref    playlistElementId,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    returnValue[playlistElementIdName] = std::string(*playlistElementId);
}


/*------------------------------------------------------------------------------
 *  Add a playtime value to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: playtimeToXmlRpcValue(
                            Ptr<const ptime>::Ref       playtime,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    XmlRpc::XmlRpcValue         timestamp;
    ptimeToXmlRpcValue(playtime, timestamp);
    returnValue[playtimeName] = timestamp;
}


/*------------------------------------------------------------------------------
 *  Add a 'from' time value to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: fromTimeToXmlRpcValue(
                            Ptr<const ptime>::Ref       from,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    XmlRpc::XmlRpcValue         timestamp;
    ptimeToXmlRpcValue(from, timestamp);
    returnValue[fromTimeName] = timestamp;
}


/*------------------------------------------------------------------------------
 *  Add a 'to' time value to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: toTimeToXmlRpcValue(
                            Ptr<const ptime>::Ref       to,
                            XmlRpc::XmlRpcValue       & returnValue)
                                                throw ()
{
    XmlRpc::XmlRpcValue         timestamp;
    ptimeToXmlRpcValue(to, timestamp);
    returnValue[toTimeName] = timestamp;
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
            Ptr<const std::vector<Ptr<PlayLogEntry>::Ref> >::Ref playLogVector,
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


/*------------------------------------------------------------------------------
 *  Extract the login name from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
XmlRpcTools :: extractLoginName(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(loginName)
        || xmlRpcValue[loginName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad login name argument");
    }

    Ptr<std::string>::Ref login(new std::string(xmlRpcValue[loginName]));
    return login;
}


/*------------------------------------------------------------------------------
 *  Extract the password from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
XmlRpcTools :: extractPassword(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(passwordName)
        || xmlRpcValue[passwordName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad password argument");
    }

    Ptr<std::string>::Ref password(new std::string(
                                        xmlRpcValue[passwordName] ));
    return password;
}


/*------------------------------------------------------------------------------
 *  Extract the search criteria from the XML-RPC parameters.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria>::Ref
XmlRpcTools :: extractSearchCriteria(XmlRpc::XmlRpcValue &  xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(searchCriteriaName)
        || xmlRpcValue[searchCriteriaName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeStruct) {
        throw std::invalid_argument("missing or bad criteria argument");
    }
    XmlRpc::XmlRpcValue         xmlCriteria = xmlRpcValue[searchCriteriaName];

    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria(xmlCriteria));
   
    return criteria;
}


/*------------------------------------------------------------------------------
 *  Convert a SearchCriteria to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: searchCriteriaToXmlRpcValue(
                                Ptr<const SearchCriteria>::Ref  criteria,
                                XmlRpc::XmlRpcValue &           returnValue)
                                                                    throw ()
{
    returnValue[searchCriteriaName] = *criteria;
}


/*------------------------------------------------------------------------------
 *  Extract a token from the XML-RPC parameters.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
XmlRpcTools :: extractToken(XmlRpc::XmlRpcValue &   xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(tokenName)
        || xmlRpcValue[tokenName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad token argument");
    }
    
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring(
                                                xmlRpcValue[tokenName] ));
    return token;
}


/*------------------------------------------------------------------------------
 *  Convert a string token to an XmlRpcValue.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: tokenToXmlRpcValue(
                            Ptr<const Glib::ustring>::Ref    token,
                            XmlRpc::XmlRpcValue &            returnValue)
                                                                    throw ()
{
    returnValue[tokenName] = std::string(*token);
}


/*------------------------------------------------------------------------------
 *  Extract the backup status from the XML-RPC parameters.
 *----------------------------------------------------------------------------*/
AsyncState
XmlRpcTools :: extractBackupStatus(XmlRpc::XmlRpcValue &    xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(backupStatusName)
        || xmlRpcValue[backupStatusName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad status argument");
    }
    
    AsyncState      status = AsyncState::fromBackupString(
                                                xmlRpcValue[backupStatusName]);
    return status;
}


/*------------------------------------------------------------------------------
 *  Convert a StorageClientInterface::AsyncState returned by one
 *  of the backup methods to an XmlRpcValue.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: backupStatusToXmlRpcValue(AsyncState             status,
                                         XmlRpc::XmlRpcValue &  returnValue)
                                                                    throw ()
{
    Ptr<const std::string>::Ref     stringValue = status.toBackupString();
    returnValue[backupStatusName] = *stringValue;
}


/*------------------------------------------------------------------------------
 *  Extract a URL string from the XML-RPC parameters.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
XmlRpcTools :: extractUrl(XmlRpc::XmlRpcValue &     xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(urlName)
        || xmlRpcValue[urlName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad url argument");
    }
    
    Ptr<Glib::ustring>::Ref     url(new Glib::ustring(
                                                xmlRpcValue[urlName] ));
    return url;
}


/*------------------------------------------------------------------------------
 *  Convert a string token to an XmlRpcValue.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: urlToXmlRpcValue(
                            Ptr<const Glib::ustring>::Ref    url,
                            XmlRpc::XmlRpcValue &            returnValue)
                                                                    throw ()
{
    returnValue[urlName] = std::string(*url);
}


/*------------------------------------------------------------------------------
 *  Extract a path string from the XML-RPC parameters.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
XmlRpcTools :: extractPath(XmlRpc::XmlRpcValue &    xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(pathName)
        || xmlRpcValue[pathName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad path argument");
    }
    
    Ptr<Glib::ustring>::Ref     path(new Glib::ustring(
                                                xmlRpcValue[pathName] ));
    return path;
}


/*------------------------------------------------------------------------------
 *  Convert a string token to an XmlRpcValue.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: pathToXmlRpcValue(
                            Ptr<const Glib::ustring>::Ref    path,
                            XmlRpc::XmlRpcValue &            returnValue)
                                                                    throw ()
{
    returnValue[pathName] = std::string(*path);
}


/*------------------------------------------------------------------------------
 *  Extract a fault string from the XML-RPC parameters.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
XmlRpcTools :: extractFaultString(XmlRpc::XmlRpcValue &     xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(faultStringName)
        || xmlRpcValue[faultStringName].getType() 
                                        != XmlRpc::XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing or bad faultString argument");
    }
    
    Ptr<Glib::ustring>::Ref     faultString(new Glib::ustring(
                                                xmlRpcValue[faultStringName] ));
    return faultString;
}


/*------------------------------------------------------------------------------
 *  Convert a fault string to an XmlRpcValue.
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: faultStringToXmlRpcValue(
                            Ptr<const Glib::ustring>::Ref    faultString,
                            XmlRpc::XmlRpcValue &            returnValue)
                                                                    throw ()
{
    returnValue[faultStringName] = std::string(*faultString);
}

