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
#include <cassert>

#include "LiveSupport/Widgets/WidgetFactory.h"

#include "ScratchpadWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "scratchpadWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "ScratchpadWindow.glade";

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
                        Gtk::ToggleButton *         windowOpenerButton)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName,
                      windowOpenerButton)
{
    // create the tree view
    glade->get_widget_derived("treeView1", treeView);
    treeView->get_selection()->set_mode(Gtk::SELECTION_MULTIPLE);

    treeView->appendColumn("",
                           modelColumns.typeColumn);
    treeView->appendColumn(*getResourceUstring("creatorColumnLabel"),
                           modelColumns.creatorColumn);
    treeView->appendColumn(*getResourceUstring("titleColumnLabel"),
                           modelColumns.titleColumn);

    treeModel = Gtk::ListStore::create(modelColumns);
    treeView->set_model(treeModel);
    treeView->connectModelSignals(treeModel);
    setupDndCallbacks();

    // register the signal handlers for treeview
    treeView->signal_button_press_event().connect(sigc::mem_fun(*this,
                                            &ScratchpadWindow::onEntryClicked),
                                            false /* call this first */);
    treeView->signal_row_activated().connect(sigc::mem_fun(*this,
                                            &ScratchpadWindow::onDoubleClick));
    treeView->signal_key_press_event().connect(sigc::mem_fun(*this,
                                            &ScratchpadWindow::onKeyPressed));

    // create the cue player widget
    cuePlayer.reset(new CuePlayer(this,
                                  treeView,
                                  modelColumns));

    // create the right-click entry context menu for audio clips
    audioClipContextMenu.reset(new Gtk::Menu());
    audioClipContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("cueMenuItem"),
                        sigc::mem_fun(*cuePlayer,
                                      &CuePlayer::onPlayItem)));
    audioClipContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("addToLiveModeMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onAddToLiveMode)));
    audioClipContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("addToPlaylistMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onAddToPlaylist)));
    audioClipContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("removeMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onRemoveMenuOption)));
    audioClipContextMenu->items().push_back(Gtk::Menu_Helpers::SeparatorElem());
    audioClipContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("uploadToHubMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onUploadToHub)));
    audioClipContextMenu->accelerate(*mainWindow);

    // create the right-click entry context menu for playlists
    playlistContextMenu.reset(new Gtk::Menu());
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("cueMenuItem"),
                        sigc::mem_fun(*cuePlayer,
                                      &CuePlayer::onPlayItem)));
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("addToLiveModeMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onAddToLiveMode)));
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("addToPlaylistMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onAddToPlaylist)));
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("removeMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onRemoveMenuOption)));
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::SeparatorElem());
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("editPlaylistMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onEditPlaylist)));
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("schedulePlaylistMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onSchedulePlaylist)));
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("exportPlaylistMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onExportPlaylist)));
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::SeparatorElem());
    playlistContextMenu->items().push_back(Gtk::Menu_Helpers::MenuElem(
                        *getResourceUstring("uploadToHubMenuItem"),
                        sigc::mem_fun(*this,
                                      &ScratchpadWindow::onUploadToHub)));
    playlistContextMenu->accelerate(*mainWindow);
    
    // set the user preferences key
    userPreferencesKey.reset(new const Glib::ustring(userPreferencesKeyName));
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
bool
ScratchpadWindow :: onEntryClicked (GdkEventButton     * event)     throw ()
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
ScratchpadWindow :: getFirstSelectedPlayable(void)                  throw ()
{
    Ptr<Playable>::Ref          playable;
    
    Glib::RefPtr<Gtk::TreeView::Selection> 
                                selection = treeView->get_selection();
    selectedPaths.reset(new std::vector<Gtk::TreePath>(
                                selection->get_selected_rows()));

    if (selectedPaths->size() > 0) {
        selectedIter = selectedPaths->begin();
        Gtk::TreeRow            row = *(treeModel->get_iter(*selectedIter));
        playable = row[modelColumns.playableColumn];
    }
    
    return playable;
}


/*------------------------------------------------------------------------------
 *  Return the next selected playable item.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
ScratchpadWindow :: getNextSelectedPlayable(void)                   throw ()
{
    Ptr<Playable>::Ref  playable;
    
    if (selectedPaths) {
        if (selectedIter != selectedPaths->end()) {
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
 *  Remove an item from the Scratchpad
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: removeItem(Ptr<const UniqueId>::Ref  id)        throw ()
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
ScratchpadWindow :: onEditPlaylist(void)                            throw ()
{
    Ptr<Playable>::Ref  playable = getNextSelectedPlayable();

    try {
        gLiveSupport->openPlaylistForEditing(playable->getId());
    } catch (XmlRpcException &e) {
        gLiveSupport->displayMessageWindow(*getResourceUstring(
                                                    "cannotEditPlaylistMsg" ));
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Schedule Playlist menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onSchedulePlaylist(void)                        throw ()
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
ScratchpadWindow :: onExportPlaylist(void)                          throw ()
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
 *  Event handler for the Add To Playlist menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onAddToPlaylist(void)                           throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        try {
            gLiveSupport->addToPlaylist(playable->getId());
        } catch (XmlRpcException &e) {
            std::cerr << "error in ScratchpadWindow::onAddToPlaylist(): "
                        << e.what() << std::endl;
            return;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Add To Live Mode menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onAddToLiveMode(void)                           throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        gLiveSupport->addToLiveMode(playable);
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for "upload to hub" in the context menu.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onUploadToHub(void)                             throw ()
{
    Ptr<Playable>::Ref  playable;

    while ((playable = getNextSelectedPlayable())) {
        gLiveSupport->uploadToHub(playable);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu item selected from the context menu.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onRemoveMenuOption(void)                        throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection
                                            = treeView->get_selection();
    std::vector<Gtk::TreePath>              selectedPaths
                                            = selection->get_selected_rows();
    
    std::vector<Gtk::TreeModel::iterator>   selectedIters;
    for (std::vector<Gtk::TreePath>::iterator pathIt = selectedPaths.begin();
                                              pathIt != selectedPaths.end();
                                              ++pathIt) {
        selectedIters.push_back(treeModel->get_iter(*pathIt));
    }
    
    Gtk::TreeModel::iterator                newSelection;
    for (std::vector<Gtk::TreeModel::iterator>::iterator
                                                iterIt = selectedIters.begin();
                                                iterIt != selectedIters.end();
                                                ++iterIt) {
        newSelection = *iterIt;
        ++newSelection;
        treeModel->erase(*iterIt);
    }
    
    if (newSelection) {
        selection->select(newSelection);
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the user double-clicking or pressing Enter.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onDoubleClick(const Gtk::TreeModel::Path &    path,
                                  const Gtk::TreeViewColumn *     column)
                                                                    throw ()
{
    Ptr<Playable>::Ref      playable = getFirstSelectedPlayable();

    if (playable) {
        onAddToLiveMode();
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for a key pressed.
 *----------------------------------------------------------------------------*/
bool
ScratchpadWindow :: onKeyPressed(GdkEventKey *    event)            throw ()
{
    if (event->type == GDK_KEY_PRESS) {
        KeyboardShortcut::Action    action = gLiveSupport->findAction(
                                                "scratchpadWindow",
                                                Gdk::ModifierType(event->state),
                                                event->keyval);
        switch (action) {
            case KeyboardShortcut::moveItemUp :
                                    if (selectionIsSingle()) {
                                        treeView->onUpMenuOption();
                                        return true;
                                    }
                                    break;

            case KeyboardShortcut::moveItemDown :
                                    if (selectionIsSingle()) {
                                        treeView->onDownMenuOption();
                                        return true;
                                    }
                                    break;
            
            case KeyboardShortcut::removeItem :
                                    onRemoveMenuOption();
                                    return true;
                                    break;
            
            default :               break;
        }
    }

    return false;
}


/*------------------------------------------------------------------------------
 *  Check whether exactly one row is selected.
 *----------------------------------------------------------------------------*/
bool
ScratchpadWindow :: selectionIsSingle(void)                         throw ()
{
    getFirstSelectedPlayable();

    return (selectedPaths->size() == 1);
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
    
    Gtk::TreeModel::Row     row = *(treeModel->prepend());
    
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
    
    Ptr<const Glib::ustring>::Ref   creator = playable->getMetadata(
                                                        "dc:creator");
    if (creator) {
        row[modelColumns.creatorColumn]   = Glib::Markup::escape_text(
                                                        *creator);
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
ScratchpadWindow :: getContents(void)                               throw ()
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
ScratchpadWindow :: hide(void)                                      throw ()
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
 *  The name of the window for the d'n'd methods.
 *----------------------------------------------------------------------------*/
Glib::ustring
ScratchpadWindow :: getWindowNameForDnd (void)                      throw ()
{
    return bundleName;
}


/*------------------------------------------------------------------------------
 *  Add an item to the Scratchpad at the given position.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: addItem(Gtk::TreeIter               iter,
                            Ptr<const UniqueId>::Ref    id)
                                                                    throw ()
{
    Ptr<Playable>::Ref      playable;
    try {
        playable = gLiveSupport->acquirePlayable(id);
    } catch (XmlRpcException &e) {
        std::cerr << "could not acquire playable in ScratchpadWindow: "
                    << e.what() << std::endl;
        return;
    }
    
    Gtk::TreeModel::Row     row = *iter;
    
    row[modelColumns.playableColumn] = playable;
    
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
    
    Ptr<const Glib::ustring>::Ref   creator = playable->getMetadata(
                                                        "dc:creator");
    if (creator) {
        row[modelColumns.creatorColumn]   = Glib::Markup::escape_text(
                                                        *creator);
    }
    row[modelColumns.titleColumn]         = Glib::Markup::escape_text(
                                                        *playable->getTitle());
}


