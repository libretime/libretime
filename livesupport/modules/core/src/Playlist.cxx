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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/Playlist.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>

#include "LiveSupport/Core/Playlist.h"

using namespace boost::posix_time;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string Playlist::configElementNameStr = "playlist";

/**
 *  The name of the attribute to get the id of the playlist.
 */
static const std::string    idAttrName = "id";

/**
 *  The name of the attribute to get the playlength of the playlist.
 */
static const std::string    playlengthAttrName = "playlength";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a playlist object based on an XML element.
 *----------------------------------------------------------------------------*/
void
Playlist :: configure(const xmlpp::Element    & element)
                                            throw (std::logic_error,
                                                   std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;
    std::stringstream           strStr;
    unsigned long int           idValue;

    if (!(attribute = element.get_attribute(idAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    strStr.str(attribute->get_value());
    strStr >> idValue;
    id.reset(new UniqueId(idValue));

    if (!(attribute = element.get_attribute(playlengthAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    playlength.reset(new time_duration(
                            duration_from_string(attribute->get_value())));
}

