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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/AudioPlayerFactory.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"
#include "HelixPlayer.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string AudioPlayerFactory::configElementNameStr =
                                                    "audioPlayer";

/*------------------------------------------------------------------------------
 *  The singleton instance of AudioPlayerFactory
 *----------------------------------------------------------------------------*/
Ptr<AudioPlayerFactory>::Ref AudioPlayerFactory::singleton;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to AudioPlayerFactory
 *----------------------------------------------------------------------------*/
Ptr<AudioPlayerFactory>::Ref
AudioPlayerFactory :: getInstance(void)                   throw ()
{
    if (!singleton.get()) {
        singleton.reset(new AudioPlayerFactory());
    }

    return singleton;
}


/*------------------------------------------------------------------------------
 *  Configure the connection manager factory.
 *----------------------------------------------------------------------------*/
void
AudioPlayerFactory :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    audioPlayer.reset();

    // try to look for a SimpleConnectionManager configuration element
    xmlpp::Node::NodeList   nodes = element.get_children(
                                        HelixPlayer::getConfigElementName());
    if (nodes.size() >= 1) {
        const xmlpp::Element  * configElement =
                         dynamic_cast<const xmlpp::Element*> (*(nodes.begin()));
        Ptr<HelixPlayer>::Ref   hp(new HelixPlayer());
        hp->configure(*configElement);
        audioPlayer = hp;
    }

    if (!audioPlayer) {
        throw std::invalid_argument("no audio player factories to configure");
    }
}


