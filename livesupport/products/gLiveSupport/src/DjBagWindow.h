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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/DjBagWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef DjBagWindow_h
#define DjBagWindow_h

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
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The DJ Bag window, showing recent and relevant audio clips and
 *  playlists.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.4 $
 */
class DjBagWindow : public Gtk::Window, public LocalizedObject
{

    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one clip per row.
         *
         *  @author $Author: maroy $
         *  @version $Revision: 1.4 $
         */
        class ModelColumns : public Gtk::TreeModel::ColumnRecord
        {
            public:
                /**
                 *  The column for the playable object shown in the row.
                 */
                Gtk::TreeModelColumn<Ptr<Playable>::Ref>    playableColumn;

                /**
                 *  The column for the type of the entry in the list
                 */
                Gtk::TreeModelColumn<Glib::ustring>         typeColumn;

                /**
                 *  The column for the title of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         titleColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                  throw ()
                {
                    add(playableColumn);
                    add(typeColumn);
                    add(titleColumn);
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
        Gtk::TreeView               treeView;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The box containing the close button.
         */
        Gtk::HButtonBox             buttonBox;

        /**
         *  The close button.
         */
        Ptr<Gtk::Button>::Ref       closeButton;

        /**
         *  The right-click context menu for audio clips,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Ptr<Gtk::Menu>::Ref         audioClipMenu;

        /**
         *  The right-click context menu for playlists,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Ptr<Gtk::Menu>::Ref         playlistMenu;

        /**
         *  Signal handler for the close button clicked.
         */
        virtual void
        onCloseButtonClicked(void)                              throw ();

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *
         *  @param event the button event recieved
         */
        virtual void
        onEntryClicked(GdkEventButton     * event)              throw ();

        /**
         *  Signal handler for the "remove" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onRemoveItem(void)                                      throw ();

        /**
         *  Signal handler for the "delete" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onDeleteItem(void)                                      throw ();

        /**
         *  Signal handler for the "up" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onUpItem(void)                                          throw ();

        /**
         *  Signal handler for the "down" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onDownItem(void)                                        throw ();

        /**
         *  Signal handler for the "add to playlist" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onAddToPlaylist(void)                                   throw ();

        /**
         *  Signal handler for the "schedule playlist" menu item selected
         *  from the entry context menu.
         */
        virtual void
        onSchedulePlaylist(void)                                throw ();

        /**
         *  Delete an item from the storage and remove it from the dj bag.
         *
         *  @param playable the Playable object to delete and remove.
         *  @exception XmlRpcException on XML-RPC errors.
         */
        void
        deleteItem(Ptr<Playable>::Ref  playable)        throw (XmlRpcException);


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        DjBagWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                    Ptr<ResourceBundle>::Ref    bundle)         throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~DjBagWindow(void)                                      throw ();

        /**
         *  Update the window contents, with the contents of the dj bag.
         */
        void
        showContents(void)                                      throw ();

        /**
         *  Remove an item from the dj bag.
         *
         *  @param id the id of the item to remove.
         */
        void
        removeItem(Ptr<const UniqueId>::Ref     id)             throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // DjBagWindow_h

