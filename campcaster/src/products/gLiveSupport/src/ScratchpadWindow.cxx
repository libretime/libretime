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

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"

#include "ScratchpadWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/**
 *  The name of the window, used by the keyboard shortcuts (or by the .gtkrc).
 */
const Glib::ustring     windowName = "scratchpadWindow";

/*------------------------------------------------------------------------------
 *  The name of the user preference for storing Scratchpad contents
 *----------------------------------------------------------------------------*/
const Glib::ustring     userPreferencesKeyName = "scratchpadContents";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ScratchpadWindow :: ScratchpadWindow (
                        Ptr<GLiveSupport>::Ref      gLiveSupport,
                        Ptr<ResourceBundle>::Ref    bundle,
                        Button *                    windowOpenerButton)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle, 
                      windowOpenerButton)
{
    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    try {
        set_title(*getResourceUstring("windowTitle"));
        addToPlaylistButton = Gtk::manage(widgetFactory->createButton(
                            *getResourceUstring("addToPlaylistButtonLabel")));
        clearListButton = Gtk::manage(widgetFactory->createButton(
                            *getResourceUstring("clearListButtonLabel")));
        removeButton = Gtk::manage(widgetFactory->createButton(
                            *getResourceUstring("removeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    addToPlaylistButton->set_name("addToPlaylistButton");
    addToPlaylistButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &ScratchpadWindow::onAddToPlaylistButtonClicked));

    clearListButton->set_name("clearListButton");
    clearListButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &ScratchpadWindow::onClearListButtonClicked));

    removeButton->set_name("removeButton");
    removeButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &ScratchpadWindow::onRemoveItemButtonClicked));

    add(vBox);

    // Create the Tree model:
    treeModel = Gtk::ListStore::create(modelColumns);
    treeView = Gtk::manage(widgetFactory->createTreeView(treeModel));
    treeView->get_selection()->set_mode(Gtk::SELECTION_MULTIPLE);
    treeView->set_reorderable(true);
    treeView->set_enable_search(false);

    // Add the TreeView's view columns:
    try {
        treeView->appendColumn(*getResourceUstring("typeColumnLabel"),
                               modelColumns.typeColumn, 20);
        treeView->appendColumn(*getResourceUstring("titleColumnLabel"),
                               modelColumns.titleColumn, 200);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // register the signal handler for treeview entries being clicked
    treeView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                            &ScratchpadWindow::onEntryClicked));
    treeView->signal_row_activated().connect(sigc::mem_fun(*this,
                                            &ScratchpadWindow::onDoubleClick));

    // register the signal handler for keyboard key presses
    treeView->signal_key_press_event().connect(sigc::mem_fun(*this,
                                            &ScratchpadWindow::onKeyPressed));

    // Add the TreeView, inside a ScrolledWindow, with the button underneath:
    scrolledWindow.add(*treeView);

    // Only show the scrollbars when they are necessary:
    scrolledWindow.set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);

    cueAudioButtons = Gtk::manage(new CuePlayer(
                                    gLiveSupport, treeView, modelColumns ));
    topButtonBox.pack_start(*cueAudioButtons, Gtk::PACK_EXPAND_PADDING);
    
    middleButtonBox.set_layout(Gtk::BUTTONBOX_END);
    middleButtonBox.set_spacing(5);
    middleButtonBox.pack_start(*addToPlaylistButton);

    bottomButtonBox.set_layout(Gtk::BUTTONBOX_END);
    bottomButtonBox.set_spacing(5);
    bottomButtonBox.pack_start(*clearListButton);
    bottomButtonBox.pack_start(*removeButton);

    // pack everything in the main box
    vBox.pack_start(topButtonBox, Gtk::PACK_SHRINK, 5);
    vBox.pack_start(scrolledWindow, Gtk::PACK_EXPAND_WIDGET, 5);
    vBox.pack_start(middleButtonBox, Gtk::PACK_SHRINK, 5);
    vBox.pack_start(bottomButtonBox, Gtk::PACK_SHRINK, 5);

    // create the right-click entry context menu for audio clips
    audioClipMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& audioClipMenuList = audioClipMenu->items();
    // register the signal handlers for the popup menu
    try {
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cueMenuItem"),
                                sigc::mem_fun(*cueAudioButtons,
                                        &CuePlayer::onPlayItem)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToLiveModeMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onAddToLiveMode)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onAddToPlaylist)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*treeView,
                                        &ZebraTreeView::onRemoveMenuOption)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("uploadToHubMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onUploadToHub)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    audioClipMenu->accelerate(*this);

    // create the right-click entry context menu for playlists
    playlistMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& playlistMenuList = playlistMenu->items();
    // register the signal handlers for the popup menu

    try{
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cueMenuItem"),
                                sigc::mem_fun(*cueAudioButtons,
                                    &CuePlayer::onPlayItem)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToLiveModeMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onAddToLiveMode)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                    &ScratchpadWindow::onAddToPlaylist)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*treeView,
                                    &ZebraTreeView::onRemoveMenuOption)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("editPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onEditPlaylist)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("schedulePlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                    &ScratchpadWindow::onSchedulePlaylist)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("exportPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onExportPlaylist)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("uploadToHubMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onUploadToHub)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    playlistMenu->accelerate(*this);
    
    userPreferencesKey.reset(new const Glib::ustring(userPreferencesKeyName));
    
    // show
    set_name(windowName);
    set_default_size(300, 330);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    
    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Event handler for the add to playlist button getting clicked.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onAddToPlaylistButtonClicked (void)         throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> 
                    selection       = treeView->get_selection();
    std::vector<Gtk::TreePath> 
                    selectedRows    = selection->get_selected_rows();

    std::vector<Gtk::TreePath>::iterator    iter;
    for (iter = selectedRows.begin(); iter != selectedRows.end(); ++iter) {
        Gtk::TreeIter   ti = treeModel->get_iter(*iter);
        if (ti) {
            Ptr<Playable>::Ref  playable = (*ti)[modelColumns.playableColumn];
            try {
                gLiveSupport->addToPlaylist(playable->getId());
            } catch (XmlRpcException &e) {
                // just ignore the bad items
                std::cerr << "error in ScratchpadWindow::"
                             "onAddToPlaylistButtonClicked(): "
                          << e.what() << std::endl;
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the clear list button getting clicked.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onClearListButtonClicked (void)             throw ()
{
    treeModel->clear();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu button getting clicked.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onRemoveItemButtonClicked(void)             throw ()
{
    treeView->onRemoveMenuOption();
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onEntryClicked (GdkEventButton     * event) throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        Gtk::TreePath           currentPath;
        Gtk::TreeViewColumn *   column;
        int     cell_x,
                cell_y;
        bool foundValidRow = treeView->get_path_at_pos(
                                            int(event->x), int(event->y),
                                            currentPath, column,
                                            cell_x, cell_y);

        if (foundValidRow) {
            Gtk::TreeIter   iter = treeModel->get_iter(currentPath);
            if (iter) {
                currentRow = *iter;
                
                Ptr<Playable>::Ref 
                            playable = currentRow[modelColumns.playableColumn];
                
                switch (playable->getType()) {
                    case Playable::AudioClipType:
                        audioClipMenu->popup(event->button, event->time);
                        break;
                        
                    case Playable::PlaylistType:
                        playlistMenu->popup(event->button, event->time);
                        break;
    
                    default:
                        break;
                }
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Select the row which contains the playable specified.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: selectRow(Ptr<Playable>::Ref    playable)   throw ()
{
    Gtk::TreeModel::const_iterator  it;

    for (it = treeModel->children().begin(); 
                                it != treeModel->children().end(); ++it) {
        
        Gtk::TreeRow        row = *it;
        Ptr<Playable>::Ref  currentPlayable = row[modelColumns.playableColumn];
        
        if (*playable->getId() == *currentPlayable->getId()) {
            Glib::RefPtr<Gtk::TreeView::Selection> 
                            selection = treeView->get_selection();
            selection->select(it);
            return;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Remove an item from the Scratchpad
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: removeItem(Ptr<const UniqueId>::Ref  id)    throw ()
{
    Gtk::TreeModel::const_iterator  it;

    for (it = treeModel->children().begin(); 
                                it != treeModel->children().end(); ++it) {

        Gtk::TreeRow        row = *it;
        Ptr<Playable>::Ref  currentPlayable = row[modelColumns.playableColumn];

        if (*id == *currentPlayable->getId()) {
            treeModel->erase(it);
            return;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Edit Playlist menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onEditPlaylist(void)                        throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    try {
        gLiveSupport->openPlaylistForEditing(playable->getId());
    } catch (XmlRpcException &e) {
        gLiveSupport->displayMessageWindow(getResourceUstring(
                                                    "cannotEditPlaylistMsg" ));
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Schedule Playlist menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onSchedulePlaylist(void)                    throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    Ptr<UniqueId>::Ref  uid      = playable->getId();
    
    Ptr<ResourceBundle>::Ref    bundle;
    Ptr<Playlist>::Ref          playlist;
    
    try {
        if (!gLiveSupport->existsPlaylist(uid)) {
            return;
        }

        playlist    = gLiveSupport->getPlaylist(uid);

        bundle      = gLiveSupport->getBundle("schedulePlaylistWindow");

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        return;
    } catch (XmlRpcException &e) {
        std::cerr << "error in ScratchpadWindow::onSchedulePlaylist(): "
                  << e.what() << std::endl;
        return;
    }
    
    schedulePlaylistWindow.reset(new SchedulePlaylistWindow(gLiveSupport,
                                                            bundle,
                                                            playlist));

    Gtk::Main::run(*schedulePlaylistWindow);
}


/*------------------------------------------------------------------------------
 *  Signal handler for "export playlist" in the context menu.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onExportPlaylist(void)                      throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    Ptr<Playlist>::Ref  playlist = playable->getPlaylist();
    
    try {
        if (playlist) {
            exportPlaylistWindow.reset(new ExportPlaylistWindow(
                                gLiveSupport,
                                gLiveSupport->getBundle("exportPlaylistWindow"),
                                playlist));
            exportPlaylistWindow->set_transient_for(*this);
            Gtk::Main::run(*exportPlaylistWindow);
        }
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Add To Playlist menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onAddToPlaylist(void)                       throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    try {
        gLiveSupport->addToPlaylist(playable->getId());
    } catch (XmlRpcException &e) {
        std::cerr << "error in ScratchpadWindow::onAddToPlaylist(): "
                    << e.what() << std::endl;
        return;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Add To Live Mode menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onAddToLiveMode(void)                       throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    gLiveSupport->addToLiveMode(playable);
}


/*------------------------------------------------------------------------------
 *  Signal handler for "upload to hub" in the context menu.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onUploadToHub(void)                         throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    gLiveSupport->uploadToHub(playable);
}


/*------------------------------------------------------------------------------
 *  Signal handler for the user double-clicking or pressing Enter.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onDoubleClick(const Gtk::TreeModel::Path &    path,
                                  const Gtk::TreeViewColumn *     column)
                                                                throw ()
{
    Gtk::TreeIter   iter = treeModel->get_iter(path);
    if (iter) {
        currentRow = *iter;
        onAddToLiveMode();
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for a key pressed.
 *----------------------------------------------------------------------------*/
bool
ScratchpadWindow :: onKeyPressed(GdkEventKey *    event)        throw ()
{
    if (event->type == GDK_KEY_PRESS) {
        KeyboardShortcut::Action    action = gLiveSupport->findAction(
                                                windowName,
                                                Gdk::ModifierType(event->state),
                                                event->keyval);
        switch (action) {
            case KeyboardShortcut::moveItemUp :
                                    if (isSelectionSingle()) {
                                        treeView->onUpMenuOption();
                                        return true;
                                    }
                                    break;

            case KeyboardShortcut::moveItemDown :
                                    if (isSelectionSingle()) {
                                        treeView->onDownMenuOption();
                                        return true;
                                    }
                                    break;
            
            case KeyboardShortcut::removeItem :
                                    onRemoveItemButtonClicked();
                                    return true;
            
            default :               break;
        }
    }

    return false;
}


/*------------------------------------------------------------------------------
 *  Check whether exactly one row is selected.
 *----------------------------------------------------------------------------*/
bool
ScratchpadWindow :: isSelectionSingle(void)                     throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> 
                    selection       = treeView->get_selection();
    std::vector<Gtk::TreePath> 
                    selectedRows    = selection->get_selected_rows();

    if (selectedRows.size() == 1) {
        Gtk::TreeIter   iter = treeModel->get_iter(selectedRows.at(0));
        currentRow = *iter;
        return true;
    } else {
        return false;
    }
}


/*------------------------------------------------------------------------------
 *  Add an item to the Scratchpad.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: addItem(Ptr<Playable>::Ref    playable)
                                                                throw ()
{
    // cache the item if it hasn't been cached yet
    if (!playable->getToken()) {
        try {
            playable = gLiveSupport->acquirePlayable(playable->getId());
        } catch (XmlRpcException &e) {
            std::cerr << "could not acquire playable in ScratchpadWindow: "
                      << e.what() << std::endl;
            return;
        }
    }
    
    removeItem(playable->getId());
    
    Gtk::TreeModel::Row     row = *treeModel->prepend();
    
    row[modelColumns.playableColumn]        = playable;
    
    Ptr<WidgetFactory>::Ref widgetFactory = WidgetFactory::getInstance();
    
    switch (playable->getType()) {
        case Playable::AudioClipType:
            row[modelColumns.typeColumn]  = widgetFactory->getPixbuf(
                                          WidgetConstants::audioClipIconImage);
            break;

        case Playable::PlaylistType:
            row[modelColumns.typeColumn]  = widgetFactory->getPixbuf(
                                          WidgetConstants::playlistIconImage);
            break;
    }
    
    row[modelColumns.titleColumn]         = Glib::Markup::escape_text(
                                                        *playable->getTitle());
}


/*------------------------------------------------------------------------------
 *  Add an item to the Scratchpad.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: addItem(Ptr<const UniqueId>::Ref    id)
                                                                throw ()
{
    Ptr<Playable>::Ref  playable;
    try {
        playable = gLiveSupport->acquirePlayable(id);
    } catch (XmlRpcException &e) {
        std::cerr << "could not acquire playable in ScratchpadWindow: "
                    << e.what() << std::endl;
        return;
    }
    
    addItem(playable);
}


/*------------------------------------------------------------------------------
 *  Return the contents of the Scratchpad.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
ScratchpadWindow :: getContents(void)                           throw ()
{
    std::ostringstream              contentsStream;
    Gtk::TreeModel::const_iterator  it;

    for (it = treeModel->children().begin(); 
                                it != treeModel->children().end(); ++it) {
        Gtk::TreeRow        row = *it;
        Ptr<Playable>::Ref  playable = row[modelColumns.playableColumn];
        contentsStream << playable->getId()->getId() << " ";
    }

    Ptr<Glib::ustring>::Ref         contents(new Glib::ustring(
                                                    contentsStream.str() ));
    return contents;
}


/*------------------------------------------------------------------------------
 *  Restore the contents of the Scratchpad.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: setContents(Ptr<const Glib::ustring>::Ref       contents)
                                                                throw ()
{
    std::vector<UniqueId::IdType>   contentsVector;
    std::istringstream              contentsStream(*contents);
    while (!contentsStream.eof()) {
        UniqueId::IdType            nextItem;
        contentsStream >> nextItem;
        if (contentsStream.fail()) {
            contentsStream.clear();
            contentsStream.ignore();
        } else {
            contentsVector.push_back(nextItem);
        }
    }
    
    treeModel->clear();
    std::vector<UniqueId::IdType>::reverse_iterator     it;
    
    for (it = contentsVector.rbegin(); it != contentsVector.rend(); ++it) {
        Ptr<const UniqueId>::Ref    id(new const UniqueId(*it));
        addItem(id);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: on_hide(void)                               throw ()
{
    if (exportPlaylistWindow) {
        exportPlaylistWindow->hide();
    }
    if (schedulePlaylistWindow) {
        schedulePlaylistWindow->hide();
    }
        
    GuiWindow::on_hide();
}

