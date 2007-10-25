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
#include <unicode/msgfmt.h>     // for ICU
#include <fileref.h>            // for TagLib
#include <audioproperties.h>    // for TagLib

#include "LiveSupport/Core/Debug.h"
#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/FileTools.h"

#include "UploadFileWindow.h"


using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "uploadFileWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "UploadFileWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
UploadFileWindow :: UploadFileWindow (
                                Gtk::ToggleButton *         windowOpenerButton)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName,
                      windowOpenerButton),
            fileType(invalidType)
{
    Gtk::Label *    fileNameLabel;
    glade->get_widget("fileNameLabel1", fileNameLabel);
    glade->get_widget("fileNameEntry1", fileNameEntry);
    glade->get_widget("browseButton1", browseButton);
    fileNameLabel->set_label(*getResourceUstring("chooseFileLabel"));
    browseButton->set_label(*getResourceUstring("chooseFileButtonLabel"));
    fileNameEntry->signal_focus_out_event().connect(sigc::mem_fun(*this,
                                &UploadFileWindow::onFileNameEntryLeave));
    browseButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &UploadFileWindow::onBrowseButtonClicked));

    Gtk::Label *    mainTabLabel;
    Gtk::Label *    musicTabLabel;
    Gtk::Label *    voiceTabLabel;
    glade->get_widget("mainTabLabel1", mainTabLabel);
    glade->get_widget("musicTabLabel1", musicTabLabel);
    glade->get_widget("voiceTabLabel1", voiceTabLabel);
    mainTabLabel->set_label(*getResourceUstring("mainSectionLabel"));
    musicTabLabel->set_label(*getResourceUstring("musicSectionLabel"));
    voiceTabLabel->set_label(*getResourceUstring("voiceSectionLabel"));

    Ptr<MetadataTypeContainer>::Ref
                metadataTypes = gLiveSupport->getMetadataTypeContainer();

    mainCounter = 0;
    musicCounter = 0;
    voiceCounter = 0;
    MetadataTypeContainer::Vector::const_iterator   it;
    for (it = metadataTypes->begin(); it != metadataTypes->end(); ++it) {
        Ptr<const MetadataType>::Ref    metadata = *it;
        Gtk::Entry *    metadataEntry = constructMetadataItem(metadata);
        if (metadataEntry) {
            metadataKeys.push_back(metadata->getDcName());
            metadataEntries.push_back(metadataEntry);
        }
    }

    Gtk::Label *    lengthLabel;
    glade->get_widget("lengthLabel1", lengthLabel);
    glade->get_widget("lengthValueLabel1", lengthValueLabel);
    lengthLabel->set_label(*getResourceUstring("lengthLabel"));
    lengthValueLabel->set_label("00:00:00");

    glade->get_widget("statusBar1", statusBar);
    statusBar->set_text("");

    glade->connect_clicked("uploadButton1", sigc::mem_fun(*this,
                                &UploadFileWindow::onUploadButtonClicked));
    glade->connect_clicked("cancelButton1", sigc::mem_fun(*this,
                                &UploadFileWindow::onCancelButtonClicked));

    fileChooserFolder = Glib::get_home_dir();
}


/*------------------------------------------------------------------------------
 *  Display the given metadata entry field in the appropriate tab.
 *----------------------------------------------------------------------------*/
Gtk::Entry *
UploadFileWindow :: constructMetadataItem(
                            Ptr<const MetadataType>::Ref   metadata)
                                                                    throw ()
{
    Gtk::Entry *    entry = 0;

    MetadataType::TabType   tab = metadata->getTab();

    switch (tab) {
        case MetadataType::mainTab :
                entry = constructMetadataItem(metadata, "main", mainCounter);
                ++mainCounter;
                break;
                
        case MetadataType::musicTab :
                entry = constructMetadataItem(metadata, "music", musicCounter);
                ++musicCounter;
                break;
                
        case MetadataType::voiceTab :
                entry = constructMetadataItem(metadata, "voice", voiceCounter);
                ++voiceCounter;
                break;
                
        case MetadataType::noTab :      // added to prevent compiler
                break;                  // warning about missing case
    }
    
    return entry;
}


/*------------------------------------------------------------------------------
 *  Display the given metadata entry field in the appropriate tab.
 *----------------------------------------------------------------------------*/
Gtk::Entry *
UploadFileWindow :: constructMetadataItem(
                            Ptr<const MetadataType>::Ref    metadata,
                            const Glib::ustring &           tabName,
                            int                             index)
                                                                    throw ()
{
    Gtk::Box *          metadataBox;
    Gtk::Label *        metadataLabel;
    Gtk::Entry *        metadataEntry;
    
    glade->get_widget(tabName + "Box" + itoa(index + 1),    metadataBox);
    glade->get_widget(tabName + "MetadataLabel" + itoa(index + 1),
                                                            metadataLabel);
    glade->get_widget(tabName + "MetadataEntry" + itoa(index + 1),
                                                            metadataEntry);

    metadataBox->show();
    metadataLabel->set_label(*metadata->getLocalizedName());
    return metadataEntry;
}


/*------------------------------------------------------------------------------
 *  The event when the choose file button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onBrowseButtonClicked(void)                     throw ()
{
    Ptr<Gtk::FileChooserDialog>::Ref    dialog(new Gtk::FileChooserDialog(
                                *getResourceUstring("fileChooserDialogTitle"),
                                Gtk::FILE_CHOOSER_ACTION_OPEN));
    dialog->set_name("uploadFileChooserDialog");

    dialog->set_current_folder(fileChooserFolder);
    dialog->set_transient_for(*mainWindow);

    //Add response buttons the the dialog:
    dialog->add_button(Gtk::Stock::CANCEL, Gtk::RESPONSE_CANCEL);
    dialog->add_button(Gtk::Stock::OPEN,   Gtk::RESPONSE_OK);

    int result = dialog->run();

    if (result == Gtk::RESPONSE_OK) {
        clearEverything();
        fileNameEntry->set_text(dialog->get_filename());
        updateFileInfo();
        fileChooserFolder = dialog->get_current_folder();
    }
}


/*------------------------------------------------------------------------------
 *  Update the file information to upload.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: updateFileInfo(void)                        throw ()
{
    Glib::ustring       fileName = fileNameEntry->get_text();

    // do not display bogus error msg for point-to-focus users
    if (fileName == "") {
        return;
    }

    // see if the file exists, and is readable
    std::ifstream   file(fileName.c_str());
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
UploadFileWindow :: readAudioClipInfo(const Glib::ustring &         fileName)
                                                                throw ()
{
    Ptr<std::string>::Ref   newUri(new std::string("file://"));
    newUri->append(fileName);
    
    Ptr<time_duration>::Ref     playlength;
    try {
        playlength = readPlaylength(fileName);
    } catch (std::invalid_argument &e) {
        statusBar->set_text(*getResourceUstring("unsupportedFileTypeMsg"));
        fileType = invalidType;
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
        
    } catch (XmlRpcMethodFaultException &e) {
        statusBar->set_text(*processException(e));
        return;
        
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
        
    } catch (XmlRpcMethodFaultException &e) {
        statusBar->set_text(*processException(e));
        return;
        
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
    
    Ptr<RestoreBackupWindow>::Ref   restoreBackupWindow(
                                                new RestoreBackupWindow(path));
    restoreBackupWindow->show();
    restoreBackupWindowList.push_back(restoreBackupWindow);
    
    clearEverything();
    hide();
}


/*------------------------------------------------------------------------------
 *  The event when the close button has been clicked.
 *----------------------------------------------------------------------------*/
void
UploadFileWindow :: onCancelButtonClicked(void)                 throw ()
{
    clearEverything();
    hide();
}


/*------------------------------------------------------------------------------
 *  Determine the length of an audio file
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
UploadFileWindow :: readPlaylength(const Glib::ustring &        fileName)
                                                throw (std::invalid_argument)
{
    // TODO: use the appropriate TagLib::X::File subclass constructors,
    // once we find some way of determining the MIME type.
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


/*------------------------------------------------------------------------------
 *  Determine the type of the given file.
 *----------------------------------------------------------------------------*/
UploadFileWindow::FileType
UploadFileWindow :: determineFileType(const Glib::ustring &         fileName)
                                                                throw ()
{
    unsigned int    dotPosition = fileName.rfind('.');
    if (dotPosition == std::string::npos) {
        return invalidType;
    }
    
    Glib::ustring   extension = fileName.substr(dotPosition).lowercase();
    if (extension == ".mp3" || extension == ".ogg") {
        return audioClipType;
        
    } else if (extension == ".tar") {
        if (FileTools::existsInTarball(fileName, "exportedPlaylist.lspl")) {
            return playlistArchiveType;
        } else if (FileTools::existsInTarball(
                                       fileName, "meta-inf/storage.xml")) {
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
    for (unsigned int i = 0; i < metadataEntries.size(); ++i) {
        Gtk::Entry *    metadataEntry = metadataEntries[i];
        metadataEntry->set_text("");
    }
    statusBar->set_text("");
    fileType = invalidType;
}


/*------------------------------------------------------------------------------
 *  Handle some known exception types.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
UploadFileWindow :: processException(const XmlRpcMethodFaultException &     e)
                                                                throw ()
{
    Ptr<const Glib::ustring>::Ref   message;
    
    if (e.getFaultCode() == 888) {
        message = getResourceUstring("duplicateFileMsg");
    
    } else {
        message.reset(new const Glib::ustring(e.what()));
    }
    
    std::cerr << e.what() << std::endl;
    
    return message;
}


