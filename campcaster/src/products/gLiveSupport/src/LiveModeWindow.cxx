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
#include "LiveSupport/Widgets/WidgetFactory.h"

#include "LiveModeWindow.h"


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
const Glib::ustring     windowName = "liveModeWindow";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
LiveModeWindow :: LiveModeWindow (Ptr<GLiveSupport>::Ref    gLiveSupport,
                                  Ptr<ResourceBundle>::Ref  bundle,
                                  Button *                  windowOpenerButton)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle, 
                      windowOpenerButton),
            isDeleting(false)
{
    try {
        set_title(*getResourceUstring("windowTitle"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    // Create the tree model:
    treeModel = Gtk::ListStore::create(modelColumns);
    
    // ... and the tree view:
    treeView = Gtk::manage(wf->createTreeView(treeModel));
    treeView->get_selection()->set_mode(Gtk::SELECTION_MULTIPLE);
    treeView->set_reorderable(true);
    treeView->set_headers_visible(false);
    treeView->set_enable_search(false);

    // Add the TreeView's view columns:
    try {
        treeView->appendLineNumberColumn("", 2 /* offset */, 50);
        treeView->appendColumn("", modelColumns.infoColumn, 200);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // register the signal handler for treeview entries being clicked
    treeView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                        &LiveModeWindow::onEntryClicked));
    treeView->signal_row_activated().connect(sigc::mem_fun(*this,
                                        &LiveModeWindow::onDoubleClick));
    treeView->signalTreeModelChanged().connect(sigc::mem_fun(*this,
                                        &LiveModeWindow::onTreeModelChanged));
    
    // register the signal handler for keyboard key presses
    treeView->signal_key_press_event().connect(sigc::mem_fun(*this,
                                            &LiveModeWindow::onKeyPressed));

    // Add the TreeView, inside a ScrolledWindow, with the button underneath:
    scrolledWindow.add(*treeView);

    // Only show the scrollbars when they are necessary:
    scrolledWindow.set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);

    // Create the play etc buttons:
    Gtk::HBox *         topButtonBox = Gtk::manage(new Gtk::HBox);
    Gtk::HButtonBox *   bottomButtonBox = Gtk::manage(new Gtk::HButtonBox);
    
    ImageButton *       outputPlayButton = Gtk::manage(wf->createButton(
                                        WidgetConstants::hugePlayButton ));
    
    Gtk::VBox *         cueAudioBox = Gtk::manage(new Gtk::VBox);
    Gtk::HBox *         cueAudioLabelBox = Gtk::manage(new Gtk::HBox);
    
    try {
        cueAudioLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("cuePlayerLabel") ));
        clearListButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("clearListButtonLabel")));
        removeButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("removeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    Gtk::HBox *         cueAudioButtonsBox = Gtk::manage(new Gtk::HBox);
    cueAudioButtons = Gtk::manage(new CuePlayer(
                                    gLiveSupport, treeView, modelColumns ));
    
    topButtonBox->pack_start(*outputPlayButton,  Gtk::PACK_EXPAND_PADDING, 10);
    topButtonBox->pack_start(*cueAudioBox,       Gtk::PACK_EXPAND_PADDING, 10);
    cueAudioBox->pack_start(*cueAudioLabelBox,   Gtk::PACK_SHRINK, 6);
    cueAudioLabelBox->pack_start(*cueAudioLabel, Gtk::PACK_EXPAND_PADDING, 1);
    cueAudioBox->pack_start(*cueAudioButtonsBox, Gtk::PACK_SHRINK, 0);
    cueAudioButtonsBox->pack_start(*cueAudioButtons, 
                                                 Gtk::PACK_EXPAND_PADDING, 1);
    
    bottomButtonBox->set_layout(Gtk::BUTTONBOX_END);
    bottomButtonBox->set_spacing(5);
    bottomButtonBox->pack_start(*clearListButton);
    bottomButtonBox->pack_start(*removeButton);
    
    vBox.pack_start(*topButtonBox,      Gtk::PACK_SHRINK, 5);
    vBox.pack_start(scrolledWindow,     Gtk::PACK_EXPAND_WIDGET, 5);
    vBox.pack_start(*bottomButtonBox,   Gtk::PACK_SHRINK, 5);
    add(vBox);

    // connect the signal handlers for the buttons
    outputPlayButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &LiveModeWindow::onOutputPlay ));
    clearListButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &LiveModeWindow::onClearListButtonClicked));
    removeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &LiveModeWindow::onRemoveItemButtonClicked));

    // create the right-click context menus
    audioClipContextMenu = constructAudioClipContextMenu();
    playlistContextMenu  = constructPlaylistContextMenu();

    // show
    set_name(windowName);
    set_default_size(400, 500);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    
    show_all_children();
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
 *  "Pop" the first item from the top of the Live Mode Window.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
LiveModeWindow :: popTop(void)                                      throw ()
{
    Ptr<Playable>::Ref          playable;
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
                                            windowName,
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
LiveModeWindow :: onSchedulePlaylist(void)                          throw ()
{
    Gtk::TreeModel::iterator    iter = getSelected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        Ptr<Playlist>::Ref      playlist = playable->getPlaylist();
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
LiveModeWindow :: onExportPlaylist(void)                            throw ()
{
    Gtk::TreeModel::iterator    iter = getSelected();

    if (iter) {
        Ptr<Playable>::Ref      playable = (*iter)[modelColumns.playableColumn];
        Ptr<Playlist>::Ref      playlist = playable->getPlaylist();
        if (playlist) {
            exportPlaylistWindow.reset(new ExportPlaylistWindow(
                                gLiveSupport,
                                gLiveSupport->getBundle("exportPlaylistWindow"),
                                playlist));
            exportPlaylistWindow->set_transient_for(*this);
            Gtk::Main::run(*exportPlaylistWindow);
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
Gtk::Menu *
LiveModeWindow :: constructAudioClipContextMenu(void)           throw ()
{
    Gtk::Menu *             contextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    try {
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("playMenuItem"),
                                  sigc::mem_fun(*this,
                                        &LiveModeWindow::onOutputPlay)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("cueMenuItem"),
                                  sigc::mem_fun(*cueAudioButtons,
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
LiveModeWindow :: constructPlaylistContextMenu(void)            throw ()
{
    Gtk::Menu *             contextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList &   contextMenuList = contextMenu->items();

    try {
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("playMenuItem"),
                                  sigc::mem_fun(*this,
                                        &LiveModeWindow::onOutputPlay)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("cueMenuItem"),
                                  sigc::mem_fun(*cueAudioButtons,
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
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    contextMenu->accelerate(*this);
    return contextMenu;
}    


/*------------------------------------------------------------------------------
 *  Event handler for the clear list button getting clicked.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onClearListButtonClicked (void)                   throw ()
{
    isDeleting = true;
    treeModel->clear();
    isDeleting = false;
    onTreeModelChanged();
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
    try {
        setBundle(gLiveSupport->getBundle("liveModeWindow"));
        
        set_title(*getResourceUstring("windowTitle"));
        cueAudioLabel->set_label(*getResourceUstring("cuePlayerLabel"));
        clearListButton->set_label(*getResourceUstring("clearListButtonLabel"));
        removeButton->set_label(*getResourceUstring("removeButtonLabel"));
    
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: on_hide(void)                                     throw ()
{
    if (exportPlaylistWindow) {
        exportPlaylistWindow->hide();
    }
    if (schedulePlaylistWindow) {
        schedulePlaylistWindow->hide();
    }
        
    GuiWindow::on_hide();
}

