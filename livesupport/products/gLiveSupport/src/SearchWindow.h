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
    Version  : $Revision: 1.14 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/SearchWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef SearchWindow_h
#define SearchWindow_h

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
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"
#include "AdvancedSearchEntry.h"
#include "BrowseEntry.h"
#include "GLiveSupport.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The Search/Browse window.
 *
 *  @author $Author: fgerlits $
 *  @version $Revision: 1.14 $
 */
class SearchWindow : public WhiteWindow, public LocalizedObject
{
    private:

        /**
         *  The simple search input field.
         */
        EntryBin *                  simpleSearchEntry;

        /**
         *  The box containing the advanced search input fields.
         */
        AdvancedSearchEntry *       advancedSearchEntry;

        /**
         *  The box containing the browse input fields.
         */
        BrowseEntry *               browseEntry;

        /**
         *  Construct the simple search view.
         *  If you enter a string in theGtk::VBox simple search view and 
         *  press Enter
         *  (or the Search button), the local storage will be searched for
         *  items (both audio clips and playlists) where either the title
         *  (dc:title), the creator (dc:creator) or the album (dc:source)
         *  metadata fields contain this string.
         *
         *  @return a pointer to the new box (already Gtk::manage()'ed)
         */
        Gtk::VBox*
        constructSimpleSearchView(void)                         throw ();

        /**
         *  Construct the advanced search view.
         *
         *  @return a pointer to the new box (already Gtk::manage()'ed)
         */
        Gtk::VBox*
        constructAdvancedSearchView(void)                       throw ();

        /**
         *  Construct the browse view.
         *
         *  @return a pointer to the new box (already Gtk::manage()'ed)
         */
        Gtk::VBox*
        constructBrowseView(void)                               throw ();

        /**
         *  Construct the search results display.
         *
         *  @return a pointer to the new tree view (already Gtk::manage()'ed)
         */
        ZebraTreeView *
        constructSearchResultsView(void)                        throw ();

        /**
         *  Event handler for the simple Search button getting clicked.
         */
        void
        onSimpleSearch(void)                                    throw ();

        /**
         *  Event handler for the advanced Search button getting clicked.
         */
        void
        onAdvancedSearch(void)                                  throw ();

        /**
         *  Event handler for changed selection in the Browse view.
         */
        void
        onBrowse(void)                                          throw ();

        /**
         *  Do the searching.
         */
        void
        onSearch(Ptr<SearchCriteria>::Ref   criteria)           throw ();

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *
         *  @param event the button event received
         */
        void
        onEntryClicked(GdkEventButton *     event)              throw ();

        /**
         *  Add a playable to the scratchpad.
         */
        void
        onAddToScratchpad(void)                                 throw ();

        /**
         *  Add a playable to the live mode.
         */
        void
        onAddToLiveMode(void)                                   throw ();

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one clip per row.
         *
         *  @author $Author: fgerlits $
         *  @version $Revision: 1.14 $
         */
        class ModelColumns : public PlayableTreeModelColumnRecord
        {
            public:
                /**
                 *  The column for the type of the entry in the list
                 */
                Gtk::TreeModelColumn<Glib::RefPtr<Gdk::Pixbuf> >
                                                            typeColumn;

                /**
                 *  The column for the title of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         titleColumn;

                /**
                 *  The column for the creator of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         creatorColumn;

                /**
                 *  The column for the length of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         lengthColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(typeColumn);
                    add(titleColumn);
                    add(creatorColumn);
                    add(lengthColumn);
                }
        };

        /**
         *  The column model.
         */
        ModelColumns                    modelColumns;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The tree view showing the search results.
         */
        ZebraTreeView *                 searchResults;

        /**
         *  The pop-up context menu for found items.
         */
        Gtk::Menu *                     contextMenu;
        
        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref          gLiveSupport;


    public:

        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        SearchWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                     Ptr<ResourceBundle>::Ref    bundle)        throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~SearchWindow(void)                                     throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // SearchWindow_h

