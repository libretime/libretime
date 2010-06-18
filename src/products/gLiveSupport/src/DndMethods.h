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
#ifndef DndMethods_h
#define DndMethods_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include <gtkmm.h>

#include "LiveSupport/Core/Playable.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An abstract class containing template methods which implement drag and drop.
 *
 *  To implement d'n'd in a GuiWindow, inherit from this class, implement the
 *  pure abstract methods declared in this class (see the requirements at the
 *  method declarations), and call setupDndCallbacks() after the tree view has
 *  been constructed.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class DndMethods
{
    protected:

        /**
         *  The tree view we want to implement d'n'd on.
         */
        virtual Gtk::TreeView *
        getTreeViewForDnd (void)                                    throw ()
                                                                    = 0;

        /**
         *  The name of the window.
         */
        virtual Glib::ustring
        getWindowNameForDnd (void)                                  throw ()
                                                                    = 0;

        /**
         *  Return the topmost selected row in the tree view.
         *
         *  @return the first selected playable item.
         */
        virtual Ptr<Playable>::Ref
        getFirstSelectedPlayable(void)                              throw ()
                                                                    = 0;

        /**
         *  Used to iterate over the selected rows in the tree view.
         *
         *  @return the next selected playable item.
         */
        virtual Ptr<Playable>::Ref
        getNextSelectedPlayable(void)                               throw ()
                                                                    = 0;

        /**
         *  Add an item to the d'n'd tree view at the given position.
         *
         *  @param  iter    the iterator pointing to the row to be filled in.
         *  @param  id      the ID of the item to add.
         */
        virtual void
        addItem (Gtk::TreeIter               iter,
                 Ptr<const UniqueId>::Ref    id)                    throw ()
                                                                    = 0;

        /**
         *  Insert a row into the tree model at the given tree view position.
         *  Creates the new row; the caller should fill it with data.
         *
         *  @param  x   the x coordinate of the location of the new row.
         *  @param  y   the y coordinate of the location of the new row.
         *  @return an iterator pointing to the newly created row.
         */
        Gtk::TreeIter
        insertRowAtPos (int     x,
                        int     y)                                  throw ();

        /**
         *  Types of d'n'd.
         */
        typedef enum {  DND_SOURCE = 1,
                        DND_DEST   = 2
                     }                      DndType;

        /**
         *  Set up the d'n'd callbacks.
         *
         *  @param  type    set up callbacks for d'n'd source or destination
         *                  (default: both).
         */
        void
        setupDndCallbacks (DndType  type = DndType(DND_SOURCE | DND_DEST))
                                                                    throw ();

        /**
         *  The callback for supplying the data for the drag and drop.
         *
         *  @param  context         the drag context.
         *  @param  selectionData   the data (filled in by this function).
         *  @param  info            not used.
         *  @param  time            timestamp for the d'n'd operation.
         */
        void
        onTreeViewDragDataGet (
            const Glib::RefPtr<Gdk::DragContext> &      context,
            Gtk::SelectionData &                        selectionData,
            guint                                       info,
            guint                                       time)
                                                                    throw ();

        /**
         *  The callback for processing the data delivered by drag and drop.
         *
         *  @param  context         the drag context.
         *  @param  x               the x coord where the data was dropped.
         *  @param  y               the y coord where the data was dropped.
         *  @param  selectionData   the data.
         *  @param  info            not used.
         *  @param  time            timestamp for the d'n'd operation.
         */
        virtual void
        onTreeViewDragDataReceived (
            const Glib::RefPtr<Gdk::DragContext> &      context,
            int                                         x,
            int                                         y,
            const Gtk::SelectionData &                  selectionData,
            guint                                       info,
            guint                                       time)
                                                                    throw ();

        /**
         *  Constructor.
         */
        DndMethods (void)                                           throw ()
        {
        }

        /**
         *  Virtual destructor.
         */
        virtual
        ~DndMethods(void)                                           throw ()
        {
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // DndMethods_h

