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
    Version  : $Revision: 1.6 $
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

/*------------------------------------------------------------------------------
 *  The name of the from member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string XmlRpcTools::fromTimeName = "from";

/*------------------------------------------------------------------------------
 *  The name of the to member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string XmlRpcTools::toTimeName = "to";

/*------------------------------------------------------------------------------
 *  The name of the playlist id member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string XmlRpcTools::scheduleEntryIdName =
                                                        "scheduleEntryId";

/*------------------------------------------------------------------------------
 *  The name of the playtime member in the XML-RPC parameter
 *  structure.
 *----------------------------------------------------------------------------*/
const std::string XmlRpcTools::playtimeName = "playtime";


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
    if (!xmlRpcValue.hasMember(scheduleEntryIdName)) {
        throw std::invalid_argument("missing schedule entry ID argument");
    }

    Ptr<UniqueId>::Ref id(new UniqueId((int) xmlRpcValue[scheduleEntryIdName]));
    return id;
}


/*------------------------------------------------------------------------------
 *  Extract the playlist ID from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
XmlRpcTools :: extractPlaylistId(XmlRpc::XmlRpcValue & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(playlistIdName)) {
        throw std::invalid_argument("missing playlist ID argument");
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
        throw std::invalid_argument("missing audio clip ID argument");
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
        throw std::invalid_argument("missing relative offset argument");
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
    xmlRpcValue["id"]         = (int) (audioClip->getId()->getId());
    xmlRpcValue["playlength"] = audioClip->getPlaylength()->total_seconds();
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
    if (!xmlRpcValue.hasMember(fromTimeName)) {
        throw std::invalid_argument("no from part in parameter structure");
    }

    struct tm       tm = (struct tm) xmlRpcValue[fromTimeName];
    gregorian::date date(tm.tm_year, tm.tm_mon, tm.tm_mday);
    time_duration   hours(tm.tm_hour, tm.tm_min, tm.tm_sec);
    Ptr<ptime>::Ref ptime(new ptime(date, hours));

    return ptime;
}


/*------------------------------------------------------------------------------
 *  Extract the to time from an XML-RPC function call parameter
 *----------------------------------------------------------------------------*/
Ptr<ptime>::Ref
XmlRpcTools :: extractToTime(
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                throw (std::invalid_argument)
{
    if (!xmlRpcValue.hasMember(toTimeName)) {
        throw std::invalid_argument("no to part in parameter structure");
    }

    struct tm       tm = (struct tm) xmlRpcValue[toTimeName];
    gregorian::date date(tm.tm_year, tm.tm_mon, tm.tm_mday);
    time_duration   hours(tm.tm_hour, tm.tm_min, tm.tm_sec);
    Ptr<ptime>::Ref ptime(new ptime(date, hours));

    return ptime;
}


/*------------------------------------------------------------------------------
 *  Convert a boost::posix_time::ptime to an XmlRpcValue
 *----------------------------------------------------------------------------*/
void
XmlRpcTools :: ptimeToXmlRpcValue(
                            Ptr<const ptime>::Ref   ptime,
                            XmlRpc::XmlRpcValue   & xmlRpcValue)
                                                                throw ()
{
    gregorian::date           date  = ptime->date();
    posix_time::time_duration hours = ptime->time_of_day();
    struct tm                 time;

    time.tm_year  = date.year();
    time.tm_mon   = date.month();
    time.tm_mday  = date.day();
    time.tm_hour  = hours.hours();
    time.tm_min   = hours.minutes();
    time.tm_sec   = hours.seconds();
    // TODO: set tm_wday, tm_yday and tm_isdst fields as well

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
        returnStruct["id"]         = (int) (entry->getId()->getId());
        returnStruct["playlistId"] = (int) (entry->getPlaylistId()->getId());

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
    if (!xmlRpcValue.hasMember(playtimeName)) {
        throw std::invalid_argument("no playtime in parameter structure");
    }

    struct tm       tm = (struct tm) xmlRpcValue[playtimeName];
    gregorian::date date(tm.tm_year, tm.tm_mon, tm.tm_mday);
    time_duration   hours(tm.tm_hour, tm.tm_min, tm.tm_sec);
    Ptr<ptime>::Ref ptime(new ptime(date, hours));

    return ptime;
}

