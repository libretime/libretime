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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/UploadFileWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <unicode/msgfmt.h>
#include <gtkmm/label.h>
#include <gtkmm/stock.h>
#include <gtkmm/filechooserdialog.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "UploadFileWindow.h"


using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
UploadFileWindow :: UploadFileWindow (Ptr<GLiveSupport>::Ref    gLiveSupport,
                                      Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
                        : LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    try {
        set_title(*getResourceUstring("windowTitle"));
        chooseFileLabel.reset(new Gtk::Label(
                                *getResourceUstring("chooseFileLabel")));
        fileNameEntry.reset(new Gtk::Entry());
        chooseFileButton.reset(new Gtk::Button(
                                *getResourceUstring("chooseFileButtonLabel")));
        nameLabel.reset(new Gtk::Label(
                                *getResourceUstring("nameLabel")));
        nameEntry.reset(new Gtk::Entry());
        uploadButton.reset(new Gtk::Button(
                                *getResourceUstring("uploadButtonLabel")));
        closeButton.reset(new Gtk::Button(
                                *getResourceUstring("closeButtonLabel")));
        statusBar.reset(new Gtk::Label(
                                *getResourceUstring("statusBar")));
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
    }

    // set up the layout, which is a button box
    layout.reset(new Gtk::Table());

    // set up the main window, and show everything
    set_border_width(10);
    layout->attach(*chooseFileLabel,   0, 1, 0, 1);
    layout->attach(*fileNameEntry,     1, 2, 0, 1);
    layout->attach(*chooseFileButton,  2, 3, 0, 1);
    layout->attach(*nameLabel,         0, 1, 1, 2);
    layout->attach(*nameEntry,         1, 2, 1, 2);
    layout->attach(*uploadButton,      1, 2, 2, 3);
    layout->attach(*closeButton,       1, 2, 3, 4);
    layout->attach(*statusBar,         0, 3, 4, 5);

    add(*layout);

    // bind events
    chooseFileButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &UploadFileWindow::onChooseFileButtonClicked));
    uploadButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &UploadFileWindow::onUploadButtonClicked));
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &UploadFileWindow::onCloseButtonClicked));

    // show everything
    show_all();
}


/*------------------------------------------------------------------------------
 *  The event when the choose file button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onChooseFileButtonClicked(void)             throw ()
{
    Ptr<Gtk::FileChooserDialog>::Ref    dialog;

    dialog.reset(new Gtk::FileChooserDialog(
                        *getResourceUstring("fileChooserDialogTitle"),
                        Gtk::FILE_CHOOSER_ACTION_OPEN));

    dialog->set_transient_for(*this);

    //Add response buttons the the dialog:
    dialog->add_button(Gtk::Stock::CANCEL, Gtk::RESPONSE_CANCEL);
    dialog->add_button(Gtk::Stock::OPEN, Gtk::RESPONSE_OK);

    int result = dialog->run();

    if (result == Gtk::RESPONSE_OK) {
        fileName.reset(new std::string(dialog->get_filename()));
        fileNameEntry->set_text(*fileName);
    }
}


/*------------------------------------------------------------------------------
 *  The event when the upload button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onUploadButtonClicked(void)                 throw ()
{
    try {
        Ptr<const Glib::ustring>::Ref   title;
        Ptr<const std::string>::Ref     fileName;
        Ptr<AudioClip>::Ref             audioClip;
        
        title.reset(new Glib::ustring(nameEntry->get_text()));
        fileName.reset(new std::string(fileNameEntry->get_text().raw()));
        
        audioClip = gLiveSupport->uploadFile(title, fileName);

        // display success in the status bar
        Ptr<UnicodeString>::Ref uTitle = ustringToUnicodeString(
                                                        audioClip->getTitle());
        Formattable             arguments[] = { *uTitle };
        Ptr<Glib::ustring>::Ref statusText = formatMessageUstring(
                                                    "clipUploadedMessage",
                                                    arguments,
                                                    1);
        statusBar->set_text(*statusText);

        // clean the entry fields
        nameEntry->set_text("");
        fileNameEntry->set_text("");
    } catch (XmlRpcException &e) {
        statusBar->set_text(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  The event when the close button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onCloseButtonClicked(void)                 throw ()
{
    hide();
}

