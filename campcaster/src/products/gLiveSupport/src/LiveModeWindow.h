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

#include "GuiWindow.h"
#include "ContentsStorable.h"
#include "DndMethods.h"

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "CuePlayer.h"
#include "GLiveSupport.h"
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
 *  The LiveMode window, showing recent and relevant audio clips and
 *  playlists.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class LiveModeWindow : public GuiWindow,
                       public ContentsStorable,
                       public DndMethods
{
    private:

        /**
         *  The Playable item at the top of the window.
         */
        Ptr<Playable>::Ref                  savedTopPlayable;

        /**
         *  A flag used to disable preload() while deleting items.
         */
        bool                                isDeleting;

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
         *  The cue player widget with play/pause and stop buttons.
         */
        Ptr<CuePlayer>::Ref                 cuePlayer;

        /**
         *  The label for the cue player.
         */
        Gtk::Label *                        cueLabel;

        /**
         *  If checked, the top item in the window will start playing
         *  automatically after the current one finishes.
         */
        Gtk::CheckButton *                  autoPlayNext;

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
         *  Construct the right-click context menu for local audio clips.
         *
         *  @return the context menu created.
         */
        Ptr<Gtk::Menu>::Ref
        constructAudioClipContextMenu(void)                         throw ();

        /**
         *  Construct the right-click context menu for local playlists.
         *
         *  @return the context menu created.
         */
        Ptr<Gtk::Menu>::Ref
        constructPlaylistContextMenu(void)                          throw ();

        /**
         *  Check whether exactly one row is selected.
         *
         *  This is an auxilliary function used by onKeyPressed().
         *
         *  @return true if a single row is selected, false if not.
         */
        bool
        selectionIsSingle(void)                                     throw ();


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
                 *  The column for the title, creator, etc.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         infoColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(infoColumn);
                }
        };


        /**
         *  The column model.
         */
        ModelColumns                modelColumns;

        /**
         *  The tree view, now only showing rows.
         */
        ZebraTreeView *             treeView;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The right-click context menu for audio clips.
         */
        Ptr<Gtk::Menu>::Ref         audioClipContextMenu;

        /**
         *  The right-click context menu for playlists.
         */
        Ptr<Gtk::Menu>::Ref         playlistContextMenu;

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
         *  @return true if the event has been handled (a popup displayed),
         *          false otherwise
         */
        bool
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
         *  The actions handled are: moveItemUp, moveItemDown, removeItem,
         *  and playAudio (which plays the item in the output player).
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
         *  Signal handler for the "schedule playlist" menu item selected
         *  from the entry context menu.
         */
        virtual void
        onSchedulePlaylist(void)                                throw ();

        /**
         *  Signal handler for the "export playlist" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onExportPlaylist(void)                                  throw ();
        
        /**
         *  Signal handler for the "add to playlist" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onAddToPlaylist(void)                                   throw ();

        /**
         *  Signal handler for the "upload to hub" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onUploadToHub(void)                                     throw ();

        /**
         *  Signal handler for the "remove" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onRemoveMenuOption(void)                                throw ();

        /**
         *  Signal handler for a change in the tree model.
         */
        virtual void
        onTreeModelChanged(void)                                throw ();

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
        LiveModeWindow(Gtk::ToggleButton *         windowOpenerButton)
                                                                throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~LiveModeWindow(void)                                   throw ()
        {
        }

        /**
         *  Add a new item to the top of the Live Mode Window.
         *
         *  @param  playable    the playable object to be added.
         */
        void
        addItem(Ptr<Playable>::Ref  playable)                   throw ();

        /**
         *  Add a new item as the given row in the Live Mode Window.
         *
         *  @param  iter        an iterator pointing to the row to be updated.
         *  @param  playable    the playable object to be added.
         */
        void
        addItem(Gtk::TreeIter               iter,
                Ptr<Playable>::Ref          playable)           throw ();

        /**
         *  Add an item to the top of the Live Mode Window, by ID.
         *
         *  @param id the id of the item to add.
         *  @see setContents().
         */
        void
        addItem(Ptr<const UniqueId>::Ref    id)                 throw ();

        /**
         *  "Pop" the first item from the top of the Live Mode Window.
         *
         *  @return the playable object at the top of the window,
         *          or 0 if the window is empty.
         */
        Ptr<Playable>::Ref
        popTop(void)                                            throw ();

        /**
         *  Update the cue player display to show a stopped state.
         */
        void
        showCuePlayerStopped(void)                              throw ()
        {
            cuePlayer->onStop();
        }

        /**
         *  Refresh the playlist in the window.
         *  Updates the playlist to the new copy supplied in the argument,
         *  if it is present in the window.
         *  This is called by GLiveSupport::savePlaylist() after the playlist
         *  has been edited.
         *
         *  @param  playlist    the new version of the playlist.
         */
        void
        refreshPlaylist(Ptr<Playlist>::Ref  playlist)           throw ();

        /**
         *  Report whether the window is non-empty.
         *
         *  @return true if there is at least one Playable item in the window.
         */
        bool
        isNotEmpty(void)                                        throw ()
        {
            return (treeModel->children().size() != 0);
        }

        /**
         *  Update the localized strings in the widget.
         */
        void
        updateStrings(void)                                     throw ();

        /**
         *  Return the contents of the Live Mode window.
         *  This means the list of audio files, plus the state of the
         *  autoPlayNext checkbox.
         *
         *  @return         0 or 1, followed by a space-separated list of the
         *                  unique IDs, in base 10.
         */
        Ptr<Glib::ustring>::Ref
        getContents(void)                                       throw ();

        /**
         *  Restore the contents of the Scratchpad.
         *  The current contents are discarded, and replaced with the items
         *  listed in the 'contents' parameter.
         *
         *  @param contents 0 or 1, followed by a space-separated list of the
         *                  unique IDs, in base 10.
         */
        void
        setContents(Ptr<const Glib::ustring>::Ref   contents)   throw ();

        /**
         *  Return the user preferences key.
         *  The contents of the window will be stored in the user preferences
         *  under this key.
         *
         *  @return the user preference key.
         */
        Ptr<const Glib::ustring>::Ref
        getUserPreferencesKey(void)                             throw ()
        {
            return userPreferencesKey;
        }

        /**
         *  Hide the window.
         *
         *  This overrides GuiWindow::hide(), and closes the Export Playlist
         *  and Schedule Playlist pop-up windows, if they are still open.
         */
        virtual void
        hide(void)                                              throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveModeWindow_h

