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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/TestStorageClient.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/posix_time/posix_time.hpp>

#include "TestStorageClient.h"

using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string TestStorageClient::configElementNameStr = "testStorage";



/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the test storage client.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    // iterate through the playlist elements ...
    xmlpp::Node::NodeList            nodes 
                  = element.get_children(Playlist::getConfigElementName());
    xmlpp::Node::NodeList::iterator  it 
                                     = nodes.begin();
    playlistMap.clear();

    while (it != nodes.end()) {
        Ptr<Playlist>::Ref      playlist(new Playlist);
        const xmlpp::Element  * element =
                                    dynamic_cast<const xmlpp::Element*> (*it);
        playlist->configure(*element);
        playlistMap[playlist->getId()->getId()] = playlist;
        ++it;
    }

    // ... and the the audio clip elements
    nodes = element.get_children(AudioClip::getConfigElementName());
    it    = nodes.begin();
    audioClipMap.clear();

    while (it != nodes.end()) {
        Ptr<AudioClip>::Ref     audioClip(new AudioClip);
        const xmlpp::Element  * element =
                                    dynamic_cast<const xmlpp::Element*> (*it);
        audioClip->configure(*element);
        audioClipMap[audioClip->getId()->getId()] = audioClip;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Tell if a playlist exists.
 *----------------------------------------------------------------------------*/
const bool
TestStorageClient :: existsPlaylist(Ptr<const UniqueId>::Ref id) const
                                                                throw ()
{
    return playlistMap.count(id->getId()) == 1 ? true : false;
}
 

/*------------------------------------------------------------------------------
 *  Return a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
TestStorageClient :: getPlaylist(Ptr<const UniqueId>::Ref id) const
                                                throw (std::invalid_argument)
{
    PlaylistMap::const_iterator   it = playlistMap.find(id->getId());

    if (it == playlistMap.end()) {
        throw std::invalid_argument("no such playlist");
    }

    return it->second;
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist.
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
TestStorageClient :: acquirePlaylist(Ptr<const UniqueId>::Ref id) const
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    Ptr<std::string>::Ref   returnValue(new std::string("/tmp/somefile.xml"));
    return returnValue;
}


/*------------------------------------------------------------------------------
 *  Release a playlist.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: releasePlaylist(Ptr<const UniqueId>::Ref id) const
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    PlaylistMap::const_iterator   it = playlistMap.find(id->getId());

    if (it == playlistMap.end()) {
        throw std::invalid_argument("no such playlist");
    }
    
    Ptr<Playlist>::Ref          playlist = it->second;
    if (playlist->isLocked()) {
        throw std::logic_error("playlist is locked");
    }
    
    bool                        success = true;
    Playlist::const_iterator    playlistIt = playlist->begin();
    while (playlistIt != playlist->end()) {
        try {
            releaseAudioClip(playlistIt->second->getAudioClip()->getId());
        }
        catch (std::invalid_argument &e) {
            success = false;
        }
        ++playlistIt;
    }
    if (!success) {
        throw std::logic_error("some audio clips in playlist do not exist");
    }
}


/*------------------------------------------------------------------------------
 *  Delete a playlist.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: deletePlaylist(Ptr<const UniqueId>::Ref id)
                                                throw (std::invalid_argument)
{
    // erase() returns the number of entries found & erased
    if (!playlistMap.erase(id->getId())) {
        throw std::invalid_argument("no such playlist");
    }
}


/*------------------------------------------------------------------------------
 *  Return a listing of all the playlists in the playlist store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
TestStorageClient :: getAllPlaylists(void) const
                                                throw ()
{
    PlaylistMap::const_iterator         it = playlistMap.begin();
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
                         playlistVector (new std::vector<Ptr<Playlist>::Ref>);

    while (it != playlistMap.end()) {
        playlistVector->push_back(it->second);
        ++it;
    }

    return playlistVector;
}


/*------------------------------------------------------------------------------
 *  Create a new playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
TestStorageClient :: createPlaylist()                                throw ()
{
    // generate a new UniqueId -- TODO: fix UniqueId to make sure
    //     this is really unique; not checked here!
    Ptr<UniqueId>::Ref       playlistId = 
                     Ptr<UniqueId>::Ref(UniqueId :: generateId());

    Ptr<time_duration>::Ref  playLength = 
                     Ptr<time_duration>::Ref(new time_duration(0,0,0));

    Ptr<Playlist>::Ref       playlist =
                     Ptr<Playlist>::Ref(new Playlist(playlistId, playLength));

    playlistMap[playlistId->getId()] = playlist;

    return playlist;   
}


/*------------------------------------------------------------------------------
 *  Tell if an audio clip exists.
 *----------------------------------------------------------------------------*/
const bool
TestStorageClient :: existsAudioClip(Ptr<const UniqueId>::Ref id) const
                                                                throw ()
{
    return audioClipMap.count(id->getId()) == 1 ? true : false;
}
 

/*------------------------------------------------------------------------------
 *  Return an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
TestStorageClient :: getAudioClip(Ptr<const UniqueId>::Ref id) const
                                                throw (std::invalid_argument)
{
    AudioClipMap::const_iterator   it = audioClipMap.find(id->getId());

    if (it == audioClipMap.end()) {
        throw std::invalid_argument("no such audio clip");
    }

    return it->second;
}


/*------------------------------------------------------------------------------
 *  Release an audio clip.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: releaseAudioClip(Ptr<const UniqueId>::Ref id) const
                                                throw (std::invalid_argument)
{
    AudioClipMap::const_iterator   it = audioClipMap.find(id->getId());

    if (it == audioClipMap.end()) {
        throw std::invalid_argument("no such audio clip");
    }
}


/*------------------------------------------------------------------------------
 *  Delete an audio clip.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: deleteAudioClip(Ptr<const UniqueId>::Ref id)
                                                throw (std::invalid_argument)
{
    // erase() returns the number of entries found & erased
    if (!audioClipMap.erase(id->getId())) {
        throw std::invalid_argument("no such audio clip");
    }
}


/*------------------------------------------------------------------------------
 *  Return a listing of all the audio clips in the audio clip store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
TestStorageClient :: getAllAudioClips(void) const
                                                throw ()
{
    AudioClipMap::const_iterator        it = audioClipMap.begin();
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
                        audioClipVector (new std::vector<Ptr<AudioClip>::Ref>);

    while (it != audioClipMap.end()) {
        audioClipVector->push_back(it->second);
        ++it;
    }

    return audioClipVector;
}

