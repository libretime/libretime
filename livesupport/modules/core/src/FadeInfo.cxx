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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/FadeInfo.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/FadeInfo.h"

using namespace boost::posix_time;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string FadeInfo::configElementNameStr = "fadeInfo";

/**
 *  The name of the attribute to get the id of the audio clip.
 */
static const std::string    idAttrName = "id";

/**
 *  The name of the attribute to get the fade in.
 */
static const std::string    fadeInAttrName = "fadeIn";

/**
 *  The name of the attribute to get the fade out.
 */
static const std::string    fadeOutAttrName = "fadeOut";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a fade info object based on an XML element.
 *----------------------------------------------------------------------------*/
void
FadeInfo :: configure(const xmlpp::Element  & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(idAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    id.reset(new UniqueId(attribute->get_value()));

    if (!(attribute = element.get_attribute(fadeInAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    Ptr<std::string>::Ref   fadeInString(new std::string(
                                                    attribute->get_value() ));
    fadeIn = TimeConversion::parseTimeDuration(fadeInString);

    if (!(attribute = element.get_attribute(fadeOutAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    Ptr<std::string>::Ref   fadeOutString(new std::string(
                                                    attribute->get_value() ));
    fadeOut = TimeConversion::parseTimeDuration(fadeOutString);
}


/*------------------------------------------------------------------------------
 *  Return a string containing the essential fields of this object, in XML.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
FadeInfo :: getXmlElementString(void)           throw ()
{
    Ptr<Glib::ustring>::Ref     xmlString(new Glib::ustring);
    
    xmlString->append("<");
    xmlString->append(configElementNameStr + " ");
    xmlString->append(idAttrName + "=\"" 
                                 + std::string(*id) 
                                 + "\" ");
    xmlString->append(fadeInAttrName + "=\"" 
                                     + toFixedString(fadeIn)
                                     + "\" ");
    xmlString->append(fadeOutAttrName + "=\"" 
                                      + toFixedString(fadeOut)
                                      + "\"/>");
    return xmlString;
}

