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

#include <gtkmm/button.h>
#include <gtkmm/table.h>
#include <gtkmm/entry.h>
#include <gtkmm/alignment.h>
#include <gtkmm/box.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/EntryBin.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/Notebook.h"
#include "LiveSupport/Widgets/ScrolledWindow.h"
#include "RestoreBackupWindow.h"
#include "GuiWindow.h"
#include "GLiveSupport.h"
#include "MasterPanelUserInfoWidget.h"

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
 *  The layout of the window is roughly the following:
 *  <pre><code>
 *  +--- upload file window ----------------+
 *  | choose file:     +-- file browser --+ |
 *  | name:            +-- name input ----+ |
 *  |                  +-- upload button -+ |
 *  |                  +-- close button --+ |
 *  | +-- status bar ---------------------+ |
 *  +---------------------------------------+
 *  </code></pre>
 *
 *  @author $Author$
 *  @version $Revision$
 */
class UploadFileWindow : public GuiWindow
{
    private:
        /**
         *  The layout used in the window.
         */
        Gtk::Box                 * layout;

        /**
         *  The choose file label
         */
        Gtk::Label                * chooseFileLabel;

        /**
         *  A container holding the file name entry field.
         */
        EntryBin                  * fileNameEntryBin;

        /**
         *  The text entry for selecting a file name
         */
        Gtk::Entry                * fileNameEntry;

        /**
         *  The file browser button.
         */
        Button                    * chooseFileButton;

        /**
         *  The notepad holding the different sections of metadata.
         */
        Notebook                  * metadataNotebook;

        /**
         *  The layout of the main section.
         */
        Gtk::Table                * mainTable;

        /**
         *  The layout of the music section.
         */
        Gtk::Table                * musicTable;

        /**
         *  The layout of the voice section.
         */
        Gtk::Table                * voiceTable;

        /**
         *  A list of the Dublin Core names of the metadata fields.
         */
        std::vector<Ptr<const Glib::ustring>::Ref>  metadataKeys;
        
        /**
         *  A list of the metadata entry fields.
         */
        std::vector<Gtk::Entry *>                   metadataEntries;

        /**
         *  The label containing the "Length: " text.
         */
        Gtk::Label                * lengthLabel;

        /**
         *  The length value label.
         */
        Gtk::Label                * lengthValueLabel;

        /**
         *  The button box.
         */
        Gtk::ButtonBox            * buttonBox;

        /**
         *  The upload button.
         */
        Gtk::Button               * uploadButton;

        /**
         *  The close button.
         */
        Gtk::Button               * closeButton;

        /**
         *  The status bar.
         */
        Gtk::Label                * statusBar;

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
        readAudioClipInfo(Ptr<const Glib::ustring>::Ref fileName)
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
        readPlaylength(Ptr<const Glib::ustring>::Ref    fileName)
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
        determineFileType(Ptr<const Glib::ustring>::Ref   fileName)
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
        onChooseFileButtonClicked(void)                     throw ();

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
         *  Function to catch the event of the close button being pressed.
         */
        virtual void
        onCloseButtonClicked(void)                          throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         *  @param windowOpenerButton   the button which was pressed to open
         *                              this window.
         */
        UploadFileWindow(Ptr<GLiveSupport>::Ref     gLiveSupport,
                         Ptr<ResourceBundle>::Ref   bundle,
                         Button *                   windowOpenerButton)
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

