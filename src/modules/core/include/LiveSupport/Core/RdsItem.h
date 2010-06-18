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
#ifndef LiveSupport_Core_RdsItem_h
#define LiveSupport_Core_RdsItem_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"


namespace LiveSupport {
namespace Core {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class for representing an RDS key - value pair.
 *
 *  This object has to be configured with an XML configuration element
 *  called rdsItem. This may look like the following:
 *
 *  <pre><code>
 *  &lt;rdsItem     key     = "PS"
 *                  value   = "BBC Four"
 *                  enabled = "1" /&gt;
 *  </code></pre>
 *
 *  The possible key values are PS, PI, RT, etc
 *  (see http://en.wikipedia.org/wiki/Radio_Data_System).
 *
 *  There value attribute can be any string.
 *
 *  The enabled attribute is either 0 (disabled) or 1 (enabled).
 *
 *  The DTD for the expected XML element looks like the following:
 *
 *  <pre><code>
 *  &lt;!ELEMENT rdsItem   EMPTY &gt;
 *  &lt;!ATTLIST rdsItem   key      CDATA   #REQUIRED &gt;
 *  &lt;!ATTLIST rdsItem   value    CDATA   #REQUIRED &gt;
 *  &lt;!ATTLIST rdsItem   enabled  CDATA   #REQUIRED &gt;
 *  </code></pre>
 *
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see RdsItemContainer
 */
class RdsItem : public Configurable
{
    private:
        /**
         *  The name of the configuration XML element used by RdsItem.
         */
        static const std::string            configElementName;

        /**
         *  The key for this RDS item.
         */
        Ptr<const Glib::ustring>::Ref       key;

        /**
         *  The value for this RDS item.
         */
        Ptr<const Glib::ustring>::Ref       value;

        /**
         *  The enabled/disabled attribute for this RDS item.
         */
        bool                                enabled;

        /**
         *  An XML document used by toXmlElement().
         */
        Ptr<xmlpp::Document>::Ref           xmlDocument;

        /**
         *  Set to true by setValue(), and to false by toXmlElement().
         */
        bool                                touched;


    public:
        /**
         *  Default constructor.
         */
        RdsItem()                                                   throw ()
            : enabled(false),
              touched(false)
        {
        }

        /**
         *  Constructor which sets the variables.
         */
        RdsItem(Ptr<const Glib::ustring>::Ref   key,
                Ptr<const Glib::ustring>::Ref   value,
                bool                            enabled = false)    throw ()
            : key(key),
              value(value),
              enabled(enabled),
              touched(false)
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~RdsItem(void)                                              throw ()
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
            return configElementName;
        }

        /**
         *  Configure the object based on an XML configuration element.
         *
         *  @param element the XML configuration element.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuration information
         */
        virtual void
        configure(const xmlpp::Element &element)
                                                throw (std::invalid_argument);

        /**
         *  Get the  key.
         *  The key can be a 0 pointer if the object has not been
         *  configured yet.
         *
         *  @return     the key of this RDS item.
         */
        Ptr<const Glib::ustring>::Ref
        getKey(void)                                                throw ()
        {
            return key;
        }

        /**
         *  Get the  value.
         *  The value can be a 0 pointer if the object has not been
         *  configured yet.
         *
         *  @return the key of this RDS item.
         */
        Ptr<const Glib::ustring>::Ref
        getValue(void)                                              throw ()
        {
            return value;
        }

        /**
         *  Set the value.
         *
         *  @param value    the new value of this RDS item.
         */
        void
        setValue(Ptr<const Glib::ustring>::Ref  value)              throw ()
        {
            this->value = value;
            touched = true;
        }
        
        /**
         *  Get the enabled/disabled flag.
         *
         *  @return true if the RDS item is enabled, false if not.
         */
        bool
        getEnabled(void)                                            throw ()
        {
            return enabled;
        }

        /**
         *  Set the enabled/disabled flag.
         *
         *  @param enabled  the new value of the flag.
         */
        void
        setEnabled(bool     enabled)                                throw ()
        {
            this->enabled = enabled;
            touched = true;
        }

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
         *  Starts out false; set to true by setValue() and setEnabled();
         *  set back to false by toXmlElement().
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

#endif // LiveSupport_Core_RdsItem_h

