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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/ZebraTreeView.h,v $

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

#include <gtkmm/treemodel.h>
#include <gtkmm/treeview.h>
#include <gtkmm/label.h>
#include <gtkmm/table.h>
#include <gtkmm/alignment.h>
#include <gtkmm/eventbox.h>
#include <gtkmm/image.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/CornerImages.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/BlueBin.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A table of items, in rows colored alternately gray and light blue.
 *
 *  TreeView's contain TreeViewColumns; these contain a title (a text Label)
 *  and a table column body (a CellRenderer).  The CellRenderer needs to be
 *  connected with a TreeModelColumn using the set_renderer() method of
 *  TreeViewColumn [which, despite its name, does not set the renderer, just
 *  connects it with a tree model column].
 *
 *  A single TreeViewColumn may contain several CellRenderer's, i.e., 
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
 *  3) connected with a TreeModelColumn using set_renderer(). 
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.12 $
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
    protected:

    public:
        /**
         *  Constructor.
         *
         *  @param treeModel the data the treeView will show.
         */
        ZebraTreeView(Glib::RefPtr<Gtk::TreeModel>   treeModel)
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
         *  Add a button column to the TreeView.
         *
         *  @param title        the title of the column
         *  @param buttonType   the type of button this view will display
         *  @param minimumWidth the minimum width of the column, in pixels
         *                      (optional)
         *  @return the number of columns after adding this one
         */
        int 
        appendColumn(const Glib::ustring&               title, 
                     WidgetFactory::ImageButtonType     buttonType,
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

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ZebraTreeView_h

