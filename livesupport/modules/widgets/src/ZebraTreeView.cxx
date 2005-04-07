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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/ZebraTreeView.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ZebraTreeView :: ZebraTreeView(Glib::RefPtr<Gtk::TreeModel>  treeModel)
                                                                    throw ()
                : Gtk::TreeView(treeModel)
{
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
ZebraTreeView :: ~ZebraTreeView(void)                               throw ()
{
}


/*------------------------------------------------------------------------------
 *  Color the table blue.
 *----------------------------------------------------------------------------*/
void 
ZebraTreeView :: colorBlue(void)                                    throw ()
{
    Gdk::Color      bgColor = Colors::getColor(Colors::LightBlue);

    for (int i = 0; i < get_columns().size(); i++) {
        Gtk::CellRenderer*  renderer = get_column_cell_renderer(i);
        renderer->property_cell_background_gdk() = bgColor;
//        renderer->property_cell_background_set() = false;
    }
}
