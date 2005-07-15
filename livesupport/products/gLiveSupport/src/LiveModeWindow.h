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
    Version  : $Revision: 1.14 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/LiveModeWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveModeWindow_h
#define LiveModeWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>

#include <unicode/resbund.h>

#include <gtkmm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/WhiteWindow.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"
#include "CuePlayer.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The LiveMode window, showing recent and relevant audio clips and
 *  playlists.
 *
 *  @author $Author: fgerlits $
 *  @version $Revision: 1.14 $
 */
class LiveModeWindow : public WhiteWindow, public LocalizedObject
{
    private:

    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one clip per row.
         *
         *  @author $Author: fgerlits $
         *  @version $Revision: 1.14 $
         */
        class ModelColumns : public PlayableTreeModelColumnRecord
        {
            public:
                /**
                 *  The column for the play button.
                 */
//              Gtk::TreeModelColumn<Glib::RefPtr<Gdk::Pixbuf> >
//                                                          playButtonColumn;

                /**
                 *  The column for the title, creator, etc.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         infoColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
//                  add(playButtonColumn);
                    add(infoColumn);
                }
        };


        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The column model.
         */
        ModelColumns                modelColumns;

        /**
         *  The main container in the window.
         */
        Gtk::VBox                   vBox;

        /**
         *  A scrolled window, so that the list can be scrolled.
         */
        Gtk::ScrolledWindow         scrolledWindow;

        /**
         *  The tree view, now only showing rows.
         */
        ZebraTreeView *             treeView;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The right-click context menu,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Gtk::Menu *                 contextMenu;

        /**
         *  Signal handler for the output play button clicked
         *  or the output play menu option selected.
         */
        void
        onOutputPlay(void)                                      throw ();

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *  This brings up the right-click context menu.
         *
         *  @param event the button event recieved
         */
        void
        onEntryClicked(GdkEventButton     * event)              throw ();

        /**
         *  Signal handler for the "rows reordered" event.
         */
        void
        onRowsReordered(const Gtk::TreeModel::Path &      path,
                        const Gtk::TreeModel::iterator&   iter,
                        int*                              newToOldMapping)
                                                                throw ()
        {
//            std::cerr << "rows changed: " << path.to_string() << "; "
//                      << "iter: " << (iter ? "true" : "false") << "\n";
        }

        /**
         *  Signal handler for the "row deleted" event.
         */
        void
        onRowDeleted(const Gtk::TreeModel::Path &   path)       throw ()
        {
//            std::cerr << "rows deleted: " << path.to_string() << ";\n";
            treeView->columns_autosize();
        }


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        LiveModeWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                       Ptr<ResourceBundle>::Ref    bundle)      throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~LiveModeWindow(void)                                   throw ()
        {
        }

        /**
         *  Add a new item to the Live Mode Window.
         */
        void
        addItem(Ptr<Playable>::Ref  playable)                   throw ();

        /**
         *  "Pop" the first item from the top of the Live Mode Window.
         */
        Ptr<Playable>::Ref
        popTop(void)                                            throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveModeWindow_h

