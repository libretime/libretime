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
    Version  : $Revision: 1.32 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/TestStorageClient.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif

#include <fstream>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/XmlRpcInvalidArgumentException.h"
#include "LiveSupport/Core/XmlRpcIOException.h"
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

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the temp files
 *----------------------------------------------------------------------------*/
static const std::string    localTempStorageAttrName = "tempFiles";

/*------------------------------------------------------------------------------
 *  The XML version used to create the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    xmlVersion = "1.0";

/*------------------------------------------------------------------------------
 *  The name of the SMIL root node.
 *----------------------------------------------------------------------------*/
static const std::string    smilRootNodeName = "smil";

/*------------------------------------------------------------------------------
 *  The name of the SMIL language description attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilLanguageAttrName = "xmlns";

/*------------------------------------------------------------------------------
 *  The value of the SMIL language description attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilLanguageAttrValue
                            = "http://www.w3.org/2001/SMIL20/Language";

/*------------------------------------------------------------------------------
 *  The name of the SMIL real networks extension attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilExtensionsAttrName = "xmlns:rn";

/*------------------------------------------------------------------------------
 *  The value of the SMIL real networks extension attribute.
 *----------------------------------------------------------------------------*/
static const std::string    smilExtensionsAttrValue
                            = "http://features.real.com/2001/SMIL20/Extensions";

/*------------------------------------------------------------------------------
 *  The name of the body node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilBodyNodeName = "body";

/*------------------------------------------------------------------------------
 *  The name of the sequential audio clip list node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilSeqNodeName = "seq";

/*------------------------------------------------------------------------------
 *  The name of the audio clip element node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilAudioClipNodeName = "audio";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the URI of the audio clip element.
 *----------------------------------------------------------------------------*/
static const std::string    smilAudioClipUriAttrName = "src";

/*------------------------------------------------------------------------------
 *  The name of the sub-playlist element node in the SMIL file.
 *----------------------------------------------------------------------------*/
static const std::string    smilPlaylistNodeName = "audio";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the URI of the sub-playlist element.
 *----------------------------------------------------------------------------*/
static const std::string    smilPlaylistUriAttrName = "src";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the test storage client.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(localTempStorageAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += localTempStorageAttrName;
        throw std::invalid_argument(eMsg);
    }

    localTempStorage = attribute->get_value();

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
        if (audioClip->getUri()) {
            audioClipUris[audioClip->getId()->getId()] = audioClip->getUri();
            Ptr<const std::string>::Ref     nullPointer;
            audioClip->setUri(nullPointer);
        }
        audioClipMap[audioClip->getId()->getId()] = audioClip;
        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Create a new playlist.
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
TestStorageClient :: createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    // generate a new UniqueId -- TODO: fix UniqueId to make sure
    //     this is really unique; not checked here!
    Ptr<UniqueId>::Ref       playlistId = 
                     Ptr<UniqueId>::Ref(UniqueId :: generateId());

    Ptr<time_duration>::Ref  playLength = 
                     Ptr<time_duration>::Ref(new time_duration(0,0,0));

    Ptr<Playlist>::Ref       playlist =
                     Ptr<Playlist>::Ref(new Playlist(playlistId, playLength));

    playlistMap[playlistId->getId()] = playlist;

    return playlist->getId();
}


/*------------------------------------------------------------------------------
 *  Tell if a playlist exists.
 *----------------------------------------------------------------------------*/
const bool
TestStorageClient :: existsPlaylist(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    return playlistMap.count(id->getId()) == 1 ? true : false;
}
 

/*------------------------------------------------------------------------------
 *  Return a playlist to be displayed.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
TestStorageClient :: getPlaylist(Ptr<SessionId>::Ref sessionId,
                                 Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    Ptr<Playlist>::Ref  playlist;

    EditedPlaylistsType::const_iterator
                    editIt = editedPlaylists.find(id->getId());
    if (editIt != editedPlaylists.end()                     // is being edited
        && (*editIt->second->getToken() == sessionId->getId())) {   // by us
        playlist = editIt->second;
    } else {
        PlaylistMapType::const_iterator
                    getIt = playlistMap.find(id->getId());
        if (getIt != playlistMap.end()) {
            playlist.reset(new Playlist(*getIt->second));   // get from storage
        } else {
            throw XmlRpcException("no such playlist");
        }
    }
    
    return playlist;
}

/*------------------------------------------------------------------------------
 *  Return a playlist to be edited.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
TestStorageClient :: editPlaylist(Ptr<SessionId>::Ref sessionId,
                                  Ptr<UniqueId>::Ref  id)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    if (editedPlaylists.find(id->getId()) != editedPlaylists.end()) {
        throw XmlRpcException("playlist is already being edited");
    }
    
    Ptr<Playlist>::Ref      playlist = getPlaylist(sessionId, id);
    Ptr<std::string>::Ref   token(new std::string(sessionId->getId()));
    playlist->setToken(token);

    editedPlaylists[id->getId()] = playlist;
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Save a playlist after editing.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: savePlaylist(Ptr<SessionId>::Ref sessionId,
                                  Ptr<Playlist>::Ref  playlist)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    if (! playlist->getToken()) {
        throw XmlRpcException("savePlaylist() called without editPlaylist()");
    }

    if (sessionId->getId() != *playlist->getToken()) {
        throw XmlRpcException("tried to save playlist in different session"
                              " than the one it was opened in???");
    }
        
    EditedPlaylistsType::iterator
                    editIt = editedPlaylists.find(playlist->getId()->getId());
    
    if ((editIt == editedPlaylists.end()) 
            || (*playlist->getToken() != *editIt->second->getToken())) {
        throw XmlRpcException("savePlaylist() called without editPlaylist()");
    }

    Ptr<std::string>::Ref   nullPointer;
    playlist->setToken(nullPointer);

    PlaylistMapType::iterator
                    storeIt = playlistMap.find(playlist->getId()->getId());

    if (storeIt == playlistMap.end()) {
        throw XmlRpcException("playlist deleted while it was being edited???");
    }
    storeIt->second = playlist;

    editedPlaylists.erase(editIt);
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
TestStorageClient :: acquirePlaylist(Ptr<SessionId>::Ref sessionId,
                                     Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    PlaylistMapType::const_iterator     playlistMapIt 
                                        = playlistMap.find(id->getId());

    if (playlistMapIt == playlistMap.end()) {
        throw XmlRpcInvalidArgumentException("no such playlist");
    }

    Ptr<Playlist>::Ref      oldPlaylist = playlistMapIt->second;
    Ptr<time_duration>::Ref playlength = oldPlaylist->getPlaylength();
    Ptr<Playlist>::Ref      newPlaylist(new Playlist(UniqueId::generateId(),
                                                     playlength));
    Ptr<xmlpp::Document>::Ref
                        smilDocument(new xmlpp::Document(xmlVersion));
    xmlpp::Element    * smilRootNode 
                        = smilDocument->create_root_node(smilRootNodeName);
    smilRootNode->set_attribute(smilLanguageAttrName,
                                smilLanguageAttrValue);
    smilRootNode->set_attribute(smilExtensionsAttrName,
                                smilExtensionsAttrValue);

    xmlpp::Element    * smilBodyNode
                        = smilRootNode->add_child(smilBodyNodeName);
    xmlpp::Element    * smilSeqNode
                        = smilBodyNode->add_child(smilSeqNodeName);
    
    Playlist::const_iterator it = oldPlaylist->begin();

    while (it != oldPlaylist->end()) {
        Ptr<PlaylistElement>::Ref   plElement = it->second;
        Ptr<FadeInfo>::Ref          fadeInfo = plElement->getFadeInfo();

        if (plElement->getType() == PlaylistElement::AudioClipType) {
            Ptr<AudioClip>::Ref audioClip 
                            = acquireAudioClip(sessionId, plElement
                                                          ->getAudioClip()
                                                          ->getId());
            Ptr<time_duration>::Ref relativeOffset
                            = plElement->getRelativeOffset();
            newPlaylist->addAudioClip(audioClip, relativeOffset, fadeInfo);

            xmlpp::Element* smilAudioClipNode
                            = smilSeqNode->add_child(smilAudioClipNodeName);
            smilAudioClipNode->set_attribute(
                            smilAudioClipUriAttrName, 
                            *(audioClip->getUri()) );
            ++it;
        } else if (plElement->getType() == PlaylistElement::PlaylistType) {
            Ptr<Playlist>::Ref playlist 
                            = acquirePlaylist(sessionId, plElement
                                                         ->getPlaylist()
                                                         ->getId());
            Ptr<time_duration>::Ref relativeOffset
                            = plElement->getRelativeOffset();
            newPlaylist->addPlaylist(playlist, relativeOffset, fadeInfo);

            xmlpp::Element* smilPlaylistNode
                            = smilSeqNode->add_child(smilPlaylistNodeName);
            smilPlaylistNode->set_attribute(
                            smilPlaylistUriAttrName, 
                            *(playlist->getUri()) );
            ++it;
        } else {          // this should never happen
            throw XmlRpcInvalidArgumentException(
                                           "unexpected playlist element type "
                                           "(neither audio clip nor playlist)");
        }
        
    }

    std::stringstream fileName;
    fileName << localTempStorage << std::string(*newPlaylist->getId())
             << "-" << std::rand() << ".smil";

    smilDocument->write_to_file(fileName.str(), "UTF-8");
   
    Ptr<std::string>::Ref   playlistUri(new std::string(fileName.str()));
    newPlaylist->setUri(playlistUri);
    return newPlaylist;
}


/*------------------------------------------------------------------------------
 *  Release a playlist.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: releasePlaylist(Ptr<SessionId>::Ref sessionId,
                                     Ptr<Playlist>::Ref  playlist) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    if (! playlist->getUri()) {
        throw XmlRpcInvalidArgumentException("playlist URI not found");
    }
    
    std::ifstream ifs(playlist->getUri()->substr(7).c_str());
    if (!ifs) {                                              // cut of "file://"
        ifs.close();
        throw XmlRpcIOException("playlist temp file not found");
    }
    ifs.close();

    std::remove(playlist->getUri()->substr(7).c_str());
   
    std::string                 eMsg = "";
    Playlist::const_iterator    it   = playlist->begin();
    while (it != playlist->end()) {
        Ptr<PlaylistElement>::Ref   plElement = it->second;
        if (plElement->getType() == PlaylistElement::AudioClipType) {
            try {
                releaseAudioClip(sessionId, it->second->getAudioClip());
            }
            catch (XmlRpcException &e) {
                eMsg += e.what();
                eMsg += "\n";
            }
            ++it;
        } else if (plElement->getType() == PlaylistElement::PlaylistType) {
            try {
                releasePlaylist(sessionId, it->second->getPlaylist());
            }
            catch (XmlRpcException &e) {
                eMsg += e.what();
                eMsg += "\n";
            }
            ++it;
        } else {                      // this should never happen
                eMsg += "unexpected playlist element type\n";
        }        
    }

    Ptr<std::string>::Ref   nullPointer;
    playlist->setUri(nullPointer);

    if (eMsg != "") {
        eMsg.insert(0, "some playlist elements could not be released:\n");
        throw XmlRpcInvalidArgumentException(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Delete a playlist.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: deletePlaylist(Ptr<SessionId>::Ref sessionId,
                                    Ptr<UniqueId>::Ref  id)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    if (editedPlaylists.find(id->getId()) != editedPlaylists.end()) {
        throw XmlRpcException("playlist is being edited");
    }

    // erase() returns the number of entries found & erased
    if (!playlistMap.erase(id->getId())) {
        throw XmlRpcException("no such playlist");
    }
}


/*------------------------------------------------------------------------------
 *  Return a listing of all the playlists in the playlist store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
TestStorageClient :: getAllPlaylists(Ptr<SessionId>::Ref sessionId)
                                                                        const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    PlaylistMapType::const_iterator         it = playlistMap.begin();
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
                         playlistVector (new std::vector<Ptr<Playlist>::Ref>);

    while (it != playlistMap.end()) {
        playlistVector->push_back(it->second);
        ++it;
    }

    return playlistVector;
}


/*------------------------------------------------------------------------------
 *  Tell if an audio clip exists.
 *----------------------------------------------------------------------------*/
const bool
TestStorageClient :: existsAudioClip(Ptr<SessionId>::Ref sessionId,
                                     Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    return audioClipMap.count(id->getId()) == 1 ? true : false;
}
 

/*------------------------------------------------------------------------------
 *  Return an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
TestStorageClient :: getAudioClip(Ptr<SessionId>::Ref sessionId,
                                  Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    AudioClipMapType::const_iterator   it = audioClipMap.find(id->getId());

    if (it == audioClipMap.end()) {
        throw XmlRpcException("no such audio clip");
    }

    Ptr<AudioClip>::Ref     copyOfAudioClip(new AudioClip(*it->second));
    return copyOfAudioClip;
}


/*------------------------------------------------------------------------------
 *  Store an audio clip.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: storeAudioClip(Ptr<SessionId>::Ref sessionId,
                                    Ptr<AudioClip>::Ref audioClip)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    if (!audioClip->getUri()) {
        throw XmlRpcException("audio clip has no URI field");
    }

    if (!audioClip->getId()) {
        audioClip->setId(UniqueId::generateId());
    }

    Ptr<AudioClip>::Ref     copyOfAudioClip(new AudioClip(*audioClip));
    
    audioClipUris[copyOfAudioClip->getId()->getId()] 
                                    = copyOfAudioClip->getUri();
    Ptr<const std::string>::Ref     nullPointer;
    copyOfAudioClip->setUri(nullPointer);

    audioClipMap[copyOfAudioClip->getId()->getId()] = copyOfAudioClip;
}


/*------------------------------------------------------------------------------
 *  Acquire resources for an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
TestStorageClient :: acquireAudioClip(Ptr<SessionId>::Ref sessionId,
                                      Ptr<UniqueId>::Ref  id) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    AudioClipUrisType::const_iterator   it = audioClipUris.find(id->getId());
    
    if (it == audioClipUris.end()) {
        throw XmlRpcException("sound file URI not found for audio clip");
    }
                                                        // cut the "file:" off
    std::string     audioClipFileName = it->second->substr(5);
    
    std::ifstream ifs(audioClipFileName.c_str());
    if (!ifs) {
        ifs.close();
        throw XmlRpcException("could not open sound file");
    }
    ifs.close();

    Ptr<std::string>::Ref  audioClipUri(new std::string("file://"));
    *audioClipUri += get_current_dir_name();        // doesn't work if current
    *audioClipUri += "/";                           // dir = /, but OK for now
    *audioClipUri += audioClipFileName;

    Ptr<AudioClip>::Ref    audioClip = getAudioClip(sessionId, id);
    audioClip->setUri(audioClipUri);
    return audioClip;
}


/*------------------------------------------------------------------------------
 *  Release an audio clip.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: releaseAudioClip(Ptr<SessionId>::Ref sessionId,
                                      Ptr<AudioClip>::Ref audioClip) const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    if (!audioClip->getUri()) {
        throw XmlRpcException("audio clip does not have a URI field");
    }
    
    Ptr<std::string>::Ref   nullPointer;
    audioClip->setUri(nullPointer);
}


/*------------------------------------------------------------------------------
 *  Delete an audio clip.
 *----------------------------------------------------------------------------*/
void
TestStorageClient :: deleteAudioClip(Ptr<SessionId>::Ref sessionId,
                                     Ptr<UniqueId>::Ref  id)
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    // erase() returns the number of entries found & erased
    if (!audioClipMap.erase(id->getId())) {
        throw XmlRpcException("no such audio clip");
    }
}


/*------------------------------------------------------------------------------
 *  Return a listing of all the audio clips in the audio clip store.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
TestStorageClient :: getAllAudioClips(Ptr<SessionId>::Ref sessionId)
                                                                        const
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    AudioClipMapType::const_iterator        it = audioClipMap.begin();
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
                        audioClipVector (new std::vector<Ptr<AudioClip>::Ref>);

    while (it != audioClipMap.end()) {
        audioClipVector->push_back(it->second);
        ++it;
    }

    return audioClipVector;
}


/*------------------------------------------------------------------------------
 *  Search for audio clips or playlists.
 *----------------------------------------------------------------------------*/
int
TestStorageClient :: search(Ptr<SessionId>::Ref      sessionId,
                            Ptr<SearchCriteria>::Ref searchCriteria) 
                                                throw (XmlRpcException)
{
    if (!sessionId) {
        throw XmlRpcException("missing session ID argument");
    }

    int     counter = 0;
    int     first   = searchCriteria->offset;
    int     last;
    if (searchCriteria->limit) {
        last = searchCriteria->offset + searchCriteria->limit;
    } else {
        last = 0;
    }

    audioClipIds.reset(new std::vector<Ptr<UniqueId>::Ref>);
    playlistIds.reset(new  std::vector<Ptr<UniqueId>::Ref>);

    if (searchCriteria->type == "audioclip" || searchCriteria->type == "all") {
        AudioClipMapType::const_iterator    it = audioClipMap.begin();
        while (it != audioClipMap.end()) {
            if (matchesCriteria(it->second, searchCriteria)) {
                if (counter >= first) {
                    audioClipIds->push_back(it->second->getId());
                }
                ++counter;
                if (last && counter >= last) {
                    return (counter - first);
                }
            }
            ++it;
        }
    }

    if (searchCriteria->type == "playlist" || searchCriteria->type == "all") {
        PlaylistMapType::const_iterator    it = playlistMap.begin();
        while (it != playlistMap.end()) {
            if (matchesCriteria(it->second, searchCriteria)) {
                if (counter >= first) {
                    playlistIds->push_back(it->second->getId());
                }
                ++counter;
                if (last && counter >= last) {
                    return (counter - first);
                }
            }
            ++it;
        }
    }
    
    return (counter - first);
}


/*------------------------------------------------------------------------------
 *  See if the Playable instance satisfies the search criteria
 *----------------------------------------------------------------------------*/
bool 
TestStorageClient :: matchesCriteria(Ptr<Playable>::Ref         playable,
                                     Ptr<SearchCriteria>::Ref   criteria)
                                                throw (XmlRpcException)
{
    bool    vetoValue;
    if (criteria->logicalOperator == "and") {
        vetoValue = false;
    } else if (criteria->logicalOperator == "or") {
        vetoValue = true;
    } else {
        std::string eMsg = "unknown logical operator: ";
        eMsg += criteria->type;
        throw XmlRpcException(eMsg);
    }
    
    bool    foundAVetoValue = false;
    
    SearchCriteria::SearchConditionListType::const_iterator 
                                    it = criteria->searchConditions.begin();
    while (it != criteria->searchConditions.end()) {
        if (satisfiesCondition(playable, *it) == vetoValue) {
            foundAVetoValue = true;
            break;
        }
        ++it;
    }
    
    if (foundAVetoValue) {
        return vetoValue;
    } else {
        return !vetoValue;
    }
}


/*------------------------------------------------------------------------------
 *  See if the Playable instance satisfies a single condition
 *----------------------------------------------------------------------------*/
bool 
TestStorageClient :: satisfiesCondition(
                        Ptr<Playable>::Ref                          playable,
                        const SearchCriteria::SearchConditionType & condition)
                                                throw (XmlRpcException)
{
    std::string             name, nameSpace;
    separateNameAndNameSpace(condition.key, name, nameSpace);
    
    Ptr<Glib::ustring>::Ref value = playable->getMetadata(name, nameSpace);
    if (!value) {
        return false;
    }
    
    std::string     op = condition.comparisonOperator;

    if (op == "=") {
        return  (*value == condition.value);
    } else if (op == "partial") {
        return  (value->find(condition.value) != std::string::npos);
    } else if (op == "prefix") {
        return  (value->find(condition.value) == 0);
    } else if (op == "<") {
        return (*value < condition.value);
    } else if (op == "<=") {
        return (*value <= condition.value);
    } else if (op == ">") {
        return (*value > condition.value);
    } else if (op == ">=") {
        return (*value >= condition.value);
    } else {
        std::string eMsg = "unknown comparison operator: ";
        eMsg += op;
        throw XmlRpcException(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Separate a key into the metadata name and its namespace
 *----------------------------------------------------------------------------*/
void
LiveSupport::Storage :: separateNameAndNameSpace(const std::string & key,
                                                 std::string &       name,
                                                 std::string &       nameSpace)
                                                            throw ()
{
    unsigned int    colonPosition = key.find(':');

    if (colonPosition != std::string::npos) {               // there is a colon
        nameSpace   = key.substr(0, colonPosition);
        name        = key.substr(colonPosition+1);
    } else {                                                // no colon found
        nameSpace   = "";
        name        = key;
    }
}

