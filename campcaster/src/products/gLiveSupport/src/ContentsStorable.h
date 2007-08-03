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
#ifndef ContentsStorable_h
#define ContentsStorable_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include <glibmm/ustring.h>
#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A virtual class to be implemented by GUI windows which
 *  want to store their contents as a user preference item.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class ContentsStorable
{
    public:

        /**
         *  Virtual destructor.
         */
        virtual
        ~ContentsStorable(void)                                     throw ()
        {
        }
        
        /**
         *  Return the user preferences key.
         *  The contents of the window will be stored in the user preferences
         *  under this key.
         *
         *  @return the user preference key.
         */
        virtual Ptr<const Glib::ustring>::Ref
        getUserPreferencesKey(void)                                 throw ()
                                                                    = 0;
        
        /**
         *  Convert the contents of the window to a string.
         *
         *  @return the contents of the window, as a string.
         */
        virtual Ptr<Glib::ustring>::Ref
        getContents(void)                                           throw ()
                                                                    = 0;
        
        /**
         *  Restore the contents of the window.
         *
         *  @param  contents    the new contents (as a string); it will replace
         *                      the current contents of the window
         */
        virtual void
        setContents(Ptr<const Glib::ustring>::Ref   contents)       throw ()
                                                                    = 0;
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // ContentsStorable_h

