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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/PlaylistListWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef PlaylistListWindow_h
#define PlaylistListWindow_h

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
#include "GtkLocalizedObject.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, showing and handling playlists.
 *
 *  The layout of the window is as follows:
 *  <code><pre>
 *  +------ PlaylistListWindow -------------------------------------+
 *  | +----- mainBox ---------------------------------------------+ |
 *  | | +---- playlistBox --------------------------------------+ | |
 *  | | | +--- listBox -----------+  +---- detailBox ---------+ | | |
 *  | | | |  listBoxLabel         |  |  detailBoxLabel        | | | |
 *  | | | |  listScrolledWindow   |  |  detailScrolledWindow  | | | |
 *  | | | +-----------------------+  +------------------------+ | | |
 *  | | +-------------------------------------------------------+ | |
 *  | | +---- buttonBox ----------------------------------------+ | |
 *  | | |  closeButton                                          | | |
 *  | | +-------------------------------------------------------+ | |
 *  | +-----------------------------------------------------------+ |
 *  +---------------------------------------------------------------+
 *  </pre></code>
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.3 $
 */
class PlaylistListWindow : public Gtk::Window, public GtkLocalizedObject
{
    private:

        /**
         *  Display audio clip info in a row of the detail tree view.
         *
         *  @param audioClip the audio clip to display
         *  @param row the row in the detail tree view to display in.
         */
        void
        displayAudioClipDetails(Ptr<AudioClip>::Ref     audioClip,
                                Gtk::TreeModel::Row   & row)
                                                                throw ();

        /**
         *  Display playlist info in a row of the detail tree view.
         *
         *  @param playlist the playlist to display
         *  @param row the row in the detail tree view to display in.
         */
        void
        displayPlaylistDetails(Ptr<Playlist>::Ref       playlist,
                                Gtk::TreeModel::Row   & row)
                                                                throw ();


    protected:

        /**
         *  The model columns, for the playlist window.
         *  Lists one playlist per row.
         *
         *  @author $Author: maroy $
         *  @version $Revision: 1.3 $
         */
        class ModelColumns : public Gtk::TreeModel::ColumnRecord
        {
            public:
                /**
                 *  The column for the id of the playlist.
                 */
                Gtk::TreeModelColumn<unsigned int>      idColumn;

                /**
                 *  The column for the length of the playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>     lengthColumn;

                /**
                 *  The column for the URI of the playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>     uriColumn;

                /**
                 *  The column for the token of the playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>     tokenColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                  throw ()
                {
                    add(idColumn);
                    add(lengthColumn);
                    add(uriColumn);
                    add(tokenColumn);
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
         *  The main container in the window.
         */
        Gtk::VBox                   mainBox;

        /**
         *  The container holding the two boxes for playlist viewing:
         *  one lists the playlist, the other the details of the selected
         *  playlist.
         */
        Gtk::HBox                   playlistBox;

        /**
         *  The container holding the playlist list tree view and accompanying
         *  label.
         */
        Gtk::VBox                   listBox;

        /**
         *  The label for listBox.
         */
        Gtk::Label                  listBoxLabel;

        /**
         *  A scrolled window holding the list of playlists
         *  so that the list can be scrolled.
         */
        Gtk::ScrolledWindow         listScrolledWindow;

        /**
         *  A tree view, showing rows only, the list of playlists.
         */
        Gtk::TreeView               listTreeView;

        /**
         *  The tree model, as a GTK reference, holding the list of
         *  playlists.
         */
        Glib::RefPtr<Gtk::ListStore>    listTreeModel;

        /**
         *  The tree selection, as a GTK reference, holding info on
         *  what's selected from the playlist list.
         */
        Glib::RefPtr<Gtk::TreeSelection>    listTreeSelection;

        /**
         *  The tree selection, as a GTK reference, holding info on
         *  what's selected from the detail view.
         */
        Glib::RefPtr<Gtk::TreeSelection>    detailTreeSelection;

        /**
         *  The container holding the playlist detail tree view and accompanying
         *  label.
         */
        Gtk::VBox                   detailBox;

        /**
         *  The label for detailBox.
         */
        Gtk::Label                  detailBoxLabel;

        /**
         *  A scrolled window holding the details of a playlist
         *  so that the details can be scrolled.
         */
        Gtk::ScrolledWindow         detailScrolledWindow;

        /**
         *  A tree view, showing rows only, the details of the selected
         *  playlist.
         */
        Gtk::TreeView               detailTreeView;

        /**
         *  The tree model, as a GTK reference, holding the details of a
         *  playlist.
         */
        Glib::RefPtr<Gtk::ListStore>    detailTreeModel;

        /**
         *  The box containing the close button.
         */
        Gtk::HButtonBox             buttonBox;

        /**
         *  The close button.
         */
        Ptr<Gtk::Button>::Ref       closeButton;

        /**
         *  Signal to catch the event of the user selecting a row
         *  in the playlist list tree view.
         */
        virtual void
        onPlaylistListSelection(void)                           throw ();

        /**
         *  Signal to catch the event of the user selecting a row
         *  in the detail tree view.
         */
        virtual void
        onDetailSelection(void)                                 throw ();

        /**
         *  Signal handler for the close button clicked.
         */
        virtual void
        onCloseButtonClicked(void)                              throw ();

        /**
         *  Update the window contents, with all the playlists.
         */
        void
        showAllPlaylists(void)                                  throw ();

        /**
         *  Display the details of a playlist in the detailTreeView.
         *
         *  @param playlistId the id of the playlist to display.
         */
        void
        showPlaylistDetails(Ptr<UniqueId>::Ref  playlistId)     throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        PlaylistListWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                           Ptr<ResourceBundle>::Ref    bundle)      throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~PlaylistListWindow(void)                                   throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // PlaylistListWindow_h

