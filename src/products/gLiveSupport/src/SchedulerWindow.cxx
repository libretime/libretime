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
#include <stdexcept>

#include "LiveSupport/Core/TimeConversion.h"
#include "SchedulerWindow.h"


using namespace boost;
using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "schedulerWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "SchedulerWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
SchedulerWindow :: SchedulerWindow (
                            Gtk::ToggleButton *         windowOpenerButton)
                                                    throw (XmlRpcException)
          : GuiWindow(bundleName,
                      gladeFileName,
                      windowOpenerButton)
{
    constructScheduleView();
    constructStatusView();
    
    Gtk::Label *    scheduleTabLabel;
    Gtk::Label *    statusTabLabel;
    glade->get_widget("scheduleTabLabel1", scheduleTabLabel);
    glade->get_widget("statusTabLabel1", statusTabLabel);
    scheduleTabLabel->set_label(*getResourceUstring("scheduleTab"));
    statusTabLabel->set_label(*getResourceUstring("statusTab"));
    
    glade->connect_clicked("closeButton1", sigc::mem_fun(*this,
                                                    &SchedulerWindow::hide));
    
    showContents();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
SchedulerWindow :: ~SchedulerWindow (void)                          throw ()
{
}


/*------------------------------------------------------------------------------
 *  Construct the Schedule view.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: constructScheduleView(void)                      throw ()
{
    glade->get_widget("calendar1", calendar);
    glade->get_widget("dateLabel1", dateLabel);

    // create the tree view for the entries
    entriesModel = Gtk::ListStore::create(entryColumns);
    glade->get_widget_derived("entriesTreeView1", entriesTreeView);
    entriesTreeView->set_model(entriesModel);
    entriesTreeView->connectModelSignals(entriesModel);

    entriesTreeView->append_column(*getResourceUstring("startColumnLabel"),
                            entryColumns.startColumn);
    entriesTreeView->append_column(*getResourceUstring("titleColumnLabel"),
                            entryColumns.titleColumn);
    entriesTreeView->append_column(*getResourceUstring("endColumnLabel"),
                            entryColumns.endColumn);

    // register the signal handler for entries view entries being clicked
    entriesTreeView->signal_button_press_event().connect_notify(
                            sigc::mem_fun(*this,
                                          &SchedulerWindow::onEntryClicked));

    // create the right-click entry context menu
    entryMenu.reset(new Gtk::Menu());
    Gtk::Menu::MenuList& menuList = entryMenu->items();
    menuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("deleteMenuItem"),
                            sigc::mem_fun(*this,
                                          &SchedulerWindow::onDeleteItem)));
    entryMenu->accelerate(*mainWindow);

    // register the signal handle for when a date is selected in the calendar
    calendar->signal_day_selected().connect(sigc::mem_fun(*this,
                                    &SchedulerWindow::onDateSelected));

    // initialize the selected date for today
    selectedDate.reset(new gregorian::date(TimeConversion::now()->date()));
}


/*------------------------------------------------------------------------------
 *  Construct the Status view.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: constructStatusView(void)                        throw ()
{
    Gtk::Label *    stopCurrentlyPlayingLabel;
    Gtk::Button *   stopCurrentlyPlayingButton;
    glade->get_widget("stopCurrentlyPlayingLabel1", stopCurrentlyPlayingLabel);
    glade->get_widget("stopCurrentlyPlayingButton1",
                                                    stopCurrentlyPlayingButton);
    stopCurrentlyPlayingLabel->set_label(
                    *getResourceUstring("stopCurrentlyPlayingText"));
    stopCurrentlyPlayingButton->set_label(
                    *getResourceUstring("stopCurrentlyPlayingButtonLabel"));
    stopCurrentlyPlayingButton->signal_clicked().connect(sigc::mem_fun(*this,
                    &SchedulerWindow::onStopCurrentlyPlayingButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Event handler for a date being selected on the calendar
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onDateSelected (void)                            throw ()
{
    guint   year;
    guint   month;
    guint   day;

    calendar->get_date(year, month, day);

    try {
        Ptr<gregorian::date>::Ref date(new gregorian::date(year, month+1, day));
        if (*date != *selectedDate) {
            selectedDate = date;
            showContents();
        }
    } catch (std::out_of_range &e) {
        // TODO: report date out of range error
        std::cerr << e.what() << std::endl;
    } catch (XmlRpcException &e) {
        // TODO: report storage server error
        std::cerr << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Move the time to be displayed to the specified time.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: setTime(Ptr<boost::posix_time::ptime>::Ref  time)
                                                                    throw ()
{
    selectedDate.reset(new gregorian::date(time->date()));
}


/*------------------------------------------------------------------------------
 *  Update the contents of the display, with regards to the currently selected
 *  date
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: showContents(void)               throw (XmlRpcException)
{
    calendar->select_month(selectedDate->month() - 1, selectedDate->year());
    calendar->select_day(selectedDate->day());

    dateLabel->set_text(to_simple_string(*selectedDate));

    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
    std::vector<Ptr<ScheduleEntry>::Ref>::iterator      it;
    std::vector<Ptr<ScheduleEntry>::Ref>::iterator      end;
    Ptr<posix_time::ptime>::Ref                         from;
    Ptr<posix_time::ptime>::Ref                         to;
    Ptr<posix_time::time_duration>::Ref                 midnight;

    // we're interested from midnight, selectedDate, to midnight, the next day
    midnight.reset(new posix_time::time_duration(0, 0, 0, 0));
    from.reset(new posix_time::ptime(*selectedDate, *midnight));
    to.reset(new posix_time::ptime(*selectedDate + gregorian::date_duration(1), 
                                   *midnight));

    entries = gLiveSupport->displaySchedule(from, to);
          
    it      = entries->begin();
    end     = entries->end();
    entriesModel->clear();
    while (it != end) {
        Ptr<Playlist>::Ref      playlist;
        Ptr<ScheduleEntry>::Ref entry = *it;
        Ptr<UniqueId>::Ref      playlistId(new UniqueId(
                                             entry->getPlaylistId()->getId()));

        if (!gLiveSupport->existsPlaylist(playlistId)) {
            ++it;
            continue;
        }

        playlist = gLiveSupport->getPlaylist(playlistId);

        Gtk::TreeModel::Row         row   = *(entriesModel->append());

        row[entryColumns.idColumn]    = entry->getId();
        row[entryColumns.startColumn] =
                                      to_simple_string(*entry->getStartTime());
        row[entryColumns.titleColumn] = *playlist->getTitle();
        row[entryColumns.endColumn]   = to_simple_string(*entry->getEndTime());

        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onEntryClicked (GdkEventButton * event)          throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        // only show the context menu, if something is already selected
        Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                entriesTreeView->get_selection();
        if (refSelection) {
            Gtk::TreeModel::iterator iter = refSelection->get_selected();
            if (iter) {
                entryMenu->popup(event->button, event->time);
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Delete menu item selected from the entry context menu
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onDeleteItem(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                 entriesTreeView->get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<const UniqueId>::Ref entryId = (*iter)[entryColumns.idColumn];

            try {
                gLiveSupport->removeFromSchedule(entryId);
            } catch (XmlRpcException &e) {
                // TODO: signal error here
            }
            
            try {
                showContents();
            } catch (XmlRpcException &e) {
                // TODO: signal error here
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the "stop currently playing" button getting clicked.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onStopCurrentlyPlayingButtonClicked(void)        throw ()
{
    Gtk::ResponseType       result = runConfirmationDialog();
    switch (result) {
        case Gtk::RESPONSE_YES:
                        break;

        case Gtk::RESPONSE_NO:
                        return;
                        break;

        default :                   // can happen if the window
                        return;     // is closed with Alt-F4
                        break;      // -- treated as No
    }

    Ptr<SessionId>::Ref     sessionId = gLiveSupport->getSessionId();
    Ptr<SchedulerClientInterface>::Ref
                            scheduler = gLiveSupport->getScheduler();
    
    try {
        scheduler->stopCurrentlyPlaying(sessionId);
        
    } catch (XmlRpcException &e) {
        gLiveSupport->displayMessageWindow(e.what());
    }
    
    showContents();
}


/*------------------------------------------------------------------------------
 *  Run the confirmation window.
 *----------------------------------------------------------------------------*/
Gtk::ResponseType
SchedulerWindow :: runConfirmationDialog(void)                      throw ()
{
    Gtk::Dialog *       confirmationDialog;
    Gtk::Label *        confirmationDialogLabel;
    glade->get_widget("confirmationDialog1", confirmationDialog);
    glade->get_widget("confirmationDialogLabel1", confirmationDialogLabel);
    
    Glib::ustring       message = "<span weight=\"bold\" ";
    message += " size=\"larger\">";
    message += *getResourceUstring("stopCurrentlyPlayingDialogMsg");
    message += "</span>";
    confirmationDialogLabel->set_label(message);

    Gtk::ResponseType   response = Gtk::ResponseType(
                                            confirmationDialog->run());
    confirmationDialog->hide();
    return response;
}

