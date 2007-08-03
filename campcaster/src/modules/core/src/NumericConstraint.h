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
#ifndef LiveSupport_Core_NumericConstraint_h
#define LiveSupport_Core_NumericConstraint_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include "LiveSupport/Core/MetadataConstraint.h"


namespace LiveSupport {
namespace Core {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class for representing a numeric metadata constraint.
 *
 *  This is a concrete subclass of MetadataConstraint.  Do not explicitly
 *  instantiate this class; create a MetadataConstraint object instead, and
 *  configure it with an XML element with the appropriate type attribute.
 *
 *  This object has to be configured with an XML configuration element
 *  called constraint.  This may look like the following:
 *
 *  <pre><code>
 *  <constraint     type = "numeric"/>
 *  </code></pre>
 *
 *  A metadata type with this kind of constraint can only accept (decimal, 
 *  non-negative) integer values, i.e., [0-9]+.
 *
 *  The DTD for the expected XML element looks like the following:
 *
 *  <pre><code>
 *  <!ELEMENT constraint            EMPTY               >
 *  <!ATTLIST constraint    type    "numeric"   #FIXED  >
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see NumericConstraintContainer
 */
class NumericConstraint : public MetadataConstraint
{
    public:
        /**
         *  Constructor.
         */
        NumericConstraint()                                        throw ()
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~NumericConstraint(void)                                   throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                              throw ()
        {
            return MetadataConstraint::getConfigElementName();
        }

        /**
         *  Configure the metadata object based on an XML configuration element.
         *
         *  @param element the XML configuration element.
         *  @exception std::invalid_argument of the supplied XML element
         *             contains bad configuration information
         */
        virtual void
        configure(const xmlpp::Element &element)
                                                throw (std::invalid_argument);

        /**
         *  Check that the given value satisfies the constraint.
         *
         *  @param  value   the value to be checked against the constraint.
         *  @return true if the value satisfies the constraint.
         *  @exception  std::logic_error    if the parameter is a 0 pointer.
         */
        virtual bool
        check(Ptr<const Glib::ustring>::Ref     value) const
                                                    throw (std::logic_error);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_NumericConstraint_h

