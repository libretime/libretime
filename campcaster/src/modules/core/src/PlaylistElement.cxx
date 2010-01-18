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
 */
static const std::string    clipStartAttrName = "clipStart";

/**
 */
static const std::string    clipEndAttrName = "clipEnd";

/**
 */
static const std::string    clipLengthAttrName = "clipLength";

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
 *  The default constructor.
 *----------------------------------------------------------------------------*/
PlaylistElement :: PlaylistElement(void)        throw ()
{
}

/*------------------------------------------------------------------------------
 *  Create a playlist element by specifying all details.
 *----------------------------------------------------------------------------*/
PlaylistElement :: PlaylistElement(Ptr<UniqueId>::Ref       id,
                                   Ptr<time_duration>::Ref  relativeOffset,
                                   Ptr<time_duration>::Ref  clipLength,
                                   Ptr<AudioClip>::Ref      audioClip,
                                   Ptr<FadeInfo>::Ref       fadeInfo)
                                                   throw ()
{
    this->id             = id;
    this->relativeOffset = relativeOffset;
    this->clipLength	 = clipLength;
    this->audioClip      = audioClip;
    this->playable       = audioClip;
    this->fadeInfo       = fadeInfo;
    this->type           = AudioClipType;
	
	setClipStart(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
	setClipEnd(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
}


/*------------------------------------------------------------------------------
 *  Create a new audio clip playlist element, with a new UniqueId,
 *  to be added to a playlist.
 *----------------------------------------------------------------------------*/
PlaylistElement :: PlaylistElement(Ptr<time_duration>::Ref  relativeOffset,
                                   Ptr<time_duration>::Ref  clipLength,
                                   Ptr<AudioClip>::Ref      audioClip,
                                   Ptr<FadeInfo>::Ref       fadeInfo)
                                                   throw ()
{
    this->id             = UniqueId::generateId();
    this->relativeOffset = relativeOffset;
    this->clipLength	 = clipLength;
    this->audioClip      = audioClip;
    this->playable       = audioClip;
    this->fadeInfo       = fadeInfo;
    this->type           = AudioClipType;
	
	setClipStart(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
	setClipEnd(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
}


/*------------------------------------------------------------------------------
 *  Create a new sub-playlist playlist element, with a new UniqueId,
 *  to be added to a playlist.
 *----------------------------------------------------------------------------*/
PlaylistElement :: PlaylistElement(Ptr<time_duration>::Ref  relativeOffset,
                                   Ptr<time_duration>::Ref  clipLength,
                                   Ptr<Playlist>::Ref       playlist,
                                   Ptr<FadeInfo>::Ref       fadeInfo)
                                                   throw ()
{
    this->id             = UniqueId::generateId();
    this->relativeOffset = relativeOffset;
    this->clipLength	 = clipLength;
    this->playlist       = playlist;
    this->playable       = playlist;
    this->fadeInfo       = fadeInfo;
    this->type           = PlaylistType;
	
	setClipStart(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
	setClipEnd(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
}


/*------------------------------------------------------------------------------
 *  A virtual destructor, as this class has virtual functions.
 *----------------------------------------------------------------------------*/
PlaylistElement :: ~PlaylistElement(void)       throw ()
{
}


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

    // set clip start
    if (attribute = element.get_attribute(clipStartAttrName)) {
		Ptr<std::string>::Ref   clipStartString(new std::string(
														attribute->get_value() ));
		clipStart = TimeConversion::parseTimeDuration(clipStartString);
    } else {
		setClipStart(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
	}

    // set clip end
    if (attribute = element.get_attribute(clipEndAttrName)) {
		Ptr<std::string>::Ref   clipEndString(new std::string(
														attribute->get_value() ));
		clipEnd = TimeConversion::parseTimeDuration(clipEndString);
    } else {
		setClipEnd(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
	}

    // set clip length
    if (attribute = element.get_attribute(clipLengthAttrName)) {
		Ptr<std::string>::Ref   clipLengthString(new std::string(
														attribute->get_value() ));
		clipLength = TimeConversion::parseTimeDuration(clipLengthString);
    } else {
		setClipLength(Ptr<time_duration>::Ref(new time_duration(0,0,0,0)));
	}

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
                                        + "\" ");
    xmlString->append(clipStartAttrName + "=\"" 
                                        + toFixedString(clipStart)
                                        + "\" ");
    xmlString->append(clipEndAttrName + "=\"" 
                                        + toFixedString(clipEnd)
                                        + "\" ");
    xmlString->append(clipLengthAttrName + "=\"" 
                                        + toFixedString(clipLength)
                                        + "\">\n");

    xmlString->append(*getPlayable()->getXmlElementString() + "\n");
    if (fadeInfo) {
        xmlString->append(*fadeInfo->getXmlElementString() + "\n");
    }
    xmlString->append("</");
    xmlString->append(configElementNameStr + ">");

    return xmlString;
}

