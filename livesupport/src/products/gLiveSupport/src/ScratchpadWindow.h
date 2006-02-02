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
#ifndef ScratchpadWindow_h
#define ScratchpadWindow_h

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
 *  The Scratchpad window, showing recent and relevant audio clips and
 *  playlists.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class ScratchpadWindow : public WhiteWindow,
                         public LocalizedObject
{
    private:
        /**
         *  Check whether exactly one row is selected, and if so, set
         *  the currentRow variable to point to it.
         *
         *  This is an auxilliary function used by onKeyPressed().
         *
         *  @return true if a single row is selected, false if not.
         */
        bool
        isSelectionSingle(void)                                 throw ();

        /**
         *  Select the (first) row which contains the playable specified.
         *  If no such row is found, then it does nothing.
         *
         *  @param playable     the playable to be selected.
         */
        void
        selectRow(Ptr<Playable>::Ref    playable)               throw ();

        /**
         *  Remove an item from the Scratchpad.
         *  If an item with the specified unique ID is found, it is removed.
         *  (There should never be more than one entry with the same ID;
         *  if there are, then only the first one is removed.)
         *  If no such item is found, the function does nothing.
         *
         *  @param id the id of the item to remove.
         */
        void
        removeItem(Ptr<const UniqueId>::Ref     id)             throw ();


    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one clip per row.
         *
         *  @author $Author$
         *  @version $Revision$
         */
        class ModelColumns : public PlayableTreeModelColumnRecord
        {
            public:
                /**
                 *  The column for the type of the entry in the list
                 */
                Gtk::TreeModelColumn<Glib::RefPtr<Gdk::Pixbuf> >
                                                            typeColumn;

                /**
                 *  The column for the title of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         titleColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(typeColumn);
                    add(titleColumn);
                }
        };


        /**
         *  The column model.
         */
        ModelColumns                modelColumns;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The tree view, now only showing rows.
         */
        ZebraTreeView *             treeView;

        /**
         *  The model row at the mouse pointer, set by onEntryClicked()
         */
        Gtk::TreeRow                currentRow;

        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The main container in the window.
         */
        Gtk::VBox                   vBox;

        /**
         *  A scrolled window, so that the list can be scrolled.
         */
        Gtk::ScrolledWindow         scrolledWindow;

        /**
         *  The box containing the box containing the audio buttons.
         */
        Gtk::HBox                   topButtonBox;

        /**
         *  The box containing the audio buttons.
         */
        CuePlayer *                 audioButtonBox;

        /**
         *  The box containing the close button.
         */
        Gtk::HButtonBox             middleButtonBox;

        /**
         *  The box containing the close button.
         */
        Gtk::HButtonBox             bottomButtonBox;

        /**
         *  The "add to playlist" button.
         */
        Button *                    addToPlaylistButton;

        /**
         *  The "clear list" button.
         */
        Button *                    clearListButton;

        /**
         *  The "remove selected item" button.
         */
        Button *                    removeButton;

        /**
         *  The right-click context menu for audio clips,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Gtk::Menu *                 audioClipMenu;

        /**
         *  The right-click context menu for playlists,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Gtk::Menu *                 playlistMenu;

        /**
         *  Signal handler for the add to playlist button clicked.
         */
        virtual void
        onAddToPlaylistButtonClicked(void)                      throw ();

        /**
         *  Signal handler for the clear list button clicked.
         */
        virtual void
        onClearListButtonClicked(void)                          throw ();

        /**
         *  Signal handler for the remove item button clicked.
         */
        virtual void
        onRemoveItemButtonClicked(void)                         throw ();

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *  This is used to pop up the right-click context menu.
         *
         *  @param event the button event recieved
         */
        virtual void
        onEntryClicked(GdkEventButton     * event)              throw ();

        /**
         *  Signal handler for the user double-clicking, or pressing Enter
         *  on one of the entries.
         *
         *  @param event the button event recieved
         */
        void
        onDoubleClick(const Gtk::TreeModel::Path &      path,
                      const Gtk::TreeViewColumn *       column)
                                                                throw ();

        /**
         *  Signal handler for a key pressed at one of the entries.
         *  The keys can be customized by the keyboardShortcutContainer
         *  element in the gLiveSupport configuration file.
         *  
         *  The actions handled are: moveItemUp, moveItemDown and removeItem.
         *
         *  @param  event the button event received
         *  @return true if the key press was fully handled, false if not
         */
        bool
        onKeyPressed(GdkEventKey *          event)              throw ();

        /**
         *  Signal handler for the "edit playlist" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onEditPlaylist(void)                                    throw ();

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
         *  Signal handler for the "add to live mode" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onAddToLiveMode(void)                                   throw ();

        /**
         *  Function to catch the event of the close button being pressed.
         */
        virtual void
        onCloseButtonClicked(void)                              throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        ScratchpadWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                         Ptr<ResourceBundle>::Ref    bundle)   throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~ScratchpadWindow(void)                                 throw ()
        {
        }

        /**
         *  Add an item to the Scratchpad.
         *  If it was already in the Scratchpad, move it to the top.
         *
         *  @param playable the Playable object to add.
         */
        void
        addItem(Ptr<Playable>::Ref    playable)                 throw ();

        /**
         *  Add an item to the Scratchpad.
         *  If it was already in the Scratchpad, move it to the top.
         *
         *  @param id the id of the item to add.
         */
        void
        addItem(Ptr<UniqueId>::Ref    id)                       throw ();

        /**
         *  Return the contents of the Scratchpad.
         *
         *  @return a space-separated list of the unique IDs, in base 10.
         *  @see restore()
         */
        Ptr<Glib::ustring>::Ref
        contents()                                              throw ();

        /**
         *  Restore the contents of the Scratchpad.
         *  The current contents are discarded, and replaced with the items
         *  listed in the 'contents' parameter.
         *
         *  @param contents a space-separated list of unique IDs, in base 10.
         *  @see contents()
         */
        void
        restore(Ptr<Glib::ustring>::Ref     contents)           throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // ScratchpadWindow_h

