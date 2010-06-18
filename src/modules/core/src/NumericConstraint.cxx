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

#include "NumericConstraint.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The value of the type attribute for this class.
 *----------------------------------------------------------------------------*/
const std::string       typeAttributeValue = "numeric";

}


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a constraint element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
NumericConstraint :: configure(const xmlpp::Element &      element)
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
}


/*------------------------------------------------------------------------------
 *  Check that the given value satisfies the constraint.
 *----------------------------------------------------------------------------*/
bool
NumericConstraint :: check(Ptr<const Glib::ustring>::Ref   value) const
                                                throw (std::logic_error)
{
    if (!value) {
        throw std::logic_error("NumericConstraint::check() called with "
                               "a 0 pointer value");
    }
    
    Glib::ustring::const_iterator   it = value->begin();
    
    if (it == value->end()) {       // the empty string is not a number
        return false;
    }
    
    for (; it != value->end(); ++it) {
        if (*it < '0' || *it > '9') {
            return false;
        }
    }
    
    return true;
}

