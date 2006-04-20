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
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Widgets/ScrolledNotebook.h"
#include "SchedulerWindow.h"


using namespace boost;
using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/**
 *  The name of the window, used by the keyboard shortcuts (or by the .gtkrc).
 */
const Glib::ustring     windowName = "schedulerWindow";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
SchedulerWindow :: SchedulerWindow (
                            Ptr<GLiveSupport>::Ref      gLiveSupport,
                            Ptr<ResourceBundle>::Ref    bundle,
                            Button *                    windowOpenerButton)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle, 
                      WidgetConstants::schedulerWindowTitleImage,
                      windowOpenerButton)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Box *          scheduleView = constructScheduleView();
    Gtk::Box *          statusView   = constructStatusView();
    
    ScrolledNotebook *  notebook = Gtk::manage(new ScrolledNotebook);
    try {
        set_title(*getResourceUstring("windowTitle"));
        closeButton = Gtk::manage(wf->createButton(*getResourceUstring(
                                                        "closeButtonLabel")));
        notebook->appendPage(*scheduleView, *getResourceUstring(
                                                        "scheduleTab"));
        notebook->appendPage(*statusView, *getResourceUstring(
                                                        "statusTab"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SchedulerWindow::onCloseButtonClicked));
    
    Gtk::ButtonBox *    bottomButtonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
    bottomButtonBox->pack_start(*closeButton);
    
    Gtk::VBox *         bigBox = Gtk::manage(new Gtk::VBox);
    bigBox->pack_start(*notebook,        Gtk::PACK_EXPAND_WIDGET);
    bigBox->pack_start(*bottomButtonBox, Gtk::PACK_SHRINK, 5);
    
    add(*bigBox);
    set_name(windowName);
    set_default_size(330, 400);
    showContents();
    show_all();
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
Gtk::VBox *
SchedulerWindow :: constructScheduleView(void)                      throw ()
{
    calendar  = Gtk::manage(new Gtk::Calendar());
    dateLabel = Gtk::manage(new Gtk::Label());

    // create the tree view for the entries
    entryColumns.reset(new ModelColumns());
    entriesModel = Gtk::ListStore::create(*entryColumns);
    entriesView  = Gtk::manage(new Gtk::TreeView());
    entriesView->set_model(entriesModel);

    // Add the TreeView's view columns:
    try {
        entriesView->append_column(*getResourceUstring("startColumnLabel"),
                               entryColumns->startColumn);
        entriesView->append_column(*getResourceUstring("titleColumnLabel"),
                               entryColumns->titleColumn);
        entriesView->append_column(*getResourceUstring("endColumnLabel"),
                               entryColumns->endColumn);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // register the signal handler for entries view entries being clicked
    entriesView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                            &SchedulerWindow::onEntryClicked));

    // create the right-click entry context menu for audio clips
    entryMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& menuList = entryMenu->items();
    // register the signal handlers for the popup menu
    try {
        menuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("deleteMenuItem"),
                                sigc::mem_fun(*this,
                                            &SchedulerWindow::onDeleteItem)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    entryMenu->accelerate(*this);

    layout = Gtk::manage(new Gtk::Table());

    layout->attach(*calendar,       0, 1, 0, 1);
    layout->attach(*dateLabel,      0, 1, 1, 2);
    layout->attach(*entriesView,    0, 1, 2, 3);

    // register the signal handle for when a date is selected in the calendar
    calendar->signal_day_selected().connect(sigc::mem_fun(*this,
                                    &SchedulerWindow::onDateSelected));

    // initialize the selected date for today
    selectedDate.reset(new gregorian::date(TimeConversion::now()->date()));
    
    // make a new box and pack the main components into it
    Gtk::VBox *         view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*layout, Gtk::PACK_SHRINK);
    
    return view;
}


/*------------------------------------------------------------------------------
 *  Construct the Status view.
 *----------------------------------------------------------------------------*/
Gtk::VBox *
SchedulerWindow :: constructStatusView(void)                        throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Label *    statusTextLabel;
    Button *        startButton;
    Button *        stopButton;
    try {
        statusTextLabel = Gtk::manage(new Gtk::Label(*getResourceUstring(
                                                        "statusText")));
        startButton = Gtk::manage(wf->createButton(*getResourceUstring(
                                                        "startButtonLabel")));
        stopButton = Gtk::manage(wf->createButton(*getResourceUstring(
                                                        "stopButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    startButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SchedulerWindow::onStartButtonClicked));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SchedulerWindow::onStopButtonClicked));
    
    Gtk::HBox *         statusReportBox = Gtk::manage(new Gtk::HBox);
    statusReportBox->pack_start(*statusTextLabel,   Gtk::PACK_SHRINK, 5);
    statusReportLabel = Gtk::manage(new Gtk::Label);
    statusReportBox->pack_start(*statusReportLabel, Gtk::PACK_SHRINK, 5);
    
    Gtk::ButtonBox *    startStopButtons = Gtk::manage(new Gtk::HButtonBox(
                                                    Gtk::BUTTONBOX_SPREAD, 20));
    startStopButtons->pack_start(*startButton);
    startStopButtons->pack_start(*stopButton);

    Gtk::VBox *         view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*statusReportBox,  Gtk::PACK_SHRINK, 20);
    view->pack_start(*startStopButtons, Gtk::PACK_SHRINK);
    
    return view;
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
        // TODO: report error
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
SchedulerWindow :: showContents(void)                               throw ()
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

        row[entryColumns->idColumn]    = entry->getId();
        row[entryColumns->startColumn] =
                                      to_simple_string(*entry->getStartTime());
        row[entryColumns->titleColumn] = *playlist->getTitle();
        row[entryColumns->endColumn]   = to_simple_string(*entry->getEndTime());

        ++it;
    }
    
    updateStatus();
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
                                                entriesView->get_selection();
        if (refSelection) {
            Gtk::TreeModel::iterator iter = refSelection->get_selected();
            if (iter) {
                entryMenu->popup(event->button, event->time);
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Delete menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onDeleteItem(void)                               throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                 entriesView->get_selection();

    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            Ptr<const UniqueId>::Ref entryId = (*iter)[entryColumns->idColumn];

            try {
                gLiveSupport->removeFromSchedule(entryId);
            } catch (XmlRpcException &e) {
                // TODO: signal error here
            }
            showContents();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the Start button getting clicked.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onStartButtonClicked(void)                       throw ()
{
    gLiveSupport->checkSchedulerClient();
    if (!gLiveSupport->isSchedulerAvailable()) {
        gLiveSupport->startSchedulerClient();
    }
    updateStatus();
}


/*------------------------------------------------------------------------------
 *  Signal handler for the Stop button getting clicked.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onStopButtonClicked(void)                        throw ()
{
    gLiveSupport->checkSchedulerClient();
    if (gLiveSupport->isSchedulerAvailable()) {
        gLiveSupport->stopSchedulerClient();
    }
    updateStatus();
}


/*------------------------------------------------------------------------------
 *  Update the status display in the Status tab.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: updateStatus(void)                               throw ()
{
    gLiveSupport->checkSchedulerClient();
    try {
        if (gLiveSupport->isSchedulerAvailable()) {
            statusReportLabel->set_text(*getResourceUstring("runningStatus"));
        } else {
            statusReportLabel->set_text(*getResourceUstring("stoppedStatus"));
        }
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
}

