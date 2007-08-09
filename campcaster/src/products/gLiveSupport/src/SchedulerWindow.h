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
#ifndef SchedulerWindow_h
#define SchedulerWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/gregorian/gregorian.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "GuiWindow.h"
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
 *  The window is tabbed, with a main Schedule tab, and a Status tab showing
 *  the status of the scheduler daemon (running/stopped).  In the Status tab,
 *  one can send a Stop signal to the Scheduler, to stop the audio player.
 *  
 *  The rough layout of the Schedule tab:
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
class SchedulerWindow : public GuiWindow
{
    private:

        /**
         *  Construct the Schedule view.
         *  This displays the list of scheduled playlists.
         */
        void
        constructScheduleView(void)                                 throw ();

        /**
         *  Construct the Status view.
         *  This shows the status of the scheduler daemon.
         */
        void
        constructStatusView(void)                                   throw ();
        
        /**
         *  Run the confirmation dialog.
         *
         *  @return the response ID returned by the dialog.
         */
        Gtk::ResponseType
        runConfirmationDialog(void)                                 throw ();


    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one scheduled item per row.
         *
         *  @author $Author$
         *  @version $Revision$
         */
        class ModelColumns : public ZebraTreeModelColumnRecord
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
         *  The date selected for display.
         */
        Ptr<boost::gregorian::date>::Ref        selectedDate;

        /**
         *  The calendar to select a specific date from.
         */
        Gtk::Calendar *             calendar;

        /**
         *  The label saying which day is being displayed.
         */
        Gtk::Label *                dateLabel;

        /**
         *  The column model.
         */
        ModelColumns                entryColumns;

        /**
         *  The tree view, now only showing rows, each scheduled entry for a
         *  specific day.
         */
        ZebraTreeView *             entriesTreeView;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    entriesModel;

        /**
         *  The right-click context menu for schedule entries.
         */
        Ptr<Gtk::Menu>::Ref         entryMenu;

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
         *  Signal handler for the "stop currently playing" button
         *  getting clicked.
         */
        virtual void
        onStopCurrentlyPlayingButtonClicked(void)               throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param windowOpenerButton   the button which was pressed to open
         *                              this window.
         */
        SchedulerWindow(Gtk::ToggleButton *         windowOpenerButton)
                                                    throw (XmlRpcException);

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
        showContents(void)                          throw (XmlRpcException);

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // SchedulerWindow_h

