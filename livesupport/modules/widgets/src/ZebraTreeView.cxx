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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/ZebraTreeView.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraCellRenderer.h"

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
ZebraTreeView :: ~ZebraTreeView(void)                           throw ()
{
}


/*------------------------------------------------------------------------------
 *  Add a column to the TreeView.
 *----------------------------------------------------------------------------*/
int 
ZebraTreeView :: appendColumn(
                    const Glib::ustring&                        title, 
                    const Gtk::TreeModelColumn<Glib::ustring>&  modelColumn)
                                                                throw ()
{
    ZebraCellRenderer*      renderer = Gtk::manage(new ZebraCellRenderer);
    // the constructor packs the renderer into the TreeViewColumn
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(new
                                Gtk::TreeViewColumn(title, *renderer) );
    // and then we associate this renderer with the model column
    viewColumn->set_renderer(*renderer, modelColumn);
    return append_column(*viewColumn);
}


/*------------------------------------------------------------------------------
 *  Set the callback function for every column.
 *----------------------------------------------------------------------------*/
void 
ZebraTreeView :: setCellDataFunction(void)                      throw ()
{
    std::list<Column*>              columnList = get_columns();
    std::list<Column*>::iterator    it;
    
    for (it = columnList.begin(); it != columnList.end(); ++it) {
        (*it)->set_cell_data_func(
                    *(*it)->get_first_cell_renderer(), 
                    sigc::mem_fun(*this, &ZebraTreeView::cellDataFunction) );
    }

//    set_rules_hint();   // suggest coloring with alternate colors
}


/*------------------------------------------------------------------------------
 *  The callback function.
 *----------------------------------------------------------------------------*/
void 
ZebraTreeView :: cellDataFunction(Gtk::CellRenderer*               cell,
                                  const Gtk::TreeModel::iterator&  iter)
                                                                throw ()
{
    ZebraTreeModelColumnRecord  model;

    Gdk::Color  color = Colors::getColor((*iter)[model.colorColumn] );
    cell->property_cell_background_gdk() = color;
}

