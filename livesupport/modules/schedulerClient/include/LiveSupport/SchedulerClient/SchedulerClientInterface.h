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
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/include/LiveSupport/SchedulerClient/SchedulerClientInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_SchedulerClient_SchedulerClientInterface_h
#define LiveSupport_SchedulerClient_SchedulerClientInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <vector>
#include "boost/date_time/posix_time/posix_time.hpp"

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/SessionId.h"
#include "LiveSupport/Core/ScheduleEntry.h"
#include "LiveSupport/Core/XmlRpcException.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/AudioClip.h"

namespace LiveSupport {
namespace SchedulerClient {

using namespace boost::posix_time;
using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface to access the scheduler daemon as a client.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.6 $
 */
class SchedulerClientInterface
{
    public:
        /**
         *  Return the XML-RPC host the client connects to.
         *
         *  @return the XML-RPC host the client connects to.
         */
        virtual Ptr<const std::string>::Ref
        getXmlRpcHost(void) const                   throw ()        = 0;

        /**
         *  Return the XML-RPC port the client connects to.
         *
         *  @return the XML-RPC port the client connects to.
         */
        virtual unsigned int
        getXmlRpcPort(void) const                   throw ()        = 0;

        /**
         *  Return the XML-RPC URI prefix used when connecting to the scheduler.
         *
         *  @return the XML-RPC URI prefix.
         */
        virtual Ptr<const std::string>::Ref
        getXmlRpcUriPrefix(void) const              throw ()        = 0;

        /**
         *  Return the version string for the scheduler this client
         *  is connected to.
         *
         *  @return the version string of the scheduler daemon.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<const std::string>::Ref
        getVersion(void)                        throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return the current time at the scheduler server.
         *
         *  @return the current time at the scheduler server.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<const ptime>::Ref
        getSchedulerTime(void)                  throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Schedule a playlist at a given time.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param playlistId the id of the playlist to schedule.
         *  @param playtime the time for which to schedule.
         *  @return the schedule entry id for which the playlist has been
         *          scheduled.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<UniqueId>::Ref
        uploadPlaylist(Ptr<SessionId>::Ref  sessionId,
                       Ptr<UniqueId>::Ref   playlistId,
                       Ptr<ptime>::Ref      playtime)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return the scheduled entries for a specified time interval.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param from the start of the interval, inclusive
         *  @param to the end of the interval, exclusive
         *  @return a vector of the schedule entries for the time period.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
        displaySchedule(Ptr<SessionId>::Ref sessionId,
                        Ptr<ptime>::Ref     from,
                        Ptr<ptime>::Ref     to)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Remove a scheduled item.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param scheduledEntryId the id of the scheduled entry to remove.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual void
        removeFromSchedule(Ptr<SessionId>::Ref  sessionId,
                           Ptr<UniqueId>::Ref   scheduleEntryId)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Add an audio clip to a playlist.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param playlistId the id of the playlist.
         *  @param audioClipId the id of the audio clip.
         *  @param relativeOffset the number of seconds between the start
         *                of the playlist and the start of the audio clip.
         *  @return the unique ID of the newly created playlist element.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<UniqueId>::Ref
        addAudioClipToPlaylist(Ptr<SessionId>::Ref      sessionId,
                               Ptr<UniqueId>::Ref       playlistId,
                               Ptr<UniqueId>::Ref       audioClipId,
                               Ptr<time_duration>::Ref  relativeOffset)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Create a new playlist.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @return the newly created playlist.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<Playlist>::Ref
        createPlaylist(Ptr<SessionId>::Ref      sessionId)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Delete a playlist.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param playlistId the id of the playlist.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual void
        deletePlaylist(Ptr<SessionId>::Ref      sessionId,
                       Ptr<UniqueId>::Ref       playlistId)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return an audio clip.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param audioClipId the id of the audio clip.
         *  @return the audio clip.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<AudioClip>::Ref
        displayAudioClip(Ptr<SessionId>::Ref      sessionId,
                         Ptr<UniqueId>::Ref       audioClipId)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return a list of audio clips.  This method returns the audio
         *  clips found by the latest search() on the storage client.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @return a std::vector of audio clips.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        displayAudioClips(Ptr<SessionId>::Ref      sessionId)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return a playlist.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param playlistId the id of the playlist.
         *  @return the playlist.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<Playlist>::Ref
        displayPlaylist(Ptr<SessionId>::Ref      sessionId,
                        Ptr<UniqueId>::Ref       playlistId)
                                                    throw (XmlRpcException)
                                                                        = 0;

        /**
         *  Return a list of playlists.  This method returns the playlists
         *  found by the latest search() on the storage client.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @return a std::vector of playlists.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        displayPlaylists(Ptr<SessionId>::Ref      sessionId)
                                                    throw (XmlRpcException)
                                                                        = 0;

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace SchedulerClient
} // namespace LiveSupport

#endif // LiveSupport_SchedulerClient_SchedulerClientInterface_h

