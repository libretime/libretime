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
    Version  : $Revision: 1.10 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/DjBagWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "SchedulePlaylistWindow.h"
#include "DjBagWindow.h"


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
DjBagWindow :: DjBagWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                            Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
          : WhiteWindow("",
                        0xffffff,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    try {
        set_title(*getResourceUstring("windowTitle"));
        playButton  = Gtk::manage(widgetFactory->createButton(
                                WidgetFactory::smallPlayButton));
        pauseButton = Gtk::manage(widgetFactory->createButton(
                                WidgetFactory::smallPauseButton));
        stopButton  = Gtk::manage(widgetFactory->createButton(
                                WidgetFactory::smallStopButton));
        clearListButton = Gtk::manage(widgetFactory->createButton(
                                *getResourceUstring("clearListButtonLabel")));
        removeButton = Gtk::manage(widgetFactory->createButton(
                                *getResourceUstring("removeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    playButton->set_name("playButton");
    playButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DjBagWindow::onPlayButtonClicked));

    pauseButton->set_name("pauseButton");
    pauseButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DjBagWindow::onPauseButtonClicked));

    stopButton->set_name("stopButton");
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DjBagWindow::onStopButtonClicked));

    clearListButton->set_name("clearListButton");
    clearListButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DjBagWindow::onClearListButtonClicked));

    removeButton->set_name("removeButton");
    removeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DjBagWindow::onRemoveItem));

    add(vBox);

    // Add the TreeView, inside a ScrolledWindow, with the button underneath:
    scrolledWindow.add(treeView);

    // Only show the scrollbars when they are necessary:
    scrolledWindow.set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);

    vBox.pack_start(topButtonBox, Gtk::PACK_SHRINK);
    vBox.pack_start(scrolledWindow);
    vBox.pack_start(bottomButtonBox, Gtk::PACK_SHRINK);

    topButtonBox.pack_start(audioButtonBox, Gtk::PACK_EXPAND_PADDING);
    
    audioButtonBox.set_border_width(5);
    audioButtonBox.pack_start(*playButton, Gtk::PACK_SHRINK);
    audioButtonBox.pack_start(*pauseButton, Gtk::PACK_SHRINK);
    audioButtonBox.pack_start(*stopButton, Gtk::PACK_SHRINK);

    bottomButtonBox.set_border_width(5);
    bottomButtonBox.set_layout(Gtk::BUTTONBOX_END);
    bottomButtonBox.pack_start(*clearListButton, Gtk::PACK_SHRINK);
    bottomButtonBox.pack_start(*removeButton, Gtk::PACK_SHRINK);

    // Create the Tree model:
    treeModel = Gtk::ListStore::create(modelColumns);
    treeView.set_model(treeModel);

    // Add the TreeView's view columns:
    try {
        treeView.append_column(*getResourceUstring("typeColumnLabel"),
                               modelColumns.typeColumn);
        treeView.append_column(*getResourceUstring("titleColumnLabel"),
                               modelColumns.titleColumn);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    // register the signal handler for treeview entries being clicked
    treeView.signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                            &DjBagWindow::onEntryClicked));


    // create the right-click entry context menu for audio clips
    audioClipMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& audioClipMenuList = audioClipMenu->items();
    // register the signal handlers for the popup menu
    audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onAddToPlaylist)));
    audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("upMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onUpItem)));
    audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("downMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onDownItem)));
    audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onRemoveItem)));
    audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("deleteMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onDeleteItem)));
    audioClipMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("playMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onPlayItem)));
    audioClipMenu->accelerate(*this);

    // create the right-click entry context menu for playlists
    playlistMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& playlistMenuList = playlistMenu->items();
    // register the signal handlers for the popup menu
    playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onAddToPlaylist)));
    playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("schedulePlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                           &DjBagWindow::onSchedulePlaylist)));
    playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("upMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onUpItem)));
    playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("downMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onDownItem)));
    playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onRemoveItem)));
    playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("deleteMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onDeleteItem)));
    playlistMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("playMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onPlayItem)));
    playlistMenu->accelerate(*this);

    // show
    set_name("scratchpadWindow");
    set_default_size(300, 300);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    set_resizable(true);
    
    showContents();
    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Show all audio clips
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: showContents(void)                       throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref    djBagContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;
    Ptr<Playable>::Ref                      playable;
    Gtk::TreeModel::Row                     row;

    djBagContents = gLiveSupport->getDjBagContents();
    it  = djBagContents->begin();
    end = djBagContents->end();
    treeModel->clear();
    while (it != end) {
        playable  = *it;
        row       = *(treeModel->append());

        row[modelColumns.playableColumn] = playable;
        switch (playable->getType()) {
            case Playable::AudioClipType:
                row[modelColumns.typeColumn]  = "audioclip";
                break;

            case Playable::PlaylistType:
                row[modelColumns.typeColumn]  = "playlist";
                break;
                
            default:
                break;
        }
        row[modelColumns.titleColumn] = *playable->getTitle();

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
DjBagWindow :: ~DjBagWindow (void)                              throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the clear list button getting clicked.
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onClearListButtonClicked (void)                  throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref    djBagContents
                                            = gLiveSupport->getDjBagContents();
    djBagContents->clear();
    showContents();
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onEntryClicked (GdkEventButton     * event)      throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        // only show the context menu, if something is already selected
        Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                      treeView.get_selection();
        if (refSelection) {
            Gtk::TreeModel::iterator iter = refSelection->get_selected();
            if (iter) {
                Ptr<Playable>::Ref  playable =
                                         (*iter)[modelColumns.playableColumn];

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
 *  Event handler for the Remove menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onRemoveItem(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView.get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

            removeItem(playable->getId());
            showContents();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Up menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onUpItem(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView.get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

            Ptr<GLiveSupport::PlayableList>::Ref    djBagContents;
            GLiveSupport::PlayableList::iterator    it;
            GLiveSupport::PlayableList::iterator    end;

            djBagContents = gLiveSupport->getDjBagContents();
            it  = djBagContents->begin();
            end = djBagContents->end();
            while (it != end) {
                Ptr<Playable>::Ref      p= *it;

                if (*p->getId() == *playable->getId()) {
                    // move one up, and insert the same before that
                    if (it == djBagContents->begin()) {
                        break;
                    }
                    djBagContents->insert(--it, playable);
                    // move back to what we've found, and erase it
                    djBagContents->erase(++it);

                    showContents();
                    break;
                }

                it++;
            }
        }
    }

}


/*------------------------------------------------------------------------------
 *  Event handler for the Down menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onDownItem(void)                             throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView.get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

            Ptr<GLiveSupport::PlayableList>::Ref    djBagContents;
            GLiveSupport::PlayableList::iterator    it;
            GLiveSupport::PlayableList::iterator    end;

            djBagContents = gLiveSupport->getDjBagContents();
            it  = djBagContents->begin();
            end = djBagContents->end();
            while (it != end) {
                Ptr<Playable>::Ref      p= *it;

                if (*p->getId() == *playable->getId()) {
                    // move two down, and insert the same before that
                    ++it;
                    if (it == end) {
                        break;
                    }
                    djBagContents->insert(++it, playable);
                    // move back to what we've found, and erase it
                    --it;
                    --it;
                    djBagContents->erase(--it);

                    showContents();
                    break;
                }

                it++;
            }
        }
    }

}


/*------------------------------------------------------------------------------
 *  Event handler for the Delete menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onDeleteItem(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView.get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

            try {
                deleteItem(playable);
            } catch (XmlRpcException &e) {
                // TODO: signal error here
            }
            showContents();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Remove an item from the dj bag
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: removeItem(Ptr<const UniqueId>::Ref    id)           throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref    djBagContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;

    djBagContents = gLiveSupport->getDjBagContents();
    it  = djBagContents->begin();
    end = djBagContents->end();
    while (it != end) {
        Ptr<Playable>::Ref      playable = *it;

        if (*playable->getId() == *id) {
            djBagContents->erase(it);
            break;
        }

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Delete an item from storage, and remove it from the dj bag
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: deleteItem(Ptr<Playable>::Ref    playable)
                                                        throw (XmlRpcException)
{
    removeItem(playable->getId());
    gLiveSupport->deletePlayable(playable);
}


/*------------------------------------------------------------------------------
 *  Event handler for the Add To Playlist menu item selected from the
 *  entry conext menu
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onAddToPlaylist(void)                            throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView.get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

            gLiveSupport->addToPlaylist(playable->getId());
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Schedule Playlist menu item selected from the
 *  entry conext menu
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onSchedulePlaylist(void)                       throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView.get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
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
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Play menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onPlayItem(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView.get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

            try {
                gLiveSupport->playAudio(playable);
            } catch (XmlRpcException &e) {
                std::cerr << "GLiveSupport::playAudio() error:" << std::endl
                          << e.what() << std::endl;
            } catch (std::exception &e) {
                std::cerr << "GLiveSupport::playAudio() error:" << std::endl
                          << e.what() << std::endl;
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Play button getting clicked
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onPlayButtonClicked(void)                      throw ()
{
    onPlayItem();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Pause button getting clicked
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onPauseButtonClicked(void)                      throw ()
{
    try {
        gLiveSupport->pauseAudio();
    } catch (std::logic_error &e) {
        std::cerr << "GLiveSupport::pauseAudio() error:" << std::endl
                    << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Stop button getting clicked
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onStopButtonClicked(void)                      throw ()
{
    try {
        gLiveSupport->stopAudio();
    } catch (XmlRpcException &e) {
        std::cerr << "GLiveSupport::stopAudio() error:" << std::endl
                    << e.what() << std::endl;
    } catch (std::logic_error &e) {
        std::cerr << "GLiveSupport::stopAudio() error:" << std::endl
                    << e.what() << std::endl;
    }
}

