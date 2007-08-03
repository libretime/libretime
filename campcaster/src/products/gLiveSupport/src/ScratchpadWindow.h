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
#include <unicode/resbund.h>
#include <gtkmm.h>
#include <libglademm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"
#include "BasicWindow.h"
#include "CuePlayer.h"
#include "ContentsStorable.h"
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
class ScratchpadWindow : public BasicWindow,
                         public ContentsStorable
{
    private:

        /**
         *  The directory where the Glade files are.
         */
        Glib::ustring                       gladeDir;

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
         *  The model row at the mouse pointer, set by onEntryClicked()
         */
        Gtk::TreeRow                    currentRow;

        /**
         *  The cue player widget controlling the audio buttons.
         */
        Ptr<CuePlayer>::Ref             cuePlayer;

        /**
         *  The right-click context menu for audio clips,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Ptr<Gtk::Menu>::Ref             audioClipMenu;

        /**
         *  The right-click context menu for playlists,
         *  that comes up when right-clicking an entry in the entry list.
         */
        Ptr<Gtk::Menu>::Ref             playlistMenu;

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *  This is used to pop up the right-click context menu.
         *
         *  @param event the button event recieved
         */
        virtual void
        onEntryClicked(GdkEventButton *     event)              throw ();

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
         *  the entry context menu.  For playlists only.
         */
        virtual void
        onEditPlaylist(void)                                    throw ();

        /**
         *  Signal handler for the "schedule playlist" menu item selected
         *  from the entry context menu.  For playlists only.
         */
        virtual void
        onSchedulePlaylist(void)                                throw ();

        /**
         *  Signal handler for the "export playlist" menu item selected from
         *  the entry context menu.  For playlists only.
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
         *  Signal handler for the "add to live mode" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onAddToLiveMode(void)                                   throw ();

        /**
         *  Signal handler for the "upload to hub" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onUploadToHub(void)                                     throw ();
        
        /**
         *  Event handler for the Remove menu item selected from
         *  the entry conext menu.
         */
        virtual void
        onRemoveMenuOption(void)                                throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         *  @param  windowOpenerButton  the button which was pressed to open
         *                              this window.
         *  @param  gladeDir        the directory where the glade file is.
         */
        ScratchpadWindow(Ptr<GLiveSupport>::Ref     gLiveSupport,
                         Ptr<ResourceBundle>::Ref   bundle,
                         Gtk::ToggleButton *        windowOpenerButton,
                         const Glib::ustring &      gladeDir)
                                                                throw ();

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
        addItem(Ptr<const UniqueId>::Ref    id)                 throw ();

        /**
         *  Return the contents of the Scratchpad.
         *
         *  @return a space-separated list of the unique IDs, in base 10.
         */
        Ptr<Glib::ustring>::Ref
        getContents(void)                                       throw ();

        /**
         *  Restore the contents of the Scratchpad.
         *  The current contents are discarded, and replaced with the items
         *  listed in the 'contents' parameter.
         *
         *  @param contents a space-separated list of unique IDs, in base 10.
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
         *  Update the cue player display to show a stopped state.
         */
        void
        showCuePlayerStopped(void)                              throw ()
        {
            cuePlayer->onStop();
        }

        /**
         *  Hide the window.
         *
         *  This overrides BasicWindow::hide(), and closes the Export Playlist
         *  and Schedule Playlist pop-up windows, if they are still open.
         */
        virtual void
        hide(void)                                              throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // ScratchpadWindow_h

