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
    Version  : $Revision: 1.3 $
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

    const xmlpp::Attribute    * attribute;
    std::stringstream           strStr;
    UniqueId::IdType            idValue;

    if (!(attribute = element.get_attribute(idAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    strStr.str(attribute->get_value());
    strStr >> idValue;
    id.reset(new UniqueId(idValue));

    if (!(attribute = element.get_attribute(relativeOffsetAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += relativeOffsetAttrName;
        throw std::invalid_argument(eMsg);
    }
    relativeOffset.reset(new time_duration(
                            duration_from_string(attribute->get_value())));

    xmlpp::Node::NodeList       childNodes 
                                = element.get_children(audioClipElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it == childNodes.end()) {
        std::string eMsg = "missing ";
        eMsg += audioClipElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Element      * audioClipElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
    audioClip.reset(new AudioClip);
    audioClip->configure(*audioClipElement);    // may throw exception
    
    ++it;
    if (it != childNodes.end()) {
        std::string eMsg = "more than one ";
        eMsg += audioClipElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }
}
