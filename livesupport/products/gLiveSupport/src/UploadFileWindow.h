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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/UploadFileWindow.h,v $

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
#include "LiveSupport/Widgets/WhiteWindow.h"

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
 *  @author $Author: fgerlits $
 *  @version $Revision: 1.4 $
 */
class UploadFileWindow : public WhiteWindow, public LocalizedObject
{
    protected:
        /**
         *  The layout used in the window.
         */
        Gtk::Table                * layout;

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
         *  The main input section.
         */
        Gtk::Alignment            * mainSection;

        /**
         *  The layout of the main section.
         */
        Gtk::Table                * mainLayout;

        /**
         *  The title label
         */
        Gtk::Label                * titleLabel;

        /**
         *  A container holding the title entry field.
         */
        EntryBin                  * titleEntryBin;

        /**
         *  The text input for the title.
         */
        Gtk::Entry                * titleEntry;

        /**
         *  The creator label
         */
        Gtk::Label                * creatorLabel;

        /**
         *  A container holding the creator entry field.
         */
        EntryBin                  * creatorEntryBin;

        /**
         *  The text input for the creator.
         */
        Gtk::Entry                * creatorEntry;

        /**
         *  The genre label
         */
        Gtk::Label                * genreLabel;

        /**
         *  A container holding the genre entry field.
         */
        EntryBin                  * genreEntryBin;

        /**
         *  The text input for the genre.
         */
        Gtk::Entry                * genreEntry;

        /**
         *  The file format label.
         */
        Gtk::Label                * fileFormatLabel;

        /**
         *  The file format combo box.
         */
        ComboBoxText              * fileFormatComboBox;

        /**
         *  The length label.
         */
        Gtk::Label                * lengthLabel;

        /**
         *  The length value label.
         */
        Gtk::Label                * lengthValueLabel;

        /**
         *  The button bar.
         */
        Gtk::HBox                 * buttonBar;

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
         *  The gLiveSupport object, handling the logic of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The name of the file to upload.
         */
        Ptr<std::string>::Ref       fileName;

        /**
         *  Signals if the file under fileName is good.
         */
        bool                        isFileGood;

        /**
         *  The URI to the file to upload.
         *  Basically same as fileName, with 'file://' prepended.
         */
        Ptr<std::string>::Ref       fileURI;

        /**
         *  The playling length of the file to upload.
         */
        Ptr<time_duration>::Ref     playlength;

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
        bool
        onFileNameEntryLeave(GdkEventFocus    * event)      throw ();

        /**
         *  Function to catch the event of the close button being pressed.
         */
        virtual void
        onCloseButtonClicked(void)                          throw ();

        /**
         *  Update the information for the file to upload, based on the
         *  value of the fileNameEntry text entry field.
         */
        void
        updateFileInfo(void)                                throw ();

        /**
         *  Determine the length of an audio file on disk.
         *
         *  @param fileName     a binary audio file (e.g., /tmp/some_clip.mp3)
         *  @return             the length of the file
         *  @exception std::invalid_argument if the file is not found, or its
         *                                   length could not be determined
         */
        Ptr<time_duration>::Ref
        readPlaylength(Ptr<const std::string>::Ref   fileName)
                                                throw (std::invalid_argument);


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the gLiveSupport object, handling the
         *         logic of the application
         *  @param bundle the resource bundle holding localized resources
         */
        UploadFileWindow(Ptr<GLiveSupport>::Ref     gLiveSupport,
                         Ptr<ResourceBundle>::Ref   bundle)
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

