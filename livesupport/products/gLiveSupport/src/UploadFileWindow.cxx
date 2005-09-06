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
    Version  : $Revision: 1.19 $
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

        // build up the notepad for the different metadata sections
        metadataNotebook = Gtk::manage(new Notebook());
        
        mainSection  = Gtk::manage(new Gtk::Alignment());
        musicSection = Gtk::manage(new Gtk::Alignment());
        talkSection  = Gtk::manage(new Gtk::Alignment());
        
        mainLayout   = Gtk::manage(new Gtk::Table());
        musicLayout  = Gtk::manage(new Gtk::Table());
        talkLayout   = Gtk::manage(new Gtk::Table());
        
        mainLayout->set_row_spacings(2);
        mainLayout->set_col_spacings(5);
        musicLayout->set_row_spacings(2);
        musicLayout->set_col_spacings(5);
        talkLayout->set_row_spacings(2);
        talkLayout->set_col_spacings(5);
        
        mainSection->add(*mainLayout);
        musicSection->add(*musicLayout);
        talkSection->add(*talkLayout);
        
        metadataNotebook->appendPage(*mainSection,
                                *getResourceUstring("mainSectionLabel"));
        metadataNotebook->appendPage(*musicSection,
                                *getResourceUstring("musicSectionLabel"));
        metadataNotebook->appendPage(*talkSection,
                                *getResourceUstring("talkSectionLabel"));
        
        // add the metadata entry fields
        Ptr<MetadataTypeContainer>::Ref
                    metadataTypes = gLiveSupport->getMetadataTypeContainer();
        MetadataTypeContainer::Vector::const_iterator   it;
        int     mainCounter  = 0;
        int     musicCounter = 0;
        int     talkCounter  = 0;
        for (it = metadataTypes->begin(); it != metadataTypes->end(); ++it) {
            Ptr<const MetadataType>::Ref    metadata = *it;
            
            MetadataType::TabType           tab = metadata->getTab();
            if (tab == MetadataType::noTab) {
                continue;
            }
            
            Gtk::Label *    metadataName = Gtk::manage(new Gtk::Label(
                                            *metadata->getLocalizedName() ));
            EntryBin *      metadataEntryBin = Gtk::manage(
                                            wf->createEntryBin() );
            
            metadataKeys.push_back(metadata->getDcName());
            metadataEntries.push_back(metadataEntryBin->getEntry());
            
            switch (tab) {
                case MetadataType::mainTab :
                        mainLayout->attach(*metadataName, 0, 1,
                                           mainCounter, mainCounter + 1);
                        mainLayout->attach(*metadataEntryBin, 1, 2,
                                           mainCounter, mainCounter + 1);
                        ++mainCounter;
                        break;
                        
                case MetadataType::musicTab :
                        musicLayout->attach(*metadataName, 0, 1,
                                           musicCounter, musicCounter + 1);
                        musicLayout->attach(*metadataEntryBin, 1, 2,
                                           musicCounter, musicCounter + 1);
                        ++musicCounter;
                        break;
                        
                case MetadataType::talkTab :
                        talkLayout->attach(*metadataName, 0, 1,
                                           talkCounter, talkCounter + 1);
                        talkLayout->attach(*metadataEntryBin, 1, 2,
                                           talkCounter, talkCounter + 1);
                        ++talkCounter;
                        break;
                        
                case MetadataType::noTab :      // added to prevent compiler
                        break;                  // warning about missing case
            }
        }

        // set up the length label, and add it to the main tab
        lengthLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("lengthLabel"),
                                Gtk::ALIGN_RIGHT));
        lengthValueLabel = Gtk::manage(new Gtk::Label());
        lengthValueLabel->set_width_chars(8);

        mainLayout->attach(*lengthLabel,      0, 1, mainCounter, mainCounter+1);
        mainLayout->attach(*lengthValueLabel, 1, 2, mainCounter, mainCounter+1);
        
        // buttons, etc.
        uploadButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("uploadButtonLabel")));
        closeButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("closeButtonLabel")));
        statusBar = Gtk::manage(new Gtk::Label(""));
        statusBar->set_ellipsize(Pango::ELLIPSIZE_END);
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

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

    // see if the file exists, and is readable
    std::ifstream   file(fileName.c_str());
    if (!file.good()) {
        file.close();
        statusBar->set_text(*getResourceUstring("couldNotOpenFileMsg"));
        isAudioClipValid = false;
        return;
    }
    file.close();

    Ptr<time_duration>::Ref     playlength;
    try {
        playlength = readPlaylength(fileName);
    } catch (std::invalid_argument &e) {
        statusBar->set_text(*getResourceUstring("unsupportedFileTypeMsg"));
        return;
    }
    if (!playlength) {
        statusBar->set_text(*getResourceUstring("couldNotReadLengthMsg"));
        playlength.reset(new time_duration(0,0,0,0));
    }

    // display the new play length
    std::ostringstream  lengthStr;
    lengthStr << std::setfill('0')
        << std::setw(2) << playlength->hours() << ":"
        << std::setw(2) << playlength->minutes() << ":"
        << std::setw(2) << (playlength->fractional_seconds() < 500000
                                                ? playlength->seconds() 
                                                : playlength->seconds() + 1);
    lengthValueLabel->set_text(lengthStr.str());

    // read the id3 tags
    Ptr<const Glib::ustring>::Ref   tempTitle(new const Glib::ustring);
    audioClip.reset(new AudioClip(tempTitle, playlength, newUri));

    try {
        audioClip->readTag(gLiveSupport->getMetadataTypeContainer());
    } catch (std::invalid_argument &e) {
        statusBar->set_text(e.what());
        isAudioClipValid = false;
        return;
    }

    for (unsigned int i=0; i < metadataKeys.size(); ++i) {
        Ptr<const Glib::ustring>::Ref   metadataKey   = metadataKeys[i];
        Gtk::Entry *                    metadataEntry = metadataEntries[i];
        
        Ptr<const Glib::ustring>::Ref   metadataValue
                                        = audioClip->getMetadata(*metadataKey);
        if (metadataValue) {
            metadataEntry->set_text(*metadataValue);
        }
    }

    statusBar->set_text("");
    isAudioClipValid = true;
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
    if (!isAudioClipValid) {
        return;
    }

    for (unsigned int i=0; i < metadataKeys.size(); ++i) {
        Ptr<const Glib::ustring>::Ref   metadataKey   = metadataKeys[i];
        Gtk::Entry *                    metadataEntry = metadataEntries[i];
        
        Ptr<const Glib::ustring>::Ref   metadataValue(new Glib::ustring(
                                            metadataEntry->get_text() ));
        if (*metadataValue != "") {
            audioClip->setMetadata(metadataValue, *metadataKey);
        }
    }
    
    Ptr<const Glib::ustring>::Ref   title = audioClip->getTitle();
    if (!title || *title == "") {
        statusBar->set_text(*getResourceUstring("missingTitleMsg"));
        return;
    }

    try {
        gLiveSupport->uploadFile(audioClip);
    } catch (XmlRpcException &e) {
        statusBar->set_text(e.what());
        return;
    }

    statusBar->set_text(*formatMessage("clipUploadedMsg",
                                       *audioClip->getTitle() ));

    fileNameEntry->set_text("");
    for (unsigned int i=0; i < metadataEntries.size(); ++i) {
        Gtk::Entry *    metadataEntry = metadataEntries[i];
        metadataEntry->set_text("");
    }

    isAudioClipValid = false;
}


/*------------------------------------------------------------------------------
 *  The event when the close button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onCloseButtonClicked(void)                 throw ()
{
    fileNameEntry->set_text("");
    for (unsigned int i=0; i < metadataEntries.size(); ++i) {
        Gtk::Entry *    metadataEntry = metadataEntries[i];
        metadataEntry->set_text("");
    }

    statusBar->set_text("");

    isAudioClipValid = false;

    hide();
}


/*------------------------------------------------------------------------------
 *  Determine the length of an audio file
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
UploadFileWindow :: readPlaylength(const std::string &   fileName)
                                                throw (std::invalid_argument)
{
    // TODO: replace this with mime-type detection (gnomevfs?) and
    // the appropriate TagLib::X::File subclass constructors
    TagLib::FileRef             fileRef(fileName.c_str());
    if (fileRef.isNull()) {
        throw std::invalid_argument("unsupported file type");
    }
    
    TagLib::AudioProperties *   audioProperties = fileRef.audioProperties();
    Ptr<time_duration>::Ref     length;
    if (audioProperties) {
        length.reset(new time_duration(
                      seconds(     audioProperties->length()             )
                    + microseconds(audioProperties->length_microseconds()) ));
    }
    return length;
}

