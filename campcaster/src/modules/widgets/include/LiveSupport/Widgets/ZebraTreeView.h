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
#ifndef LiveSupport_Widgets_ZebraTreeView_h
#define LiveSupport_Widgets_ZebraTreeView_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/liststore.h>
#include <gtkmm/treeview.h>
#include <libglademm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/WidgetConstants.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A table of items, in rows colored alternately gray and light blue.
 *
 *  NOTE: the ZebraTreeView works only with models based on a 
 *  ZebraTreeModelColumRecord column record, because cellDataFunction()
 *  and renumberRows() refer to the row number column of the model.
 *  AFAIK there is no way to syntactically enforce this, so you need to
 *  remember it.
 *
 *  General comments about TreeViews:
 *
 *  TreeViews contain TreeViewColumns; these contain a title (a text Label)
 *  and a table column body (a CellRenderer).  The CellRenderer needs to be
 *  both 'added' to the TreeViewColumn, and 'connected' to a TreeModel
 *  column.
 *
 *  A single TreeViewColumn may contain several CellRenderers, i.e., 
 *  sub-columns.
 *
 *  The standard CellRenderer types (CellRendererText etc) can not be 
 *  instantiated by the user; they can only be created by the shortcut
 *  TreeViewColumn constructor or the append_column() or insert_column()
 *  functions in TreeView.  These create the appropriate CellRenderer,
 *  add it the tree view column, and connect it with the tree model column.
 *
 *  A derived CellRenderer sub-type needs to be 1) instantiated;
 *  2) added to a TreeViewColumn using a constructor or pack_start() etc;
 *  3) connected with a TreeModelColumn using TreeViewColumn::set_renderer().
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class ZebraTreeView : public Gtk::TreeView
{
    private:
        /**
         *  Default constructor.
         */
        ZebraTreeView(void)                                     throw ()
        {
        }

        /**
         *  The callback function to set the colors of the rows.
         *
         *  @param  cell    the cell renderer of the column.
         *  @param  iter    points to the current row in the model.
         */
        void 
        cellDataFunction(Gtk::CellRenderer*               cell,
                         const Gtk::TreeModel::iterator&  iter)
                                                                throw ();

        /**
         *  The callback function for the line number columns.
         *  It reads the line number from the rowNumberColumn of the model.
         *
         *  @param  cell    the cell renderer of the column.
         *  @param  iter    points to the current row in the model.
         *  @param  offset  the line number of the first row, set by the
         *                  call to appendLineNumberColumn()
         */
        void 
        lineNumberCellDataFunction(
                        Gtk::CellRenderer*               cell,
                        const Gtk::TreeModel::iterator&  iter,
                        int                              offset)
                                                                throw ();

        /**
         *  Emit the "cell has been edited" signal.
         */
        void
        emitSignalCellEdited(const Glib::ustring &  path,
                             const Glib::ustring &  newText,
                             int                    columnId)
                                                                throw ()
        {
            signalCellEdited().emit(path, columnId, newText);
        }

        /**
         *  Emit the "tree model has changed" signal.
         */
        void
        emitSignalTreeModelChanged(void)
                                                                throw ()
        {
            signalTreeModelChanged().emit();
        }

        /**
         *  Renumber the rows after they have changed.
         *
         *  This is called from the onRowInserted(), onRowDeleted() and
         *  onRowsReordered() signal handlers.
         */
        void
        renumberRows(void)                                      throw ();

        /**
         *  Find the selected row.
         *  Returns the selected row (if the selection type is single),
         *  or the first selected row (if the selection type is multiple).
         *  May return 0 if no row is selected.
         *
         *  @return     an iterator pointing to the selected row; or 0.
         */
        Gtk::TreeModel::iterator
        getSelectedRow(void)                                    throw ();


    protected:
        /**
         *  A signal object to notify people that a cell has been edited.
         */
        sigc::signal<void, 
                     const Glib::ustring &, 
                     int, 
                     const Glib::ustring &>     signalCellEditedObject;

        /**
         *  A signal object to notify people that the tree model has changed.
         */
        sigc::signal<void>                      signalTreeModelChangedObject;

        /**
         *  Event handler for the row_inserted signal.
         *
         *  @param  path    a path pointing to the inserted row.
         *  @param  iter    an iterator pointing to the inserted row.
         */
        void
        onRowInserted(const Gtk::TreeModel::Path &      path,
                      const Gtk::TreeModel::iterator &  iter)
                                                                throw ();

        /**
         *  Event handler for the row_deleted signal.
         *
         *  @param  path    points to the previous location of the deleted row.
         */
        void
        onRowDeleted(const Gtk::TreeModel::Path &   path)       throw ();

        /**
         *  Event handler for the rows_reordered signal.
         *
         *  @param  path    points to the tree node whose children have been
         *                  reordered.
         *  @param  iter    points to the node whose children have been
         *                  reordered, or 0 if the depth of path is 0.
         *  @param  mapping an array of integers mapping the current position 
         *                  of each child to its old position before the 
         *                  re-ordering, i.e. mapping[newpos] = oldpos.
         */
        void
        onRowsReordered(const Gtk::TreeModel::Path &      path,
                        const Gtk::TreeModel::iterator&   iter,
                        int*                              mapping)
                                                                throw ();

        /**
         *  Event handler for the row_expanded signal.
         *
         *  @param  iter    points to the expanded row.
         *  @param  path    points to the expanded row.
         */
        void
        onRowExpanded(const Gtk::TreeModel::iterator &  iter,
                      const Gtk::TreeModel::Path &      path)    throw ();

        /**
         *  Event handler for the row_collapsed signal.
         *
         *  @param  iter    points to the collapsed row.
         *  @param  path    points to the collapsed row.
         */
        void
        onRowCollapsed(const Gtk::TreeModel::iterator &  iter,
                       const Gtk::TreeModel::Path &      path)   throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param treeModel the data the treeView will show.
         */
        ZebraTreeView(Glib::RefPtr<Gtk::TreeModel>   treeModel)
                                                                throw ();

        /**
         *  Constructor to be used with Glade::Xml::get_widget_derived().
         *
         *  @param baseClass    widget of the parent class, created by Glade.
         *  @param glade        the Glade object.
         */
        ZebraTreeView(_GtkTreeView *                            baseClass,
                      const Glib::RefPtr<Gnome::Glade::Xml> &   glade)
                                                                throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~ZebraTreeView(void)                                    throw ();

        /**
         *  Add a text column to the TreeView.
         *
         *  @param title    the title of the column
         *  @param modelColumn  the model column this view will display
         *  @param minimumWidth the minimum width of the column, in pixels
         *                      (optional)
         *  @return the number of columns after adding this one
         */
        int 
        appendColumn(const Glib::ustring&                       title, 
                     const Gtk::TreeModelColumn<Glib::ustring>& modelColumn,
                     int   minimumWidth = 0)
                                                                throw ();

        /**
         *  Add an image column to the TreeView.
         *
         *  @param title        the title of the column
         *  @param modelColumn  the model column this view will display
         *  @param minimumWidth the minimum width of the column, in pixels
         *                      (optional)
         *  @return the number of columns after adding this one
         */
        int 
        appendColumn(
                    const Glib::ustring&               title, 
                    const Gtk::TreeModelColumn<Glib::RefPtr<Gdk::Pixbuf> > &
                                                       modelColumn,
                    int                                minimumWidth = 0)
                                                                throw ();

        /**
         *  Add a centered text column to the TreeView.
         *
         *  @param title    the title of the column
         *  @param modelColumn  the model column this view will display
         *  @param minimumWidth the minimum width of the column, in pixels
         *                      (optional)
         *  @return the number of columns after adding this one
         */
        int 
        appendCenteredColumn(
                     const Glib::ustring&                       title, 
                     const Gtk::TreeModelColumn<Glib::ustring>& modelColumn,
                     int   minimumWidth = 0)
                                                                throw ();

        /**
         *  Add a centered line number column to the TreeView.
         *
         *  @param title    the title of the column
         *  @param offset   the line number of the first row
         *  @param minimumWidth the minimum width of the column, in pixels
         *                      (optional)
         *  @return the number of columns after adding this one
         */
        int 
        appendLineNumberColumn(
                     const Glib::ustring&   title, 
                     int                    offset = 0,
                     int                    minimumWidth = 0)
                                                                throw ();

        /**
         *  Add an editable text column to the TreeView.
         *
         *  The signal_edited() signal of the cell renderer gets connected
         *  to the signalEdited() signal of the ZebraTreeView object; the
         *  columnId argument will get passed to the signal handler.
         *
         *  This is used to display fade info (time durations), so the text is
         *  right aligned in the column.
         *
         *  @param title        the title of the column
         *  @param modelColumn  the model column this view will display
         *  @param columnId     the column ID passed to the signal handler
         *  @param minimumWidth the minimum width of the column, in pixels
         *                      (optional)
         *  @return the number of columns after adding this one
         */
        int 
        appendEditableColumn(
                     const Glib::ustring&                       title, 
                     const Gtk::TreeModelColumn<Glib::ustring>& modelColumn,
                     int   columnId,
                     int   minimumWidth = 0)
                                                                throw ();

        /**
         *  Signal handler for the "up" menu option selected from
         *  the context menu.
         */
        void
        onUpMenuOption(void)                                    throw ();

        /**
         *  Signal handler for the "down" menu option selected from
         *  the context menu.
         */
        void
        onDownMenuOption(void)                                  throw ();

        /**
         *  Signal handler for the "remove" menu option selected from
         *  the context menu.
         */
        void
        onRemoveMenuOption(void)                                throw ();

        /**
         *  Remove an item from the window.
         *
         *  @param  iter    points to the row to be removed
         */
        void
        removeItem(const Gtk::TreeModel::iterator &   iter)     throw ();

        /**
         *  The signal raised when a cell has been edited.
         *
         *  @return the signal object (a protected member of this class)
         */
        sigc::signal<void, const Glib::ustring &, int, const Glib::ustring &>
        signalCellEdited(void)                                  throw ()
        {
            return signalCellEditedObject;
        }

        /**
         *  The signal raised when the rows in the tree model have changed.
         *  This signal is emitted whenever the tree model emits a
         *  row_inserted, row_deleted or rows_reordered signal.
         *
         *  @return the signal object (a protected member of this class)
         */
        sigc::signal<void>
        signalTreeModelChanged(void)                            throw ()
        {
            return signalTreeModelChangedObject;
        }

        /**
         *  Manually connect the 'model has changed' signals to the tree view.
         *  This is useful if you want to use the same ZebraTreeView object
         *  to alternately display two (or more) different tree models.
         *
         *  @param  treeModel   the tree model whose changes should trigger
         *                      a redraw of the tree view object
         */
        void
        connectModelSignals(Glib::RefPtr<Gtk::TreeModel>  treeModel)
                                                                throw ();

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ZebraTreeView_h

