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

#include "TaskbarIcons.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
const std::string   TaskbarIcons::configElementName = "taskbarIcons";

namespace {

/**
 *  The name of the icon sub-element.
 */
const std::string   iconElementName                 = "icon";

/**
 *  The name of the path attribute.
 */
const std::string   pathAttributeName               = "path";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a taskbar icons object based on an XML element.
 *----------------------------------------------------------------------------*/
void
TaskbarIcons :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementName) {
        throw std::invalid_argument("bad coniguration element "
                                  + element.get_name());
    }

    xmlpp::Node::NodeList childNodes = element.get_children(iconElementName);
    xmlpp::Node::NodeList::const_iterator it;

    for (it = childNodes.begin(); it != childNodes.end(); ++it) {
        const xmlpp::Element *      iconElement
                                    = dynamic_cast<const xmlpp::Element*> (*it);
        xmlpp::Attribute *          pathAttr = iconElement->get_attribute(
                                                        pathAttributeName );
        if (pathAttr) {
            Glib::ustring           path = pathAttr->get_value();
            try {
                Glib::RefPtr<Gdk::Pixbuf>
                                    image = Gdk::Pixbuf::create_from_file(path);
                taskbarIconList.push_back(image);
                
            } catch (Glib::FileError &e) {
                Glib::ustring   errorMsg = "could not open icon image file: ";
                errorMsg += e.what();
                throw std::invalid_argument(errorMsg);
            } catch (Gdk::PixbufError &e) {
                Glib::ustring   errorMsg = "could not create icon image: ";
                errorMsg += e.what();
                throw std::invalid_argument(errorMsg);
            }
        } else {
            throw std::invalid_argument("missing path attribute in "
                                        "taskbarIcons/icon");
        }
    }
}
