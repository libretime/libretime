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
    Version  : $Revision: 1.2 $
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
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

#include "GLiveSupport.h"
#include "MasterPanelUserInfoWidget.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

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
 *  @author $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class UploadFileWindow : public Gtk::Window, public LocalizedObject
{
    protected:
        /**
         *  The layout used in the window.
         */
        Ptr<Gtk::Table>::Ref        layout;

        /**
         *  The choose file label
         */
        Ptr<Gtk::Label>::Ref        chooseFileLabel;

        /**
         *  The text entry for selecting a file name
         */
        Ptr<Gtk::Entry>::Ref        fileNameEntry;

        /**
         *  The file browser button.
         */
        Ptr<Gtk::Button>::Ref       chooseFileButton;

        /**
         *  The name label
         */
        Ptr<Gtk::Label>::Ref        nameLabel;

        /**
         *  The text input for the name.
         */
        Ptr<Gtk::Entry>::Ref        nameEntry;

        /**
         *  The upload button.
         */
        Ptr<Gtk::Button>::Ref       uploadButton;

        /**
         *  The close button.
         */
        Ptr<Gtk::Button>::Ref       closeButton;

        /**
         *  The status bar.
         */
        Ptr<Gtk::Label>::Ref        statusBar;

        /**
         *  The gLiveSupport object, handling the logic of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The name of the file to upload.
         */
        Ptr<std::string>::Ref       fileName;

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
         *  Function to catch the event of the close button being pressed.
         */
        virtual void
        onCloseButtonClicked(void)                          throw ();


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

