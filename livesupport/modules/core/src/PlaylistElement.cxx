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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/PlaylistElement.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/Playlist.h"
#include "LiveSupport/Core/PlaylistElement.h"

using namespace boost::posix_time;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string PlaylistElement::configElementNameStr = "playlistElement";

/**
 *  The name of the attribute of the id of the playlist element.
 */
static const std::string    idAttrName = "id";

/**
 *  The name of the attribute of the relative offset of the playlist element.
 */
static const std::string    relativeOffsetAttrName = "relativeOffset";

/**
 *  The name of the audio clip child element of the playlist element.
 */
static const std::string    audioClipElementName = "audioClip";

/**
 *  The name of the playlist child element of the playlist element.
 */
static const std::string    playlistElementName = "playlist";

/**
 *  The name of the fade info child element of the playlist element.
 */
static const std::string    fadeInfoElementName = "fadeInfo";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a playlist element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
PlaylistElement :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    // set id
    const xmlpp::Attribute*     attribute;

    if (!(attribute = element.get_attribute(idAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    id.reset(new UniqueId(attribute->get_value()));

    // set relative offset
    if (!(attribute = element.get_attribute(relativeOffsetAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += relativeOffsetAttrName;
        throw std::invalid_argument(eMsg);
    }
    Ptr<std::string>::Ref   relativeOffsetString(new std::string(
                                                    attribute->get_value() ));
    relativeOffset = TimeConversion::parseTimeDuration(relativeOffsetString);

    // set audio clip
    xmlpp::Node::NodeList       childNodes 
                                = element.get_children(audioClipElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it != childNodes.end()) {
        const xmlpp::Element      * audioClipElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
        type = AudioClipType;
        audioClip.reset(new AudioClip);
        playable = audioClip;
        audioClip->configure(*audioClipElement);        // may throw exception
        
        ++it;
        if (it != childNodes.end()) {
            std::string eMsg = "more than one ";
            eMsg += audioClipElementName;
            eMsg += " XML element";
            throw std::invalid_argument(eMsg);
        }
    } else {
        childNodes  = element.get_children(playlistElementName);
        it          = childNodes.begin();
        if (it != childNodes.end()) {
            const xmlpp::Element      * playlistElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
            type = PlaylistType;
            playlist.reset(new Playlist);
            playable = playlist;
            playlist->configure(*playlistElement);      // may throw exception
            ++it;
            if (it != childNodes.end()) {
                std::string eMsg = "more than one ";
                eMsg += playlistElementName;
                eMsg += " XML element";
                throw std::invalid_argument(eMsg);
            }
        } else {
            std::string eMsg = "missing ";
            eMsg += audioClipElementName;
            eMsg += " or ";
            eMsg += playlistElementName;
            eMsg += " XML element in PlaylistElement configuration";
            throw std::invalid_argument(eMsg);
        }
    }

    // set fade info
    childNodes  = element.get_children(fadeInfoElementName);
    it          = childNodes.begin();

    if (it == childNodes.end()) {                   // no fade info is OK
        return;
    }

    const xmlpp::Element      * fadeInfoElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
    fadeInfo.reset(new FadeInfo);
    fadeInfo->configure(*fadeInfoElement);          // may throw exception
    
    ++it;
    if (it != childNodes.end()) {
        std::string eMsg = "more than one ";
        eMsg += fadeInfoElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Return a string containing the essential fields of this object, in XML.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
PlaylistElement :: getXmlElementString(void)    throw ()
{
    Ptr<Glib::ustring>::Ref     xmlString(new Glib::ustring);
    
    xmlString->append("<");
    xmlString->append(configElementNameStr + " ");
    xmlString->append(idAttrName + "=\"" 
                                 + std::string(*id) 
                                 + "\" ");
    xmlString->append(relativeOffsetAttrName + "=\"" 
                                        + toFixedString(relativeOffset)
                                        + "\">\n");

    xmlString->append(*getPlayable()->getXmlElementString() + "\n");
    if (fadeInfo) {
        xmlString->append(*fadeInfo->getXmlElementString() + "\n");
    }
    xmlString->append("</");
    xmlString->append(configElementNameStr + ">");

    return xmlString;
}

