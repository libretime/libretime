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
#ifndef GuiComponent_h
#define GuiComponent_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "GuiObject.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The common ancestor of all window components in the GUI.
 *  These are non-standalone sub-windows, like the AdvancedSearchEntry, and
 *  sub-widgets of those, like the AdvancedSearchItem.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class GuiComponent : public GuiObject
{
    protected:

        /**
         *  The parent object.
         */
        GuiObject *                     parent;

        /**
         *  Protected constructor.
         *
         *  @param  parent      the GuiObject which contains this one.
         *  @param  bundleName  the name of the localization resource bundle
         *                      (optional); if missing, the parent's bundle
         *                      is used.
         */
        GuiComponent(GuiObject *                parent,
                     const Glib::ustring &      bundleName = "")
                                                                    throw ();


    public:

        /**
         *  Virtual destructor.
         */
        virtual
        ~GuiComponent(void)                                          throw ()
        {
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // GuiComponent_h

