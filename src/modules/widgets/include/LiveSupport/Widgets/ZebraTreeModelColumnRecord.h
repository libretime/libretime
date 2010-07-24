/*------------------------------------------------------------------------------

    Copyright (c) 2010 Sourcefabric O.P.S.
 
    This file is part of the Campcaster project.
    http://campcaster.sourcefabric.org/
 
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
 
------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_ZebraTreeModelColumnRecord_h
#define LiveSupport_Widgets_ZebraTreeModelColumnRecord_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/treemodelcolumn.h>

namespace LiveSupport {
namespace Widgets {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A basic column record class for tree models with colorable rows.
 */
class ZebraTreeModelColumnRecord : public Gtk::TreeModelColumnRecord
{
    public:
        /**
         *  The column for the color of the row.
         */
        Gtk::TreeModelColumn<int>       rowNumberColumn;

        /**
         *  Constructor.
         */
        ZebraTreeModelColumnRecord(void)                        throw ()
        {
            add(rowNumberColumn);
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ZebraTreeModelColumnRecord_h

