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
 *  @author $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class LocalizedObject
{
    private:
        /**
         *  The resource bundle holding the localized resources for this
         *  object.
         */
        Ptr<ResourceBundle>::Ref    bundle;

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
         *  ustring.
         *
         *  @param key the key identifying the requested string.
         *  @return the requested string
         *  @exception std::invalid_argument if there is no string for the
         *             specified key.
         */
        virtual Ptr<UnicodeString>::Ref
        getResourceString(const char  * key)
                                                throw (std::invalid_argument);
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_LocalizedObject_h

