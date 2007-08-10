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
#ifndef UploadFileWindow_h
#define UploadFileWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "RestoreBackupWindow.h"
#include "GLiveSupport.h"

#include "GuiWindow.h"
#include "LiveSupport/Core/NumericTools.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The upload file window.
 *
 *  It allows one to select a file from the file system, add metadata,
 *  and upload it to the storage server.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class UploadFileWindow : public  GuiWindow,
                         private NumericTools
{
    private:

        /**
         *  The text entry for selecting a file name
         */
        Gtk::Entry *                fileNameEntry;

        /**
         *  The file browser button.
         */
        Gtk::Button *               browseButton;

        /**
         *  A list of the Dublin Core names of the metadata fields.
         */
        std::vector<Ptr<const Glib::ustring>::Ref>
                                    metadataKeys;
        
        /**
         *  A list of the metadata entry fields.
         */
        std::vector<Gtk::Entry *>   metadataEntries;

        /**
         *  A counter for the metadata entries in the Main tab.
         */
        int                         mainCounter;

        /**
         *  A counter for the metadata entries in the Music tab.
         */
        int                         musicCounter;

        /**
         *  A counter for the metadata entries in the Voice tab.
         */
        int                         voiceCounter;

        /**
         *  The length value label.
         */
        Gtk::Label *                lengthValueLabel;

        /**
         *  The upload button.
         */
        Gtk::Button *               uploadButton;

        /**
         *  The cancel button.
         */
        Gtk::Button *               cancelButton;

        /**
         *  The status bar.
         */
        Gtk::Label *                statusBar;

        /**
         *  The audio clip to be uploaded.
         */
        Ptr<AudioClip>::Ref         audioClip;

        /**
         *  The restore backup windows opened by this window.
         */
        std::vector<Ptr<RestoreBackupWindow>::Ref>
                                    restoreBackupWindowList;

        /**
         *  The possible file types.
         */
        typedef enum { audioClipType, 
                       playlistArchiveType, 
                       storageArchiveType, 
                       invalidType }
                                    FileType;

        /**
         *  The type of the currently selected file.
         */
        FileType                    fileType;

        /**
         *  The starting folder for the file chooser dialog.
         */
        Glib::ustring               fileChooserFolder;

        /**
         *  Construct the metadata entry item.
         *
         *  @param  metadata    the metadata to display in the entry.
         *  @return the entry field for the metadata.
         */
        Gtk::Entry *
        constructMetadataItem(Ptr<const MetadataType>::Ref   metadata)
                                                            throw ();

        /**
         *  Construct the metadata entry item.
         *  This is an auxiliary method, called by the other method with
         *  the same name.
         *
         *  @param  metadata    the metadata to display in the entry.
         *  @param  tabName     the name of the tab: "main", "music" or "voice".
         *  @param  index       the index of the item in its tab.
         *  @return the entry field for the metadata.
         */
        Gtk::Entry *
        constructMetadataItem(Ptr<const MetadataType>::Ref      metadata,
                              const Glib::ustring &             tabName,
                              int                               index)
                                                            throw ();

        /**
         *  Update the information for the file to upload, based on the
         *  value of the fileNameEntry text entry field.
         */
        void
        updateFileInfo(void)                                throw ();

        /**
         *  Read the playlength and metadata info from the binary audio file.
         *
         *  @param  fileName    the local file name (with path) for the 
         *                      binary audio file.
         */
        void
        readAudioClipInfo(const Glib::ustring &     fileName)
                                                            throw ();

        /**
         *  Determine the length of an audio file on disk.
         *
         *  @param fileName     a binary audio file (e.g., /tmp/some_clip.mp3)
         *  @return             the length of the file; a null pointer if the
         *                      length could not be read (see bug #1426)
         *  @exception std::invalid_argument if the file is not found, or its
         *                                   format is not supported by TagLib
         */
        Ptr<time_duration>::Ref
        readPlaylength(const Glib::ustring &        fileName)
                                                throw (std::invalid_argument);

        /**
         *  Upload an audio clip to the storage.
         */
        void
        uploadAudioClip(void)                               throw ();

        /**
         *  Upload a playlist archive to the storage.
         */
        void
        uploadPlaylistArchive(void)                         throw ();

        /**
         *  Upload a storage archive to the storage.
         */
        void
        uploadStorageArchive(void)                          throw ();

        /**
         *  Determine the type of the given file.
         *
         *  This method looks at the extension only.
         *  TODO: replace this with proper mime-type detection
         *  (gnomevfs, system("file fileName"), or ...?)
         *
         *  @param  fileName    the name (with path) of the local file.
         *  @return the type of the file.
         */
        FileType
        determineFileType(const Glib::ustring &     fileName)
                                                            throw ();

        /**
         *  Clear all the input fields and set the fileType to 'invalidType'.
         */
        void
        clearEverything(void)                               throw ();

        /**
         *  Handle some known exception types.
         *
         *  @param  e   the exception to be processed.
         *  @return a localized error message if e has one of the recognized
         *          faultCode values; e.what() if not.
         */
        Ptr<const Glib::ustring>::Ref
        processException(const XmlRpcMethodFaultException &     e)
                                                            throw ();


    protected:

        /**
         *  Function to catch the event of the choose file button being
         *  pressed.
         */
        virtual void
        onBrowseButtonClicked(void)                         throw ();

        /**
         *  Function to catch the event of the upload button being
         *  pressed.
         */
        virtual void
        onUploadButtonClicked(void)                         throw ();

        /**
         *  Signal handler for the user leaving the filename entry box,
         *  where persumably he may have types in a new filename.
         *
         *  @param event the event recieved.
         *  @return true if the event has been processed, false otherwise.
         */
        virtual bool
        onFileNameEntryLeave(GdkEventFocus    * event)      throw ();

        /**
         *  Function to catch the event of the cancel button being pressed.
         */
        virtual void
        onCancelButtonClicked(void)                         throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param  windowOpenerButton  the button which was pressed to open
         *                              this window.
         */
        UploadFileWindow(Gtk::ToggleButton *        windowOpenerButton)
                                                                throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~UploadFileWindow(void)                                 throw ()
        {
        }

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // UploadFileWindow_h

