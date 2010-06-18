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
#ifndef PlaylistWindow_h
#define PlaylistWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "GuiWindow.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The Simple Playlist Management Window. Allow to edit playlists in
 *  a top-down view fashion.
 *
 *  The layout of this window is roughly the following:
 *  <pre><code>
 *  +--- simple playlist management window --------+
 *  | name:    +-- name input -------------+       |
 *  | +-- playlist entries ----------------------+ |
 *  | | +-- entry1 ----------------------------+ | |
 *  | | +-- entry2 ----------------------------+ | |
 *  | |  ...                                     | |
 *  | +------------------------------------------+ |
 *  | +- lock fades checkbox -+ |
 *  |        +- save button -+  +- close button -+ |
 *  | +-- status bar ----------------------------+ |
 *  +----------------------------------------------+
 *  </code></pre>
 *
 *  @author $Author$
 *  @version $Revision$
 */
class PlaylistWindow : public GuiWindow
{
    private:

        /**
         *  Constants for identifying the two fade info columns.
         */
        enum {  fadeInColumnId,
                fadeOutColumnId  };

        /**
         *  A flag set to true when the edited playlist is modified.
         */
        bool                        isPlaylistModified;

        /**
         *  A flag controlled by the "lock fades" check button.
         *  This determines whether the fade-out of a clip is assumed to
         *  be equal to the fade-in of the next clip.
         */
        bool                        areFadesLocked;

        /**
         *  An iterator pointing to the current row, for popup functions.
         *  It is set by onEntryClicked(), before popping up the menu.
         */
        Gtk::TreeIter               currentItem;

        /**
         *  The input text field for the name of the playlist.
         */
        Gtk::Entry *                nameEntry;

        /**
         *  The entry tree view, now only showing rows.
         */
        ZebraTreeView *             entriesView;

        /**
         *  The entry tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    entriesModel;

        /**
         *  The label containing the length of the playlist.
         */
        Gtk::Label *                lengthValueLabel;

        /**
         *  The status bar.
         */
        Gtk::Label *                statusBar;

        /**
         *  The Save Button.
         */
        Gtk::Button *               saveButton;

        /**
         *  The right-click context menu that comes up when right-clicking
         *  a playlist element.
         */
        Ptr<Gtk::Menu>::Ref         rightClickMenu;

        /**
         *  Find (an iterator pointing to) the currently selected row.
         *
         *  This is an auxilliary function used by onKeyPressed().
         */
        void
        findCurrentItem(void)                                   throw ();

        /**
         *  Select (highlight) the nth row.
         *
         *  This is an auxilliary function used by onUpItem() and onDownItem().
         *
         *  @param rowNumber    the number of the row to be selected.
         */
        void
        selectRow(int   rowNumber)                              throw ();

        /**
         *  Swap two playlist elements in the edited playlist.
         *  This is used by onUpItem() and onDownItem().
         *
         *  @param firstIter    the first item, to be swapped...
         *  @param secondIter   ... with this second item
         */
        void
        swapPlaylistElements(Gtk::TreeIter   firstIter,
                             Gtk::TreeIter   secondIter)        throw ();

        /**
         *  Signal handler for the "remove" menu item selected from
         *  the right-click context menu.
         */
        void
        onRemoveItem(void)                                      throw ();
        
        /**
         *  Set the fade in of a playlist element.
         */
        void
        setFadeIn(Ptr<PlaylistElement>::Ref   playlistElement,
                  Ptr<time_duration>::Ref     newFadeIn)        throw();

        /**
         *  Set the fade out of a playlist element.
         */
        void
        setFadeOut(Ptr<PlaylistElement>::Ref  playlistElement,
                   Ptr<time_duration>::Ref    newFadeOut)       throw();

        /**
         *  Check that fades are not longer than the whole clip.
         *
         *  @return true if (fadeIn + fadeOut <= playlength).
         */
        bool
        isLengthOkay(Ptr<PlaylistElement>::Ref  playlistElement,
                     Ptr<FadeInfo>::Ref         newFadeInfo)    throw();

        /**
         *  Clean and close the window.
         *  Set all widgets to empty and close the window.
         */
        void
        closeWindow(void)                                       throw();

        /**
         *  Save the edited playlist.
         *
         *  @param  reopen  true if the playlist needs to be opened for
         *                  editing again after saving it.
         *  @return true if the playlist was saved successully.
         */
        bool
        savePlaylist(bool   reopen)                             throw ();
        
        /**
         *  Set the value of the isPlaylistModified variable.
         */
        void
        setPlaylistModified(bool    newValue)                   throw ();

        /**
         *  Run the confirmation dialog.
         *
         *  @return the response ID returned by the dialog.
         */
        Gtk::ResponseType
        runConfirmationDialog(void)                             throw ();


    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one playlist entry per row.
         *
         *  @author $Author$
         *  @version $Revision$
         */
        class ModelColumns : public ZebraTreeModelColumnRecord
        {
            public:
                /**
                 *  The column for the start of the playlist entry.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             startColumn;

                /**
                 *  The column for the title of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             titleColumn;

                /**
                 *  The column for the fade in of the playlist entry.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             fadeInColumn;

                /**
                 *  The column for the length of the playlist entry.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             lengthColumn;

                /**
                 *  The column for the fade out of the playlist entry.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             fadeOutColumn;

                /**
                 *  The column for the pointer to the playlist element.
                 */
                Gtk::TreeModelColumn<Ptr<PlaylistElement>::Ref>
                                                        playlistElementColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                  throw ()
                {
                    add(startColumn);
                    add(titleColumn);
                    add(fadeInColumn);
                    add(lengthColumn);
                    add(fadeOutColumn);
                    add(playlistElementColumn);
                }
        };

        /**
         *  The column model.
         */
        ModelColumns                modelColumns;

        /**
         *  Signal handler for the title being edited.
         */
        void
        onTitleEdited(void)                                     throw();

        /**
         *  Signal handler for the fade info being edited.
         *
         *  @path       the path representing the row in the tree model
         *  @columnId   the ID of the row which was passed to appendColumn()
         *  @newText    the new fade value
         */
        void
        onFadeInfoEdited(const Glib::ustring &  path,
                         int                    columnId,
                         const Glib::ustring &  newText)        throw();

        /**
         *  Signal handler for the playlist being modified outside the window.
         */
        void
        onPlaylistModified(void)                                throw();

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *  This is used to pop up the right-click context menu.
         *
         *  @param event the button event recieved
         */
        void
        onEntryClicked(GdkEventButton *     event)              throw ();

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
         *  Signal handler for a click on the save button.
         */
        void
        onSaveButtonClicked(void)                               throw ();

        /**
         *  Signal handler for a click on the close button at the bottom
         *  right corner.
         *  This cancels the edited playlist; the normal close button (X)
         *  at the upper right corner hides the window only.
         *  If the playlist has been modified, a confirmation message will
         *  be displayed.
         */
        void
        onBottomCloseButtonClicked(void)                        throw ();

        /**
         *  Signal handler for the "lock fades" check button toggled.
         */
        void
        onLockFadesCheckButtonClicked(void)                     throw ();

        /**
         *  Signal handler for the "up" menu item selected from
         *  the right-click context menu.
         */
        void
        onUpItem(void)                                          throw ();

        /**
         *  Signal handler for the "down" menu item selected from
         *  the right-click context menu.
         */
        void
        onDownItem(void)                                        throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param  windowOpenerButton  the button which was pressed to open
         *                              this window.
         */
        PlaylistWindow(Gtk::ToggleButton *          windowOpenerButton)
                                                                throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~PlaylistWindow(void)                                   throw ();

        /**
         *  Show / update the contents of the playlist management window.
         */
        virtual void
        showContents(void)                                      throw ();

        /**
         *  Cancel the edited playlist, after asking for confirmation.
         *
         *  Displays a "Save playlist?" dialog, with "yes", "no" and
         *  "cancel" buttons.
         *
         *  @return true if the window was closed (the user selected "yes"
         *          or "no"); false if not (the user selected "cancel").
         *  @see GLiveSupport::cancelEditedPlaylist()
         *  @see closeWindow()
         */
        virtual bool
        cancelPlaylist(void)                                    throw();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // PlaylistWindow_h

