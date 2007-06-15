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

#include "LiveSupport/Core/RdsItem.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
const std::string           RdsItem::configElementName = "rdsItem";

namespace {

/**
 *  The name of the "key" attribute.
 */
const std::string           keyAttributeName    = "key";

/**
 *  The name of the "value" attribute.
 */
const std::string           valueAttributeName  = "value";

/**
 *  The name of the "enabled" attribute.
 */
const std::string           enabledAttributeName  = "enabled";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create an RDS item object based on an XML element.
 *----------------------------------------------------------------------------*/
void
RdsItem :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementName) {
        throw std::invalid_argument("bad coniguration element "
                                    + element.get_name());
    }

    xmlpp::Attribute *      keyAttribute = element.get_attribute(
                                                        keyAttributeName);
    if (keyAttribute) {
        key.reset(new Glib::ustring(keyAttribute->get_value()));
    } else {
        throw std::invalid_argument("missing " 
                                    + keyAttributeName + " attribute");
    }

    xmlpp::Attribute *      valueAttribute = element.get_attribute(
                                                        valueAttributeName);
    if (valueAttribute) {
        value.reset(new Glib::ustring(valueAttribute->get_value()));
    } else {
        throw std::invalid_argument("missing " 
                                    + valueAttributeName + " attribute");
    }

    xmlpp::Attribute *      enabledAttribute = element.get_attribute(
                                                        enabledAttributeName);
    if (enabledAttribute) {
        Glib::ustring   enabledString = enabledAttribute->get_value();
        if (enabledString == "0") {
            enabled = false;
        } else if (enabledString == "1") {
            enabled = true;
        } else {
            throw std::invalid_argument("bad " 
                                        + enabledAttributeName + " attribute");
        }
    } else {
        throw std::invalid_argument("missing " 
                                    + enabledAttributeName + " attribute");
    }
}


/*------------------------------------------------------------------------------
 *  Convert the object to a string.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
RdsItem :: toString(void)                                           throw ()
{
    Ptr<Glib::ustring>::Ref     rdsString(new Glib::ustring);
    
    if (enabled) {
        rdsString->append(*key);
        rdsString->append("=");
        rdsString->append(*value);
        rdsString->append("\r\n");
    }
    
    return rdsString;
}


/*------------------------------------------------------------------------------
 *  Convert the object to XML.
 *----------------------------------------------------------------------------*/
const xmlpp::Element *
RdsItem :: toXmlElement(void)                                       throw ()
{
    if (!touched && xmlDocument) {
        return xmlDocument->get_root_node();
    }
    
    xmlDocument.reset(new xmlpp::Document());
    xmlpp::Element *    rootNode = xmlDocument->create_root_node(
                                                            configElementName);
    rootNode->set_attribute(keyAttributeName, *key);
    rootNode->set_attribute(valueAttributeName, *value);
    rootNode->set_attribute(enabledAttributeName, enabled ? "1" : "0");
        
    touched = false;
    return rootNode;
}

