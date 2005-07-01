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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/SimplePlaylistManagementWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef SimplePlaylistManagementWindow_h
#define SimplePlaylistManagementWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>

#include <unicode/resbund.h>

#include <gtkmm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/WhiteWindow.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "LiveSupport/Widgets/DialogWindow.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The Simple Playlist Management Window. Allow to edit playlists in
 *  a top-down view fashion.
 *
 *  The layout of this window is roughly the following:
 *  <pre><code>
 *  +--- simple playlist management window --------+
 *  | name:    +-- name input -------------+       |
 *  | +-- playlist entries ----------------------+ |
 *  | | +-- entry1 ----------------------------+ | |
 *  | | +-- entry2 ----------------------------+ | |
 *  | |  ...                                     | |
 *  | +------------------------------------------+ |
 *  |        +- save button -+  +- close button -+ |
 *  | +-- status bar ----------------------------+ |
 *  +----------------------------------------------+
 *  </code></pre>
 *
 *  @author $Author: fgerlits $
 *  @version $Revision: 1.7 $
 */
class SimplePlaylistManagementWindow : public WhiteWindow,
                                       public LocalizedObject
{

    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one playlist entry per row.
         *
         *  @author $Author: fgerlits $
         *  @version $Revision: 1.7 $
         */
        class ModelColumns : public ZebraTreeModelColumnRecord
        {
            public:
                /**
                 *  The column for the id of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Ptr<const UniqueId>::Ref>  idColumn;

                /**
                 *  The column for the start of the playlist entry.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             startColumn;

                /**
                 *  The column for the title of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             titleColumn;

                /**
                 *  The column for the length of the playlist entry.
                 */
                Gtk::TreeModelColumn<Glib::ustring>             lengthColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                  throw ()
                {
                    add(idColumn);
                    add(startColumn);
                    add(titleColumn);
                    add(lengthColumn);
                }
        };


        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The column model.
         */
        ModelColumns                modelColumns;

        /**
         *  The label for the name entry.
         */
        Gtk::Label                * nameLabel;

        /**
         *  The test input entry for the name of the playlist.
         */
        Gtk::Entry                * nameEntry;

        /**
         *  A scrolled window, so that the entry list can be scrolled.
         */
        Gtk::ScrolledWindow       * entriesScrolledWindow;

        /**
         *  The entry tree view, now only showing rows.
         */
        ZebraTreeView             * entriesView;

        /**
         *  The entry tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    entriesModel;

        /**
         *  The save button.
         */
        Button                    * saveButton;

        /**
         *  The close button.
         */
        Button                    * closeButton;

        /**
         *  The status bar.
         */
        Gtk::Label                * statusBar;

        /**
         *  The "are you sure you want to exit?" dialog window.
         */
        Ptr<DialogWindow>::Ref      dialogWindow;

        /**
         *  Save the edited playlist.
         *
         *  @return true if the playlist was saved successully.
         */
        virtual bool
        savePlaylist(void)                                      throw ();

        /**
         *  Signal handler for the save button clicked.
         */
        virtual void
        onSaveButtonClicked(void)                               throw ();

        /**
         *  Signal handler for the close button clicked.
         */
        virtual void
        onCloseButtonClicked(void)                              throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        SimplePlaylistManagementWindow(Ptr<GLiveSupport>::Ref    gLiveSupport,
                                       Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~SimplePlaylistManagementWindow(void)                   throw ();

        /**
         *  Show / update the contents of the playlist management window.
         */
        virtual void
        showContents(void)                                      throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // SimplePlaylistManagementWindow_h

