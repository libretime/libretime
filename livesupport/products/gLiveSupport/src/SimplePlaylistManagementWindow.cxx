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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/SimplePlaylistManagementWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "SimplePlaylistManagementWindow.h"


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
SimplePlaylistManagementWindow :: SimplePlaylistManagementWindow (
                                    Ptr<GLiveSupport>::Ref      gLiveSupport,
                                    Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
                    : LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    try {
        set_title(*getResourceUstring("windowTitle"));
        nameLabel.reset(new Gtk::Label(*getResourceUstring("nameLabel")));
        saveButton.reset(new Gtk::Button(
                                    *getResourceUstring("saveButtonLabel")));
        closeButton.reset(new Gtk::Button(
                                    *getResourceUstring("closeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    nameEntry.reset(new Gtk::Entry());
    entriesScrolledWindow.reset(new Gtk::ScrolledWindow());
    entriesView.reset(new Gtk::TreeView());

    // set up the entry scrolled window, with the entry treeview inside.
    entriesScrolledWindow->add(*entriesView);
    entriesScrolledWindow->set_policy(Gtk::POLICY_AUTOMATIC,
                                      Gtk::POLICY_AUTOMATIC);

    // Create the Tree model:
    entriesModel = Gtk::ListStore::create(modelColumns);
    entriesView->set_model(entriesModel);

    // Add the TreeView's view columns:
    try {
        entriesView->append_column(*getResourceUstring("titleColumnLabel"),
                                   modelColumns.titleColumn);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    statusBar.reset(new Gtk::Label("status bar"));

    // set up the layout
    layout.reset(new Gtk::Table());

    set_border_width(10);
    layout->attach(*nameLabel,              0, 1, 0, 1);
    layout->attach(*nameEntry,              1, 2, 0, 1);
    layout->attach(*entriesScrolledWindow,  0, 2, 1, 2);
    layout->attach(*saveButton,             1, 2, 2, 3);
    layout->attach(*closeButton,            1, 2, 3, 4);
    layout->attach(*statusBar,              0, 2, 4, 5);

    add(*layout);

    // Register the signal handlers
    saveButton->signal_clicked().connect(sigc::mem_fun(*this,
                       &SimplePlaylistManagementWindow::onSaveButtonClicked));
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                       &SimplePlaylistManagementWindow::onCloseButtonClicked));

    // show all
    show_all();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
SimplePlaylistManagementWindow :: ~SimplePlaylistManagementWindow (void)
                                                                    throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the save button getting clicked.
 *----------------------------------------------------------------------------*/
void
SimplePlaylistManagementWindow :: onSaveButtonClicked (void)        throw ()
{
    try {
        Ptr<const Glib::ustring>::Ref   title;
        Ptr<Playlist>::Ref              playlist;

        title.reset(new Glib::ustring(nameEntry->get_text()));

        playlist = gLiveSupport->uploadPlaylist(title);

        Glib::ustring   statusText("uploaded playlist ");
        statusText += *playlist->getTitle();
        statusBar->set_text(statusText);
    } catch (XmlRpcException &e) {
        statusBar->set_text(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
SimplePlaylistManagementWindow :: onCloseButtonClicked (void)       throw ()
{
    hide();
}


/*------------------------------------------------------------------------------
 *  Show the contents of the currently edited playlist.
 *----------------------------------------------------------------------------*/
void
SimplePlaylistManagementWindow :: showContents(void)                throw ()
{
    Ptr<Playlist>::Ref                      playlist;
    Playlist::const_iterator                it;
    Playlist::const_iterator                end;

    playlist = gLiveSupport->getEditedPlaylist();
    it  = playlist->begin();
    end = playlist->end();
    entriesModel->clear();
    while (it != end) {
        Ptr<PlaylistElement>::Ref  playlistElem  = it->second;
        Ptr<Playable>::Ref         playable      = playlistElem->getPlayable();
        Gtk::TreeModel::Row        row           = *(entriesModel->append());

        row[modelColumns.idColumn]    = playable->getId();
        row[modelColumns.titleColumn] = *playable->getTitle();

        it++;
    }
}


