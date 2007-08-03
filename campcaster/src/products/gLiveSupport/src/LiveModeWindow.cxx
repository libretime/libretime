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
#include <glibmm.h>

#include "LiveSupport/Core/TimeConversion.h"

#include "LiveModeWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "LiveModeWindow.glade";

/*------------------------------------------------------------------------------
 *  The name of the user preference for storing contents of the window.
 *----------------------------------------------------------------------------*/
const Glib::ustring     userPreferencesKeyName = "liveModeContents";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
LiveModeWindow :: LiveModeWindow (Ptr<GLiveSupport>::Ref    gLiveSupport,
                                  Ptr<ResourceBundle>::Ref  bundle,
                                  Gtk::ToggleButton *       windowOpenerButton,
                                  const Glib::ustring &     gladeDir)
                                                                    throw ()
          : BasicWindow(gLiveSupport,
                        bundle,
                        windowOpenerButton,
                        gladeDir + gladeFileName),
            gladeDir(gladeDir),
            isDeleting(false)
{
    glade->get_widget_derived("treeView1", treeView);
    treeModel = Gtk::ListStore::create(modelColumns);
    treeView->set_model(treeModel);
    treeView->connectModelSignals(treeModel);

    treeView->appendLineNumberColumn("", 2 /* offset */, 50);
    treeView->appendColumn("", modelColumns.infoColumn, 200);

    treeView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                        &LiveModeWindow::onEntryClicked));
    treeView->signal_row_activated().connect(sigc::mem_fun(*this,
                                        &LiveModeWindow::onDoubleClick));
    treeView->signalTreeModelChanged().connect(sigc::mem_fun(*this,
                                        &LiveModeWindow::onTreeModelChanged));
    
    treeView->signal_key_press_event().connect(sigc::mem_fun(*this,
                                        &LiveModeWindow::onKeyPressed));

    glade->get_widget("cueLabel1", cueLabel);
    cueLabel->set_label(*getResourceUstring("cuePlayerLabel"));
    cuePlayer.reset(new CuePlayer(gLiveSupport,
                                  treeView,
                                  modelColumns,
                                  glade));

    glade->get_widget("autoPlayNext1", autoPlayNext);
    autoPlayNext->set_label(*getResourceUstring("autoPlayNextLabel"));
    
    glade->connect_clicked("outputPlayButton1", sigc::mem_fun(*this,
                                        &LiveModeWindow::onOutputPlay));

    audioClipContextMenu = constructAudioClipContextMenu();
    playlistContextMenu  = constructPlaylistContextMenu();

    userPreferencesKey.reset(new const Glib::ustring(userPreferencesKeyName));
}


/*------------------------------------------------------------------------------
 *  Add a new item to the top of the Live Mode Window.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: addItem(Ptr<Playable>::Ref  playable)             throw ()
{
    addItem(treeModel->append(), playable);
    onTreeModelChanged();
}


/*------------------------------------------------------------------------------
 *  Add a new item as the given row in the Live Mode Window.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: addItem(Gtk::TreeModel::iterator  iter,
                          Ptr<Playable>::Ref        playable)       throw ()
{
    
    Gtk::TreeModel::Row     row       = *iter;
    row[modelColumns.playableColumn]  = playable;

    Ptr<Glib::ustring>::Ref     infoString(new Glib::ustring);
    
    infoString->append("<span font_desc='Bitstream Vera Sans"
                       " Bold 16'>");
    infoString->append(Glib::Markup::escape_text(*playable->getTitle()));
    infoString->append("</span>");

    // TODO: rewrite this using the Core::Metadata class

    Ptr<Glib::ustring>::Ref 
                        creator = playable->getMetadata("dc:creator");
    if (creator) {
        infoString->append("\n<span font_desc='Bitstream Vera Sans"
                           " Bold 12'>");
        infoString->append(Glib::Markup::escape_text(*creator));
        infoString->append("</span>");
    }

    Ptr<Glib::ustring>::Ref 
                        album = playable->getMetadata("dc:source");
    if (album) {
        infoString->append("\n<span font_desc='Bitstream Vera Sans"
                           " Bold 12'>");
        infoString->append(Glib::Markup::escape_text(*album));
        infoString->append("</span>");
    }

    infoString->append("\n<span font_desc='Bitstream Vera Sans 12'>"
                       "duration: ");
    infoString->append(*TimeConversion::timeDurationToHhMmSsString(
                                            playable->getPlaylength() ));
    infoString->append("</span>");

    row[modelColumns.infoColumn] = *infoString;
    gLiveSupport->runMainLoop();
}


/*------------------------------------------------------------------------------
 *  Add an item to the Live Mode window, by ID.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: addItem(Ptr<const UniqueId>::Ref    id)
                                                                    throw ()
{
    Ptr<Playable>::Ref  playable;
    try {
        playable = gLiveSupport->acquirePlayable(id);
    } catch (XmlRpcException &e) {
        std::cerr << "could not acquire playable in LiveModeWindow: "
                    << e.what() << std::endl;
        return;
    }
    
    addItem(playable);
}


/*------------------------------------------------------------------------------
 *  "Pop" the first item from the top of the Live Mode Window.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
LiveModeWindow :: popTop(void)                                      throw ()
{
    Ptr<Playable>::Ref          playable;
    if (!autoPlayNext->get_active()) {
        return playable;        // return a 0 pointer if auto is set to off
    }

    Gtk::TreeModel::iterator    iter = treeModel->children().begin();
    if (iter) {
        playable = (*iter)[modelColumns.playableColumn];
        treeModel->erase(iter);
    }
    gLiveSupport->runMainLoop();

    return playable;
}


/*------------------------------------------------------------------------------
 *  Find the selected row.
 *----------------------------------------------------------------------------*/
Gtk::TreeModel::iterator
LiveModeWindow :: getSelected(void)                                 throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection
                                            = treeView->get_selection();
    std::vector<Gtk::TreeModel::Path>       selectedPaths
                                            = selection->get_selected_rows();
    
    Gtk::TreeModel::iterator                it;
    if (selectedPaths.size() > 0) {
        it = treeModel->get_iter(selectedPaths.front());
    }
    return it;
}


/*------------------------------------------------------------------------------
 *  Signal handler for the output play button clicked.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onOutputPlay(void)                                throw ()
{
    Gtk::TreeModel::iterator        iter = getSelected();

    if (!iter) {
        iter = treeModel->children().begin();
    }
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        try {
            gLiveSupport->playOutputAudio(playable);
            gLiveSupport->setNowPlaying(playable);
            treeView->removeItem(iter);
            gLiveSupport->runMainLoop();
        } catch (std::runtime_error &e) {
            std::cerr << "cannot play on live mode output device: "
                      << e.what() << std::endl;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onEntryClicked(GdkEventButton *   event)          throw ()
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
                Ptr<Playable>::Ref  playable =
                                         (*iter)[modelColumns.playableColumn];
                
                if (playable) {
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
                }
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the user double-clicking or pressing Enter.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onDoubleClick(const Gtk::TreeModel::Path &    path,
                                const Gtk::TreeViewColumn *     column)
                                                                    throw ()
{
    onOutputPlay();
}


/*------------------------------------------------------------------------------
 *  Event handler for a key pressed.
 *----------------------------------------------------------------------------*/
bool
LiveModeWindow :: onKeyPressed(GdkEventKey *    event)              throw ()
{
    if (event->type == GDK_KEY_PRESS) {
        Gtk::TreeModel::iterator        iter = getSelected();
        
        if (iter) {
            KeyboardShortcut::Action    action = gLiveSupport->findAction(
                                            "liveModeWindow",
                                            Gdk::ModifierType(event->state),
                                            event->keyval);
            switch (action) {
                case KeyboardShortcut::moveItemUp :
                                        treeView->onUpMenuOption();
                                        return true;

                case KeyboardShortcut::moveItemDown :
                                        treeView->onDownMenuOption();
                                        return true;
                
                case KeyboardShortcut::removeItem :
                                        onRemoveItemButtonClicked();
                                        return true;
                
                case KeyboardShortcut::playAudio :
                                        onOutputPlay();
                                        return true;
                
                default :               break;
            }
        }
    }
    
    return false;
}


/*------------------------------------------------------------------------------
 *  Event handler for the Edit Playlist menu item selected from the
 *  entry context menu.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onEditPlaylist(void)                              throw ()
{
    Gtk::TreeModel::iterator    iter = getSelected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        Ptr<Playlist>::Ref      playlist = playable->getPlaylist();
        if (playlist) {
            try {
                gLiveSupport->openPlaylistForEditing(playlist->getId());
            } catch (XmlRpcException &e) {
                gLiveSupport->displayMessageWindow(*getResourceUstring(
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
LiveModeWindow :: onSchedulePlaylist(void)                          throw ()
{
    Gtk::TreeModel::iterator    iter = getSelected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        Ptr<Playlist>::Ref      playlist = playable->getPlaylist();
        if (playlist) {
            schedulePlaylistWindow.reset(new SchedulePlaylistWindow(
                            gLiveSupport,
                            gladeDir,
                            playlist));
            schedulePlaylistWindow->getWindow()->set_transient_for(*mainWindow);
            Gtk::Main::run(*schedulePlaylistWindow->getWindow());
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for "export playlist" in the context menu.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onExportPlaylist(void)                            throw ()
{
    Gtk::TreeModel::iterator    iter = getSelected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        Ptr<Playlist>::Ref      playlist = playable->getPlaylist();
        if (playlist) {
            exportPlaylistWindow.reset(new ExportPlaylistWindow(
                                gLiveSupport,
                                gladeDir,
                                playlist));
            exportPlaylistWindow->getWindow()->set_transient_for(*mainWindow);
            Gtk::Main::run(*exportPlaylistWindow->getWindow());
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Add To Playlist menu item selected from the
 *  entry context menu
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onAddToPlaylist(void)                             throw ()
{
    Gtk::TreeModel::iterator    iter = getSelected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        try {
            gLiveSupport->addToPlaylist(playable->getId());
        } catch (XmlRpcException &e) {
            std::cerr << "error in LiveModeWindow::onAddToPlaylist(): "
                        << e.what() << std::endl;
            return;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for "upload to hub" in the context menu.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onUploadToHub(void)                               throw ()
{
    Gtk::TreeModel::iterator    iter = getSelected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        gLiveSupport->uploadToHub(playable);
    }
}


/*------------------------------------------------------------------------------
 *  Refresh the playlist in the window.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: refreshPlaylist(Ptr<Playlist>::Ref    playlist)   throw ()
{
    for (Gtk::TreeModel::iterator   iter = treeModel->children().begin();
                iter != treeModel->children().end(); ++iter) {
        Ptr<Playable>::Ref  currentItem = (*iter)[modelColumns.playableColumn];
        if (*currentItem->getId() == *playlist->getId()) {
            addItem(iter, playlist);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for local audio clips.
 *----------------------------------------------------------------------------*/
Ptr<Gtk::Menu>::Ref
LiveModeWindow :: constructAudioClipContextMenu(void)           throw ()
{
    Ptr<Gtk::Menu>::Ref     contextMenu(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("playMenuItem"),
                                sigc::mem_fun(*this,
                                    &LiveModeWindow::onOutputPlay)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cueMenuItem"),
                                sigc::mem_fun(*cuePlayer,
                                    &CuePlayer::onPlayItem)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToPlaylistMenuItem"),
                            sigc::mem_fun(*this,
                                    &LiveModeWindow::onAddToPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*treeView,
                                    &ZebraTreeView::onRemoveMenuOption)));
    contextMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("uploadToHubMenuItem"),
                                sigc::mem_fun(*this,
                                    &LiveModeWindow::onUploadToHub)));

    contextMenu->accelerate(*mainWindow);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Construct the right-click context menu for local playlists.
 *----------------------------------------------------------------------------*/
Ptr<Gtk::Menu>::Ref
LiveModeWindow :: constructPlaylistContextMenu(void)            throw ()
{
    Ptr<Gtk::Menu>::Ref     contextMenu(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("playMenuItem"),
                                sigc::mem_fun(*this,
                                    &LiveModeWindow::onOutputPlay)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cueMenuItem"),
                                sigc::mem_fun(*cuePlayer,
                                    &CuePlayer::onPlayItem)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("addToPlaylistMenuItem"),
                            sigc::mem_fun(*this,
                                    &LiveModeWindow::onAddToPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*treeView,
                                    &ZebraTreeView::onRemoveMenuOption)));
    contextMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("editPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                    &LiveModeWindow::onEditPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("schedulePlaylistMenuItem"),
                            sigc::mem_fun(*this,
                                    &LiveModeWindow::onSchedulePlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("exportPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                    &LiveModeWindow::onExportPlaylist)));
    contextMenuList.push_back(Gtk::Menu_Helpers::SeparatorElem());
    contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("uploadToHubMenuItem"),
                                sigc::mem_fun(*this,
                                    &LiveModeWindow::onUploadToHub)));

    contextMenu->accelerate(*mainWindow);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu button getting clicked.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onRemoveItemButtonClicked(void)                   throw ()
{
    isDeleting = true;
    treeView->onRemoveMenuOption();
    isDeleting = false;
    onTreeModelChanged();
}


/*------------------------------------------------------------------------------
 *  Signal handler for a change in the tree model.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onTreeModelChanged(void)                          throw ()
{
    if (isDeleting) {
        return;
    }
    
    Gtk::TreeModel::iterator    iter = treeModel->children().begin();
    
    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        if (playable) {
            if (!savedTopPlayable || savedTopPlayable &&
                    *savedTopPlayable->getId() != *playable->getId()) {
                gLiveSupport->preload(playable);
            }
            savedTopPlayable = playable;
        }
        
    }
}


/*------------------------------------------------------------------------------
 *  Update the strings in the widget.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: updateStrings(void)                               throw ()
{
    setBundle(gLiveSupport->getBundle("liveModeWindow"));
    
    setTitle(getResourceUstring("windowTitle"));
    cueLabel->set_label(*getResourceUstring("cuePlayerLabel"));
}


/*------------------------------------------------------------------------------
 *  Return the contents of the Scratchpad.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
LiveModeWindow :: getContents(void)                                 throw ()
{
    std::ostringstream              contentsStream;

    contentsStream << int(autoPlayNext->get_active()) << " ";

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
LiveModeWindow :: setContents(Ptr<const Glib::ustring>::Ref     contents)
                                                                    throw ()
{
    std::istringstream              contentsStream(*contents);
    if (contentsStream.eof()) {
        return;
    }
    
    int     autoPlayNextValue;
    contentsStream >> autoPlayNextValue;
    autoPlayNext->set_active(autoPlayNextValue);
    
    std::vector<UniqueId::IdType>   contentsVector;
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
LiveModeWindow :: hide(void)                                        throw ()
{
    if (exportPlaylistWindow) {
        exportPlaylistWindow->getWindow()->hide();
    }
    if (schedulePlaylistWindow) {
        schedulePlaylistWindow->getWindow()->hide();
    }
        
    BasicWindow::hide();
}

