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
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/ExportPlaylistWindow.cxx $

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

#include <curl/curl.h>
#include <curl/easy.h>
#include <gtkmm/filechooserdialog.h>
#include <gtkmm/stock.h>

#include "LiveSupport/Widgets/WidgetFactory.h"

#include "ExportPlaylistWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the window, used by the keyboard shortcuts (or by the .gtkrc).
 *----------------------------------------------------------------------------*/
const Glib::ustring     windowName = "exportPlaylistWindow";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ExportPlaylistWindow :: ExportPlaylistWindow(
                        Ptr<GLiveSupport>::Ref      gLiveSupport,
                        Ptr<ResourceBundle>::Ref    bundle,
                        Ptr<Playlist>::Ref          playlist)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle, 
                      ""),
            playlist(playlist)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Label *    playlistTitleLabel;
    Gtk::Label *    formatLabel;
    Button *        cancelButton;
    Button *        saveButton;
    try {
        set_title(*getResourceUstring("windowTitle"));
        playlistTitleLabel  = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("playlistTitleLabel")));
        formatLabel         = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("formatLabel")));
        cancelButton        = Gtk::manage(wf->createButton(
                                *getResourceUstring("cancelButtonLabel")));
        saveButton          = Gtk::manage(wf->createButton(
                                *getResourceUstring("saveButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    cancelButton->signal_clicked().connect(sigc::mem_fun(
                        *this, &ExportPlaylistWindow::onCancelButtonClicked ));
    saveButton->signal_clicked().connect(sigc::mem_fun(
                        *this, &ExportPlaylistWindow::onSaveButtonClicked ));
    
    Gtk::Box *      playlistTitleBox = Gtk::manage(new Gtk::HBox);
    Gtk::Label *    playlistTitle    = Gtk::manage(new Gtk::Label(
                                                    *playlist->getTitle() ));
    playlistTitleBox->pack_start(*playlistTitleLabel, Gtk::PACK_SHRINK, 5);
    playlistTitleBox->pack_start(*playlistTitle,      Gtk::PACK_SHRINK, 5);
    
    formatButtons   = Gtk::manage(new ExportFormatRadioButtons(bundle));
    
    Gtk::Box *      formatBox = Gtk::manage(new Gtk::HBox);
    formatBox->pack_start(*formatLabel,   Gtk::PACK_SHRINK, 5);
    formatBox->pack_start(*formatButtons, Gtk::PACK_SHRINK, 5);
    
    Gtk::Box *      buttonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
    buttonBox->pack_start(*cancelButton);
    buttonBox->pack_start(*saveButton);
    
    Gtk::Box *      extraSpace = Gtk::manage(new Gtk::HBox);
    Gtk::Label *    statusBar  = Gtk::manage(new Gtk::Label(""));
    
    Gtk::Box *      layout = Gtk::manage(new Gtk::VBox);
    layout->pack_start(*extraSpace,       Gtk::PACK_SHRINK, 5);
    layout->pack_start(*playlistTitleBox, Gtk::PACK_SHRINK, 5);
    layout->pack_start(*formatBox,        Gtk::PACK_SHRINK, 0);
    layout->pack_start(*statusBar,        Gtk::PACK_SHRINK, 10);
    layout->pack_start(*buttonBox,        Gtk::PACK_SHRINK, 0);

    add(*layout);
    
    set_name(windowName);
    show_all();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Cancel button being clicked.
 *----------------------------------------------------------------------------*/
void
ExportPlaylistWindow :: onCancelButtonClicked(void)                 throw ()
{
    hide();
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
        gLiveSupport->displayMessageWindow(errorMsg);
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
        bool success = copyUrlToFile(url, fileName);
        if (!success) {
            Ptr<Glib::ustring>::Ref errorMsg = getResourceUstring(
                                                    "saveExportErrorMsg");
            gLiveSupport->displayMessageWindow(errorMsg);
        }
    }
    
    // close the exporting operation
    resetToken();
    
    hide();
}


/*------------------------------------------------------------------------------
 *  Fetch the exported playlist from a URL and save it to a local file.
 *----------------------------------------------------------------------------*/
bool
ExportPlaylistWindow :: copyUrlToFile(Ptr<Glib::ustring>::Ref   url,
                                      Ptr<Glib::ustring>::Ref   fileName)
                                                                    throw ()
{
    FILE*   localFile      = fopen(fileName->c_str(), "wb");
    if (!localFile) {
        return false;
    }

    CURL*    handle     = curl_easy_init();
    if (!handle) {
        fclose(localFile);
        return false;
    }
    
    int    status =   curl_easy_setopt(handle, CURLOPT_URL, url->c_str()); 
    status |=   curl_easy_setopt(handle, CURLOPT_WRITEDATA, localFile);
    status |=   curl_easy_setopt(handle, CURLOPT_HTTPGET);

    if (status) {
        fclose(localFile);
        return false;
    }

    status =    curl_easy_perform(handle);

    if (status) {
        fclose(localFile);
        return false;
    }

    curl_easy_cleanup(handle);
    fclose(localFile);
    return true;
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
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
        gLiveSupport->displayMessageWindow(errorMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
 *----------------------------------------------------------------------------*/
void
ExportPlaylistWindow :: on_hide(void)                               throw ()
{
    if (token) {
        resetToken();
    }
        
    GuiWindow::on_hide();
}

