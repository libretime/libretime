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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/AudioClipListWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "AudioClipListWindow.h"


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
AudioClipListWindow :: AudioClipListWindow (
                                    Ptr<GLiveSupport>::Ref      gLiveSupport,
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
                                 &AudioClipListWindow::onCloseButtonClicked));


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
        treeView.append_column(*getResourceUstring("idColumnLabel"),
                               modelColumns.idColumn);
        treeView.append_column(*getResourceUstring("lengthColumnLabel"),
                               modelColumns.lengthColumn);
        treeView.append_column(*getResourceUstring("uriColumnLabel"),
                               modelColumns.uriColumn);
        treeView.append_column(*getResourceUstring("tokenColumnLabel"),
                               modelColumns.tokenColumn);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    showAllAudioClips();

    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Show all audio clips
 *----------------------------------------------------------------------------*/
void
AudioClipListWindow :: showAllAudioClips(void)                  throw ()
{
    Ptr<SessionId>::Ref                             sessionId;
    Ptr<StorageClientInterface>::Ref                storage;
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref     audioClips;
    std::vector<Ptr<AudioClip>::Ref>::iterator      it;
    std::vector<Ptr<AudioClip>::Ref>::iterator      end;
    Ptr<AudioClip>::Ref                             clip;
    Gtk::TreeModel::Row                             row;
    std::string                                     lengthStr;

    sessionId  = gLiveSupport->getSessionId();
    storage    = gLiveSupport->getStorage();
    audioClips = storage->getAllAudioClips(sessionId);
    it  = audioClips->begin();
    end = audioClips->end();
    while (it < end) {
        clip      = *it;
        row       = *(treeModel->append());
        lengthStr = boost::posix_time::to_simple_string(*clip->getPlaylength());

        row[modelColumns.idColumn]     = clip->getId()->getId();
        row[modelColumns.lengthColumn] = lengthStr;
        row[modelColumns.uriColumn]    = clip->getUri().get() ? *clip->getUri()
                                                               : "";
        row[modelColumns.tokenColumn]  = clip->getToken().get()
                                                    ? *clip->getUri()
                                                    : "";

        it++;
    }
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
AudioClipListWindow :: ~AudioClipListWindow (void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
AudioClipListWindow :: onCloseButtonClicked (void)                  throw ()
{
    hide();
}


