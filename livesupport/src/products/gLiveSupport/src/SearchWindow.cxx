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
const std::string    searchWhereLocalKey  = "searchWhereLocal";

/*------------------------------------------------------------------------------
 *  The 'search where' combo box key for remote searches.
 *----------------------------------------------------------------------------*/
const std::string    searchWhereRemoteKey = "searchWhereRemote";

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
                      WidgetConstants::searchWindowTitleImage,
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
    ScrolledWindow *    searchResultsView = constructSearchResultsView();

    // set the sizes of the two parts of the window
    searchInput      ->set_size_request(750, 240);
    searchResultsView->set_size_request(750, 300);
    
    // put them in one big box
    Gtk::VBox *         bigBox = Gtk::manage(new Gtk::VBox);
    bigBox->pack_start(*searchWhereBox, Gtk::PACK_SHRINK);
    bigBox->pack_start(*searchInput,      Gtk::PACK_SHRINK);
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
ScrolledWindow *
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
                                    modelColumns.titleColumn, 360);
        searchResultsTreeView->appendColumn(
                                    *getResourceUstring("creatorColumnLabel"),
                                    modelColumns.creatorColumn, 260);
        searchResultsTreeView->appendColumn(
                                    *getResourceUstring("lengthColumnLabel"),
                                    modelColumns.lengthColumn, 50);
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
    
    constructAudioClipContextMenu();
    constructPlaylistContextMenu();
    constructRemoteContextMenu();
    
    // put the tree view inside a scrolled window
    ScrolledWindow *    view = Gtk::manage(new ScrolledWindow);
    view->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
    view->add(*searchResultsTreeView);

    return view;
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
    
    onSearch(criteria);
}


/*------------------------------------------------------------------------------
 *  Event handler for the advanced Search button getting clicked.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAdvancedSearch(void)                          throw ()
{
    onSearch(advancedSearchEntry->getSearchCriteria());
}


/*------------------------------------------------------------------------------
 *  Event handler for changed selection in the Browse view.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onBrowse(void)                                  throw ()
{
    onSearch(browseEntry->getSearchCriteria());
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
    Ptr<GLiveSupport::PlayableList>::Ref    searchResults;
    try {
        searchResults = gLiveSupport->search(criteria);
    } catch (XmlRpcException &e) {
        std::cerr << e.what() << std::endl;
        return;
    }
    
    displaySearchResults(searchResults, localSearchResults);
}


/*------------------------------------------------------------------------------
 *  Display the search results.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: displaySearchResults(
                    Ptr<GLiveSupport::PlayableList>::Ref    searchResults,
                    Glib::RefPtr<Gtk::ListStore>            treeModel)
                                                                throw ()
{
    treeModel->clear();
    searchResultsTreeView->set_model(treeModel);
    
    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    GLiveSupport::PlayableList::const_iterator it;
    
    for (it = searchResults->begin(); it != searchResults->end(); ++it) {
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
    
    Ptr<StorageClientInterface>::Ref 
                                storage   = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId = gLiveSupport->getSessionId();
    
    if (remoteSearchToken) {
        try {
            storage->cancelTransport(sessionId, remoteSearchToken);
        } catch (XmlRpcException &e) {
            gLiveSupport->displayMessageWindow(formatMessage(
                                                    "remoteSearchErrorMsg",
                                                    e.what() ));
            return;
        }
    }
    
    try {
        remoteSearchToken = storage->remoteSearchOpen(sessionId, criteria);
    } catch (XmlRpcException &e) {
        gLiveSupport->displayMessageWindow(formatMessage(
                                                    "remoteSearchErrorMsg",
                                                    e.what() ));
    }
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
        
        AsyncState                                  state;
        Ptr<Glib::ustring>::Ref                     errorMessage;
        try {
            state = storage->checkTransport(remoteSearchToken, errorMessage);
        } catch (XmlRpcException &e) {
            gLiveSupport->displayMessageWindow(formatMessage(
                                                    "remoteSearchErrorMsg",
                                                    e.what() ));
            return;
        }
        
        Ptr<GLiveSupport::PlayableList>::Ref        results;
        
        if (state == AsyncState::finishedState) {
            try {
                storage->remoteSearchClose(remoteSearchToken);
            } catch (XmlRpcException &e) {
                gLiveSupport->displayMessageWindow(formatMessage(
                                                "remoteSearchErrorMsg",
                                                e.what() ));
                return;
            }
            remoteSearchToken.reset();
            
            try {
                results = gLiveSupport->getSearchResults();
            } catch (XmlRpcException &e) {
                gLiveSupport->displayMessageWindow(formatMessage(
                                                "remoteSearchErrorMsg",
                                                e.what() ));
                return;
            }
            
            displaySearchResults(results, remoteSearchResults);
            
        } else if (state == AsyncState::closedState) {
            remoteSearchToken.reset();
            displayMessage("remoteSearchErrorMsg", remoteSearchResults);
            
        } else if (state == AsyncState::failedState) {
                remoteSearchToken.reset();
                displayMessage(*errorMessage, remoteSearchResults);
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
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onEntryClicked (GdkEventButton *    event)      throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                        searchResultsTreeView->get_selection();
        if (refSelection) {
            Gtk::TreeModel::iterator iter = refSelection->get_selected();
            
            // if nothing is currently selected, select row at mouse pointer
            if (!iter) {
                Gtk::TreeModel::Path    path;
                Gtk::TreeViewColumn *   column;
                int     cell_x,
                        cell_y;
                if (searchResultsTreeView->get_path_at_pos(
                                                int(event->x), int(event->y),
                                                path, column,
                                                cell_x, cell_y )) {
                    refSelection->select(path);
                    iter = refSelection->get_selected();
                }
            }

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
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                        searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator iter = refSelection->get_selected();
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            try {
                gLiveSupport->addToScratchpad(playable);
            } catch (XmlRpcException &e) {
                std::cerr << "error in SearchWindow::onAddToScratchpad(): "
                          << e.what() << std::endl;
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Add a playable to the live mode.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: onAddToLiveMode(void)                           throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                        searchResultsTreeView->get_selection();
    Gtk::TreeModel::iterator iter = refSelection->get_selected();
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            gLiveSupport->addToLiveMode(playable);
            try {
                gLiveSupport->addToScratchpad(playable);
            } catch (XmlRpcException &e) {
                std::cerr << "error in SearchWindow::onAddToLiveMode(): "
                          << e.what() << std::endl;
            }
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
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
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
        searchInput->activatePage(3);
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
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            try {
                searchInput->activatePage(3);
                transportList->addDownload(playable);
                
            } catch (XmlRpcException &e) {
                gLiveSupport->displayMessageWindow(formatMessage(
                                        "downloadFromHubErrorMsg", e.what() ));
                return;
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
    onAddToScratchpad();
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
        searchResultsTreeView->set_model(localSearchResults);
    } else {
        searchResultsTreeView->set_model(remoteSearchResults);
    }
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
void
SearchWindow :: constructAudioClipContextMenu(void)             throw ()
{
    audioClipContextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& contextMenuList = audioClipContextMenu->items();

    try {
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToScratchpadMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onAddToScratchpad)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToLiveModeMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onAddToLiveMode)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("uploadToHubMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onUploadToHub)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    audioClipContextMenu->accelerate(*this);
}    


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for local playlists.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructPlaylistContextMenu(void)              throw ()
{
    playlistContextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& contextMenuList = playlistContextMenu->items();

    try {
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToScratchpadMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onAddToScratchpad)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToLiveModeMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onAddToLiveMode)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("exportPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onExportPlaylist)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("uploadToHubMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onUploadToHub)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    playlistContextMenu->accelerate(*this);
}    


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for remote audio clips & playlists.
 *----------------------------------------------------------------------------*/
void
SearchWindow :: constructRemoteContextMenu(void)                throw ()
{
    remoteContextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& contextMenuList = remoteContextMenu->items();

    try {
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("downloadFromHubMenuItem"),
                                sigc::mem_fun(*this,
                                        &SearchWindow::onDownloadFromHub)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    remoteContextMenu->accelerate(*this);
}    

