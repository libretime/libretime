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

#ifdef HAVE_PWD_H
#include <pwd.h>
#else
#error need pwd.h
#endif

#include "LiveSupport/Core/FileTools.h"

#include "ExportPlaylistWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "exportPlaylistWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "ExportPlaylistWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ExportPlaylistWindow :: ExportPlaylistWindow(Ptr<Playlist>::Ref   playlist)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName),
            playlist(playlist)
{
    Gtk::Label *            playlistTitleTextLabel;
    Gtk::Label *            formatLabel;
    glade->get_widget("playlistTitleTextLabel1", playlistTitleTextLabel);
    glade->get_widget("formatLabel1", formatLabel);
    playlistTitleTextLabel->set_label(*getResourceUstring(
                                                        "playlistTitleLabel"));
    formatLabel->set_label(*getResourceUstring("formatLabel"));

    Gtk::Label *            playlistTitleValueLabel;
    glade->get_widget("playlistTitleValueLabel1", playlistTitleValueLabel);
    playlistTitleValueLabel->set_label(*playlist->getTitle());

    glade->connect_clicked("cancelButton1", sigc::mem_fun(*this,
                                &ExportPlaylistWindow::onCancelButtonClicked));
    glade->connect_clicked("saveButton1", sigc::mem_fun(*this,
                                &ExportPlaylistWindow::onSaveButtonClicked));
    
    formatButtons.reset(new ExportFormatRadioButtons(this));
}


/*------------------------------------------------------------------------------
 *  Event handler for the Cancel button being clicked.
 *----------------------------------------------------------------------------*/
void
ExportPlaylistWindow :: onCancelButtonClicked(void)                 throw ()
{
    mainWindow->hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Save button being clicked.
 *----------------------------------------------------------------------------*/
void
ExportPlaylistWindow :: onSaveButtonClicked(void)                   throw ()
{
    if (token) {
        resetToken();
    }
    
    // run the storage method
    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    Ptr<UniqueId>::Ref          playlistId  = playlist->getId();
    StorageClientInterface::ExportFormatType
                                format      = formatButtons->getFormat();
    Ptr<Glib::ustring>::Ref     url(new Glib::ustring);
    
    try {
        token = storage->exportPlaylistOpen(sessionId, playlistId, format, url);
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref errorMsg = getResourceUstring(
                                                    "createExportErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(*errorMsg);
        return;
    }
    
    // run the file chooser dialog
    Ptr<Gtk::FileChooserDialog>::Ref    dialog;
    try {
        dialog.reset(new Gtk::FileChooserDialog(
                                *getResourceUstring("fileChooserDialogTitle"),
                                Gtk::FILE_CHOOSER_ACTION_SAVE));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    struct passwd *             pwd = getpwuid(getuid());
    if (pwd) {
        dialog->set_current_folder(pwd->pw_dir);
    }
    
    Ptr<Glib::ustring>::Ref             fileName(new Glib::ustring(
                                                        *playlist->getTitle()));
    fileName->append(".tar");
    dialog->set_current_name(*fileName);
    
    dialog->add_button(Gtk::Stock::CANCEL,  Gtk::RESPONSE_CANCEL);
    dialog->add_button(Gtk::Stock::SAVE,    Gtk::RESPONSE_OK);
    
    int result = dialog->run();
    
    // save the exported playlist as a local file
    if (result == Gtk::RESPONSE_OK) {
        fileName->assign(dialog->get_filename());
        try {
            FileTools::copyUrlToFile(*url, *fileName);
            
        } catch (std::runtime_error &e) {
            Ptr<Glib::ustring>::Ref errorMsg = getResourceUstring(
                                                    "saveExportErrorMsg");
            gLiveSupport->displayMessageWindow(*errorMsg);
        }
    }
    
    // close the exporting operation
    resetToken();
    
    mainWindow->hide();
}


/*------------------------------------------------------------------------------
 *  Cancel the current operation.
 *----------------------------------------------------------------------------*/
void
ExportPlaylistWindow :: resetToken(void)                            throw ()
{
    Ptr<StorageClientInterface>::Ref    storage
                                        = gLiveSupport->getStorageClient();
    try {
        storage->exportPlaylistClose(token);
        token.reset();
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref         errorMsg = getResourceUstring(
                                                    "createExportErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(*errorMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
 *----------------------------------------------------------------------------*/
bool
ExportPlaylistWindow :: onDeleteEvent(GdkEventAny *     event)      throw ()
{
std::cerr << "ExportPlaylistWindow :: onDeleteEvent called\n";
    if (token) {
        resetToken();
    }
        
    return false;
}

