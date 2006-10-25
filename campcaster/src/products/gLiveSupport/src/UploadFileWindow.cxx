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
#include "LiveSupport/Core/FileTools.h"

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
UploadFileWindow :: UploadFileWindow (
                                Ptr<GLiveSupport>::Ref      gLiveSupport,
                                Ptr<ResourceBundle>::Ref    bundle,
                                Button *                    windowOpenerButton)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle,
                      windowOpenerButton),
            fileType(invalidType)
{
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

        // add the metadata entry fields
        mainTable   = Gtk::manage(new Gtk::Table());
        musicTable  = Gtk::manage(new Gtk::Table());
        voiceTable   = Gtk::manage(new Gtk::Table());
                
        Ptr<MetadataTypeContainer>::Ref
                    metadataTypes = gLiveSupport->getMetadataTypeContainer();
        MetadataTypeContainer::Vector::const_iterator   it;
        int     mainCounter  = 0;
        int     musicCounter = 0;
        int     voiceCounter  = 0;
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
                        mainTable->attach(*metadataName, 0, 1,
                                           mainCounter, mainCounter + 1);
                        mainTable->attach(*metadataEntryBin, 1, 2,
                                           mainCounter, mainCounter + 1);
                        ++mainCounter;
                        break;
                        
                case MetadataType::musicTab :
                        musicTable->attach(*metadataName, 0, 1,
                                           musicCounter, musicCounter + 1);
                        musicTable->attach(*metadataEntryBin, 1, 2,
                                           musicCounter, musicCounter + 1);
                        ++musicCounter;
                        break;
                        
                case MetadataType::voiceTab :
                        voiceTable->attach(*metadataName, 0, 1,
                                           voiceCounter, voiceCounter + 1);
                        voiceTable->attach(*metadataEntryBin, 1, 2,
                                           voiceCounter, voiceCounter + 1);
                        ++voiceCounter;
                        break;
                        
                case MetadataType::noTab :      // added to prevent compiler
                        break;                  // warning about missing case
            }
        }

        // set up the length label, and add it to the main tab
        lengthLabel = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("lengthLabel") ));
        lengthValueLabel = Gtk::manage(new Gtk::Label());

        mainTable->attach(*lengthLabel,      0, 1, mainCounter, mainCounter+1);
        mainTable->attach(*lengthValueLabel, 1, 2, mainCounter, mainCounter+1);
        
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

    // build up the notepad for the different metadata sections
    metadataNotebook = Gtk::manage(new Notebook());
    
    mainTable->set_row_spacings(2);
    mainTable->set_col_spacings(5);
    musicTable->set_row_spacings(2);
    musicTable->set_col_spacings(5);
    voiceTable->set_row_spacings(2);
    voiceTable->set_col_spacings(5);
      
    // expand the input fields horizontally, but shrink-wrap vertically
    Gtk::Alignment *    mainAlignment  = Gtk::manage(new Gtk::Alignment(
                                                0.0, 0.0, 1.0, 0.0));
    Gtk::Alignment *    musicAlignment = Gtk::manage(new Gtk::Alignment(
                                                0.0, 0.0, 1.0, 0.0));
    Gtk::Alignment *    voiceAlignment  = Gtk::manage(new Gtk::Alignment(
                                                0.0, 0.0, 1.0, 0.0));

    mainAlignment->add(*mainTable);
    musicAlignment->add(*musicTable);
    voiceAlignment->add(*voiceTable);
    
    ScrolledWindow *   mainScrolledWindow 
                            = Gtk::manage(new ScrolledWindow());
    ScrolledWindow *   musicScrolledWindow 
                            = Gtk::manage(new ScrolledWindow());
    ScrolledWindow *   voiceScrolledWindow 
                            = Gtk::manage(new ScrolledWindow());

    mainScrolledWindow->set_policy(Gtk::POLICY_AUTOMATIC, 
                                   Gtk::POLICY_AUTOMATIC);
    musicScrolledWindow->set_policy(Gtk::POLICY_AUTOMATIC, 
                                    Gtk::POLICY_AUTOMATIC);
    voiceScrolledWindow->set_policy(Gtk::POLICY_AUTOMATIC, 
                                   Gtk::POLICY_AUTOMATIC);

    mainScrolledWindow->add(*mainAlignment);
    musicScrolledWindow->add(*musicAlignment);
    voiceScrolledWindow->add(*voiceAlignment);
       
    try {
        metadataNotebook->appendPage(*mainScrolledWindow,
                                *getResourceUstring("mainSectionLabel"));
        metadataNotebook->appendPage(*musicScrolledWindow,
                                *getResourceUstring("musicSectionLabel"));
        metadataNotebook->appendPage(*voiceScrolledWindow,
                                *getResourceUstring("voiceSectionLabel"));
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // build up the button box
    buttonBox   = Gtk::manage(new Gtk::HButtonBox());
    buttonBox->set_layout(Gtk::BUTTONBOX_END);
    buttonBox->set_spacing(5);
    buttonBox->pack_start(*closeButton);
    buttonBox->pack_start(*uploadButton);

    // set up the main window, and show everything
    Gtk::Box *      topBox = Gtk::manage(new Gtk::HBox);
    topBox->pack_start(*chooseFileLabel,  Gtk::PACK_SHRINK, 5);
    topBox->pack_start(*fileNameEntryBin, Gtk::PACK_EXPAND_WIDGET, 5);
    topBox->pack_start(*chooseFileButton, Gtk::PACK_SHRINK, 5);
    
    Gtk::Box *      extraSpace = Gtk::manage(new Gtk::HBox);
        
    layout = Gtk::manage(new Gtk::VBox());
    layout->pack_start(*extraSpace, Gtk::PACK_SHRINK, 5);
    layout->pack_start(*topBox,     Gtk::PACK_SHRINK, 5);
    layout->pack_start(*metadataNotebook, Gtk::PACK_EXPAND_WIDGET, 5);
    layout->pack_start(*buttonBox,  Gtk::PACK_SHRINK, 5);
    layout->pack_start(*statusBar,  Gtk::PACK_SHRINK, 5);

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

    // set the file chooser's default folder to the user's home directory
    fileChooserFolder = Glib::get_home_dir();

    // show everything
    set_name("uploadFileWindow");
    set_default_size(350, 500);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    
    show_all_children();
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

    dialog->set_name("uploadFileChooserDialog");
    gLiveSupport->getWindowPosition(dialog);

    dialog->set_current_folder(fileChooserFolder);
    dialog->set_transient_for(*this);

    //Add response buttons the the dialog:
    dialog->add_button(Gtk::Stock::CANCEL, Gtk::RESPONSE_CANCEL);
    dialog->add_button(Gtk::Stock::OPEN,   Gtk::RESPONSE_OK);

    int result = dialog->run();

    if (result == Gtk::RESPONSE_OK) {
        clearEverything();
        fileNameEntry->set_text(dialog->get_filename());
        updateFileInfo();
        fileChooserFolder = dialog->get_current_folder();
        gLiveSupport->putWindowPosition(dialog);
    }
}


/*------------------------------------------------------------------------------
 *  Update the file information to upload.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: updateFileInfo(void)                        throw ()
{
    Ptr<Glib::ustring>::Ref fileName(new Glib::ustring(
                                        fileNameEntry->get_text() ));

    // see if the file exists, and is readable
    std::ifstream   file(fileName->c_str());
    if (!file.good()) {
        file.close();
        statusBar->set_text(*getResourceUstring("couldNotOpenFileMsg"));
        fileType = invalidType;
        return;
    }
    file.close();
    
    fileType = determineFileType(fileName);
    
    switch (fileType) {
        case audioClipType:         readAudioClipInfo(fileName);
                                    break;
        
        case playlistArchiveType:   statusBar->set_text("");
                                    break;
        
        case storageArchiveType:    statusBar->set_text("");
                                    break;
        
        case invalidType:           statusBar->set_text(*getResourceUstring(
                                                    "unsupportedFileTypeMsg"));
                                    break;
    }
}


/*------------------------------------------------------------------------------
 *  Read the playlength and metadata info from the binary audio file.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: readAudioClipInfo(Ptr<const Glib::ustring>::Ref   fileName)
                                                                throw ()
{
    Ptr<std::string>::Ref   newUri(new std::string("file://"));
    newUri->append(*fileName);
    
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
    Ptr<const std::string>::Ref lengthStr
                                = TimeConversion::timeDurationToHhMmSsString(
                                        playlength);
    lengthValueLabel->set_text(*lengthStr);

    // read the id3 tags
    Ptr<const Glib::ustring>::Ref   tempTitle(new const Glib::ustring);
    audioClip.reset(new AudioClip(tempTitle, playlength, newUri));

    try {
        audioClip->readTag(gLiveSupport->getMetadataTypeContainer());
    } catch (std::invalid_argument &e) {
        statusBar->set_text(e.what());
        fileType = invalidType;
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
    switch (fileType) {
        case audioClipType:         uploadAudioClip();
                                    break;
        
        case playlistArchiveType:   uploadPlaylistArchive();
                                    break;
        
        case storageArchiveType:    uploadStorageArchive();
                                    break;
        
        case invalidType:           break;
    }
}


/*------------------------------------------------------------------------------
 *  Upload an audio clip to the storage.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: uploadAudioClip(void)                       throw ()
{
    Ptr<MetadataTypeContainer>::Ref
                metadataTypes = gLiveSupport->getMetadataTypeContainer();

    for (unsigned int i=0; i < metadataKeys.size(); ++i) {
        Ptr<const Glib::ustring>::Ref   metadataKey   = metadataKeys[i];
        Gtk::Entry *                    metadataEntry = metadataEntries[i];
        
        Ptr<const Glib::ustring>::Ref   metadataValue(new Glib::ustring(
                                            metadataEntry->get_text() ));
        if (*metadataValue != "") {
            if (metadataTypes->check(metadataValue, *metadataKey)) {
                audioClip->setMetadata(metadataValue, *metadataKey);
            } else {
                Ptr<const MetadataType>::Ref
                        metadata = metadataTypes->getByDcName(*metadataKey);
                Ptr<const Glib::ustring>::Ref
                        localizedName = metadata->getLocalizedName();
                statusBar->set_text(*formatMessage("badMetadataMsg",
                                                   *localizedName));
                return;
            }
        }
    }
    
    Ptr<const Glib::ustring>::Ref   title = audioClip->getTitle();
    if (!title || *title == "") {
        statusBar->set_text(*getResourceUstring("missingTitleMsg"));
        return;
    }

    try {
        gLiveSupport->uploadAudioClip(audioClip);
    } catch (XmlRpcException &e) {
        statusBar->set_text(e.what());
        std::cerr << e.what();
        return;
    }

    clearEverything();
    statusBar->set_text(*formatMessage("fileUploadedMsg",
                                       *audioClip->getTitle() ));
    hide();
}


/*------------------------------------------------------------------------------
 *  Upload a playlist archive to the storage.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: uploadPlaylistArchive(void)                 throw ()
{
    Ptr<const Glib::ustring>::Ref   path(new const Glib::ustring(
                                                fileNameEntry->get_text() ));
    
    Ptr<Playlist>::Ref              playlist;
    try {
        playlist = gLiveSupport->uploadPlaylistArchive(path);
    } catch (XmlRpcException &e) {
        statusBar->set_text(e.what());
        return;
    }
    
    clearEverything();
    statusBar->set_text(*formatMessage("fileUploadedMsg",
                                       *playlist->getTitle() ));
    hide();
}


/*------------------------------------------------------------------------------
 *  Upload a storage archive to the storage.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: uploadStorageArchive(void)                  throw ()
{
    Ptr<const Glib::ustring>::Ref   path(new const Glib::ustring(
                                                fileNameEntry->get_text() ));
    
    Ptr<ResourceBundle>::Ref        restoreBackupBundle;
    try {
        restoreBackupBundle = gLiveSupport->getBundle("restoreBackupWindow");
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    Ptr<RestoreBackupWindow>::Ref   restoreBackupWindow(
                                                new RestoreBackupWindow(
                                                        gLiveSupport,
                                                        restoreBackupBundle,
                                                        path));
    restoreBackupWindow->show();
    restoreBackupWindowList.push_back(restoreBackupWindow);
    
    clearEverything();
    hide();
}


/*------------------------------------------------------------------------------
 *  The event when the close button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onCloseButtonClicked(void)                 throw ()
{
    clearEverything();
    hide();
}


/*------------------------------------------------------------------------------
 *  Determine the length of an audio file
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
UploadFileWindow :: readPlaylength(Ptr<const Glib::ustring>::Ref    fileName)
                                                throw (std::invalid_argument)
{
    // TODO: use the appropriate TagLib::X::File subclass constructors,
    // once we find some way of determining the MIME type.
    TagLib::FileRef             fileRef(fileName->c_str());
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


/*------------------------------------------------------------------------------
 *  Determine the type of the given file.
 *----------------------------------------------------------------------------*/
UploadFileWindow::FileType
UploadFileWindow :: determineFileType(Ptr<const Glib::ustring>::Ref   fileName)
                                                                throw ()
{
    unsigned int    dotPosition = fileName->rfind('.');
    if (dotPosition == std::string::npos) {
        return invalidType;
    }
    
    Glib::ustring   extension = fileName->substr(dotPosition).lowercase();
    if (extension == ".mp3" || extension == ".ogg") {
        return audioClipType;
        
    } else if (extension == ".tar") {
        if (FileTools::existsInTarball(*fileName, "exportedPlaylist.lspl")) {
            return playlistArchiveType;
        } else if (FileTools::existsInTarball(
                                       *fileName, "meta-inf/storage.xml")) {
            return storageArchiveType;
        } else {
            return invalidType;
        }
        
    } else {
        return invalidType;
    }
}


/*------------------------------------------------------------------------------
 *  Clear all the input fields and set the fileType to 'invalidType'.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: clearEverything(void)                       throw ()
{
    fileNameEntry->set_text("");
    for (unsigned int i=0; i < metadataEntries.size(); ++i) {
        Gtk::Entry *    metadataEntry = metadataEntries[i];
        metadataEntry->set_text("");
    }
    statusBar->set_text("");
    fileType = invalidType;
}

