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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/Attic/PlayLogEntry.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>

#include "PlayLogEntry.h"

using namespace boost::posix_time;
using namespace LiveSupport::Core;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string PlayLogEntry::configElementNameStr = "playLogEntry";

/**
 *  The name of the attribute to get the id of the audio clip.
 */
static const std::string    idAttrName = "id";

/**
 *  The name of the attribute to get the ID of the audio clip logged.
 */
static const std::string    audioClipIdAttrName = "audioClipId";

/**
 *  The name of the attribute to get the time the audio clip was played.
 */
static const std::string    timeStampAttrName = "timeStamp";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a play log entry object based on an XML element.
 *----------------------------------------------------------------------------*/
void
PlayLogEntry :: configure(const xmlpp::Element  & element)
                                                  throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;
    std::stringstream           idStrStream;
    unsigned long int           idValue;

    if (!(attribute = element.get_attribute(idAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    idStrStream.str(attribute->get_value());
    idStrStream >> idValue;
    id.reset(new UniqueId(idValue));

    std::stringstream           audioClipIdStrStream;
    unsigned long int           audioClipIdValue;

    if (!(attribute = element.get_attribute(audioClipIdAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += audioClipIdAttrName;
        throw std::invalid_argument(eMsg);
    }
    audioClipIdStrStream.str(attribute->get_value());
    audioClipIdStrStream >> audioClipIdValue;
    audioClipId.reset(new UniqueId(audioClipIdValue));

    if (!(attribute = element.get_attribute(timeStampAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += timeStampAttrName;
        throw std::invalid_argument(eMsg);
    }
    timeStamp.reset(new ptime(time_from_string(attribute->get_value())));
}
