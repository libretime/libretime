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

#include "LiveSupport/Core/ScheduleEntry.h"

using namespace boost::posix_time;

using namespace LiveSupport::Core;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the schedule entry element
 *----------------------------------------------------------------------------*/
const std::string ScheduleEntry::scheduleEntryElementName = "scheduleEntry";

/*------------------------------------------------------------------------------
 *  The name of the id attribute in the schedule entry element
 *----------------------------------------------------------------------------*/
const std::string ScheduleEntry::idAttrName = "id";

/*------------------------------------------------------------------------------
 *  The name of the playlist attribute in the schedule entry element
 *----------------------------------------------------------------------------*/
const std::string ScheduleEntry::playlistIdAttrName = "playlistId";

/*------------------------------------------------------------------------------
 *  The name of the startTime attribute in the schedule entry element
 *----------------------------------------------------------------------------*/
const std::string ScheduleEntry::startTimeAttrName = "startTime";

/*------------------------------------------------------------------------------
 *  The name of the endTime attribute in the schedule entry element
 *----------------------------------------------------------------------------*/
const std::string ScheduleEntry::endTimeAttrName = "endTime";

/*------------------------------------------------------------------------------
 *  The name of the playlength attribute in the schedule entry element
 *----------------------------------------------------------------------------*/
const std::string ScheduleEntry::playlengthAttrName = "playlength";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a schedule entry based on a previously exported DOM element
 *----------------------------------------------------------------------------*/
ScheduleEntry :: ScheduleEntry(xmlpp::Element     * element)
                                                 throw (std::invalid_argument)
{
    if (element->get_name() != scheduleEntryElementName) {
        std::string eMsg = "bad configuration element ";
        eMsg += element->get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute = 0;

    if (!(attribute = element->get_attribute(idAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    id = UniqueId::fromDecimalString(attribute->get_value());

    if (!(attribute = element->get_attribute(playlistIdAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    playlistId = UniqueId::fromDecimalString(attribute->get_value().c_str());

    if (!(attribute = element->get_attribute(startTimeAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    startTime.reset(new ptime(from_iso_string(attribute->get_value())));

    if (!(attribute = element->get_attribute(endTimeAttrName))) {
        std::string eMsg = "missing attribute ";
        eMsg += idAttrName;
        throw std::invalid_argument(eMsg);
    }
    endTime.reset(new ptime(from_iso_string(attribute->get_value())));

    playlength.reset(new time_duration(*endTime - *startTime));
}


/*------------------------------------------------------------------------------
 *  Export a schedule entry into a DOM element.
 *----------------------------------------------------------------------------*/
void
ScheduleEntry :: toDom(xmlpp::Element     * element) const          throw ()
{
    xmlpp::Element    * node = element->add_child(scheduleEntryElementName);

    node->set_attribute(idAttrName,         *(id->toDecimalString()));
    node->set_attribute(playlistIdAttrName, *(playlistId->toDecimalString()));
    node->set_attribute(startTimeAttrName,  to_iso_string(*startTime));
    node->set_attribute(endTimeAttrName,    to_iso_string(*endTime));
    node->set_attribute(playlengthAttrName, to_simple_string(*playlength));
}

