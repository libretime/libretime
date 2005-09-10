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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/SchedulerWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef SchedulerWindow_h
#define SchedulerWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>

#include <boost/date_time/gregorian/gregorian.hpp>

#include <unicode/resbund.h>

#include <gtkmm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/WhiteWindow.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The Scheduler window, showing and allowing scheduling of playlists.
 *
 *  The rough layout of the window is:
 *  <code><pre>
 *  +--- scheduler window ----------------------------+
 *  | +--- calendar --------------------------------+ |
 *  | |                                             | |
 *  | +---------------------------------------------+ |
 *  | +--- the selected day ------------------------+ |
 *  | +--- entires for the selected day ------------+ |
 *  | | +--- entry 1 -----------------------------+ | |
 *  | | | +-- start --+ +-- title --+ +-- end --+ | | |
 *  | | +-----------------------------------------+ | |
 *  | | +--- entry 2 -----------------------------+ | |
 *  | | | +-- start --+ +-- title --+ +-- end --+ | | |
 *  | | +-----------------------------------------+ | |
 *  | +---------------------------------------------+ |
 *  | +-- close button -----------------------------+ |
 *  +-------------------------------------------------+
 *  </pre></code>
 *
 *  @author $Author$
 *  @version $Revision$
 */
class SchedulerWindow : public WhiteWindow, public LocalizedObject
{

    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one scheduled item per row.
         *
         *  @author $Author$
         *  @version $Revision$
         */
        class ModelColumns : public Gtk::TreeModel::ColumnRecord
        {
            public:
                /**
                 *  The column for the id of the playlist.
                 */
                Gtk::TreeModelColumn<Ptr<const UniqueId>::Ref>  idColumn;

                /**
                 *  The column for the start of the playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         startColumn;

                /**
                 *  The column for the title of the playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         titleColumn;

                /**
                 *  The column for the end of the playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         endColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                  throw ()
                {
                    add(idColumn);
                    add(startColumn);
                    add(titleColumn);
                    add(endColumn);
                }
        };


        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The date selected for display.
         */
        Ptr<boost::gregorian::date>::Ref        selectedDate;

        /**
         *  The main container in the window.
         */
        Gtk::Table                * layout;

        /**
         *  The calendar to select a specific date from.
         */
        Gtk::Calendar             * calendar;

        /**
         *  The label saying which day is being displayed.
         */
        Gtk::Label                * dateLabel;

        /**
         *  The column model.
         */
        Ptr<ModelColumns>::Ref          entryColumns;

        /**
         *  The tree view, now only showing rows, each scheduled entry for a
         *  specific day.
         */
        Gtk::TreeView                 * entriesView;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    entriesModel;

        /**
         *  The right-click context menu for schedule entries.
         */
        Gtk::Menu                     * entryMenu;

        /**
         *  The close button.
         */
        Gtk::Button                   * closeButton;

        /**
         *  Signal handler for when a date is selected in the calendar.
         */
        virtual void
        onDateSelected(void)                                    throw ();

        /**
         *  Signal handler for the mouse clicked on one of the entries.
         *
         *  @param event the button event recieved
         */
        virtual void
        onEntryClicked(GdkEventButton     * event)              throw ();

        /**
         *  Signal handler for the "delete" menu item selected from
         *  the entry context menu.
         */
        virtual void
        onDeleteItem(void)                                      throw ();

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
        SchedulerWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                        Ptr<ResourceBundle>::Ref    bundle)         throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~SchedulerWindow(void)                                      throw ();

        /**
         *  Select a specific timepoint to display.
         *  Call showContents() after this call.
         *
         *  @param time display the schedule around this timepoint.
         *  @see #showContents
         */
        virtual void
        setTime(Ptr<boost::posix_time::ptime>::Ref  time)           throw ();

        /**
         *  Update the display, with regards to the currently selected day.
         */
        virtual void
        showContents(void)                                          throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // SchedulerWindow_h

