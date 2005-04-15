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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/LocalizedObject.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <unicode/msgfmt.h>

#include "LiveSupport/Core/LocalizedObject.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string LocalizedObject::configElementNameStr = "resourceBundle";

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
Ptr<ResourceBundle>::Ref
LocalizedObject :: getBundle(const xmlpp::Element     & element)
                                            throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
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
    std::string path = attribute->get_value();

    if (!(attribute = element.get_attribute(localeAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += localeAttrName;
        throw std::invalid_argument(eMsg);
    }
    std::string locale = attribute->get_value();

    UErrorCode                  status = U_ZERO_ERROR;
    Ptr<ResourceBundle>::Ref    resourceBundle(
                                    new ResourceBundle(path.c_str(),
                                                       locale.c_str(),
                                                       status));
    if (!U_SUCCESS(status)) {
        throw std::invalid_argument("opening resource bundle a failure");
    }

    return resourceBundle;
}


/*------------------------------------------------------------------------------
 *  Get a resource bundle by the specified key
 *----------------------------------------------------------------------------*/
Ptr<ResourceBundle>::Ref
LocalizedObject :: getBundle(const char  * key)
                                                throw (std::invalid_argument)
{
    UErrorCode                  status = U_ZERO_ERROR;
    Ptr<ResourceBundle>::Ref    resourceBundle(new ResourceBundle(
                                        bundle->getWithFallback(key, status)));
    if (!U_SUCCESS(status)) {
        throw std::invalid_argument("can't get resource bundle");
    }

    return resourceBundle;
}


/*------------------------------------------------------------------------------
 *  Get a string from a resource bundle un Glib ustring format
 *----------------------------------------------------------------------------*/
Ptr<UnicodeString>::Ref
LocalizedObject :: getResourceString(const char * key)
                                                throw (std::invalid_argument)
{
    Ptr<ResourceBundle>::Ref    rb = getBundle(key);
    if (rb->getType() == URES_STRING) {
        UErrorCode                status = U_ZERO_ERROR;
        Ptr<UnicodeString>::Ref   str(new UnicodeString(rb->getString(status)));
        if (!U_SUCCESS(status)) {
            throw std::invalid_argument("requested resource not a string");
        }

        return str;
    } else {
        throw std::invalid_argument("requested resource not a string");
    }
}


/*------------------------------------------------------------------------------
 *  Format a message
 *----------------------------------------------------------------------------*/
Ptr<UnicodeString>::Ref
LocalizedObject :: formatMessage(Ptr<const UnicodeString>::Ref   pattern,
                                 Formattable                   * arguments,
                                 unsigned int                    nArguments)
                                            throw (std::invalid_argument)
{
    Ptr<UnicodeString>::Ref     message(new UnicodeString());
    UErrorCode                  err = U_ZERO_ERROR;
    MessageFormat::format(*pattern, arguments, nArguments, *message, err);
    if (!U_SUCCESS(err)) {
        throw std::invalid_argument("can't format string");
    }

    return message;
}


/*------------------------------------------------------------------------------
 *  Format a message, based on a resource key for its pattern
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
LocalizedObject :: formatMessage(const char       * patternKey,
                                 Formattable      * arguments,
                                 unsigned int       nArguments)
                                            throw (std::invalid_argument)
{
    return unicodeStringToUstring(
           formatMessage(getResourceString(patternKey), arguments, nArguments));
}


/*------------------------------------------------------------------------------
 *  Format a message, based on a resource key for its pattern
 *  and one argument for formatting.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
LocalizedObject :: formatMessage(const std::string    & patternKey,
                                 const Glib::ustring  & argument1)
                                                throw (std::invalid_argument)
{
    Ptr<UnicodeString>::Ref     uArgument1 = ustringToUnicodeString(argument1);
    Formattable                 arguments[] = { *uArgument1 };

    return formatMessage(patternKey, arguments, 1);
}


/*------------------------------------------------------------------------------
 *  Format a message, based on a resource key for its pattern
 *  and two arguments for formatting.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
LocalizedObject :: formatMessage(const std::string    & patternKey,
                                 const Glib::ustring  & argument1,
                                 const Glib::ustring  & argument2)
                                                throw (std::invalid_argument)
{
    Ptr<UnicodeString>::Ref     uArgument1 = ustringToUnicodeString(argument1);
    Ptr<UnicodeString>::Ref     uArgument2 = ustringToUnicodeString(argument2);
    Formattable                 arguments[] = { *uArgument1,
                                                *uArgument2 };

    return formatMessage(patternKey, arguments, 2);
}


/*------------------------------------------------------------------------------
 *  Format a message, based on a resource key for its pattern
 *  and one argument for formatting.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
LocalizedObject :: formatMessage(const std::string    & patternKey,
                                 const Glib::ustring  & argument1,
                                 const Glib::ustring  & argument2,
                                 const Glib::ustring  & argument3)
                                                throw (std::invalid_argument)
{
    Ptr<UnicodeString>::Ref     uArgument1 = ustringToUnicodeString(argument1);
    Ptr<UnicodeString>::Ref     uArgument2 = ustringToUnicodeString(argument2);
    Ptr<UnicodeString>::Ref     uArgument3 = ustringToUnicodeString(argument3);
    Formattable                 arguments[] = { *uArgument1,
                                                *uArgument2,
                                                *uArgument3 };
    return formatMessage(patternKey, arguments, 3);
}


/*------------------------------------------------------------------------------
 *  Create a Glib ustring from an ICU UnicodeString
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
LocalizedObject :: unicodeStringToUstring(
                            Ptr<const UnicodeString>::Ref   unicodeString)
                                                                    throw ()
{
    const UChar   * uchars = unicodeString->getBuffer();
    int32_t         length = unicodeString->length();
    Ptr<Glib::ustring>::Ref    ustr(new Glib::ustring());
    ustr->reserve(length);

    while (length--) {
        ustr->push_back((gunichar) (*(uchars++)));
    }

    return ustr;
}


/*------------------------------------------------------------------------------
 *  Create an ICU UnicodeString from a Glib ustring
 *----------------------------------------------------------------------------*/
Ptr<UnicodeString>::Ref
LocalizedObject :: ustringToUnicodeString(
                                Ptr<const Glib::ustring>::Ref   gString)
                                                                    throw ()
{
    Ptr<UnicodeString>::Ref     uString(new UnicodeString());

    Glib::ustring::const_iterator     it  = gString->begin();
    Glib::ustring::const_iterator     end = gString->end();
    while (it < end) {
        uString->append((UChar32) *it++);
    }

    return uString;
}


/*------------------------------------------------------------------------------
 *  Create an ICU UnicodeString from a Glib ustring
 *----------------------------------------------------------------------------*/
Ptr<UnicodeString>::Ref
LocalizedObject :: ustringToUnicodeString(const Glib::ustring   & gString)
                                                                    throw ()
{
    Ptr<UnicodeString>::Ref     uString(new UnicodeString());

    Glib::ustring::const_iterator     it  = gString.begin();
    Glib::ustring::const_iterator     end = gString.end();
    while (it < end) {
        uString->append((UChar32) *it++);
    }

    return uString;
}

