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
#include "NumericRangeConstraint.h"
#include "EnumerationConstraint.h"

#include "LiveSupport/Core/MetadataConstraint.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string   MetadataConstraint::configElementNameStr    = "constraint";

/*------------------------------------------------------------------------------
 *  The name of the type attribute.
 *----------------------------------------------------------------------------*/
const std::string   MetadataConstraint::typeAttributeName       = "type";

/*------------------------------------------------------------------------------
 *  The name of the configuration element for the constraint values.
 *----------------------------------------------------------------------------*/
const std::string   MetadataConstraint::valueElementName        = "value";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a constraint element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
MetadataConstraint :: configure(const xmlpp::Element &      element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        throw std::invalid_argument("bad configuration element "
                                    + element.get_name());
    }

    const xmlpp::Attribute*     typeAttribute;
    if (!(typeAttribute = element.get_attribute(typeAttributeName))) {
        throw std::invalid_argument("missing attribute " + typeAttributeName);
    }
    std::string                 type = typeAttribute->get_value();

    if (type == "numeric") {
        concreteConstraint.reset(new NumericConstraint());
        concreteConstraint->configure(element);
    
    } else if (type == "numericRange") {
        concreteConstraint.reset(new NumericRangeConstraint());
        concreteConstraint->configure(element);
    
    } else if (type == "enumeration") {
        concreteConstraint.reset(new EnumerationConstraint());
        concreteConstraint->configure(element);
    
    } else {
        throw std::invalid_argument("unknown metadata constraint" + type);
    }
}


/*------------------------------------------------------------------------------
 *  Check that the given value satisfies the constraint.
 *----------------------------------------------------------------------------*/
inline bool
MetadataConstraint :: check(Ptr<const Glib::ustring>::Ref   value) const
                                                throw (std::logic_error)
{
    if (concreteConstraint) {
        return concreteConstraint->check(value);
    } else {
        throw std::logic_error("MetadataConstraint not configured yet");
    }
}

