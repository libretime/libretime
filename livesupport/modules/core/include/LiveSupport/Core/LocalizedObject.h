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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/LocalizedObject.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_LocalizedObject_h
#define LiveSupport_Core_LocalizedObject_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include <unicode/resbund.h>
#include <unicode/fmtable.h>
#include <libxml++/libxml++.h>

#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Base class for localized objects, containing some helper functions
 *  to make localized life easier.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class LocalizedObject
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  The resource bundle holding the localized resources for this
         *  object.
         */
        Ptr<ResourceBundle>::Ref    bundle;


    protected:
        /**
         *  The default constructor.
         */
        LocalizedObject(void)                                   throw ()
        {
        }


    public:
        /**
         *  Constructor.
         *
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        LocalizedObject(Ptr<ResourceBundle>::Ref    bundle)     throw ()
        {
            this->bundle = bundle;
        }

        /**
         *  Virtual destructor.
         */
        virtual
        ~LocalizedObject(void)                                  throw ()
        {
        }

        /**
         *  Return the name of the XML element that is expected
         *  to be sent to a call to getBundle().
         *  
         *  @return the name of the expected XML configuration element.
         *  @see #getBundle(const xmlpp::Element &)
         */
        static const std::string
        getConfigElementName(void)                      throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Load a resource bundle based on an XML configuration element.
         *
         *  The DTD for the lement to be supplied to this function is:
         *  <pre><code>
         *  <!DOCTYPE resourceBundle [
         *  <!ELEMENT resourceBundle    EMPTY >
         *  <!ATTLIST resourceBundle    path    CDATA   #REQUIRED >
         *  <!ATTLIST resourceBundle    locale  CDATA   #REQUIRED >
         *  ]>
         *  </code></pre>
         *
         *  a sample configuration element is as follows:
         *
         *  <pre><code>
         *  <resourceBundle path   = "./tmp/Core"
         *                  locale = "en"
         *  />
         *  </code></pre>
         *
         *  for an overview of resource bundle parameters, see the ICU
         *  documentation on <a
         *  href=http://oss.software.ibm.com/icu/userguide/ResourceManagement.html>
         *  resource management</a>
         *
         *  @param element the XML configuration element
         *  @return the resource bundle, based on this element.
         *  @exception std::invalid_argument if the supplied element is not
         *             a proper resource bundle configuration element.
         *  @see http://oss.software.ibm.com/icu/userguide/ResourceManagement.html
         */
        static Ptr<ResourceBundle>::Ref
        getBundle(const xmlpp::Element    & element)
                                                throw (std::invalid_argument);

        /**
         *  Get the resource bundle for this object.
         *
         *  @return the resource bundle for this object.
         */
        Ptr<ResourceBundle>::Ref
        getBundle(void) const                                   throw ()
        {
            return bundle;
        }

        /**
         *  Change the resource bundle for this object.
         *
         *  @param the new resource bundle used by the object.
         */
        virtual void
        setBundle(Ptr<ResourceBundle>::Ref  bundle)             throw ()
        {
            this->bundle = bundle;
        }

        /**
         *  Get a resource bundle nested inside our bundle.
         *
         *  @param key the name of the resource bundle to get.
         *  @exception std::invalid_argument if there is no bundle by
         *             the specified key
         */
        Ptr<ResourceBundle>::Ref
        getBundle(const char  * key)            throw (std::invalid_argument);

        /**
         *  Get a string from the resource bundle.
         *
         *  @param key the key identifying the requested string.
         *  @return the requested string
         *  @exception std::invalid_argument if there is no string for the
         *             specified key.
         */
        virtual Ptr<UnicodeString>::Ref
        getResourceString(const char  * key)
                                                throw (std::invalid_argument);

        /**
         *  A convenience function to format a message.
         *  For more information, see the ICU MessageFormat class
         *  documentation.
         *
         *  @param pattern the pattern to format
         *  @param arguments the arguments to use in the formatting
         *  @param nArguments the number of arguments supplied
         *  @return the formatted string
         *  @exception std::invalid_argument if the pattern is bad, or
         *             the arguments do not match
         *  @see http://oss.software.ibm.com/icu/apiref/classMessageFormat.html
         */
        static Ptr<UnicodeString>::Ref
        formatMessage(Ptr<const UnicodeString>::Ref   pattern,
                      Formattable                   * arguments,
                      unsigned int                    nArguments)
                                                throw (std::invalid_argument);

        /**
         *  A convenience function to format a message, based on a pattern
         *  loaded from a resource.
         *  For more information, see the ICU MessageFormat class
         *  documentation.
         *
         *  @param patternKey the key of the pattern to format
         *  @param arguments the arguments to use in the formatting
         *  @param nArguments the number of arguments supplied
         *  @return the formatted string
         *  @exception std::invalid_argument if the pattern is bad, or
         *             the arguments do not match, or there is no resource
         *             specified by patternKey
         *  @see http://oss.software.ibm.com/icu/apiref/classMessageFormat.html
         */
        virtual Ptr<Glib::ustring>::Ref
        formatMessage(const char      * patternKey,
                      Formattable     * arguments,
                      unsigned int      nArguments)
                                                throw (std::invalid_argument);

        /**
         *  A convenience function to format a message, based on a pattern
         *  loaded from a resource.
         *  For more information, see the ICU MessageFormat class
         *  documentation.
         *
         *  @param patternKey the key of the pattern to format
         *  @param arguments the arguments to use in the formatting
         *  @param nArguments the number of arguments supplied
         *  @return the formatted string
         *  @exception std::invalid_argument if the pattern is bad, or
         *             the arguments do not match, or there is no resource
         *             specified by patternKey
         *  @see http://oss.software.ibm.com/icu/apiref/classMessageFormat.html
         */
        virtual Ptr<Glib::ustring>::Ref
        formatMessage(const std::string  & patternKey,
                      Formattable        * arguments,
                      unsigned int         nArguments)
                                                throw (std::invalid_argument)
        {
            return formatMessage(patternKey.c_str(), arguments, nArguments);
        }

        /**
         *  A convenience function to format a message, based on a pattern
         *  loaded from a resource, with one argument.
         *  For more information, see the ICU MessageFormat class
         *  documentation.
         *
         *  @param patternKey the key of the pattern to format
         *  @param argument1 the single argument to the message.
         *  @return the formatted string
         *  @exception std::invalid_argument if the pattern is bad, or
         *             the arguments do not match, or there is no resource
         *             specified by patternKey
         *  @see http://oss.software.ibm.com/icu/apiref/classMessageFormat.html
         */
        virtual Ptr<Glib::ustring>::Ref
        formatMessage(const std::string     & patternKey,
                      const Glib::ustring   & argument1)
                                                throw (std::invalid_argument);

        /**
         *  A convenience function to format a message, based on a pattern
         *  loaded from a resource, with two arguments.
         *  For more information, see the ICU MessageFormat class
         *  documentation.
         *
         *  @param patternKey the key of the pattern to format
         *  @param argument1 the first argument to the message.
         *  @param argument2 the second argument to the message.
         *  @return the formatted string
         *  @exception std::invalid_argument if the pattern is bad, or
         *             the arguments do not match, or there is no resource
         *             specified by patternKey
         *  @see http://oss.software.ibm.com/icu/apiref/classMessageFormat.html
         */
        virtual Ptr<Glib::ustring>::Ref
        formatMessage(const std::string     & patternKey,
                      const Glib::ustring   & argument1,
                      const Glib::ustring   & argument2)
                                                throw (std::invalid_argument);

        /**
         *  A convenience function to format a message, based on a pattern
         *  loaded from a resource, with three arguments.
         *  For more information, see the ICU MessageFormat class
         *  documentation.
         *
         *  @param patternKey the key of the pattern to format
         *  @param argument1 the first argument to the message.
         *  @param argument2 the second argument to the message.
         *  @param argument3 the second argument to the message.
         *  @return the formatted string
         *  @exception std::invalid_argument if the pattern is bad, or
         *             the arguments do not match, or there is no resource
         *             specified by patternKey
         *  @see http://oss.software.ibm.com/icu/apiref/classMessageFormat.html
         */
        virtual Ptr<Glib::ustring>::Ref
        formatMessage(const std::string     & patternKey,
                      const Glib::ustring   & argument1,
                      const Glib::ustring   & argument2,
                      const Glib::ustring   & argument3)
                                                throw (std::invalid_argument);

        /**
         *  Convert an ICU unicode string to a Glib ustring.
         *
         *  @param unicodeString the ICU unicode string to convert.
         *  @return the same string as supplied, in Glib ustring form.
         */
        static Ptr<Glib::ustring>::Ref
        unicodeStringToUstring(Ptr<const UnicodeString>::Ref   unicodeString)
                                                                    throw ();

        /**
         *  Convert a Glib ustring to an ICU unicode string.
         *
         *  @param gString the Glib ustring to convert
         *  @return the same string as supplied, in ICU unicode form.
         */
        static Ptr<UnicodeString>::Ref
        ustringToUnicodeString(Ptr<const Glib::ustring>::Ref   gString)
                                                                    throw ();

        /**
         *  Convert a Glib ustring to an ICU unicode string.
         *
         *  @param gString the Glib ustring to convert
         *  @return the same string as supplied, in ICU unicode form.
         */
        static Ptr<UnicodeString>::Ref
        ustringToUnicodeString(const Glib::ustring            & gString)
                                                                    throw ();

        /**
         *  Get a string from the resource bundle, as a Glib ustring.
         *
         *  @param key the key identifying the requested string.
         *  @return the requested string
         *  @exception std::invalid_argument if there is no string for the
         *             specified key.
         */
        Ptr<Glib::ustring>::Ref
        getResourceUstring(const char * key)
                                                throw (std::invalid_argument)
        {
            return unicodeStringToUstring(getResourceString(key));
        }

        /**
         *  Get a string from the resource bundle, as a Glib ustring.
         *
         *  @param key the key identifying the requested string.
         *  @return the requested string
         *  @exception std::invalid_argument if there is no string for the
         *             specified key.
         */
        Ptr<Glib::ustring>::Ref
        getResourceUstring(const std::string &key)
                                                throw (std::invalid_argument)
        {
            return unicodeStringToUstring(getResourceString(key.c_str()));
        }

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_LocalizedObject_h

