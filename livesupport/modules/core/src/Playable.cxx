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
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include "LiveSupport/Core/AudioClip.h"
#include "LiveSupport/Core/Playlist.h"

#include "LiveSupport/Core/Playable.h"

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return an audio clip pointer to this object.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
Playable :: getAudioClip(void)                                 throw ()
{
    Ptr<AudioClip>::Ref  audioClip;
    if (type == AudioClipType) {
        audioClip = boost::dynamic_pointer_cast<AudioClip,Playable>
                                                (shared_from_this());
    }
    return audioClip;
}


/*------------------------------------------------------------------------------
 *  Return a playlist pointer to this object.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
Playable :: getPlaylist(void)                                  throw ()
{
    Ptr<Playlist>::Ref  playlist;
    if (type == PlaylistType) {
        playlist = boost::dynamic_pointer_cast<Playlist,Playable>
                                                (shared_from_this());
    }
    return playlist;
}

