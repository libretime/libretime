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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/GtkLocalizedObject.h,v $

------------------------------------------------------------------------------*/
#ifndef GtkLocalizedObject_h
#define GtkLocalizedObject_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <glibmm/ustring.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Base class for localized objects, using GTK+ strings.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class GtkLocalizedObject : public LocalizedObject
{
    public:
        /**
         *  Constructor.
         *
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        GtkLocalizedObject(Ptr<ResourceBundle>::Ref    bundle)     throw ()
                    : LocalizedObject(bundle)
        {
        }

        /**
         *  Virtual destructor.
         */
        virtual
        ~GtkLocalizedObject(void)                                  throw ()
        {
        }

        /**
         *  Convert an ICU unicode string to a Glib ustring.
         *
         *  @param unicodeString the ICU unicode string to conver.
         *  @return the same string as supplied, in Glib ustring form.
         */
        static Ptr<Glib::ustring>::Ref
        unicodeStringToUstring(Ptr<const UnicodeString>::Ref   unicodeString)
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
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // GtkLocalizedObject_h

