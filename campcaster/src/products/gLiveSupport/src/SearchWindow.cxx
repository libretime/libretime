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
#include "LiveSupport/Widgets/Button.h"
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
 *  The 'search where' combo box key for local searches.
 *----------------------------------------------------------------------------*/
const std::string       searchWhereLocalKey  = "searchWhereLocal";

/*------------------------------------------------------------------------------
 *  The 'search where' combo box key for remote searches.
 *----------------------------------------------------------------------------*/
const std::string       searchWhereRemoteKey = "searchWhereRemote";

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
SearchWindow :: SearchWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                              Ptr<ResourceBundle>::Ref    bundle,
                              Button *                    windowOpenerButton)
                                                                throw ()
          : GuiWindow(gLiveSupport,
                      bundle,
                      windowOpenerButton)
{
    Gtk::Box *          searchWhereBox     = constructSearchWhereBox();

    Gtk::Box *          simpleSearchView   = constructSimpleSearchView();
    Gtk::Box *          advancedSearchView = constructAdvancedSearchView();
    Gtk::Box *          browseView         = constructBrowseView();
    Gtk::Box *          transportsView     = constructTransportsView();

    searchInput = Gtk::manage(new ScrolledNotebook);
    try {
        set_title(*getResourceUstring("windowTitle"));
        searchInput->appendPage(*simpleSearchView, *getResourceUstring(
                                                        "simpleSearchTab"));
        searchInput->appendPage(*advancedSearchView, *getResourceUstring(
                                                        "advancedSearchTab"));
        searchInput->appendPage(*browseView, *getResourceUstring(
                                                        "browseTab"));
        searchInput->appendPage(*transportsView, *getResourceUstring(
                                                        "transportsTab"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // set up the search results box
    Gtk::Box *          searchResultsView = constructSearchResultsView();

    // set the sizes of the two parts of the window
    searchInput      ->set_size_request(766, 231);
    searchResultsView->set_size_request(766, 343);
    
    // put them in one big box
    Gtk::VBox *         bigBox = Gtk::manage(new Gtk::VBox);
    bigBox->pack_start(*searchWhereBox, Gtk::PACK_SHRINK);
    bigBox->pack_start(*searchInput,    Gtk::PACK_SHRINK);
    bigBox->pack_start(*searchResultsView);
    add(*bigBox);
    
    // show
    set_name("searchWindow");
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    
    show_all_children();
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
Gtk::VBox*
SearchWindow :: constructSearchWhereBox(void)                   throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Label *                searchWhereLabel;
    try {
        searchWhereLabel = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("searchWhereLabel") ));
        searchWhereEntry = Gtk::manage(wf->createComboBoxText());
        
        Ptr<Glib::ustring>::Ref localKey(new Glib::ustring(
                                                searchWhereLocalKey));
        Ptr<Glib::ustring>::Ref remoteKey(new Glib::ustring(
                                                searchWhereRemoteKey));
        
        searchWhereEntry->appendPair(getResourceUstring(searchWhereLocalKey),
                                     localKey);
        searchWhereEntry->appendPair(getResourceUstring(searchWhereRemoteKey),
                                     remoteKey);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    searchWhereEntry->set_active(0);
    searchWhereEntry->signalSelectionChanged().connect(sigc::mem_fun(
                                *this, &SearchWindow::onSearchWhereChanged ));
    
    Gtk::HBox *         hBox = Gtk::manage(new Gtk::HBox);
    hBox->pack_start(*searchWhereLabel, Gtk::PACK_SHRINK, 5);
    hBox->pack_start(*searchWhereEntry, Gtk::PACK_SHRINK);
    
    Gtk::HBox *         padding = Gtk::manage(new Gtk::HBox);
    
    Gtk::VBox *         vBox = Gtk::manage(new Gtk::VBox);
    vBox->pack_start(*hBox,    Gtk::PACK_SHRINK, 5);
    vBox->pack_start(*padding, Gtk::PACK_SHRINK, 5);
    
    return vBox;
}    


/*------------------------------------------------------------------------------
 *  Construct the simple search view.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
SearchWindow :: constructSimpleSearchView(void)                 throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    // set up the entry box
    simpleSearchEntry = Gtk::manage(wf->createEntryBin());
    
    Button *        searchButton;
    try {
        searchButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("searchButtonLabel") ));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    simpleSearchEntry->signal_activate().connect(sigc::mem_fun(
                                    *this, &SearchWindow::onSimpleSearch ));
    searchButton->signal_clicked().connect(sigc::mem_fun(
                                    *this, &SearchWindow::onSimpleSearch ));

    Gtk::HBox *         entryBox = Gtk::manage(new Gtk::HBox);
    entryBox->pack_start(*simpleSearchEntry, Gtk::PACK_EXPAND_WIDGET,  5);
    entryBox->pack_start(*searchButton,      Gtk::PACK_SHRINK,         5);

    // make the search entry + button take up 50% of the window horizontally
    Gtk::Alignment *    entryAlignment = Gtk::manage(new Gtk::Alignment(
                                                           0, 0, 0.5, 0));
    entryAlignment->add(*entryBox);

    // make a new box and pack the main components into it
    Gtk::VBox *         view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*entryAlignment, Gtk::PACK_SHRINK, 5);
    
    return view;
}


/*------------------------------------------------------------------------------
 *  Construct the advanced search view.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
SearchWindow :: constructAdvancedSearchView(void)               throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    // the two main components of the window
    advancedSearchEntry = Gtk::manage(new AdvancedSearchEntry(gLiveSupport));
    Gtk::Box *  searchButtonBox = Gtk::manage(new Gtk::HButtonBox(
                                    Gtk::BUTTONBOX_END ));
    
    // set up the callback function for the entry field
    advancedSearchEntry->connectCallback(sigc::mem_fun(
                                    *this, &SearchWindow::onAdvancedSearch ));
    
    // set up the search button box
    try {
        Button *        searchButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("searchButtonLabel") ));
        searchButton->signal_clicked().connect(sigc::mem_fun(
                                    *this, &SearchWindow::onAdvancedSearch ));
        searchButtonBox->pack_start(*searchButton, Gtk::PACK_SHRINK, 5);

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    // make a new box and pack the main components into it
    Gtk::VBox *     view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*advancedSearchEntry,    Gtk::PACK_SHRINK,         5);
    view->pack_start(*searchButtonBox,        Gtk::PACK_SHRINK,         5);
    
    return view;
}


/*------------------------------------------------------------------------------
 *  Construct the browse view.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
SearchWindow :: constructBrowseView(void)                       throw ()
{
    // set up the browse input fields
    browseEntry = Gtk::manage(new BrowseEntry(gLiveSupport, getBundle()));
    
    browseEntry->signalSelectionChanged().connect(sigc::mem_fun(
                                            *this, &SearchWindow::onBrowse ));

    // make a new box and pack the main components into it
    Gtk::VBox *         view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*browseEntry,    Gtk::PACK_EXPAND_WIDGET, 5);
    return view;
}


/*------------------------------------------------------------------------------
 *  Construct the advanced search view.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
SearchWindow :: constructTransportsView(void)                   throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    try {
        transportList = Gtk::manage(new TransportList(
                                    gLiveSupport,
                                    gLiveSupport->getBundle("transportList") ));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    ScrolledWindow *    scrolledWindow = Gtk::manage(new ScrolledWindow);
    scrolledWindow->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
    scrolledWindow->add(*transportList);
    
    Gtk::VBox *         view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*scrolledWindow);
    
    return view;
}


/*------------------------------------------------------------------------------
 *  Construct the search results display.
 *----------------------------------------------------------------------------*/
Gtk::Box *
SearchWindow :: constructSearchResultsView(void)                throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    localSearchResults  = Gtk::ListStore::create(modelColumns);
    remoteSearchResults = Gtk::ListStore::create(modelColumns);
    
    searchResultsTreeView = Gtk::manage(wf->createTreeView(localSearchResults));
    searchResultsTreeView->connectModelSignals(remoteSearchResults);

    // add the TreeView's view columns
    try {
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
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    // register the signal handler for treeview entries being clicked
    searchResultsTreeView->signal_button_press_event().connect_notify(
                                sigc::mem_fun(
                                    *this, &SearchWindow::onEntryClicked));
    searchResultsTreeView->signal_row_activated().connect(sigc::mem_fun(
                                    *this, &SearchWindow::onDoubleClick));
    
    // create the right-click context menus
    audioClipContextMenu    = constructAudioClipContextMenu();
    playlistContextMenu     = constructPlaylistContextMenu();
    remoteContextMenu       = constructRemoteContextMenu();
    
    // put the tree view inside a scrolled window
    ScrolledWindow *    resultsWindow = Gtk::manage(new ScrolledWindow);
    resultsWindow->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
    resultsWindow->add(*searchResultsTreeView);
    
    // create the paging toolbar
    try {
        backwardButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("backwardButtonLabel")));
        forwardButton  = Gtk::manage(wf->createButton(
                                *getResourceUstring("forwardButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    backwardButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SearchWindow::onBackwardButtonClicked));
    forwardButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SearchWindow::onForwardButtonClicked));
    
    Gtk::Box *  pagingButtonBox = Gtk::manage(new Gtk::HButtonBox(
                                    Gtk::BUTTONBOX_DEFAULT_STYLE, 5));
    pagingButtonBox->add(*backwardButton);
    pagingButtonBox->add(*forwardButton);
    
    searchResultsCountLabel = Gtk::manage(new Gtk::Label());
    
    Gtk::Box *  pagingToolbar = Gtk::manage(new Gtk::HBox);
    pagingToolbar->pack_start(*searchResultsCountLabel,
                                                Gtk::PACK_EXPAND_WIDGET, 5);
    pagingToolbar->pack_start(*pagingButtonBox, Gtk::PACK_SHRINK,        5);
    
    updatePagingToolbar();

    // pack everything in a box
    Gtk::Box *      view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*pagingToolbar,  Gtk::PACK_SHRINK,         5);
    view->pack_start(*resultsWindow,  Gtk::PACK_EXPAND_WIDGET,  0);
    
    return   view;
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
            gLiveSupport->displayMessageWindow(formatMessage("longErrorMsg",
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
    gLiveSupport->displayMessageWindow(formatMessage("longErrorMsg",
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
void
SearchWindow :: onEntryClicked (GdkEventButton *    event)      throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        Gtk::TreePath           currentPath;
        Gtk::TreeViewColumn *   column;
        int     cell_x,
                cell_y;
        bool foundValidRow = searchResultsTreeView->get_path_at_pos(
                                            int(event->x), int(event->y),
                                            currentPath, column,
                                            cell_x, cell_y);

        if (foundValidRow) {
            Gtk::TreeIter   iter = searchResultsTreeView->get_model()
                                                        ->get_iter(currentPath);
            if (iter) {
                Ptr<Playable>::Ref  playable =
                                         (*iter)[modelColumns.playableColumn];
                
                if (playable) {
                    if (searchIsLocal()) {
                        switch (playable->getType()) {
                            case Playable::AudioClipType:
                                audioClipContextMenu->popup(event->button,
                                                            event->time);
                                break;
                                
                            case Playable::PlaylistType:
                                playlistContextMenu->popup(event->button,
                                                           event->time);
                                break;

                            default:
                                break;
                        }
                    } else {
                        remoteContextMenu->popup(event->button, event->time);
                    }
                }
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Add a playable to the scratchpad.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAddToScratchpad(void)                         throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            try {
                gLiveSupport->addToScratchpad(playable);
            } catch (XmlRpcException &e) {
                Ptr<Glib::ustring>::Ref     errorMessage(new Glib::ustring(
                            "error in SearchWindow::onAddToScratchpad(): "));
                errorMessage->append(e.what());
                gLiveSupport->displayMessageWindow(errorMessage);
            }
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
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        try {
            gLiveSupport->addToPlaylist(playable->getId());
        } catch (XmlRpcException &e) {
                Ptr<Glib::ustring>::Ref     errorMessage(new Glib::ustring(
                            "error in SearchWindow::onAddToPlaylist(): "));
                errorMessage->append(e.what());
                gLiveSupport->displayMessageWindow(errorMessage);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Add a playable to the live mode.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAddToLiveMode(void)                           throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            try {
                gLiveSupport->addToScratchpad(playable);
                playable = gLiveSupport->getPlayable(playable->getId());
                gLiveSupport->addToLiveMode(playable);
            } catch (XmlRpcException &e) {
                Ptr<Glib::ustring>::Ref     errorMessage(new Glib::ustring(
                            "error in SearchWindow::onAddToLiveMode(): "));
                errorMessage->append(e.what());
                gLiveSupport->displayMessageWindow(errorMessage);
            }
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
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        Ptr<Playlist>::Ref      playlist = playable->getPlaylist();
        if (playlist) {
            try {
                gLiveSupport->openPlaylistForEditing(playlist->getId());
            } catch (XmlRpcException &e) {
                gLiveSupport->displayMessageWindow(getResourceUstring(
                                                    "cannotEditPlaylistMsg" ));
            }
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
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();

    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        Ptr<Playlist>::Ref  playlist = playable->getPlaylist();
        if (playlist) {
            schedulePlaylistWindow.reset(new SchedulePlaylistWindow(
                            gLiveSupport,
                            gLiveSupport->getBundle("schedulePlaylistWindow"),
                            playlist));
            schedulePlaylistWindow->set_transient_for(*this);
            Gtk::Main::run(*schedulePlaylistWindow);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for "export playlist" in the context menu.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onExportPlaylist(void)                          throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            Ptr<Playlist>::Ref  playlist = playable->getPlaylist();
            if (playlist) {
                try {
                    exportPlaylistWindow.reset(new ExportPlaylistWindow(
                                gLiveSupport,
                                gLiveSupport->getBundle("exportPlaylistWindow"),
                                playlist));
                } catch (std::invalid_argument &e) {
                    std::cerr << e.what() << std::endl;
                    return;
                }
                exportPlaylistWindow->set_transient_for(*this);
                Gtk::Main::run(*exportPlaylistWindow);
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for "upload to hub" in the context menu.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onUploadToHub(void)                             throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();

    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            uploadToHub(playable);
        }
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
        searchInput->setActivePage(3);
        transportList->addUpload(playable);
        
    } catch (XmlRpcException &e) {
        gLiveSupport->displayMessageWindow(formatMessage("uploadToHubErrorMsg",
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
    Glib::RefPtr<Gtk::TreeView::Selection>
                        refSelection = searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator
                        iter         = refSelection->get_selected();

    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            if (!gLiveSupport->existsPlayable(playable->getId())) {
                try {
                    searchInput->setActivePage(3);
                    transportList->addDownload(playable);
                    
                } catch (XmlRpcException &e) {
                    gLiveSupport->displayMessageWindow(formatMessage(
                                        "downloadFromHubErrorMsg", e.what() ));
                    return;
                }
            } else {
                onAddToScratchpad();
            }
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
    if (searchIsLocal()) {
        onAddToScratchpad();
    } else {
        onDownloadFromHub();
    }
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: on_hide(void)                                   throw ()
{
    if (exportPlaylistWindow) {
        exportPlaylistWindow->hide();
    }
    if (schedulePlaylistWindow) {
        schedulePlaylistWindow->hide();
    }
    
    GuiWindow::on_hide();
}


/*------------------------------------------------------------------------------
 *  Check the status of the "search where" input box.
 *----------------------------------------------------------------------------*/
bool
SearchWindow :: searchIsLocal(void)                             throw ()
{
    Ptr<const Glib::ustring>::Ref   searchWhere
                                    = searchWhereEntry->getActiveKey();
    
    if (*searchWhere == searchWhereLocalKey) {
        return true;
    } else {
        return false;
    }
}


/*------------------------------------------------------------------------------
 *  Change the displayed search results (local or remote).
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onSearchWhereChanged(void)                      throw ()
{
    if (searchIsLocal()) {
        searchInput->setPageSensitive(2, true);
        searchResultsTreeView->set_model(localSearchResults);
    } else {
        if (searchInput->getActivePage() == 2) {
            searchInput->setActivePage(0);
        }
        searchInput->setPageSensitive(2, false);
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
Gtk::Menu *
SearchWindow :: constructAudioClipContextMenu(void)             throw ()
{
    Gtk::Menu *             contextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    try {
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
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    contextMenu->accelerate(*this);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for local playlists.
 *----------------------------------------------------------------------------*/
Gtk::Menu *
SearchWindow :: constructPlaylistContextMenu(void)              throw ()
{
    Gtk::Menu *             contextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    try {
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
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    contextMenu->accelerate(*this);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for remote audio clips & playlists.
 *----------------------------------------------------------------------------*/
Gtk::Menu *
SearchWindow :: constructRemoteContextMenu(void)                throw ()
{
    Gtk::Menu *             contextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    try {
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("downloadFromHubMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onDownloadFromHub)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    contextMenu->accelerate(*this);
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
        backwardButton->setDisabled(offset == 0);
        forwardButton->setDisabled(offset + getSearchResultsSize() >= count);
    } else {
        searchResultsCountLabel->set_text("");
        backwardButton->setDisabled(true);
        forwardButton->setDisabled(true);
    }        
}


/*------------------------------------------------------------------------------
 *  Convert an integer to a string.
 *----------------------------------------------------------------------------*/
Glib::ustring
SearchWindow :: itoa(int    number)                             throw ()
{
    std::ostringstream  stream;
    stream << number;
    Glib::ustring       string = stream.str();
    return string;
}

