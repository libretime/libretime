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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"

#include "SearchWindow.h"


using namespace Glib;
using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "searchWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "SearchWindow.glade";

/*------------------------------------------------------------------------------
 *  The number of items which can be shown in the search results.
 *----------------------------------------------------------------------------*/
const int               searchResultsSize = 25;

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
SearchWindow :: SearchWindow (Gtk::ToggleButton *         windowOpenerButton)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName,
                      windowOpenerButton)
{
    glade->get_widget("searchInputNoteBook1", searchInput);
    
    Gtk::Label *    simpleSearchTabLabel;
    Gtk::Label *    advancedSearchTabLabel;
    Gtk::Label *    browseTabLabel;
    Gtk::Label *    transportsTabLabel;
    glade->get_widget("simpleSearchTabLabel1", simpleSearchTabLabel);
    glade->get_widget("advancedSearchTabLabel1", advancedSearchTabLabel);
    glade->get_widget("browseTabLabel1", browseTabLabel);
    glade->get_widget("transportsTabLabel1", transportsTabLabel);
    simpleSearchTabLabel->set_label(*getResourceUstring("simpleSearchTab"));
    advancedSearchTabLabel->set_label(*getResourceUstring("advancedSearchTab"));
    browseTabLabel->set_label(*getResourceUstring("browseTab"));
    transportsTabLabel->set_label(*getResourceUstring("transportsTab"));

    constructSearchWhereBox();
    
    constructSimpleSearchView();
    constructAdvancedSearchView();
    constructBrowseView();
    constructTransportsView();
    
    constructSearchResultsView();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
SearchWindow :: ~SearchWindow (void)                            throw ()
{
}


/*------------------------------------------------------------------------------
 *  Construct the transport type selection box.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructSearchWhereBox(void)                   throw ()
{
    Gtk::Label *                searchWhereLabel;
    glade->get_widget("searchWhereLabel1", searchWhereLabel);
    searchWhereLabel->set_label(*getResourceUstring("searchWhereLabel"));

    glade->get_widget_derived("searchWhereEntry1", searchWhereEntry);
    searchWhereEntry->append_text(*getResourceUstring("searchWhereLocal"));
    searchWhereEntry->append_text(*getResourceUstring("searchWhereRemote"));
    searchWhereEntry->set_active(0);
    searchWhereEntry->signal_changed().connect(sigc::mem_fun(*this,
                                    &SearchWindow::onSearchWhereChanged));
}    


/*------------------------------------------------------------------------------
 *  Construct the simple search view.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructSimpleSearchView(void)                 throw ()
{
    glade->get_widget("simpleSearchEntry1", simpleSearchEntry);
    simpleSearchEntry->signal_activate().connect(sigc::mem_fun(*this,
                                            &SearchWindow::onSimpleSearch));
    
    Gtk::Button *       simpleSearchButton;
    glade->get_widget("simpleSearchButton1", simpleSearchButton);
    simpleSearchButton->set_label(*getResourceUstring("searchButtonLabel"));
    simpleSearchButton->signal_clicked().connect(sigc::mem_fun(*this,
                                            &SearchWindow::onSimpleSearch));
}


/*------------------------------------------------------------------------------
 *  Construct the advanced search view.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructAdvancedSearchView(void)               throw ()
{
    advancedSearchEntry.reset(new AdvancedSearchEntry(this));
    advancedSearchEntry->connectCallback(sigc::mem_fun(*this,
                                            &SearchWindow::onAdvancedSearch ));
    
    Gtk::Button *   advancedSearchButton;
    glade->get_widget("advancedSearchButton1", advancedSearchButton);
    advancedSearchButton->set_label(*getResourceUstring("searchButtonLabel"));
    advancedSearchButton->signal_clicked().connect(sigc::mem_fun(*this,
                                            &SearchWindow::onAdvancedSearch));
}


/*------------------------------------------------------------------------------
 *  Construct the browse view.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructBrowseView(void)                       throw ()
{
    browseEntry.reset(new BrowseEntry(this));    
    browseEntry->signalChanged().connect(sigc::mem_fun(*this,
                                                &SearchWindow::onBrowse));
}


/*------------------------------------------------------------------------------
 *  Construct the transports view.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructTransportsView(void)                   throw ()
{
    transportList.reset(new TransportList(this));
}


/*------------------------------------------------------------------------------
 *  Construct the search results display.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructSearchResultsView(void)                throw ()
{
    localSearchResults  = Gtk::ListStore::create(modelColumns);
    remoteSearchResults = Gtk::ListStore::create(modelColumns);
    
    glade->get_widget_derived("searchResultsTreeView1", searchResultsTreeView);
    searchResultsTreeView->get_selection()->set_mode(Gtk::SELECTION_MULTIPLE);
    searchResultsTreeView->set_model(localSearchResults);
    searchResultsTreeView->connectModelSignals(localSearchResults);
    searchResultsTreeView->connectModelSignals(remoteSearchResults);

    searchResultsTreeView->appendColumn(
                                *getResourceUstring("typeColumnLabel"),
                                modelColumns.typeColumn, 20);
    searchResultsTreeView->appendColumn(
                                *getResourceUstring("titleColumnLabel"),
                                modelColumns.titleColumn, 300);
    searchResultsTreeView->appendColumn(
                                *getResourceUstring("creatorColumnLabel"),
                                modelColumns.creatorColumn, 200);
    searchResultsTreeView->appendColumn(
                                *getResourceUstring("sourceColumnLabel"),
                                modelColumns.sourceColumn, 145);
    searchResultsTreeView->appendCenteredColumn(
                                *getResourceUstring("lengthColumnLabel"),
                                modelColumns.lengthColumn, 55);
    
    searchResultsTreeView->signal_button_press_event().connect(
                                sigc::mem_fun(*this,
                                              &SearchWindow::onEntryClicked),
                                false /* call this first */);
    searchResultsTreeView->signal_row_activated().connect(sigc::mem_fun(*this,
                                            &SearchWindow::onDoubleClick));
    setupDndCallbacks(DND_SOURCE);
    
    audioClipContextMenu    = constructAudioClipContextMenu();
    playlistContextMenu     = constructPlaylistContextMenu();
    remoteContextMenu       = constructRemoteContextMenu();
    
    glade->get_widget("searchResultsCountLabel1", searchResultsCountLabel);
    glade->get_widget("backwardButton1", backwardButton);
    glade->get_widget("forwardButton1", forwardButton);
    backwardButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SearchWindow::onBackwardButtonClicked));
    forwardButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SearchWindow::onForwardButtonClicked));

    updatePagingToolbar();
}


/*------------------------------------------------------------------------------
 *  Event handler for the simple Search button getting clicked.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onSimpleSearch(void)                            throw ()
{
    Glib::ustring                   value = simpleSearchEntry->get_text();
    
    Ptr<MetadataTypeContainer>::Ref metadataTypes 
                                    = gLiveSupport->getMetadataTypeContainer();
    MetadataTypeContainer::Vector::const_iterator
                                    it = metadataTypes->begin();
    
    Ptr<SearchCriteria>::Ref        criteria(new SearchCriteria("all", "or"));
    Ptr<const MetadataType>::Ref    metadata;
    
    if (it != metadataTypes->end()) {
        metadata = *it;
        criteria->addCondition(*metadata->getDcName(), "partial", value);
    }

    if (++it != metadataTypes->end()) {
        metadata = *it;
        criteria->addCondition(*metadata->getDcName(), "partial", value);
    }
    
    if (++it != metadataTypes->end()) {
        metadata = *it;
        criteria->addCondition(*metadata->getDcName(), "partial", value);
    }
    
    onInitialSearch(criteria);
}


/*------------------------------------------------------------------------------
 *  Event handler for the advanced Search button getting clicked.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAdvancedSearch(void)                          throw ()
{
    onInitialSearch(advancedSearchEntry->getSearchCriteria());
}


/*------------------------------------------------------------------------------
 *  Event handler for changed selection in the Browse view.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onBrowse(void)                                  throw ()
{
    onInitialSearch(browseEntry->getSearchCriteria());
}


/*------------------------------------------------------------------------------
 *  Do the searching (first set of results).
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onInitialSearch(Ptr<SearchCriteria>::Ref    criteria)
                                                                throw ()
{
    criteria->setOffset(0);
    criteria->setLimit(getSearchResultsSize());
    onSearch(criteria);
}


/*------------------------------------------------------------------------------
 *  Do the searching (after paging backward or forward).
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onContinuedSearch(int   offset)
                                                                throw ()
{
    Ptr<SearchCriteria>::Ref    criteria = getSearchCriteria();
    criteria->setOffset(offset);
    onSearch(criteria);    
}


/*------------------------------------------------------------------------------
 *  Do the searching.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onSearch(Ptr<SearchCriteria>::Ref   criteria)
                                                                throw ()
{
    if (searchIsLocal()) {
        localSearch(criteria);
    } else {
        remoteSearchOpen(criteria);
    }
}


/*------------------------------------------------------------------------------
 *  Search in the local storage.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: localSearch(Ptr<SearchCriteria>::Ref    criteria)
                                                                throw ()
{
    displayMessage("pleaseWaitMsg", localSearchResults);
    gLiveSupport->runMainLoop();

    Ptr<StorageClientInterface>::Ref 
                                storage   = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId = gLiveSupport->getSessionId();
    
    Ptr<SearchResultsType>::Ref searchResults;
    try {
        localSearchResultsCount = storage->search(sessionId, criteria);
        searchResults           = storage->getLocalSearchResults();
    } catch (XmlRpcException &e) {
        displayLocalSearchError(e);
        return;
    }
    
    localSearchCriteria         = criteria;
    
    displaySearchResults(searchResults, localSearchResults);
}


/*------------------------------------------------------------------------------
 *  Display the search results.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: displaySearchResults(
                    Ptr<SearchResultsType>::Ref     searchResults,
                    Glib::RefPtr<Gtk::ListStore>    treeModel)
                                                                throw ()
{
    treeModel->clear();
    searchResultsTreeView->set_model(treeModel);
    updatePagingToolbar();
    
    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    SearchResultsType::const_iterator it = searchResults->begin();
    
    if (it == searchResults->end()) {
        displayMessage("nothingFoundMsg", treeModel);
        return;
    }
    
    for ( ; it != searchResults->end(); ++it) {
        Ptr<Playable>::Ref      playable = *it;
        Gtk::TreeModel::Row     row = *treeModel->append();
        
        row[modelColumns.playableColumn]    = playable;
        
        switch (playable->getType()) {
            case Playable::AudioClipType:
                row[modelColumns.typeColumn]  = widgetFactory->getPixbuf(
                                        WidgetConstants::audioClipIconImage);
                break;
            case Playable::PlaylistType:
                row[modelColumns.typeColumn]  = widgetFactory->getPixbuf(
                                        WidgetConstants::playlistIconImage);
                break;
            default:
                break;
        }

        Ptr<const Glib::ustring>::Ref
                    title   = playable->getTitle();
        row[modelColumns.titleColumn] 
                            = title ? Glib::Markup::escape_text(*title)
                                    : "";

        Ptr<Glib::ustring>::Ref
                    creator = playable->getMetadata("dc:creator");
        row[modelColumns.creatorColumn]
                            = creator ? Glib::Markup::escape_text(*creator)
                                      : "";

        Ptr<Glib::ustring>::Ref
                    source = playable->getMetadata("dc:source");
        row[modelColumns.sourceColumn]
                            = source ? Glib::Markup::escape_text(*source)
                                     : "";

        Ptr<time_duration>::Ref length = playable->getPlaylength();
        row[modelColumns.lengthColumn] = length ? 
                    *TimeConversion::timeDurationToHhMmSsString(length) : "";
    }
}


/*------------------------------------------------------------------------------
 *  Search on the network hub (initiate the async operation).
 *----------------------------------------------------------------------------*/
void
SearchWindow :: remoteSearchOpen(Ptr<SearchCriteria>::Ref   criteria)
                                                                throw ()
{
    displayMessage("pleaseWaitMsg", remoteSearchResults);
    remoteSearchCriteria.reset();
    updatePagingToolbar();
    
    Ptr<StorageClientInterface>::Ref 
                                storage   = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId = gLiveSupport->getSessionId();
    
    if (remoteSearchToken) {
        try {
            storage->cancelTransport(sessionId, remoteSearchToken);
        } catch (XmlRpcException &e) {
            displayRemoteSearchError(e);
            return;
        }
    }
    
    try {
        remoteSearchToken = storage->remoteSearchOpen(sessionId, criteria);
    } catch (XmlRpcException &e) {
        displayRemoteSearchError(e);
    }
    
    remoteSearchCriteria = criteria;
}


/*------------------------------------------------------------------------------
 *  Search on the network hub (finish the async operation).
 *----------------------------------------------------------------------------*/
void
SearchWindow :: remoteSearchClose(void)
                                                                throw ()
{
    if (remoteSearchToken) {
        Ptr<StorageClientInterface>::Ref 
                                storage   = gLiveSupport->getStorageClient();
        Ptr<SessionId>::Ref     sessionId = gLiveSupport->getSessionId();
        
        AsyncState                      state;
        Ptr<Glib::ustring>::Ref         errorMessage(new Glib::ustring());
        try {
            state = storage->checkTransport(remoteSearchToken, errorMessage);
        } catch (XmlRpcException &e) {
            displayRemoteSearchError(e);
            return;
        }
        
        Ptr<SearchResultsType>::Ref     results;
        
        if (state == AsyncState::finishedState) {
            try {
                remoteSearchResultsCount =
                                storage->remoteSearchClose(remoteSearchToken);
            } catch (XmlRpcException &e) {
                displayRemoteSearchError(e);
                return;
            }
            remoteSearchToken.reset();
            
            try {
                results = storage->getRemoteSearchResults();
            } catch (XmlRpcException &e) {
                displayRemoteSearchError(e);
                return;
            }
            
            displaySearchResults(results, remoteSearchResults);
            
        } else if (state == AsyncState::closedState) {
            remoteSearchToken.reset();
            displayMessage("shortErrorMsg", remoteSearchResults);
            
        } else if (state == AsyncState::failedState) {
            remoteSearchToken.reset();
            gLiveSupport->displayMessageWindow(*formatMessage("longErrorMsg",
                                                              *errorMessage ));
            displayMessage("shortErrorMsg", remoteSearchResults);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Display a (usually error) message in the search results tree view.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: displayMessage(const Glib::ustring &          messageKey,
                               Glib::RefPtr<Gtk::ListStore>   treeModel)
                                                                throw ()
{
    treeModel->clear();
    
    Gtk::TreeModel::Row         row = *treeModel->append();
    row[modelColumns.titleColumn]   = *getResourceUstring(messageKey);

    searchResultsTreeView->set_model(treeModel);
}


/*------------------------------------------------------------------------------
 *  Display an error message which occurred during a search.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: displayError(const XmlRpcException &        error,
                             Glib::RefPtr<Gtk::ListStore>   treeModel)
                                                                throw ()
{
    gLiveSupport->displayMessageWindow(*formatMessage("longErrorMsg",
                                                      error.what() ));
    displayMessage("shortErrorMsg", treeModel);
}


/*------------------------------------------------------------------------------
 *  Display an error message which occurred during a local search.
 *----------------------------------------------------------------------------*/
inline void
SearchWindow :: displayLocalSearchError(const XmlRpcException &     error)
                                                                throw ()
{
    displayError(error, localSearchResults);
}


/*------------------------------------------------------------------------------
 *  Display an error message which occurred during a remote search.
 *----------------------------------------------------------------------------*/
inline void
SearchWindow :: displayRemoteSearchError(const XmlRpcException &    error)
                                                                throw ()
{
    displayError(error, remoteSearchResults);
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
bool
SearchWindow :: onEntryClicked (GdkEventButton *    event)      throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        Ptr<Playable>::Ref      playable = getFirstSelectedPlayable();

        if (selectedPaths->size() == 1) {
            if (playable->getType() == Playable::AudioClipType) {
                audioClipContextMenu->popup(event->button, event->time);
                return true;
                
            } else if (playable->getType() ==  Playable::PlaylistType) {
                playlistContextMenu->popup(event->button, event->time);
                return true;
            }
            
        } else if (selectedPaths->size() > 1) {
            audioClipContextMenu->popup(event->button, event->time);
            return true;
        }
    }
    
    return false;
}


/*------------------------------------------------------------------------------
 *  Return the first selected playable item.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
SearchWindow :: getFirstSelectedPlayable(void)                      throw ()
{
    Ptr<Playable>::Ref      playable;
    
    Glib::RefPtr<Gtk::TreeView::Selection> 
                            selection = searchResultsTreeView->get_selection();
    selectedPaths.reset(new std::vector<Gtk::TreePath>(
                            selection->get_selected_rows()));

    if (selectedPaths->size() > 0) {
        selectedIter = selectedPaths->begin();
        Glib::RefPtr<Gtk::TreeModel>
                            treeModel = searchResultsTreeView->get_model();
        Gtk::TreeRow        row = *(treeModel->get_iter(*selectedIter));
        playable = row[modelColumns.playableColumn];
    }
    
    return playable;
}


/*------------------------------------------------------------------------------
 *  Return the next selected playable item.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
SearchWindow :: getNextSelectedPlayable(void)                       throw ()
{
    Ptr<Playable>::Ref      playable;
    
    if (selectedPaths) {
        if (selectedIter != selectedPaths->end()) {
            Glib::RefPtr<Gtk::TreeModel>
                            treeModel = searchResultsTreeView->get_model();
            Gtk::TreeRow    row = *(treeModel->get_iter(*selectedIter));
            playable = row[modelColumns.playableColumn];
            ++selectedIter;
        } else {
            selectedPaths.reset();
        }
    }
    
    return playable;
}


/*------------------------------------------------------------------------------
 *  Add a playable to the scratchpad.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAddToScratchpad(void)                         throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        try {
            gLiveSupport->addToScratchpad(playable);
        } catch (XmlRpcException &e) {
            Ptr<Glib::ustring>::Ref     errorMessage(new Glib::ustring(
                        "error in SearchWindow::onAddToScratchpad(): "));
            errorMessage->append(e.what());
            gLiveSupport->displayMessageWindow(*errorMessage);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the Add To Playlist menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAddToPlaylist(void)                           throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        try {
            gLiveSupport->addToPlaylist(playable->getId());
        } catch (XmlRpcException &e) {
            Ptr<Glib::ustring>::Ref     errorMessage(new Glib::ustring(
                        "error in SearchWindow::onAddToPlaylist(): "));
            errorMessage->append(e.what());
            gLiveSupport->displayMessageWindow(*errorMessage);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Add a playable to the live mode.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAddToLiveMode(void)                           throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        try {
            gLiveSupport->addToScratchpad(playable);
            playable = gLiveSupport->getPlayable(playable->getId());
            gLiveSupport->addToLiveMode(playable);
        } catch (XmlRpcException &e) {
            Ptr<Glib::ustring>::Ref     errorMessage(new Glib::ustring(
                        "error in SearchWindow::onAddToLiveMode(): "));
            errorMessage->append(e.what());
            gLiveSupport->displayMessageWindow(*errorMessage);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Edit Playlist menu item selected from the
 *  entry context menu.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onEditPlaylist(void)                            throw ()
{
    Ptr<Playable>::Ref  playable = getNextSelectedPlayable();
    Ptr<Playlist>::Ref  playlist = playable->getPlaylist();

    if (playlist) {
        try {
            gLiveSupport->openPlaylistForEditing(playlist->getId());
        } catch (XmlRpcException &e) {
            gLiveSupport->displayMessageWindow(*getResourceUstring(
                                                "cannotEditPlaylistMsg" ));
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Schedule Playlist menu item selected from the
 *  entry context menu.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onSchedulePlaylist(void)                        throw ()
{
    Ptr<Playable>::Ref  playable = getNextSelectedPlayable();
    Ptr<Playlist>::Ref  playlist = playable->getPlaylist();

    if (playlist) {
        schedulePlaylistWindow.reset(new SchedulePlaylistWindow(playlist));
        schedulePlaylistWindow->getWindow()->set_transient_for(*mainWindow);
        Gtk::Main::run(*schedulePlaylistWindow->getWindow());
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for "export playlist" in the context menu.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onExportPlaylist(void)                          throw ()
{
    Ptr<Playable>::Ref  playable = getNextSelectedPlayable();
    Ptr<Playlist>::Ref  playlist = playable->getPlaylist();

    if (playlist) {
        exportPlaylistWindow.reset(new ExportPlaylistWindow(playlist));
        exportPlaylistWindow->getWindow()->set_transient_for(*mainWindow);
        Gtk::Main::run(*exportPlaylistWindow->getWindow());
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for "upload to hub" in the context menu.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onUploadToHub(void)                             throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        uploadToHub(playable);
    }
}


/*------------------------------------------------------------------------------
 *  Add the Playable object to the list of pending "upload to hub"
 *  tasks displayed in the Transports tab.
 *----------------------------------------------------------------------------*/
bool
SearchWindow :: uploadToHub(Ptr<Playable>::Ref  playable)       throw ()
{
    try {
        searchInput->set_current_page(3);
        transportList->addUpload(playable);
        
    } catch (XmlRpcException &e) {
        gLiveSupport->displayMessageWindow(*formatMessage("uploadToHubErrorMsg",
                                                          e.what() ));
        return false;
    }
    
    return true;
}


/*------------------------------------------------------------------------------
 *  Signal handler for "download from hub" in the context menu.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onDownloadFromHub(void)                         throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        if (!gLiveSupport->existsPlayable(playable->getId())) {
            try {
                searchInput->set_current_page(3);
                transportList->addDownload(playable);
                
            } catch (XmlRpcException &e) {
                gLiveSupport->displayMessageWindow(*formatMessage(
                                    "downloadFromHubErrorMsg", e.what() ));
                return;
            }
        } else {
            onAddToScratchpad();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the user double-clicking or pressing Enter.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onDoubleClick(const Gtk::TreeModel::Path &    path,
                              const Gtk::TreeViewColumn *     column)
                                                                throw ()
{
    Ptr<Playable>::Ref      playable = getFirstSelectedPlayable();

    if (playable) {
        if (searchIsLocal()) {
            onAddToScratchpad();
        } else {
            onDownloadFromHub();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: hide(void)                                      throw ()
{
    if (exportPlaylistWindow) {
        exportPlaylistWindow->getWindow()->hide();
    }
    if (schedulePlaylistWindow) {
        schedulePlaylistWindow->getWindow()->hide();
    }
    
    GuiWindow::hide();
}


/*------------------------------------------------------------------------------
 *  Check the status of the "search where" input box.
 *----------------------------------------------------------------------------*/
bool
SearchWindow :: searchIsLocal(void)                             throw ()
{
    int     searchWhere = searchWhereEntry->get_active_row_number();
    
    switch (searchWhere) {
        case 0: return true;
                break;
        
        case 1: return false;
                break;
        
        default:
                std::cerr << "impossible value in SearchWindow::searchIsLocal()"
                          << std::endl;
                std::exit(1);
                break;
    }
}


/*------------------------------------------------------------------------------
 *  Change the displayed search results (local or remote).
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onSearchWhereChanged(void)                      throw ()
{
    if (searchIsLocal()) {
        searchInput->get_nth_page(2)->set_sensitive(true);
        searchResultsTreeView->set_model(localSearchResults);
    } else {
        if (searchInput->get_current_page() == 2) {
            searchInput->set_current_page(0);
        }
        searchInput->get_nth_page(2)->set_sensitive(false);
        searchResultsTreeView->set_model(remoteSearchResults);
    }
    
    updatePagingToolbar();
}


/*------------------------------------------------------------------------------
 *  Perform the periodic checks on the asynchronous methods.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onTimer(void)                                   throw ()
{
    remoteSearchClose();
    transportList->updateSilently();
}


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for local audio clips.
 *----------------------------------------------------------------------------*/
Ptr<Gtk::Menu>::Ref
SearchWindow :: constructAudioClipContextMenu(void)             throw ()
{
    Ptr<Gtk::Menu>::Ref     contextMenu(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToLiveModeMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onAddToLiveMode)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToPlaylistMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onAddToPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToScratchpadMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onAddToScratchpad)));
    contextMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("uploadToHubMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onUploadToHub)));

    contextMenu->accelerate(*mainWindow);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for local playlists.
 *----------------------------------------------------------------------------*/
Ptr<Gtk::Menu>::Ref
SearchWindow :: constructPlaylistContextMenu(void)              throw ()
{
    Ptr<Gtk::Menu>::Ref     contextMenu(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToLiveModeMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onAddToLiveMode)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToPlaylistMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onAddToPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToScratchpadMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onAddToScratchpad)));
    contextMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("editPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                    &SearchWindow::onEditPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("schedulePlaylistMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onSchedulePlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("exportPlaylistMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onExportPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("uploadToHubMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onUploadToHub)));

    contextMenu->accelerate(*mainWindow);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for remote audio clips & playlists.
 *----------------------------------------------------------------------------*/
Ptr<Gtk::Menu>::Ref
SearchWindow :: constructRemoteContextMenu(void)                throw ()
{
    Ptr<Gtk::Menu>::Ref     contextMenu(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("downloadFromHubMenuItem"),
                            sigc::mem_fun(*this,
                                    &SearchWindow::onDownloadFromHub)));

    contextMenu->accelerate(*mainWindow);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Return the number of search results which can be displayed.
 *----------------------------------------------------------------------------*/
int
SearchWindow :: getSearchResultsSize(void)                      throw ()
{
    return searchResultsSize;
}


/*------------------------------------------------------------------------------
 *  Event handler for a click on the Backward button.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onBackwardButtonClicked(void)                   throw ()
{
    Ptr<SearchCriteria>::Ref    criteria    = getSearchCriteria();
    int                         offset      = criteria->getOffset();    
    
    if (offset > 0) {
        int     newOffset = offset - getSearchResultsSize();
        if (newOffset < 0) {
            newOffset = 0;
        }
        onContinuedSearch(newOffset);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for a click on the Forward button.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onForwardButtonClicked(void)                    throw ()
{
    Ptr<SearchCriteria>::Ref    criteria    = getSearchCriteria();
    int                         offset      = criteria->getOffset();    
    int                         count       = getSearchResultsCount();
    
    int     newOffset = offset + getSearchResultsSize();
    if (newOffset < count) {
        onContinuedSearch(newOffset);
    }
}


/*------------------------------------------------------------------------------
 *  Enable or disable the Backward and Forward buttons.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: updatePagingToolbar(void)                       throw ()
{
    Ptr<SearchCriteria>::Ref    criteria    = getSearchCriteria();
    
    if (criteria) {
        int     offset      = criteria->getOffset();    
        int     count       = getSearchResultsCount();
        int     lastNumber  = std::min(offset + getSearchResultsSize(), count);
        
        try {
            if (count > 0) {
                searchResultsCountLabel->set_text(*formatMessage(
                                        "searchResultsCountLabel",
                                        itoa(offset + 1),
                                        itoa(lastNumber),
                                        itoa(count) ));
            } else {
                searchResultsCountLabel->set_text("");
            }
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            std::exit(1);
        }
        backwardButton->set_sensitive(offset != 0);
        forwardButton->set_sensitive(offset + getSearchResultsSize() < count);
    } else {
        searchResultsCountLabel->set_text("");
        backwardButton->set_sensitive(false);
        forwardButton->set_sensitive(false);
    }        
}


/*------------------------------------------------------------------------------
 *  The name of the window for the d'n'd methods.
 *----------------------------------------------------------------------------*/
Glib::ustring
SearchWindow :: getWindowNameForDnd (void)                          throw ()
{
    return bundleName;
}


