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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <sstream>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/Colors.h"

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
    connectModelSignals(treeModel);
    this->signal_row_expanded().connect(sigc::mem_fun(*this,
                                            &ZebraTreeView::onRowExpanded));
    this->signal_row_collapsed().connect(sigc::mem_fun(*this,
                                            &ZebraTreeView::onRowCollapsed));
}


/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ZebraTreeView :: ZebraTreeView(
                        _GtkTreeView *                            baseClass,
                        const Glib::RefPtr<Gnome::Glade::Xml> &   glade)
                                                                throw ()
          : Gtk::TreeView(baseClass)
{
    this->signal_row_expanded().connect(sigc::mem_fun(*this,
                                            &ZebraTreeView::onRowExpanded));
    this->signal_row_collapsed().connect(sigc::mem_fun(*this,
                                            &ZebraTreeView::onRowCollapsed));
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
 *  Add an editable centered text column to the TreeView.
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
    Gtk::TreeModel::iterator        iter = getSelectedRow();
    
    Glib::RefPtr<Gtk::ListStore>    treeModel
                    = Glib::RefPtr<Gtk::ListStore>::cast_dynamic(get_model());
    ZebraTreeModelColumnRecord      modelColumns;

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
    Gtk::TreeModel::iterator        iter = getSelectedRow();

    Glib::RefPtr<Gtk::ListStore>    treeModel
                    = Glib::RefPtr<Gtk::ListStore>::cast_dynamic(get_model());
    ZebraTreeModelColumnRecord      modelColumns;

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
    Gtk::TreeModel::iterator                newSelection;
    
    if (selection->get_mode() == Gtk::SELECTION_SINGLE) {
        Gtk::TreeModel::iterator    it = selection->get_selected();
        if (it) {
            newSelection = it;
            ++newSelection;
            removeItem(it);
        }
    
    } else {
        std::vector<Gtk::TreePath>  selectedPaths
                                    = selection->get_selected_rows();
        
        std::vector<Gtk::TreeModel::iterator>   selectedIters;
        std::vector<Gtk::TreePath>::iterator    pathIt = selectedPaths.begin();
        for ( ; pathIt != selectedPaths.end(); ++pathIt) {
            selectedIters.push_back(get_model()->get_iter(*pathIt));
        }
        
        std::vector<Gtk::TreeModel::iterator>::iterator
                                                iterIt = selectedIters.begin();
        for ( ; iterIt != selectedIters.end(); ++iterIt) {
            newSelection = *iterIt;
            ++newSelection;
            removeItem(*iterIt);
        }
        
    }
    
    if (newSelection) {
        selection->select(newSelection);
    }
}


/*------------------------------------------------------------------------------
 *  Remove an item from the window.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: removeItem(const Gtk::TreeModel::iterator &   iter)
                                                                    throw ()
{
    Glib::RefPtr<Gtk::ListStore>    treeModel
                    = Glib::RefPtr<Gtk::ListStore>::cast_dynamic(get_model());
    treeModel->erase(iter);
}


/*------------------------------------------------------------------------------
 *  Find the selected row.
 *----------------------------------------------------------------------------*/
Gtk::TreeModel::iterator
ZebraTreeView :: getSelectedRow(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection = get_selection();
    std::vector<Gtk::TreePath>              selectedRows;
    Gtk::TreeModel::iterator                it;
    
    switch (selection->get_mode()) {
        case Gtk::SELECTION_SINGLE:
                it = selection->get_selected();
                break;

        case Gtk::SELECTION_MULTIPLE:
                selectedRows = selection->get_selected_rows();
                if (selectedRows.size() > 0) {
                    it = get_model()->get_iter(selectedRows.front());
                }
                break;

        default:
                break;
    }
    
    return it;
}


/*------------------------------------------------------------------------------
 *  Event handler for the row_inserted signal.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onRowInserted(const Gtk::TreeModel::Path &      path,
                               const Gtk::TreeModel::iterator &  iter)
                                                                    throw ()
{
    renumberRows();
    columns_autosize();
    emitSignalTreeModelChanged();
}


/*------------------------------------------------------------------------------
 *  Event handler for the row_deleted signal.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onRowDeleted(const Gtk::TreeModel::Path &      path)
                                                                    throw ()
{
    renumberRows();
    columns_autosize();
    emitSignalTreeModelChanged();
}


/*------------------------------------------------------------------------------
 *  Event handler for the rows_reordered signal.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onRowsReordered(const Gtk::TreeModel::Path &      path,
                                 const Gtk::TreeModel::iterator &  iter,
                                 int*                              mapping)
                                                                    throw ()
{
    renumberRows();
    emitSignalTreeModelChanged();
}


/*------------------------------------------------------------------------------
 *  Event handler for the row_expanded signal.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onRowExpanded(const Gtk::TreeModel::iterator &  iter,
                               const Gtk::TreeModel::Path &      path)
                                                                    throw ()
{
    renumberRows();
}


/*------------------------------------------------------------------------------
 *  Event handler for the row_collapsed signal.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: onRowCollapsed(const Gtk::TreeModel::iterator &  iter,
                                const Gtk::TreeModel::Path &      path)
                                                                    throw ()
{
    renumberRows();
}


/*------------------------------------------------------------------------------
 *  Renumber the rows after they have changed.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: renumberRows(void)                                 throw ()
{
    Glib::RefPtr<Gtk::TreeModel>    treeModel = get_model();
    ZebraTreeModelColumnRecord      modelColumns;
    int                             rowNumber = 0;
    Gtk::TreeModel::iterator        iter;
    Gtk::TreeModel::iterator        it;

    for (iter = treeModel->children().begin(); 
                            iter != treeModel->children().end(); ++iter) {
        Gtk::TreeRow    row = *iter;
        row[modelColumns.rowNumberColumn] = rowNumber++;

        if (row_expanded(treeModel->get_path(row))) {
            for (it = row.children().begin(); it != row.children().end();
                                                                     ++it) {
                Gtk::TreeRow    childRow = *it;
                childRow[modelColumns.rowNumberColumn] = rowNumber++;
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Manually connect the 'model has changed' signals to the tree view.
 *----------------------------------------------------------------------------*/
void
ZebraTreeView :: connectModelSignals(Glib::RefPtr<Gtk::TreeModel>  treeModel)
                                                                    throw ()
{
    treeModel->signal_row_inserted().connect(sigc::mem_fun(*this,
                                            &ZebraTreeView::onRowInserted));
    treeModel->signal_row_deleted().connect(sigc::mem_fun(*this,
                                            &ZebraTreeView::onRowDeleted));
    treeModel->signal_rows_reordered().connect(sigc::mem_fun(*this,
                                            &ZebraTreeView::onRowsReordered));
}

