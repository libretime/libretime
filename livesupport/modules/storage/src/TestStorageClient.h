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
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/src/TestStorageClient.h,v $

------------------------------------------------------------------------------*/
#ifndef TestStorageClient_h
#define TestStorageClient_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/StorageClientInterface.h"


namespace LiveSupport {
namespace Storage {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A dummy storage client, only used for test purposes.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.8 $
 */
class TestStorageClient :
                    virtual public Configurable,
                    virtual public StorageClientInterface
{
    private:
        /**
         *  The name of the configuration XML elmenent used by TestStorageClient
         */
        static const std::string    configElementNameStr;

        /**
         *  The map type containing the playlists by their ids.
         */
        typedef std::map<const UniqueId::IdType, Ptr<Playlist>::Ref>
                                                                PlaylistMap;

        /**
         *  The map holding all contained playlists, by ids.
         */
        PlaylistMap                 playlistMap;

        /**
         *  The map type containing the audio clips by their ids.
         */
        typedef std::map<const UniqueId::IdType, Ptr<AudioClip>::Ref>
                                                                AudioClipMap;

        /**
         *  The map holding all contained audio clips, by ids.
         */
        AudioClipMap                audioClipMap;


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~TestStorageClient(void)                        throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                      throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Configure the object based on the XML element supplied.
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the scheduler daemon has already
         *             been configured, and can not be reconfigured.
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  Tell if a playlist with a given id exists.
         *
         *  @param id the id of the playlist to check for.
         *  @return true if a playlist with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsPlaylist(Ptr<const UniqueId>::Ref id) const
                                                                throw ();

        /**
         *  Return a playlist with the specified id.
         *
         *  @param id the id of the playlist to return.
         *  @return the requested playlist.
         *  @exception std::invalid_argument if no playlist with the specified
         *             id exists.
         */
        virtual Ptr<Playlist>::Ref
        getPlaylist(Ptr<const UniqueId>::Ref id) const
                                            throw (std::invalid_argument);

        /**
         *  Release the lock on a playlist with the specified id.
         *  At this point, this does not do anything.
         *
         *  @param id the id of the playlist to release.
         *  @exception std::invalid_argument if no playlist with the specified
         *             specified id exists. 
         */
        virtual void
        releasePlaylist(Ptr<const UniqueId>::Ref id) const
                                            throw (std::invalid_argument,
                                                   std::logic_error);

        /**
         *  Delete the playlist with the specified id.
         *
         *  @param id the id of the playlist to be deleted.
         *  @exception std::invalid_argument if no playlist with the specified
         *             id exists.
         */
        virtual void
        deletePlaylist(Ptr<const UniqueId>::Ref id)
                                            throw (std::invalid_argument);

        /**
         *  Return a list of all playlists in the playlist store.
         *
         *  @return a vector containing the playlists.
         */
        virtual Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
        getAllPlaylists(void) const         throw ();

        /**
         *  Create a new playlist.
         *
         *  @return the newly created playlist.
         */
        virtual Ptr<Playlist>::Ref
        createPlaylist()                    throw ();

        /**
         *  Tell if an audio clip with a given id exists.
         *
         *  @param id the id of the audio clip to check for.
         *  @return true if an audio clip with the specified id exists,
         *          false otherwise.
         */
        virtual const bool
        existsAudioClip(Ptr<const UniqueId>::Ref id) const
                                            throw ();

        /**
         *  Return an audio clip with the specified id.
         *
         *  @param id the id of the audio clip to return.
         *  @return the requested audio clip.
         *  @exception std::invalid_argument if no audio clip with the 
         *             specified id exists.
         */
        virtual Ptr<AudioClip>::Ref
        getAudioClip(Ptr<const UniqueId>::Ref id) const
                                            throw (std::invalid_argument);

        /**
         *  Release the lock on an audio clip with the specified id.
         *  At this point, this does not do anything.
         *
         *  @param id the id of the audio clip to release.
         *  @exception std::invalid_argument if no audio clip with the 
         *             specified id exists. 
         */
        virtual void
        releaseAudioClip(Ptr<const UniqueId>::Ref id) const
                                            throw (std::invalid_argument);

        /**
         *  Delete the audio clip with the specified id.
         *
         *  @param id the id of the audio clip to be deleted.
         *  @exception std::invalid_argument if no audio clip with the 
         *             specified id exists.
         */
        virtual void
        deleteAudioClip(Ptr<const UniqueId>::Ref id)
                                            throw (std::invalid_argument);

        /**
         *  Return a list of all audio clips in the playlist store.
         *
         *  @return a vector containing the audio clips.
         */
        virtual Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
        getAllAudioClips(void) const         throw ();

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // TestStorageClient_h

