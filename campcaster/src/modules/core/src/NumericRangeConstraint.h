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
#ifndef LiveSupport_Core_NumericRangeConstraint_h
#define LiveSupport_Core_NumericRangeConstraint_h

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
 *  A class for representing a metadata constraint which allows numbers between
 *  two given values (inclusive).
 *
 *  This is a concrete subclass of MetadataConstraint.  Do not explicitly
 *  instantiate this class; create a MetadataConstraint object instead, and
 *  configure it with an XML element with the appropriate type attribute.
 *
 *  This object has to be configured with an XML configuration element
 *  called constraint.  This may look like the following:
 *
 *  <pre><code>
 *  <constraint     type = "numericRange">
 *      <value>1</value>
 *      <value>12</value>
 *  </constraint>
 *  </code></pre>
 *
 *  A metadata type with this kind of constraint can only accept (decimal, 
 *  non-negative) integer values, i.e., [0-9]+, which are greater than or
 *  equal to the first value given, and less than or equal to the second
 *  value given.
 *
 *  The DTD for the expected XML element looks like the following:
 *
 *  <pre><code>
 *  <!ELEMENT constraint            (value, value)          >
 *  <!ATTLIST constraint    type    "numericRange"  #FIXED  >
 *  <!ELEMENT value                 (#PCDATA)               >
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see NumericRangeConstraintContainer
 */
class NumericRangeConstraint : public MetadataConstraint
{
    private:
        /**
         *  The integer type used by the constraint.
         */
        typedef unsigned long long      ValueType;

        /**
         *  The smallest value allowed by the constraint.
         */
        ValueType                       minValue;
        
        /**
         *  The largest value allowed by the constraint.
         */
        ValueType                       maxValue;

        /**
         *  Read a number from an XML node.
         *
         *  @param  node        the node containing the number.
         *  @return             the number read from the node.
         *  @exception  std::invalid_argument   if the XML node is not 
         *                                      of the expected form.
         */
        ValueType
        readNumberFromNode(const xmlpp::Node *      node) const
                                                throw (std::invalid_argument);

        /**
         *  Read a number from a string.
         *
         *  @param  value       the string containing the number.
         *  @return             the number read from the string.
         *  @exception  std::invalid_argument   if the string does not contain
         *                                      a number.
         */
        ValueType
        readNumber(Ptr<const Glib::ustring>::Ref    value) const
                                                throw (std::invalid_argument);


    public:
        /**
         *  Constructor.
         */
        NumericRangeConstraint()                                    throw ()
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~NumericRangeConstraint(void)                               throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                                  throw ()
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

#endif // LiveSupport_Core_NumericRangeConstraint_h

