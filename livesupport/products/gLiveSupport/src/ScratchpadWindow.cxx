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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.28 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/ScratchpadWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "SchedulePlaylistWindow.h"
#include "ScratchpadWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ScratchpadWindow :: ScratchpadWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                                      Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
          : WhiteWindow(WidgetFactory::scratchpadWindowTitleImage,
                        Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
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

    // Add the TreeView, inside a ScrolledWindow, with the button underneath:
    scrolledWindow.add(*treeView);

    // Only show the scrollbars when they are necessary:
    scrolledWindow.set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);

    audioButtonBox = Gtk::manage(new CuePlayer(
                                    gLiveSupport, treeView, modelColumns ));
    topButtonBox.pack_start(*audioButtonBox, Gtk::PACK_EXPAND_PADDING);
    
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
                                *getResourceUstring("addToPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onAddToPlaylist)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("upMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onUpItem)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("downMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onDownItem)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onRemoveItem)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cueMenuItem"),
                                sigc::mem_fun(*audioButtonBox,
                                        &CuePlayer::onPlayItem)));
        audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToLiveModeMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onAddToLiveMode)));
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
                                *getResourceUstring("editPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onEditPlaylist)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                    &ScratchpadWindow::onAddToPlaylist)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("schedulePlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                    &ScratchpadWindow::onSchedulePlaylist)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("upMenuItem"),
                                sigc::mem_fun(*this,
                                    &ScratchpadWindow::onUpItem)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("downMenuItem"),
                                sigc::mem_fun(*this,
                                    &ScratchpadWindow::onDownItem)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*this,
                                    &ScratchpadWindow::onRemoveItem)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cueMenuItem"),
                                sigc::mem_fun(*audioButtonBox,
                                    &CuePlayer::onPlayItem)));
        playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToLiveModeMenuItem"),
                                sigc::mem_fun(*this,
                                        &ScratchpadWindow::onAddToLiveMode)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    playlistMenu->accelerate(*this);

    // show
    set_name("scratchpadWindow");
    set_default_size(300, 330);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    
    showContents();
    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Show all audio clips
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: showContents(void)                          throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref    scratchpadContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;
    Ptr<Playable>::Ref                      playable;
    Gtk::TreeModel::Row                     row;

    scratchpadContents = gLiveSupport->getScratchpadContents();
    it  = scratchpadContents->begin();
    end = scratchpadContents->end();
    treeModel->clear();
    int     rowNumber = 0;
    
    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    while (it != end) {
        playable  = *it;
        row       = *(treeModel->append());

        row[modelColumns.playableColumn] = playable;
        switch (playable->getType()) {
            case Playable::AudioClipType:
                row[modelColumns.typeColumn]  = widgetFactory->getPixbuf(
                                            WidgetFactory::audioClipIconImage);
                break;

            case Playable::PlaylistType:
                row[modelColumns.typeColumn]  = widgetFactory->getPixbuf(
                                            WidgetFactory::playlistIconImage);
                break;
                
            default:
                break;
        }
        row[modelColumns.titleColumn]     = Glib::Markup::escape_text(
                                                        *playable->getTitle());
        row[modelColumns.rowNumberColumn] = rowNumber;

        ++it;
        ++rowNumber;
    }
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
            gLiveSupport->addToPlaylist(playable->getId());
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the clear list button getting clicked.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onClearListButtonClicked (void)             throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref
            scratchpadContents = gLiveSupport->getScratchpadContents();
    scratchpadContents->clear();
    showContents();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu button getting clicked.
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onRemoveItemButtonClicked(void)             throw ()
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
            removeItem(playable->getId());
        }
    }
    showContents();
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
 *  Event handler for the Up menu item selected from the entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onUpItem(void)                              throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];

    Ptr<GLiveSupport::PlayableList>::Ref    scratchpadContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;

    scratchpadContents = gLiveSupport->getScratchpadContents();
    it  = scratchpadContents->begin();
    end = scratchpadContents->end();
    while (it != end) {
        Ptr<Playable>::Ref      p= *it;

        if (*p->getId() == *playable->getId()) {
            // move one up, and insert the same before that
            if (it == scratchpadContents->begin()) {
                break;
            }
            scratchpadContents->insert(--it, playable);
            // move back to what we've found, and erase it
            scratchpadContents->erase(++it);

            showContents();
            break;
        }

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Down menu item selected from the entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onDownItem(void)                            throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];

    Ptr<GLiveSupport::PlayableList>::Ref    scratchpadContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;

    scratchpadContents = gLiveSupport->getScratchpadContents();
    it  = scratchpadContents->begin();
    end = scratchpadContents->end();
    while (it != end) {
        Ptr<Playable>::Ref      p= *it;

        if (*p->getId() == *playable->getId()) {
            // move two down, and insert the same before that
            ++it;
            if (it == end) {
                break;
            }
            scratchpadContents->insert(++it, playable);
            // move back to what we've found, and erase it
            --it;
            --it;
            scratchpadContents->erase(--it);

            showContents();
            break;
        }

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu item selected from the entry context menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onRemoveItem(void)                          throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    removeItem(playable->getId());
    showContents();
}


/*------------------------------------------------------------------------------
 *  Remove an item from the Scratchpad
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: removeItem(Ptr<const UniqueId>::Ref    id)  throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref    scratchpadContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;

    scratchpadContents = gLiveSupport->getScratchpadContents();
    it  = scratchpadContents->begin();
    end = scratchpadContents->end();
    while (it != end) {
        Ptr<Playable>::Ref      playable = *it;

        if (*playable->getId() == *id) {
            scratchpadContents->erase(it);
            break;
        }

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Edit Playlist menu item selected from the
 *  entry conext menu
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
 *  Event handler for the Add To Playlist menu item selected from the
 *  entry conext menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onAddToPlaylist(void)                       throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    gLiveSupport->addToPlaylist(playable->getId());
}


/*------------------------------------------------------------------------------
 *  Event handler for the Schedule Playlist menu item selected from the
 *  entry conext menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onSchedulePlaylist(void)                    throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    Ptr<UniqueId>::Ref  uid      = playable->getId();

    Ptr<SessionId>::Ref                 sessionId = 
                                            gLiveSupport->getSessionId();
    Ptr<StorageClientInterface>::Ref    storage =
                                            gLiveSupport->getStorage();

    if (!storage->existsPlaylist(sessionId, uid)) {
        return;
    }

    Ptr<Playlist>::Ref  playlist = storage->getPlaylist(sessionId, uid);

    Ptr<ResourceBundle>::Ref    bundle;
    try {
        bundle = gLiveSupport->getBundle("schedulePlaylistWindow");
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        return;
    }

    Ptr<SchedulePlaylistWindow>::Ref    scheduleWindow;
    scheduleWindow.reset(new SchedulePlaylistWindow(gLiveSupport,
                                                    bundle,
                                                    playlist));

    Gtk::Main::run(*scheduleWindow);
}


/*------------------------------------------------------------------------------
 *  Event handler for the Add To Live Mode menu item selected from the
 *  entry conext menu
 *----------------------------------------------------------------------------*/
void
ScratchpadWindow :: onAddToLiveMode(void)                       throw ()
{
    Ptr<Playable>::Ref  playable = currentRow[modelColumns.playableColumn];
    gLiveSupport->addToLiveMode(playable);
}

