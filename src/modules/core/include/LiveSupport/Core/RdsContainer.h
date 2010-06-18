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
#ifndef LiveSupport_Core_RdsContainer_h
#define LiveSupport_Core_RdsContainer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <map>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"

#include "LiveSupport/Core/RdsItem.h"


namespace LiveSupport {
namespace Core {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Container holding RdsItem objects.
 *
 *  It is used by the OptionContainer class to hold RDS strings
 *  (see http://en.wikipedia.org/wiki/Radio_Data_System).
 *
 *  This object has to be configured with an XML configuration element
 *  called rdsContainer. This may look like the following:
 *
 *  <pre><code>
 *  &lt;rdsContainer&gt;
 *      &lt;rdsItem&gt; ... &lt;/rdsItem&gt;
 *      &lt;rdsItem&gt; ... &lt;/rdsItem&gt;
 *      ...
 *      &lt;rdsItem&gt; ... &lt;/rdsItem&gt;
 *  &lt;/rdsContainer&gt;
 *  </code></pre>
 *
 *  The DTD for the expected XML element is the following:
 *
 *  <pre><code>
 *  <!ELEMENT rdsContainer (rdsItem*) >
 *  </code></pre>
 *
 *  For a description of the rdsItem XML element, see the documentation
 *  of the RdsItem class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see KeyboardShortcut
 */
class RdsContainer : public Configurable
{
    private:
        /**
         *  The name of the configuration XML element used by
         *  RdsContainer.
         */
        static const std::string    configElementName;

        /**
         *  A vector type holding contant KeyboardShortcut references.
         */
        typedef std::vector<Ptr<RdsItem>::Ref>
                                    RdsItemListType;

        /**
         *  The list of all RdsItem references.
         */
        RdsItemListType             rdsItemList;

        /**
         *  An XML document used by toXmlElement().
         */
        Ptr<xmlpp::Document>::Ref   xmlDocument;
        
        /**
         *  Set to true by setRdsString(), and to false by toXmlElement().
         */
        bool                        touched;


    public:
        /**
         *  Constructor.
         */
        RdsContainer()                                 throw ()
            : touched(false)
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~RdsContainer(void)                            throw ()
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
            return configElementName;
        }

        /**
         *  Configure the object based on an XML configuration element.
         *
         *  @param element the XML configuration element.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuration information.
         */
        virtual void
        configure(const xmlpp::Element &element)
                                                throw (std::invalid_argument);

        /**
         *  Set the value of the RDS options.
         *  The key can be any of the RDS data codes, like PS, PI, PTY, RT,
         *  etc.  If there is already a value set for this code, it gets
         *  overwritten, otherwise a new key-value pair is added.
         *
         *  @param      key     which setting to modify.
         *  @param      value   the new value of the RDS setting.
         *  @param      enabled whether this value will be broadcast.
         */
        void
        setRdsOptions(Ptr<const Glib::ustring>::Ref  key,
                      Ptr<const Glib::ustring>::Ref  value,
                      bool                           enabled)       throw ();
        
        /**
         *  Get the value of an RDS string.
         *  The key can be any of the RDS data codes, like PS, PI, PTY, RT,
         *  etc.
         *
         *  @param      key     which setting to modify.
         *  @return     the value of the RDS setting.
         *  @exception  std::invalid_argument   if there is no such RDS option.
         */
        Ptr<const Glib::ustring>::Ref
        getRdsValue(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument);

        /**
         *  Get the enabled/disabled state of an RDS option.
         *
         *  @param      key     which setting to modify.
         *  @return     true if the RDS option is enabled, false otherwise.
         *  @exception  std::invalid_argument   if there is no such RDS option.
         */
        bool
        getRdsEnabled(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument);

        /**
         *  Convert the object to a string.
         *
         *  @return a string which can be sent to the RDS encoder.
         */
        Ptr<Glib::ustring>::Ref
        toString(void)                                              throw ();

        /**
         *  Convert the object to XML.
         *
         *  @return an XML Element, which can be passed to configure()
         *          to create an object identical to this one.
         */
        const xmlpp::Element *
        toXmlElement(void)                                          throw ();

        /**
         *  Tells you whether the object has been touched since the last save.
         *  Starts out false; set to true by setRdsString() and set back to
         *  false by toXmlElement().
         *
         *  @return whether the object has been touched.
         */
        bool
        isTouched(void)                                             throw ()
        {
            return touched;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_RdsContainer_h

