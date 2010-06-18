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

#include <iostream>
#include <stdexcept>
#include <cassert>

#include "DndMethods.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the D'n'D callbacks.
 *----------------------------------------------------------------------------*/
void
DndMethods :: setupDndCallbacks (DndType    type)                   throw ()
{
    Gtk::TreeView *                 treeView = getTreeViewForDnd();

    std::list<Gtk::TargetEntry>     targets;
    targets.push_back(Gtk::TargetEntry("STRING",
                                       Gtk::TARGET_SAME_APP));
    
    if (type | DND_SOURCE) {
        treeView->enable_model_drag_source(targets);
        treeView->signal_drag_data_get().connect(sigc::mem_fun(*this,
                                    &DndMethods::onTreeViewDragDataGet));
    }

    if (type | DND_DEST) {
        treeView->enable_model_drag_dest(targets);
        treeView->signal_drag_data_received().connect(sigc::mem_fun(*this,
                                    &DndMethods::onTreeViewDragDataReceived));
    }
}


/*------------------------------------------------------------------------------
 *  The callback for supplying the data for the drag and drop.
 *----------------------------------------------------------------------------*/
void
DndMethods :: onTreeViewDragDataGet (
                    const Glib::RefPtr<Gdk::DragContext> &      context,
                    Gtk::SelectionData &                        selectionData,
                    guint                                       info,
                    guint                                       time)
                                                                    throw ()
{
    Glib::ustring       dropString = getWindowNameForDnd();
    Ptr<Playable>::Ref  playable = getFirstSelectedPlayable();

    while ((playable = getNextSelectedPlayable())) {
        dropString += " ";
        dropString += std::string(*playable->getId());
    }

    selectionData.set(selectionData.get_target(),
                        8 /* 8 bits format*/,
                        (const guchar *) dropString.c_str(),
                        dropString.bytes());
}


/*------------------------------------------------------------------------------
 *  The callback for processing the data delivered by drag and drop.
 *----------------------------------------------------------------------------*/
void
DndMethods :: onTreeViewDragDataReceived(
                    const Glib::RefPtr<Gdk::DragContext> &      context,
                    int                                         x,
                    int                                         y,
                    const Gtk::SelectionData &                  selectionData,
                    guint                                       info,
                    guint                                       time)
                                                                    throw ()
{
    Glib::ustring   windowName = getWindowNameForDnd();

    if (selectionData.get_length() < 0 || selectionData.get_format() != 8) {
        std::cerr << "unknown type of data dropped on the tree view in "
                  << windowName << std::endl;
        context->drag_finish(false, false, time);
        return;
    }

    Glib::ustring       data = selectionData.get_data_as_string();
    std::stringstream   dataStream(data);
    Glib::ustring       sourceWindow;
    dataStream >> sourceWindow;

    Gtk::TreeIter       iter = insertRowAtPos(x, y);
    Glib::ustring       idAsString;
    dataStream >> idAsString;               // only works for 1 item, for now
    Ptr<UniqueId>::Ref  id(new UniqueId(idAsString));
    addItem(iter, id);

    if (sourceWindow == windowName) {
        context->drag_finish(true, true, time);     // delete the original
        
    } else {
        context->drag_finish(true, false, time);    // don't delete the original
    }
}


/*------------------------------------------------------------------------------
 *  Insert a row into the tree model at the given tree view position.
 *----------------------------------------------------------------------------*/
Gtk::TreeIter
DndMethods :: insertRowAtPos (int     x,
                              int     y)                            throw ()
{
    Gtk::TreeView *     treeView = getTreeViewForDnd();
    Glib::RefPtr<Gtk::ListStore>
                        treeModel = Glib::RefPtr<Gtk::ListStore>::cast_dynamic(
                                                    treeView->get_model() );

    Gtk::TreePath               destPath;
    Gtk::TreeViewDropPosition   destPos;
    bool                        pathIsValid = treeView->get_dest_row_at_pos(
                                                    x, y, destPath, destPos);
    // get_drag_dest_row() does not work here, for some strange reason
    Gtk::TreeIter       newIter;

    if (pathIsValid) {
        assert (!destPath.empty());
        Gtk::TreeIter   destination = treeModel->get_iter(destPath);

        if (destPos == Gtk::TREE_VIEW_DROP_BEFORE
                        || destPos == Gtk::TREE_VIEW_DROP_INTO_OR_BEFORE) {
            newIter = treeModel->insert(destination);
            
        } else if (destPos == Gtk::TREE_VIEW_DROP_AFTER
                        || destPos == Gtk::TREE_VIEW_DROP_INTO_OR_AFTER) {
            newIter = treeModel->insert_after(destination);
            
        } else {
            assert (false);
        }
    } else {
        newIter = treeModel->append();
    }

    return newIter;
}

