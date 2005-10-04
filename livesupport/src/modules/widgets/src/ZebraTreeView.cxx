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
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <sstream>

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
 *  Add a text column to the TreeView.
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
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(
                                new Gtk::TreeViewColumn(title, *renderer) );
                                
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
 *  Add an image column to the TreeView.
 *----------------------------------------------------------------------------*/
int 
ZebraTreeView :: appendColumn(
                        const Glib::ustring&            title, 
                        const Gtk::TreeModelColumn<Glib::RefPtr<Gdk::Pixbuf> > &
                                                        modelColumn,
                        int                             minimumWidth)
                                                                throw ()
{
    // a standard cell renderer; can be replaced with a ZebraCellRenderer
    Gtk::CellRendererPixbuf*  renderer = Gtk::manage(
                                    new Gtk::CellRendererPixbuf );
    
    // the constructor packs the renderer into the TreeViewColumn
    Gtk::TreeViewColumn*      viewColumn = Gtk::manage(
                                    new Gtk::TreeViewColumn(title, *renderer) );
                                
    // and then we associate this renderer with the model column
    viewColumn->add_attribute(renderer->property_pixbuf(), modelColumn);

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
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(
                                new Gtk::TreeViewColumn(title, *renderer) );

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
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(
                                new Gtk::TreeViewColumn(title, *renderer) );
                                
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
 *  Add a centered line number column to the TreeView.
 *----------------------------------------------------------------------------*/
int 
ZebraTreeView :: appendLineNumberColumn(
                    const Glib::ustring&        title, 
                    int                         offset,
                    int                         minimumWidth)
                                                                throw ()
{
    // a standard cell renderer; can be replaced with a ZebraCellRenderer
    Gtk::CellRendererText*  renderer = Gtk::manage(new Gtk::CellRendererText);
    
    // center the text in the column
    renderer->property_xalign() = 0.5;

    // the constructor packs the renderer into the TreeViewColumn
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(
                                new Gtk::TreeViewColumn(title, *renderer) );
                                
    // this cell data function will do the blue-gray zebra stripes
    // and fill in the line number from the model.rowNumberColumn
    viewColumn->set_cell_data_func(
        *renderer,
        sigc::bind<int>(
            sigc::mem_fun(*this, &ZebraTreeView::lineNumberCellDataFunction),
            offset ));
    
    // set the minimum width of the column
    if (minimumWidth) {
        viewColumn->set_min_width(minimumWidth);
    }
    
    return append_column(*viewColumn);
}


/*------------------------------------------------------------------------------
 *  The callback function for the line number column(s).
 *----------------------------------------------------------------------------*/
void 
ZebraTreeView :: lineNumberCellDataFunction(
                                Gtk::CellRenderer*                  cell,
                                const Gtk::TreeModel::iterator&     iter,
                                int                                 offset)
                                                                throw ()
{
    ZebraTreeModelColumnRecord  model;
    int                         rowNumber = (*iter)[model.rowNumberColumn];
    
    Colors::ColorName   colorName =  rowNumber % 2 ? Colors::Gray
                                                   : Colors::LightBlue;
    cell->property_cell_background_gdk() = Colors::getColor(colorName);
    cell->property_cell_background_gdk() = Colors::getColor(colorName);

    Glib::ustring       numberString;
    numberString.append("<span font_desc='Bitstream Vera Sans Bold 16'>");
    std::stringstream   numberStr;
    numberStr << (rowNumber + offset);
    numberString.append(numberStr.str());
    numberString.append("</span>");
    Gtk::CellRendererText *     textCell 
                                = dynamic_cast<Gtk::CellRendererText*>(cell);
    textCell->property_markup() = numberString;
}


/*------------------------------------------------------------------------------
 *  Add a centered text column to the TreeView.
 *----------------------------------------------------------------------------*/
int 
ZebraTreeView :: appendEditableColumn(
                    const Glib::ustring&                        title, 
                    const Gtk::TreeModelColumn<Glib::ustring>&  modelColumn,
                    int                                         columnId,
                    int                                         minimumWidth)
                                                                throw ()
{
    // a standard cell renderer; can be replaced with a ZebraCellRenderer
    Gtk::CellRendererText*  renderer = Gtk::manage(new Gtk::CellRendererText);
    
    // right align the text in the column
    renderer->property_xalign() = 1;

    // set the cells to be editable, and connect the signal to our own
    renderer->property_editable() = true;
    renderer->signal_edited().connect(sigc::bind<int>(
                    sigc::mem_fun(*this, &ZebraTreeView::emitSignalCellEdited),
                    columnId ));

    // the constructor packs the renderer into the TreeViewColumn
    Gtk::TreeViewColumn*    viewColumn = Gtk::manage(
                                new Gtk::TreeViewColumn(title, *renderer) );
                                
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
 *  Event handler for the Up menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onUpMenuOption(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection = get_selection();
    Gtk::TreeModel::iterator                iter = selection->get_selected();
    Glib::RefPtr<Gtk::ListStore>            treeModel
                    = Glib::RefPtr<Gtk::ListStore>::cast_dynamic(get_model());
    ZebraTreeModelColumnRecord              modelColumns;

    if (iter && iter != treeModel->children().begin()) {
        Gtk::TreeModel::iterator    previous = iter;
        --previous;
        
        int     rowNumber = (*previous)[modelColumns.rowNumberColumn];
        (*iter)    [modelColumns.rowNumberColumn] = rowNumber;
        (*previous)[modelColumns.rowNumberColumn] = ++rowNumber;
        
        treeModel->iter_swap(previous, iter);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Down menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onDownMenuOption(void)                             throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection = get_selection();
    Gtk::TreeModel::iterator                iter = selection->get_selected();
    Glib::RefPtr<Gtk::ListStore>            treeModel
                    = Glib::RefPtr<Gtk::ListStore>::cast_dynamic(get_model());
    ZebraTreeModelColumnRecord              modelColumns;

    if (iter) {
        Gtk::TreeModel::iterator    next = iter;
        ++next;
        if (next != treeModel->children().end()) {
        
            int     rowNumber = (*iter)[modelColumns.rowNumberColumn];
            (*next)[modelColumns.rowNumberColumn] = rowNumber;
            (*iter)[modelColumns.rowNumberColumn] = ++rowNumber;

            treeModel->iter_swap(iter, next);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onRemoveMenuOption(void)                           throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection = get_selection();
    Gtk::TreeModel::iterator                iter = selection->get_selected();

    if (iter) {
        removeItem(iter);
    }
}


/*------------------------------------------------------------------------------
 *  Remove an item from the window.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: removeItem(const Gtk::TreeModel::iterator &   iter)
                                                                    throw ()
{
    Glib::RefPtr<Gtk::ListStore>            treeModel
                    = Glib::RefPtr<Gtk::ListStore>::cast_dynamic(get_model());
    ZebraTreeModelColumnRecord              modelColumns;

    Gtk::TreeModel::iterator    later = iter;

    int     rowNumber = (*iter)[modelColumns.rowNumberColumn];
    for (++later; later != treeModel->children().end(); ++later) {
        (*later)[modelColumns.rowNumberColumn] = rowNumber++;
    }
    
    treeModel->erase(iter);
}
