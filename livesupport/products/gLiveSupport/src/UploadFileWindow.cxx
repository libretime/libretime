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
    Version  : $Revision: 1.13 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/UploadFileWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <sstream>
#include <fstream>
#include <unicode/msgfmt.h>
#include <gtkmm/label.h>
#include <gtkmm/stock.h>
#include <gtkmm/filechooserdialog.h>
#include <fileref.h>
#include <audioproperties.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "UploadFileWindow.h"


using namespace LiveSupport::Widgets;
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
          : WhiteWindow("",
                        Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    isAudioClipValid = false;

    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    try {
        // generic resources
        set_title(*getResourceUstring("windowTitle"));
        chooseFileLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("chooseFileLabel")));
        fileNameEntryBin = Gtk::manage(wf->createEntryBin());
        fileNameEntry    = fileNameEntryBin->getEntry();
        chooseFileButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("chooseFileButtonLabel")));

        // main section resources
        titleLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("titleLabel"),
                                Gtk::ALIGN_RIGHT));
        titleEntryBin = Gtk::manage(wf->createEntryBin());
        titleEntry    = titleEntryBin->getEntry();

        creatorLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("creatorLabel"),
                                Gtk::ALIGN_RIGHT));
        creatorEntryBin = Gtk::manage(wf->createEntryBin());
        creatorEntry    = creatorEntryBin->getEntry();

        genreLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("genreLabel"),
                                Gtk::ALIGN_RIGHT));
        genreEntryBin = Gtk::manage(wf->createEntryBin());
        genreEntry    = genreEntryBin->getEntry();

        fileFormatLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("fileFormatLabel"),
                                Gtk::ALIGN_RIGHT));
        fileFormatComboBox = Gtk::manage(wf->createComboBoxText());

        lengthLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("lengthLabel"),
                                Gtk::ALIGN_RIGHT));
        lengthValueLabel = Gtk::manage(new Gtk::Label());

        // build up the notepad for the different metadata sections
        mainSection = Gtk::manage(new Gtk::Alignment());
        metadataNotebook = Gtk::manage(new Notebook());
        metadataNotebook->appendPage(*mainSection,
                                *getResourceUstring("mainSectionLabel"));

        // buttons, etc.
        uploadButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("uploadButtonLabel")));
        closeButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("closeButtonLabel")));
        statusBar = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("statusBar")));
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // build up the main section
    // TODO: don't hard-code supported file format types
    fileFormatComboBox->append_text("mp3");
    fileFormatComboBox->set_active_text("mp3");
    mainLayout  = Gtk::manage(new Gtk::Table());
    mainLayout->attach(*titleLabel,         0, 1, 0, 1);
    mainLayout->attach(*titleEntryBin,      1, 2, 0, 1);
    mainLayout->attach(*creatorLabel,       0, 1, 1, 2);
    mainLayout->attach(*creatorEntryBin,    1, 2, 1, 2);
    mainLayout->attach(*genreLabel,         0, 1, 2, 3);
    mainLayout->attach(*genreEntryBin,      1, 2, 2, 3);
    mainLayout->attach(*fileFormatLabel,    2, 3, 0, 1);
    mainLayout->attach(*fileFormatComboBox, 3, 4, 0, 1);
    mainLayout->attach(*lengthLabel,        2, 3, 1, 2);
    mainLayout->attach(*lengthValueLabel,   3, 4, 1, 2);
    mainSection->add(*mainLayout);

    // build up the button bar
    buttonBar   = Gtk::manage(new Gtk::HBox());
    buttonBar->add(*closeButton);
    buttonBar->add(*uploadButton);

    // set up the layout, which is a button box
    layout = Gtk::manage(new Gtk::Table());

    // set up the main window, and show everything
    layout->attach(*chooseFileLabel,   0, 1, 0, 1);
    layout->attach(*fileNameEntryBin,  0, 1, 1, 2);
    layout->attach(*chooseFileButton,  1, 2, 1, 2);
    layout->attach(*metadataNotebook,  0, 2, 2, 3);
    layout->attach(*buttonBar,         0, 2, 3, 4);
    layout->attach(*statusBar,         0, 2, 4, 5);

    add(*layout);

    // bind events
    chooseFileButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &UploadFileWindow::onChooseFileButtonClicked));
    fileNameEntry->signal_focus_out_event().connect(sigc::mem_fun(*this,
                                &UploadFileWindow::onFileNameEntryLeave));
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

    try {
        dialog.reset(new Gtk::FileChooserDialog(
                        *getResourceUstring("fileChooserDialogTitle"),
                        Gtk::FILE_CHOOSER_ACTION_OPEN));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    dialog->set_transient_for(*this);

    //Add response buttons the the dialog:
    dialog->add_button(Gtk::Stock::CANCEL, Gtk::RESPONSE_CANCEL);
    dialog->add_button(Gtk::Stock::OPEN, Gtk::RESPONSE_OK);

    int result = dialog->run();

    if (result == Gtk::RESPONSE_OK) {
        fileNameEntry->set_text(dialog->get_filename());
        updateFileInfo();
    }
}


/*------------------------------------------------------------------------------
 *  Update the file information to upload.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: updateFileInfo(void)                        throw ()
{
    std::string             fileName = fileNameEntry->get_text().raw();
    Ptr<std::string>::Ref   newUri(new std::string("file://"));
    newUri->append(fileName);

    if (!isAudioClipValid || 
            *audioClip->getUri() != *newUri) {
        // see if the file exists, and is readable
        std::ifstream   file(fileName.c_str());
        if (!file.good()) {
            isAudioClipValid = false;
            file.close();
            return;
        }
        file.close();
        isAudioClipValid = true;

        Ptr<time_duration>::Ref     playlength;
        try {
            playlength = readPlaylength(fileName);
        } catch (std::invalid_argument &e) {
            statusBar->set_text(e.what());
            playlength.reset(new time_duration(0,0,0,0));
        }

        Ptr<const Glib::ustring>::Ref   tempTitle(new const Glib::ustring);
        audioClip.reset(new AudioClip(tempTitle, playlength, newUri));

        // read the id3 tags
        try {
            audioClip->readTag(gLiveSupport->getMetadataTypeContainer());
        } catch (std::invalid_argument &e) {
            statusBar->set_text(e.what());
            isAudioClipValid = false;
            return;
        }

        titleEntry->set_text(*audioClip->getTitle());
        Ptr<const Glib::ustring>::Ref   creator 
                                        = audioClip->getMetadata("dc:creator");
        creatorEntry->set_text(creator ? *creator : "");

        Ptr<const Glib::ustring>::Ref   genre
                                        = audioClip->getMetadata("dc:type");
        genreEntry->set_text(genre ? *genre : "");

        // display the new play length
        std::ostringstream  lengthStr;
        lengthStr << std::setfill('0')
            << std::setw(2) << playlength->hours() << ":"
            << std::setw(2) << playlength->minutes() << ":"
            << std::setw(2) << (playlength->fractional_seconds() < 500000
                                                  ? playlength->seconds() 
                                                  : playlength->seconds() + 1);
        lengthValueLabel->set_text(lengthStr.str());
    }
}


/*------------------------------------------------------------------------------
 *  The event when the user has left the file name entry box.
 *----------------------------------------------------------------------------*/
bool
UploadFileWindow :: onFileNameEntryLeave(GdkEventFocus    * event)
                                                                    throw ()
{
    updateFileInfo();
    return false;
}


/*------------------------------------------------------------------------------
 *  The event when the upload button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onUploadButtonClicked(void)                 throw ()
{
    try {
        updateFileInfo();
        if (!isAudioClipValid) {
            // TODO: localize error message
            throw std::invalid_argument("file does not exist");
        }

        // set the metadata available
        Ptr<const Glib::ustring>::Ref   ustrValue(new Glib::ustring(
                                                    titleEntry->get_text() ));
        audioClip->setTitle(ustrValue);
        ustrValue.reset(new Glib::ustring(creatorEntry->get_text()));
        audioClip->setMetadata(ustrValue, "dc:creator");
        ustrValue.reset(new Glib::ustring(genreEntry->get_text()));
        audioClip->setMetadata(ustrValue, "dc:type");
        ustrValue.reset(new Glib::ustring(
                                    fileFormatComboBox->get_active_text()));
        audioClip->setMetadata(ustrValue, "dc:format");
        // TODO: is this really what we mean by dc:format?

        // upload the audio clip
        gLiveSupport->uploadFile(audioClip);

        // display success in the status bar
        Ptr<Glib::ustring>::Ref statusText = formatMessage(
                                                    "clipUploadedMessage",
                                                    *audioClip->getTitle());
        statusBar->set_text(*statusText);

        // clean the entry fields
        //titleEntry->set_text("");
        //fileNameEntry->set_text("");
    } catch (std::invalid_argument &e) {
        statusBar->set_text(e.what());
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


/*------------------------------------------------------------------------------
 *  Determine the length of an audio file
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
UploadFileWindow :: readPlaylength(const std::string &   fileName)
                                                throw (std::invalid_argument)
{
    TagLib::FileRef             fileRef(fileName.c_str());
    TagLib::AudioProperties *   audioProperties = fileRef.audioProperties();

    if (audioProperties) {
        Ptr<time_duration>::Ref length(new time_duration(
                      seconds(     audioProperties->length()             )
                    + microseconds(audioProperties->length_microseconds()) ));
        return length;
    } else {
        throw std::invalid_argument("could not read file length");
    }
}

