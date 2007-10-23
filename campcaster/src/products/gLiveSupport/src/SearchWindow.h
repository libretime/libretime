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
#ifndef SearchWindow_h
#define SearchWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "GuiWindow.h"
#include "DndMethods.h"
#include "LiveSupport/Core/NumericTools.h"

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"
#include "AdvancedSearchEntry.h"
#include "BrowseEntry.h"
#include "GLiveSupport.h"
#include "SchedulePlaylistWindow.h"
#include "ExportPlaylistWindow.h"
#include "TransportList.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The Search/Browse window.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class SearchWindow : public  GuiWindow,
                     public  DndMethods,
                     private NumericTools
{
    private:

        /**
         *  The criteria for the last local search.
         */
        Ptr<SearchCriteria>::Ref    localSearchCriteria;

        /**
         *  The criteria for the last remote search.
         */
        Ptr<SearchCriteria>::Ref    remoteSearchCriteria;

        /**
         *  The number of items found by the last local search.
         */
        int                         localSearchResultsCount;

        /**
         *  The number of items found by the last remote search.
         */
        int                         remoteSearchResultsCount;

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
         *  The "search where" input field.
         */
        ComboBoxText *              searchWhereEntry;

        /**
         *  The simple search input field.
         */
        Gtk::Entry *                simpleSearchEntry;

        /**
         *  The box containing the advanced search input fields.
         */
        Ptr<AdvancedSearchEntry>::Ref   advancedSearchEntry;

        /**
         *  The box containing the browse input fields.
         */
        Ptr<BrowseEntry>::Ref       browseEntry;

        /**
         *  The list of transports in progress.
         */
        Ptr<TransportList>::Ref     transportList;

        /**
         *  The button for paging in the search results backward.
         */
        Gtk::Button *               backwardButton;

        /**
         *  The button for paging in the search results forward.
         */
        Gtk::Button *               forwardButton;

        /**
         *  The Schedule Playlist pop-up window.
         */
        Ptr<SchedulePlaylistWindow>::Ref    schedulePlaylistWindow;

        /**
         *  The Export Playlist pop-up window.
         */
        Ptr<ExportPlaylistWindow>::Ref      exportPlaylistWindow;

        /**
         *  The tree model, as a GTK reference, for the local search results.
         */
        Glib::RefPtr<Gtk::ListStore>    localSearchResults;

        /**
         *  The tree model, as a GTK reference, for the remote search results.
         */
        Glib::RefPtr<Gtk::ListStore>    remoteSearchResults;

        /**
         *  The tree view showing the search results.
         */
        ZebraTreeView *                 searchResultsTreeView;

        /**
         *  The label showing the number of search results.
         */
        Gtk::Label *                    searchResultsCountLabel;

        /**
         *  The notebook for the various tabs in the window.
         */
        Gtk::Notebook *                 searchInput;

        /**
         *  The transport token used when a remote search is pending.
         */
        Ptr<const Glib::ustring>::Ref   remoteSearchToken;

        /**
         *  The pop-up context menu for local audio clips.
         */
        Ptr<Gtk::Menu>::Ref             audioClipContextMenu;

        /**
         *  The pop-up context menu for local playlists.
         */
        Ptr<Gtk::Menu>::Ref             playlistContextMenu;

        /**
         *  The pop-up context menu for remote audio clips and playlists.
         */
        Ptr<Gtk::Menu>::Ref             remoteContextMenu;

        /**
         *  Construct the "search where" box.
         *  This contains a combo box, where the user can choose between
         *  local search or hub search.
         */
        void
        constructSearchWhereBox(void)                           throw ();

        /**
         *  Construct the simple search view.
         *  If you enter a string in theGtk::VBox simple search view and 
         *  press Enter
         *  (or the Search button), the local storage will be searched for
         *  items (both audio clips and playlists) where either the title
         *  (dc:title), the creator (dc:creator) or the album (dc:source)
         *  metadata fields contain this string.
         */
        void
        constructSimpleSearchView(void)                         throw ();

        /**
         *  Construct the advanced search view.
         */
        void
        constructAdvancedSearchView(void)                       throw ();

        /**
         *  Construct the browse view.
         */
        void
        constructBrowseView(void)                               throw ();

        /**
         *  Construct the advanced search view.
         */
        void
        constructTransportsView(void)                           throw ();

        /**
         *  Construct the search results display.
         */
        void
        constructSearchResultsView(void)                        throw ();

        /**
         *  Construct the right-click context menu for local audio clips.
         *
         *  @return the context menu created.
         */
        Ptr<Gtk::Menu>::Ref
        constructAudioClipContextMenu(void)                     throw ();

        /**
         *  Construct the right-click context menu for local playlists.
         *
         *  @return the context menu created.
         */
        Ptr<Gtk::Menu>::Ref
        constructPlaylistContextMenu(void)                      throw ();

        /**
         *  Construct the right-click context menu for remote audio clips
         *  and playlists.
         *
         *  @return the context menu created.
         */
        Ptr<Gtk::Menu>::Ref
        constructRemoteContextMenu(void)                        throw ();

        /**
         *  Check the status of the "search where" input box.
         */
        bool
        searchIsLocal(void)                                     throw ();

        /**
         *  Get the search criteria used for the last search
         *  of the currently selected kind.
         *  Returns either localSearchCriteria or remoteSearchCriteria
         *  depending on the value of searchIsLocal().
         *
         *  @return the saved search criteria of the appropriate kind;
         *          or a 0 pointer if nothing has been saved yet.
         */
        Ptr<SearchCriteria>::Ref
        getSearchCriteria(void)                                 throw ()
        {
            return searchIsLocal() ? localSearchCriteria
                                   : remoteSearchCriteria;
        }

        /**
         *  Get the number of search results found by the last search
         *  of the currently selected kind.
         *  Returns either localSearchResultsCount or remoteSearchResultsCount
         *  depending on the value of searchIsLocal().
         *
         *  @return the saved search result count of the appropriate kind;
         *          undefined if nothing has been saved yet.
         */
        int
        getSearchResultsCount(void)                             throw ()
        {
            return searchIsLocal() ? localSearchResultsCount
                                   : remoteSearchResultsCount;
        }

        /**
         *  Change the displayed search results (local or remote).
         */
        void
        onSearchWhereChanged(void)                              throw ();

        /**
         *  Search in the local storage.
         *
         *  @param  criteria    the search criteria.
         */
        void
        localSearch(Ptr<SearchCriteria>::Ref    criteria)       throw ();

        /**
         *  Search on the network hub (initiate the async operation).
         *
         *  @param  criteria    the search criteria.
         */
        void
        remoteSearchOpen(Ptr<SearchCriteria>::Ref   criteria)   throw ();

        /**
         *  Search on the network hub (finish the async operation).
         */
        void
        remoteSearchClose(void)                                 throw ();

        /**
         *  Typedef to save some typing.
         */
        typedef StorageClientInterface::SearchResultsType
                                                    SearchResultsType;

        /**
         *  Display the search results.
         *  The most important metadata are shown in the rows of the given
         *  tree model.
         */
        void
        displaySearchResults(
                    Ptr<SearchResultsType>::Ref     searchResults,
                    Glib::RefPtr<Gtk::ListStore>    treeModel)
                                                                    throw ();

        /**
         *  Update the paging portion of the search results view.
         *  Prints the number of results, and enables or disables
         *  the Backward and Forward buttons.
         */
        void
        updatePagingToolbar(void)                                   throw ();

        /**
         *  Display a (usually error) message in the search results tree view.
         *
         *  @param  messageKey  the localization key for the message.
         *  @param  treeModel   the tree model to display the message in.
         */
        void
        displayMessage(const Glib::ustring &          messageKey,
                       Glib::RefPtr<Gtk::ListStore>   treeModel)
                                                                    throw ();
        
        /**
         *  Display an error message which occurred during a search.
         *
         *  @param  error       the error which occurred.
         *  @param  treeModel   the tree model to display the message in.
         */
        void
        displayError(const XmlRpcException &        error,
                     Glib::RefPtr<Gtk::ListStore>   treeModel)
                                                                    throw ();
        
        /**
         *  Display an error message which occurred during a local search.
         *
         *  @param  error       the error which occurred.
         */
        void
        displayLocalSearchError(const XmlRpcException &     error)
                                                                    throw ();
        
        /**
         *  Display an error message which occurred during a remote search.
         *
         *  @param  error       the error which occurred.
         */
        void
        displayRemoteSearchError(const XmlRpcException &    error)
                                                                    throw ();
        

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
                 *  The column for the creator of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         creatorColumn;

                /**
                 *  The column for the album of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         sourceColumn;

                /**
                 *  The column for the length of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         lengthColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(typeColumn);
                    add(titleColumn);
                    add(creatorColumn);
                    add(sourceColumn);
                    add(lengthColumn);
                }
        };

        /**
         *  The column model.
         */
        ModelColumns                    modelColumns;

        /**
         *  Return the number of search results which can be displayed.
         *  As currently implemented, this returns a constant 10.
         *
         *  @return the number of rows which can be displayed in the
         *          search results section of the window.
         */
        int
        getSearchResultsSize(void)                              throw ();

        /**
         *  Event handler for the simple Search button getting clicked.
         */
        void
        onSimpleSearch(void)                                    throw ();

        /**
         *  Event handler for the advanced Search button getting clicked.
         */
        void
        onAdvancedSearch(void)                                  throw ();

        /**
         *  Event handler for changed selection in the Browse view.
         */
        void
        onBrowse(void)                                          throw ();

        /**
         *  Do the searching (first set of results).
         *  Sets the offset to 0, and calls onSearch().
         *
         *  @param  criteria    the search criteria.
         */
        void
        onInitialSearch(Ptr<SearchCriteria>::Ref    criteria)   throw ();

        /**
         *  Do the searching (after paging backward or forward).
         *  Sets the offset to the given value, and calls onSearch().
         *
         *  @param  offset  the new offset to use for this search.
         */
        void
        onContinuedSearch(int   offset)                         throw ();

        /**
         *  Do the searching.
         *  Calls either localSearch() or remoteSearch().
         *
         *  @param  criteria    the search criteria.
         */
        void
        onSearch(Ptr<SearchCriteria>::Ref       criteria)       throw ();

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *
         *  @param event the button event received
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
         *  Add a playable to the scratchpad.
         */
        void
        onAddToScratchpad(void)                                 throw ();

        /**
         *  Signal handler for the "add to playlist" menu item selected from
         *  the entry context menu.
         */
        void
        onAddToPlaylist(void)                                   throw ();

        /**
         *  Add a playable to the live mode.
         */
        void
        onAddToLiveMode(void)                                   throw ();

        /**
         *  Signal handler for the "edit playlist" menu item selected from
         *  the entry context menu.
         */
        void
        onEditPlaylist(void)                                    throw ();

        /**
         *  Signal handler for the "schedule playlist" menu item selected
         *  from the entry context menu.
         */
        void
        onSchedulePlaylist(void)                                throw ();

        /**
         *  Signal handler for the "export playlist" menu item selected from
         *  the entry context menu.
         */
        void
        onExportPlaylist(void)                                  throw ();
        
        /**
         *  Signal handler for "upload to hub" in the context menu.
         */
        void
        onUploadToHub(void)                                     throw ();
        
        /**
         *  Signal handler for "download from hub" in the context menu.
         */
        void
        onDownloadFromHub(void)                                 throw ();
        
        /**
         *  Event handler for a click on the Backward button.
         */
        void
        onBackwardButtonClicked(void)                           throw ();

        /**
         *  Event handler for a click on the Forward button.
         */
        void
        onForwardButtonClicked(void)                            throw ();

        /**
         *  The tree view we want to implement d'n'd on.
         */
        virtual Gtk::TreeView *
        getTreeViewForDnd (void)                                    throw ()
        {
            return searchResultsTreeView;
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
        getFirstSelectedPlayable(void)                              throw ();

        /**
         *  Used to iterate over the selected rows.
         *  Reset to the first row by onEntryClicked().
         *  Returns a 0 pointer if nothing is selected or we have reached
         *  the end of the list of selected rows.
         *
         *  @return the next selected playable item.
         */
        virtual Ptr<Playable>::Ref
        getNextSelectedPlayable(void)                               throw ();

        /**
         *  Add an item to the tree view at the given position.
         *  Required to implement by DndMethods, does not do anything here.
         *
         *  @param  iter    the iterator pointing to the row to be filled in.
         *  @param  id      the ID of the item to add.
         */
        virtual void
        addItem(Gtk::TreeIter               iter,
                Ptr<const UniqueId>::Ref    id)                     throw ()
        {
        }


    public:

        /**
         *  Constructor.
         *
         *  @param  windowOpenerButton  the button which was pressed to open
         *                              this window.
         */
        SearchWindow(Gtk::ToggleButton *         windowOpenerButton)
                                                                throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~SearchWindow(void)                                     throw ();
        
        /**
         *  Perform the periodic checks on the asynchronous methods.
         *  This is called every few seconds by the onUpdateTime() function 
         *  in the MasterPanelWindow.
         */
        void
        onTimer(void)                                           throw ();
        
        /**
         *  Add the Playable object to the list of pending "upload to hub"
         *  tasks displayed in the Transports tab.
         *
         *  @param  playable    the object to be uploaded to the hub.
         *  @return true        on success.
         */
        bool
        uploadToHub(Ptr<Playable>::Ref  playable)               throw ();

        /**
         *  Hide the window.
         *
         *  This overrides GuiWindow::hide(), and closes the Export Playlist
         *  and Schedule Playlist windows, if they are still open.
         */
        virtual void
        hide(void)                                              throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // SearchWindow_h

