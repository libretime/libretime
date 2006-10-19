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

#include <sstream>

#include "NumericConstraint.h"

#include "NumericRangeConstraint.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The value of the type attribute for this class.
 *----------------------------------------------------------------------------*/
const std::string       typeAttributeValue = "numericRange";

}


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a constraint element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
NumericRangeConstraint :: configure(const xmlpp::Element &      element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != getConfigElementName()) {
        throw std::invalid_argument("bad configuration element "
                                    + element.get_name());
    }

    const xmlpp::Attribute*     typeAttribute;
    if (!(typeAttribute = element.get_attribute(typeAttributeName))) {
        throw std::invalid_argument("missing attribute " + typeAttributeName);
    }
    std::string                 type = typeAttribute->get_value();

    if (type != typeAttributeValue) {
        throw std::invalid_argument(typeAttributeValue
                                    + " constraint configured with a"
                                    + " constraint element of type "
                                    + type);
    }

    xmlpp::Node::NodeList   childNodes = element.get_children(valueElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it != childNodes.end()) {
        minValue = readNumberFromNode(*it);
    } else {
        throw std::invalid_argument("sub-element not found in constraint");
    }
        
    ++it;
    if (it != childNodes.end()) {
        maxValue = readNumberFromNode(*it);
    } else {
        throw std::invalid_argument("sub-element not found in constraint");
    }
}


/*------------------------------------------------------------------------------
 *  Read a number from an xml element.
 *----------------------------------------------------------------------------*/
NumericRangeConstraint :: ValueType
NumericRangeConstraint :: readNumberFromNode(
                                const xmlpp::Node *     node) const
                                                throw (std::invalid_argument)
{
    const xmlpp::Element *      valueElement 
                                = dynamic_cast<const xmlpp::Element*> (node);
    if (valueElement) {
        Ptr<Glib::ustring>::Ref value(new Glib::ustring(
                                        valueElement->get_child_text()
                                                    ->get_content() ));
        return readNumber(value);
    } else {
        throw std::invalid_argument("bad sub-element found in constraint");
    }
}


/*------------------------------------------------------------------------------
 *  Read a number from a string.
 *----------------------------------------------------------------------------*/
NumericRangeConstraint :: ValueType
NumericRangeConstraint :: readNumber(
                                Ptr<const Glib::ustring>::Ref   value) const
                                                throw (std::invalid_argument)
{
    NumericConstraint   numericConstraint;
    if (!numericConstraint.check(value)) {
        throw std::invalid_argument("bad number found in constraint");
    }
    
    std::istringstream  valueStream(*value);
    ValueType           valueNumber;
    valueStream >> valueNumber;
    
    return valueNumber;
}


/*------------------------------------------------------------------------------
 *  Check that the given value satisfies the constraint.
 *----------------------------------------------------------------------------*/
bool
NumericRangeConstraint :: check(Ptr<const Glib::ustring>::Ref   value) const
                                                throw (std::logic_error)
{
    ValueType   valueNumber = readNumber(value);
        
    if (valueNumber >= minValue && valueNumber <= maxValue) {
        return true;
    } else {
        return false;
    }
}

