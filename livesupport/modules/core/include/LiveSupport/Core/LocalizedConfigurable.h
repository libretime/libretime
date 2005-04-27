/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/LocalizedConfigurable.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_LocalizedConfigurable_h
#define LiveSupport_Core_LocalizedConfigurable_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/LocalizedObject.h"


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A configurable element, that is also localized. Reads localization
 *  information from the configuration file itself.
 *
 *  The configure() function expects the following XML element:
 *
 *  <pre><code>
 *  <resourceBundle path   = "path/to/Bundle"
 *                  locale = "en"
 *  />
 *  </code></pre>
 *
 *  <pre><code>
 *  <!DOCTYPE resourceBundle [
 *
 *  <!ELEMENT resourceBundle    EMPTY >
 *  <!ATTLIST resourceBundle    path    CDATA   #REQUIRED >
 *  <!ATTLIST resourceBundle    locale  CDATA   #REQUIRED >
 *
 *  ]>
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class LocalizedConfigurable : public Configurable, public LocalizedObject
{
    private:
        /**
         *  The path to the resource bundles.
         */
        std::string                 bundlePath;


    public:
        /**
         *  The default constructor.
         */
        LocalizedConfigurable(void)                             throw ()
        {
        }

        /**
         *  A virtual destructor.
         */
        virtual
        ~LocalizedConfigurable(void)                            throw ()
        {
        }

        /**
         *  Configure the object based on the XML element supplied.
         *  The supplied element is expected to be of the name
         *  returned by configElementName().
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the object has already
         *             been configured, and can not be reconfigured.
         *  @see LocalizedObject#getBundle
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);


        /**
         *  Change the current locale, which was previously specified by
         *  configure(), to the new locale. This results in a replacement
         *  of the resource bundle, read from the same path as in the
         *  configuration element sent to configure(), but with the new
         *  locale id.
         *
         *  @param newLocale the new locale id.
         *  @exception std::invalid_argument if there is no bundle by
         *             the specified locale
         */
        virtual void
        changeLocale(const std::string      newLocale)
                                                throw (std::invalid_argument);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_LocalizedConfigurable_h

