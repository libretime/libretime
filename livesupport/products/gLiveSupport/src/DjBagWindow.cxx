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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/DjBagWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "DjBagWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
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
                    : LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    try {
        set_title(*getResourceUstring("windowTitle"));
        closeButton.reset(new Gtk::Button(
                                    *getResourceUstring("closeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    // set up the close button
    closeButton->set_name("closeButton");
    closeButton->set_flags(Gtk::CAN_FOCUS|Gtk::CAN_DEFAULT|Gtk::HAS_DEFAULT);
    closeButton->set_relief(Gtk::RELIEF_NORMAL);
    // Register the signal handler for the button getting clicked.
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                          &DjBagWindow::onCloseButtonClicked));


    set_border_width(5);
    set_default_size(400, 200);

    add(vBox);

    // Add the TreeView, inside a ScrolledWindow, with the button underneath:
    scrolledWindow.add(treeView);

    // Only show the scrollbars when they are necessary:
    scrolledWindow.set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);

    vBox.pack_start(scrolledWindow);
    vBox.pack_start(buttonBox, Gtk::PACK_SHRINK);

    buttonBox.pack_start(*closeButton, Gtk::PACK_SHRINK);
    buttonBox.set_border_width(5);
    buttonBox.set_layout(Gtk::BUTTONBOX_END);

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


    // create the right-click entry context menu
    entryMenu.reset(new Gtk::Menu());
    Gtk::Menu::MenuList& menuList = entryMenu->items();
    // register the signal handlers for the popup menu
    menuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("addToPlaylistMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onAddToPlaylist)));
    menuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("removeMenuItem"),
                                sigc::mem_fun(*this,
                                               &DjBagWindow::onRemoveItem)));
    entryMenu->accelerate(*this);

    // show
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

        row[modelColumns.idColumn]    = playable->getId();
        switch (playable->getType()) {
            case Playable::AudioClipType:
            default:
                row[modelColumns.typeColumn]  = "audioclip";
                break;

            case Playable::PlaylistType:
                row[modelColumns.typeColumn]  = "playlist";
                break;
        }
        row[modelColumns.titleColumn] = *playable->getTitle();

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
DjBagWindow :: ~DjBagWindow (void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
DjBagWindow :: onCloseButtonClicked (void)                  throw ()
{
    hide();
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
            if (refSelection->get_selected()) {
                entryMenu->popup(event->button, event->time);
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
            Ptr<const UniqueId>::Ref id    = (*iter)[modelColumns.idColumn];

            removeItem(id);
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
            Ptr<const UniqueId>::Ref id    = (*iter)[modelColumns.idColumn];

            gLiveSupport->addToPlaylist(id);
        }
    }
}

