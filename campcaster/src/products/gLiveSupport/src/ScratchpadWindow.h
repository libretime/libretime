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

#include "GuiWindow.h"
#include "ContentsStorable.h"
#include "DndMethods.h"

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "CuePlayer.h"
#include "ExportPlaylistWindow.h"
#include "SchedulePlaylistWindow.h"


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
class ScratchpadWindow : public GuiWindow,
                         public ContentsStorable,
                         public DndMethods
{
    private:

        /**
         *  The user preferences key.
         */
        Ptr<const Glib::ustring>::Ref       userPreferencesKey;

        /**
         *  The Export Playlist pop-up window.
         */
        Ptr<ExportPlaylistWindow>::Ref      exportPlaylistWindow;

        /**
         *  The Schedule Playlist pop-up window.
         */
        Ptr<SchedulePlaylistWindow>::Ref    schedulePlaylistWindow;

        /**
         *  The list of selected rows, as path references (row numbers).
         *  Reset by onEntryClicked().
         */
        Ptr<std::vector<Gtk::TreePath> >::Ref           selectedPaths;
        /**
         *  One of the selected rows, set to the first one by onEntryClicked().
         *  Incremented by getNextSelectedPlayable().
         */
        std::vector<Gtk::TreePath>::const_iterator      selectedIter;

        /**
         *  Check whether exactly one row is selected.
         *
         *  This is an auxilliary function used by onKeyPressed().
         *
         *  @return true if a single row is selected, false if not.
         */
        bool
        selectionIsSingle(void)                                     throw ();

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
        removeItem(Ptr<const UniqueId>::Ref     id)                 throw ();


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
                 *  The column for the creator of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         creatorColumn;

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
                    add(creatorColumn);
                    add(titleColumn);
                }
        };


        /**
         *  The column model.
         */
        ModelColumns                    modelColumns;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The tree view, now only showing rows.
         */
        ZebraTreeView *                 treeView;

        /**
         *  The cue player widget controlling the audio buttons.
         */
        Ptr<CuePlayer>::Ref             cuePlayer;

        /**
         *  The right-click context menu for audio clips,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Ptr<Gtk::Menu>::Ref             audioClipContextMenu;

        /**
         *  The right-click context menu for playlists,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Ptr<Gtk::Menu>::Ref             playlistContextMenu;

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *  This is used to pop up the right-click context menu.
         *
         *  @param event the button event recieved
         *  @return true if the event has been handled (a popup displayed),
         *          false otherwise
         */
        virtual bool
        onEntryClicked(GdkEventButton *     event)              throw ();

        /**
         *  Signal handler for the user double-clicking, or pressing Enter
         *  on one of the entries.
         *
         *  @param  path    the TreePath of the row clicked on (ignored).
         *  @param  column  the TreeViewColumn clicked on (ignored).
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
        onKeyPressed(GdkEventKey *          event)                  throw ();

        /**
         *  Signal handler for the "edit playlist" menu item selected from
         *  the entry context menu.  For playlists only.
         */
        virtual void
        onEditPlaylist(void)                                        throw ();

        /**
         *  Signal handler for the "schedule playlist" menu item selected
         *  from the entry context menu.  For playlists only.
         */
        virtual void
        onSchedulePlaylist(void)                                    throw ();

        /**
         *  Signal handler for the "export playlist" menu item selected from
         *  the entry context menu.  For playlists only.
         */
        virtual void
        onExportPlaylist(void)                                      throw ();
        
        /**
         *  Signal handler for the "add to playlist" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onAddToPlaylist(void)                                       throw ();

        /**
         *  Signal handler for the "add to live mode" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onAddToLiveMode(void)                                       throw ();

        /**
         *  Signal handler for the "upload to hub" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onUploadToHub(void)                                         throw ();
        
        /**
         *  Event handler for the Remove menu item selected from
         *  the entry conext menu.
         */
        virtual void
        onRemoveMenuOption(void)                                    throw ();

        /**
         *  The tree view we want to implement d'n'd on.
         */
        virtual Gtk::TreeView *
        getTreeViewForDnd (void)                                    throw ()
        {
            return treeView;
        }

        /**
         *  The name of the window for the d'n'd methods.
         */
        virtual Glib::ustring
        getWindowNameForDnd (void)                                  throw ();

        /**
         *  Return the topmost selected row.
         *  Sets selectedPaths and selectedIter; does not increment it.
         *
         *  @return the first selected playable item.
         */
        virtual Ptr<Playable>::Ref
        getFirstSelectedPlayable (void)                             throw ();

        /**
         *  Used to iterate over the selected rows.
         *  Reset to the first row by onEntryClicked().
         *  Returns a 0 pointer if nothing is selected or we have reached
         *  the end of the list of selected rows.
         *
         *  @return the next selected playable item.
         */
        virtual Ptr<Playable>::Ref
        getNextSelectedPlayable (void)                              throw ();

        /**
         *  Add an item to the d'n'd tree view at the given position.
         *
         *  @param  iter    the iterator pointing to the row to be filled in.
         *  @param  id      the ID of the item to add.
         */
        virtual void
        addItem (Gtk::TreeIter              iter,
                 Ptr<const UniqueId>::Ref   id)                     throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param  windowOpenerButton  the button which was pressed to open
         *                              this window.
         */
        ScratchpadWindow(Gtk::ToggleButton *        windowOpenerButton)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~ScratchpadWindow(void)                                     throw ()
        {
        }

        /**
         *  Add an item to the Scratchpad.
         *  If it was already in the Scratchpad, move it to the top.
         *
         *  @param playable the Playable object to add.
         */
        void
        addItem(Ptr<Playable>::Ref    playable)                     throw ();

        /**
         *  Add an item to the Scratchpad.
         *  If it was already in the Scratchpad, move it to the top.
         *
         *  @param id the id of the item to add.
         */
        void
        addItem(Ptr<const UniqueId>::Ref    id)                     throw ();

        /**
         *  Return the contents of the Scratchpad.
         *
         *  @return a space-separated list of the unique IDs, in base 10.
         */
        Ptr<Glib::ustring>::Ref
        getContents(void)                                           throw ();

        /**
         *  Restore the contents of the Scratchpad.
         *  The current contents are discarded, and replaced with the items
         *  listed in the 'contents' parameter.
         *
         *  @param contents a space-separated list of unique IDs, in base 10.
         */
        void
        setContents(Ptr<const Glib::ustring>::Ref   contents)       throw ();

        /**
         *  Return the user preferences key.
         *  The contents of the window will be stored in the user preferences
         *  under this key.
         *
         *  @return the user preference key.
         */
        Ptr<const Glib::ustring>::Ref
        getUserPreferencesKey(void)                                 throw ()
        {
            return userPreferencesKey;
        }

        /**
         *  Update the cue player display to show a stopped state.
         */
        void
        showCuePlayerStopped(void)                                  throw ()
        {
            cuePlayer->onStop();
        }

        /**
         *  Hide the window.
         *
         *  This overrides GuiWindow::hide(), and closes the Export Playlist
         *  and Schedule Playlist pop-up windows, if they are still open.
         */
        virtual void
        hide(void)                                                  throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // ScratchpadWindow_h

