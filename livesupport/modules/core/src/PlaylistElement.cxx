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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/PlaylistElement.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>

#include "LiveSupport/Core/UniqueId.h"
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
 *  The name of the (audio clip) child element of the playlist element.
 */
static const std::string    audioClipElementName = "audioClip";

/**
 *  The name of the attribute of the id of the audio clip element.
 */
static const std::string    audioClipIdAttrName = "id";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a playlist object based on an XML element.
 *----------------------------------------------------------------------------*/
void
PlaylistElement :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;
    std::stringstream           strStr;
    UniqueId::IdType            idValue;

    if (!(attribute = element.get_attribute(idAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    strStr.str(attribute->get_value());
    strStr >> idValue;
    id.reset(new UniqueId(idValue));

    if (!(attribute = element.get_attribute(relativeOffsetAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += relativeOffsetAttrName;
        throw std::invalid_argument(eMsg);
    }
    relativeOffset.reset(new time_duration(
                            duration_from_string(attribute->get_value())));

    xmlpp::Node::NodeList       childNodes 
                                = element.get_children(audioClipElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it == childNodes.end()) {
        std::string eMsg = "Missing ";
        eMsg += audioClipElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }

    // this is not really a new allocation, but a new pointer into the inside
    // of the parameter '& element' -- so no delete is needed (nor allowed)
    xmlpp::Element            * audioClipElement 
                                = new xmlpp::Element( (*it)->cobj() );

    if (!(attribute= audioClipElement->get_attribute(audioClipIdAttrName))) {
        std::string eMsg = "Missing ";
        eMsg += audioClipElementName;
        eMsg += "attribute ";
        eMsg += audioClipIdAttrName;
        throw std::invalid_argument(eMsg);
    }

    std::stringstream           audioClipStrStr;
    UniqueId::IdType            audioClipIdValue;
    audioClipStrStr.str(attribute->get_value());
    audioClipStrStr >> audioClipIdValue;
    audioClipId.reset(new UniqueId(audioClipIdValue));

    ++it;
    if (it != childNodes.end()) {
        std::string eMsg = "More than one ";
        eMsg += audioClipElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }
}
