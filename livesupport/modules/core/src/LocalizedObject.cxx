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
    Version  : $Revision: 1.3 $
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


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Get a resource bundle by the specified key
 *----------------------------------------------------------------------------*/
Ptr<ResourceBundle>::Ref
LocalizedObject :: getBundle(const char  * key)
                                                throw (std::invalid_argument)
{
    UErrorCode                  status = U_ZERO_ERROR;
    Ptr<ResourceBundle>::Ref    resourceBundle(new ResourceBundle(
                                                    bundle->get(key, status)));
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
    UErrorCode                status = U_ZERO_ERROR;
    Ptr<UnicodeString>::Ref   unicodeStr;

    unicodeStr.reset(new UnicodeString(bundle->getStringEx(key, status)));
    if (!U_SUCCESS(status)) {
        throw std::invalid_argument("can't get string from bundle");
    }

    return unicodeStr;
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
Ptr<UnicodeString>::Ref
LocalizedObject :: formatMessage(const char       * patternKey,
                                 Formattable      * arguments,
                                 unsigned int       nArguments)
                                            throw (std::invalid_argument)
{
    return formatMessage(getResourceString(patternKey), arguments, nArguments);
}

