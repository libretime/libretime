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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/LocalizedConfigurable.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <unicode/msgfmt.h>

#include "LiveSupport/Core/LocalizedConfigurable.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the attribute to get the path of the resource bundle.
 */
static const std::string    pathAttrName = "path";

/**
 *  The name of the attribute to get the locale of the resource bundle.
 */
static const std::string    localeAttrName = "locale";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Load a resource bunlde based on an XML configuration element.
 *----------------------------------------------------------------------------*/
void
LocalizedConfigurable :: configure(const xmlpp::Element    & element)
                                                 throw (std::invalid_argument,
                                                        std::logic_error)
{
    if (element.get_name() != LocalizedObject::getConfigElementName()) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(pathAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += pathAttrName;
        throw std::invalid_argument(eMsg);
    }
    bundlePath = attribute->get_value();

    if (!(attribute = element.get_attribute(localeAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += localeAttrName;
        throw std::invalid_argument(eMsg);
    }
    std::string locale = attribute->get_value();

    changeLocale(locale);
}


/*------------------------------------------------------------------------------
 *  Change the resource bundle to reflect the specified locale
 *----------------------------------------------------------------------------*/
void
LocalizedConfigurable :: changeLocale(const std::string     newLocale)
                                                throw (std::invalid_argument)
{
    UErrorCode                  status = U_ZERO_ERROR;
    Ptr<ResourceBundle>::Ref    resourceBundle(
                                    new ResourceBundle(bundlePath.c_str(),
                                                       newLocale.c_str(),
                                                       status));
    if (!U_SUCCESS(status)) {
        throw std::invalid_argument("opening resource bundle a failure");
    }

    setBundle(resourceBundle);
}


