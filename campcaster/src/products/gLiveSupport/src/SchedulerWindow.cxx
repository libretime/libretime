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
                                                    throw (XmlRpcException)
          : GuiWindow(gLiveSupport,
                      bundle, 
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
    
    show_all_children();
    
    // set up the dialog window
    Ptr<Glib::ustring>::Ref     confirmationMessage;
    try {
        confirmationMessage.reset(new Glib::ustring(
                        *getResourceUstring("stopCurrentlyPlayingDialogMsg") ));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    dialogWindow.reset(new DialogWindow(confirmationMessage,
                                        DialogWindow::noButton |
                                        DialogWindow::yesButton,
                                        gLiveSupport->getBundle() ));
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
    
    Gtk::Label *    stopCurrentlyPlayingLabel;
    Button *        stopCurrentlyPlayingButton;
    try {
        stopCurrentlyPlayingLabel = Gtk::manage(new Gtk::Label(
                    *getResourceUstring("stopCurrentlyPlayingText")));
        stopCurrentlyPlayingButton = Gtk::manage(wf->createButton(
                    *getResourceUstring("stopCurrentlyPlayingButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    stopCurrentlyPlayingButton->signal_clicked().connect(sigc::mem_fun(
                    *this,
                    &SchedulerWindow::onStopCurrentlyPlayingButtonClicked));
    
    Gtk::HBox *     stopCurrentlyPlayingBox = Gtk::manage(new Gtk::HBox);
    stopCurrentlyPlayingBox->pack_start(*stopCurrentlyPlayingLabel,
                                        Gtk::PACK_SHRINK, 5);
    stopCurrentlyPlayingBox->pack_start(*stopCurrentlyPlayingButton, 
                                        Gtk::PACK_SHRINK, 5);
    
    Gtk::VBox *     view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*stopCurrentlyPlayingBox, Gtk::PACK_SHRINK, 20);
    
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

        row[entryColumns->idColumn]    = entry->getId();
        row[entryColumns->startColumn] =
                                      to_simple_string(*entry->getStartTime());
        row[entryColumns->titleColumn] = *playlist->getTitle();
        row[entryColumns->endColumn]   = to_simple_string(*entry->getEndTime());

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
 *  Event handler for the Delete menu item selected from the entry context menu
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
    DialogWindow::ButtonType    result = dialogWindow->run();
    switch (result) {
        case DialogWindow::yesButton:       break;

        case DialogWindow::noButton:        return;
                                            break;

        // can happen if the window is closed with Alt-F4 -- treated as No
        default :                           return;
                                            break;
    }

    Ptr<SessionId>::Ref     sessionId = gLiveSupport->getSessionId();
    Ptr<SchedulerClientInterface>::Ref
                            scheduler = gLiveSupport->getScheduler();
    
    try {
        scheduler->stopCurrentlyPlaying(sessionId);
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     errorMessage(new Glib::ustring(e.what()));
        gLiveSupport->displayMessageWindow(errorMessage);
    }
}

