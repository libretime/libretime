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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/PlaylistListWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "PlaylistListWindow.h"


using namespace Glib;
using namespace Gtk;

using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
PlaylistListWindow :: PlaylistListWindow (
                                    Ptr<GLiveSupport>::Ref      gLiveSupport,
                                    Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
                    : LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    // get localized resources
    try {
        set_title(*getResourceUstring("windowTitle"));
        listBoxLabel.set_text(*getResourceUstring("listBoxLabel"));
        detailBoxLabel.set_text(*getResourceUstring("detailBoxLabel"));
        closeButton.reset(new Button(
                                    *getResourceUstring("closeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    // set up the close button
    closeButton->set_name("closeButton");
    closeButton->set_flags(CAN_FOCUS|CAN_DEFAULT|HAS_DEFAULT);
    closeButton->set_relief(RELIEF_NORMAL);
    // Register the signal handler for the button getting clicked.
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                 &PlaylistListWindow::onCloseButtonClicked));


    set_border_width(5);
    set_default_size(400, 200);

    // set up the main box
    add(mainBox);
    mainBox.pack_start(playlistBox);
    mainBox.pack_start(buttonBox, PACK_SHRINK);

    // set up the playlist box
    playlistBox.pack_start(listBox, PACK_EXPAND_WIDGET, 5);
    playlistBox.pack_start(detailBox, PACK_EXPAND_WIDGET, 5);

    // set up the listBox
    listBox.pack_start(listBoxLabel, PACK_SHRINK);
    listBox.pack_start(listScrolledWindow);

    // set up the listScrolledWindow
    listScrolledWindow.add(listTreeView);
    listScrolledWindow.set_policy(POLICY_AUTOMATIC, POLICY_AUTOMATIC);

    // create the list tree view, and add its columns
    listTreeModel = ListStore::create(modelColumns);
    listTreeView.set_model(listTreeModel);
    try {
        listTreeView.append_column(*getResourceUstring("idColumnLabel"),
                                    modelColumns.idColumn);
        listTreeView.append_column(*getResourceUstring("lengthColumnLabel"),
                                    modelColumns.lengthColumn);
        listTreeView.append_column(*getResourceUstring("uriColumnLabel"),
                                    modelColumns.uriColumn);
        listTreeView.append_column(*getResourceUstring("tokenColumnLabel"),
                                   modelColumns.tokenColumn);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    // attach the event handler for the user selecting a playlist from
    // the list of playlists
    listTreeSelection = listTreeView.get_selection();
    listTreeSelection->signal_changed().connect(
            sigc::mem_fun(*this, &PlaylistListWindow::onPlaylistListSelection));

    // set up the detailBox
    detailBox.pack_start(detailBoxLabel, PACK_SHRINK);
    detailBox.pack_start(detailScrolledWindow);

    // set up the detailed scroll window
    detailScrolledWindow.add(detailTreeView);
    detailScrolledWindow.set_policy(POLICY_AUTOMATIC, POLICY_AUTOMATIC);

    // create the detail tree view, and add its columns
    detailTreeModel = ListStore::create(modelColumns);
    detailTreeView.set_model(detailTreeModel);
    try {
        detailTreeView.append_column(*getResourceUstring("idColumnLabel"),
                                     modelColumns.idColumn);
        detailTreeView.append_column(*getResourceUstring("lengthColumnLabel"),
                                     modelColumns.lengthColumn);
        detailTreeView.append_column(*getResourceUstring("uriColumnLabel"),
                                     modelColumns.uriColumn);
        detailTreeView.append_column(*getResourceUstring("tokenColumnLabel"),
                                      modelColumns.tokenColumn);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    // attach the event handler for the user selecting an entry from
    // the list of playlist details
    detailTreeSelection = detailTreeView.get_selection();
    detailTreeSelection->signal_changed().connect(
                sigc::mem_fun(*this, &PlaylistListWindow::onDetailSelection));

    // set up the button box
    buttonBox.pack_start(*closeButton, PACK_SHRINK);
    buttonBox.set_border_width(5);
    buttonBox.set_layout(BUTTONBOX_END);

    showAllPlaylists();

    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Show all playlists
 *----------------------------------------------------------------------------*/
void
PlaylistListWindow :: showAllPlaylists(void)                    throw ()
{
    Ptr<SessionId>::Ref                             sessionId;
    Ptr<StorageClientInterface>::Ref                storage;
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref      playlists;
    std::vector<Ptr<Playlist>::Ref>::iterator       it;
    std::vector<Ptr<Playlist>::Ref>::iterator       end;
    Ptr<Playlist>::Ref                              playlist;
    TreeModel::Row                                  row;
    std::string                                     lengthStr;

    sessionId  = gLiveSupport->getSessionId();
    storage    = gLiveSupport->getStorage();
    playlists  = storage->getAllPlaylists(sessionId);
    it  = playlists->begin();
    end = playlists->end();
    while (it != end) {
        playlist  = *it;
        row       = *(listTreeModel->append());
        lengthStr = boost::posix_time::to_simple_string(
                                                *playlist->getPlaylength());

        row[modelColumns.idColumn]     = playlist->getId()->getId();
        row[modelColumns.lengthColumn] = lengthStr;
        row[modelColumns.uriColumn]    = playlist->getUri().get()
                                                    ? *playlist->getUri()
                                                    : "";
        row[modelColumns.tokenColumn]  = playlist->getToken().get()
                                                    ? *playlist->getUri()
                                                    : "";

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
PlaylistListWindow :: ~PlaylistListWindow (void)                throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
PlaylistListWindow :: onCloseButtonClicked (void)               throw ()
{
    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for a row being selected in the playlist list tree view.
 *----------------------------------------------------------------------------*/
void
PlaylistListWindow :: onPlaylistListSelection(void)             throw ()
{
    TreeModel::iterator iter = listTreeSelection->get_selected();
    if (iter) {
        TreeModel::Row     row = *iter;
        Ptr<UniqueId>::Ref playlistId(new UniqueId(row[modelColumns.idColumn]));

        showPlaylistDetails(playlistId);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for a row being selected in the detail tree view.
 *----------------------------------------------------------------------------*/
void
PlaylistListWindow :: onDetailSelection(void)                   throw ()
{
    TreeModel::iterator iter = detailTreeSelection->get_selected();
    if (iter) {
        TreeModel::Row     row = *iter;
        Ptr<UniqueId>::Ref selectedId(new UniqueId(row[modelColumns.idColumn]));

        // TODO: only proceed if the selected item is a playlist,
        //       not an audio clip

        // find the item in listTreeModel with the same id, and select it
        // TODO: find a more efficient way of doing this
        TreeModel::iterator    it  = listTreeModel->children().begin();
        TreeModel::iterator    end = listTreeModel->children().end();

        while (it != end) {
            row = *it;
            Ptr<UniqueId>::Ref  id(new UniqueId(row[modelColumns.idColumn]));
            if (*id == *selectedId) {
                listTreeSelection->select(row);
                break;
            }
            ++it;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Show the details of a playlist
 *----------------------------------------------------------------------------*/
void
PlaylistListWindow :: showPlaylistDetails(
                                Ptr<UniqueId>::Ref  playlistId)
                                                                    throw ()
{
    Ptr<SessionId>::Ref                 sessionId;
    Ptr<StorageClientInterface>::Ref    storage;
    Ptr<Playlist>::Ref                  playlist;
    Playlist::const_iterator            it;
    Playlist::const_iterator            end;
    Ptr<PlaylistElement>::Ref           playlistElement;
    TreeModel::Row                      row;

    sessionId  = gLiveSupport->getSessionId();
    storage    = gLiveSupport->getStorage();
    try {
        playlist   = storage->getPlaylist(sessionId, playlistId);
    } catch (std::logic_error &e) {
        // just don't do anything if there is no such playlist.
        return;
    }

    detailTreeModel->clear();
    it  = playlist->begin();
    end = playlist->end();
    while (it != end) {
        playlistElement = it->second;

        switch (playlistElement->getType()) {
            case PlaylistElement::AudioClipType:
                row = *(detailTreeModel->append());
                displayAudioClipDetails(playlistElement->getAudioClip(), row);
                break;

            case PlaylistElement::PlaylistType:
                row = *(detailTreeModel->append());
                displayPlaylistDetails(playlistElement->getPlaylist(), row);
                break;

            default:
                break;
        }

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Display audio clip info in a row of the detail tree view.
 *----------------------------------------------------------------------------*/
void
PlaylistListWindow :: displayAudioClipDetails(
                                Ptr<AudioClip>::Ref     audioClip,
                                Gtk::TreeModel::Row   & row)
                                                                throw ()
{
    std::string lengthStr = boost::posix_time::to_simple_string(
                                            *audioClip->getPlaylength());

    row[modelColumns.idColumn]     = audioClip->getId()->getId();
    row[modelColumns.lengthColumn] = lengthStr;
    row[modelColumns.uriColumn]    = audioClip->getUri().get()
                                                ? *audioClip->getUri()
                                                : "";
    row[modelColumns.tokenColumn]  = audioClip->getToken().get()
                                                ? *audioClip->getUri()
                                                : "";
}


/*------------------------------------------------------------------------------
 *  Display playlist info in a row of the detail tree view.
 *----------------------------------------------------------------------------*/
void
PlaylistListWindow :: displayPlaylistDetails(
                                Ptr<Playlist>::Ref      playlist,
                                Gtk::TreeModel::Row   & row)
                                                                throw ()
{
    std::string lengthStr = boost::posix_time::to_simple_string(
                                            *playlist->getPlaylength());

    row[modelColumns.idColumn]     = playlist->getId()->getId();
    row[modelColumns.lengthColumn] = lengthStr;
    row[modelColumns.uriColumn]    = playlist->getUri().get()
                                                ? *playlist->getUri()
                                                : "";
    row[modelColumns.tokenColumn]  = playlist->getToken().get()
                                                ? *playlist->getUri()
                                                : "";
}


