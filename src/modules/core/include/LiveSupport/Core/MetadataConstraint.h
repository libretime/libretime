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
#ifndef LiveSupport_Core_MetadataConstraint_h
#define LiveSupport_Core_MetadataConstraint_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"


namespace LiveSupport {
namespace Core {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class for representing a constraint on the values of a metadata type.
 *
 *  This is an abstract-cum-factory class for constructing the concrete
 *  constraint subclasses.  You construct and configure this class, which
 *  will transparently construct the concrete subclass desired, and delegate
 *  the actual value checking to it.
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
 *  The type attribute identifies this MetadataConstraint object as belonging
 *  to the subclass NumericRangeConstraint.  Other subclasses are 
 *  NumericConstraint, EnumerationConstraint etc.
 *
 *  Each MetadataType object may contain an optional MetadataConstraint member
 *  object, which restricts the acceptable values for this metadata type.
 *
 *  The DTD for the expected XML element looks like the following:
 *
 *  <pre><code>
 *  <!ELEMENT constraint            (value*)              >
 *  <!ATTLIST constraint    type    NMTOKEN     #REQUIRED >
 *  <!ELEMENT value                 (CDATA)               >
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see MetadataConstraintContainer
 */
class MetadataConstraint : public Configurable
{
    private:
        /**
         *  The name of the configuration XML element used by MetadataConstraint.
         */
        static const std::string        configElementNameStr;

        /**
         *  A reference to a concrete subclass.
         */
        Ptr<MetadataConstraint>::Ref    concreteConstraint;


    protected:
        /**
         *  The name of the type attribute.
         */
        static const std::string        typeAttributeName;

        /**
         *  The name of the configuration element for the constraint values.
         */
        static const std::string        valueElementName;


    public:
        /**
         *  Constructor.
         */
        MetadataConstraint()                                        throw ()
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~MetadataConstraint(void)                                   throw ()
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
            return configElementNameStr;
        }

        /**
         *  Configure the metadata object based on an XML configuration element.
         *
         *  @param  element  the XML configuration element.
         *  @exception std::invalid_argument of the supplied XML element
         *             contains bad configuration information
         */
        virtual void
        configure(const xmlpp::Element &    element)
                                                throw (std::invalid_argument);

        /**
         *  Check that the given value satisfies the constraint.
         *
         *  @param  value   the value to be checked against the constraint.
         *  @return true if the value satisfies the constraint.
         *  @exception  std::logic_error    if the object has not been 
         *                                  configured yet.
         */
        virtual bool
        check(Ptr<const Glib::ustring>::Ref value) const
                                                throw (std::logic_error);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_MetadataConstraint_h

