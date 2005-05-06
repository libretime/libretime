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
    Version  : $Revision: 1.12 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/ZebraTreeView.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"

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
                    const Gtk::TreeModelColumn<Glib::ustring>&  modelColumn,
                    int                                         minimumWidth)
                                                                throw ()
{
    // a standard cell renderer; can be replaced with a ZebraCellRenderer
    Gtk::CellRendererText*  renderer = Gtk::manage(new Gtk::CellRendererText);
    
    // the constructor packs the renderer into the TreeViewColumn
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(new
                                Gtk::TreeViewColumn(title, *renderer) );
                                
    // and then we associate this renderer with the model column
    viewColumn->add_attribute(renderer->property_markup(), modelColumn);

    // this cell data function will do the blue-gray zebra stripes
    viewColumn->set_cell_data_func(
                    *renderer,
                    sigc::mem_fun(*this, &ZebraTreeView::cellDataFunction) );
    
    // set the minimum width of the column
    if (minimumWidth) {
        viewColumn->set_min_width(minimumWidth);
    }
    
    return append_column(*viewColumn);
}


/*------------------------------------------------------------------------------
 *  Add a button column to the TreeView.
 *----------------------------------------------------------------------------*/
int 
ZebraTreeView :: appendColumn(
                    const Glib::ustring &           title, 
                    WidgetFactory::ImageButtonType  buttonType,
                    int                             minimumWidth)
                                                                throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    ImageButton *               button = Gtk::manage(wf->createButton(
                                                                buttonType ));
    Glib::RefPtr<Gdk::Pixbuf>   passiveImage = button->getPassiveImage();

    // a standard cell renderer
    Gtk::CellRendererPixbuf*    renderer 
                                = Gtk::manage(new Gtk::CellRendererPixbuf);
    
    // set the image of the renderer
    renderer->property_pixbuf() = passiveImage;
    
    // the constructor packs the renderer into the TreeViewColumn
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(new
                                Gtk::TreeViewColumn(title, *renderer) );

    // this cell data function will do the blue-gray zebra stripes
    viewColumn->set_cell_data_func(
                    *renderer,
                    sigc::mem_fun(*this, &ZebraTreeView::cellDataFunction) );
    
    // set the minimum width of the column
    if (minimumWidth) {
        viewColumn->set_min_width(minimumWidth);
    }
    
    return append_column(*viewColumn);
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
    Colors::ColorName   colorName = (*iter)[model.rowNumberColumn] % 2
                                                  ? Colors::Gray
                                                  : Colors::LightBlue;
    cell->property_cell_background_gdk() = Colors::getColor(colorName);
}


/*------------------------------------------------------------------------------
 *  Add a centered text column to the TreeView.
 *----------------------------------------------------------------------------*/
int 
ZebraTreeView :: appendCenteredColumn(
                    const Glib::ustring&                        title, 
                    const Gtk::TreeModelColumn<Glib::ustring>&  modelColumn,
                    int                                         minimumWidth)
                                                                throw ()
{
    // a standard cell renderer; can be replaced with a ZebraCellRenderer
    Gtk::CellRendererText*  renderer = Gtk::manage(new Gtk::CellRendererText);
    
    // center the text in the column
    renderer->property_xalign() = 0.5;

    // the constructor packs the renderer into the TreeViewColumn
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(new
                                Gtk::TreeViewColumn(title, *renderer) );
                                
    // and then we associate this renderer with the model column
    viewColumn->add_attribute(renderer->property_markup(), modelColumn);
    
    // this cell data function will do the blue-gray zebra stripes
    viewColumn->set_cell_data_func(
                    *renderer,
                    sigc::mem_fun(*this, &ZebraTreeView::cellDataFunction) );
    
    // set the minimum width of the column
    if (minimumWidth) {
        viewColumn->set_min_width(minimumWidth);
    }
    
    return append_column(*viewColumn);
}

