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
    Version  : $Revision: 1.15 $
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
using namespace LiveSupport::Widgets;
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
          : WhiteWindow(WidgetFactory::playlistsWindowTitleImage,
                        Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    try {
        set_title(*getResourceUstring("windowTitle"));
        nameLabel = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("nameLabel")));
        saveButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("saveButtonLabel")));
        closeButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("closeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    EntryBin *      nameEntryBin = Gtk::manage(wf->createEntryBin());
    nameEntry             = nameEntryBin->getEntry();
    entriesScrolledWindow = Gtk::manage(new Gtk::ScrolledWindow());
    entriesModel          = Gtk::ListStore::create(modelColumns);
    entriesView           = Gtk::manage(wf->createTreeView(entriesModel));

    // set up the entry scrolled window, with the entry treeview inside.
    entriesScrolledWindow->add(*entriesView);
    entriesScrolledWindow->set_policy(Gtk::POLICY_AUTOMATIC,
                                      Gtk::POLICY_AUTOMATIC);

    // Add the TreeView's view columns:
    try {
        entriesView->appendColumn(*getResourceUstring("startColumnLabel"),
                                   modelColumns.startColumn, 120);
        entriesView->appendColumn(*getResourceUstring("titleColumnLabel"),
                                   modelColumns.titleColumn, 200);
        entriesView->appendColumn(*getResourceUstring("lengthColumnLabel"),
                                   modelColumns.lengthColumn, 120);

        statusBar = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("statusBar")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // set up the layout
    Gtk::VBox *         mainBox = Gtk::manage(new Gtk::VBox);
    
    Gtk::HBox *         nameBox = Gtk::manage(new Gtk::HBox);
    nameBox->pack_start(*nameLabel, Gtk::PACK_SHRINK, 10);
    Gtk::Alignment *    nameEntryAlignment = Gtk::manage(new Gtk::Alignment(
                                        Gtk::ALIGN_LEFT, Gtk::ALIGN_CENTER,
                                        0.7));  // take up 70% of available room
    nameEntryAlignment->add(*nameEntryBin);
    nameBox->pack_start(*nameEntryAlignment, Gtk::PACK_EXPAND_WIDGET, 5);
    mainBox->pack_start(*nameBox, Gtk::PACK_SHRINK, 5);

    mainBox->pack_start(*entriesScrolledWindow, Gtk::PACK_EXPAND_WIDGET, 5);
    
    Gtk::ButtonBox *    buttonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
    buttonBox->pack_start(*saveButton);
    buttonBox->pack_start(*closeButton);
    mainBox->pack_start(*buttonBox, Gtk::PACK_SHRINK, 0);
    
    Gtk::Alignment *    statusBarAlignment = Gtk::manage(new Gtk::Alignment(
                                        Gtk::ALIGN_LEFT, Gtk::ALIGN_CENTER,
                                        0.0));  // do not expand the label
    statusBarAlignment->add(*statusBar);
    mainBox->pack_start(*statusBarAlignment, Gtk::PACK_SHRINK, 5);

    add(*mainBox);

    // Register the signal handlers
    saveButton->signal_clicked().connect(sigc::mem_fun(*this,
                       &SimplePlaylistManagementWindow::onSaveButtonClicked));
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                       &SimplePlaylistManagementWindow::onCloseButtonClicked));

    // show
    set_name("simplePlaylistManagementWindow");
    set_default_size(450, 300);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);

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
        // TODO: check for empty title and display "are you sure?" message

        playlist = gLiveSupport->getEditedPlaylist();
        if (!playlist) {
            return;
        }
        
        playlist->setTitle(title);

        playlist = gLiveSupport->savePlaylist();

        Ptr<Glib::ustring>::Ref statusText = formatMessage(
                                                    "playlistSavedMessage",
                                                    *playlist->getTitle());
        statusBar->set_text(*statusText);

        // clean the entry fields
        nameEntry->set_text("");
        entriesModel->clear();
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
    // TODO: display "are you sure?" message
    gLiveSupport->cancelEditedPlaylist();

    hide();
}


/*------------------------------------------------------------------------------
 *  Show the contents of the currently edited playlist.
 *----------------------------------------------------------------------------*/
void
SimplePlaylistManagementWindow :: showContents(void)                throw ()
{
    Ptr<Playlist>::Ref          playlist;
    Playlist::const_iterator    it;
    Playlist::const_iterator    end;
    int                         rowNumber;

    playlist = gLiveSupport->getEditedPlaylist();
    
    if (playlist) {
        entriesModel->clear();
        rowNumber = 0;
        for (it = playlist->begin(); it != playlist->end(); ++it) {
            Ptr<PlaylistElement>::Ref  
                                    playlistElem  = it->second;
            Ptr<Playable>::Ref      playable      = playlistElem->getPlayable();
            Gtk::TreeModel::Row     row           = *(entriesModel->append());
    
            row[modelColumns.rowNumberColumn]
                        = rowNumber++;
            row[modelColumns.idColumn]
                        = playable->getId();
            row[modelColumns.startColumn]
                        = to_simple_string(*playlistElem->getRelativeOffset());
            row[modelColumns.titleColumn]
                        = Glib::Markup::escape_text(*playable->getTitle());
            row[modelColumns.lengthColumn]
                        = to_simple_string(*playable->getPlaylength());
        }
    }
}

