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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/SchedulerWindow.cxx,v $

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


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
SchedulerWindow :: SchedulerWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                                    Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
                    : LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    try {
        set_title(*getResourceUstring("windowTitle"));
        closeButton.reset(new Gtk::Button(
                                    *getResourceUstring("closeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    calendar.reset(new Gtk::Calendar());
    dateLabel.reset(new Gtk::Label());

    // create the tree view for the entries
    entryColumns.reset(new ModelColumns());
    entriesModel = Gtk::ListStore::create(*entryColumns);
    entriesView.reset(new Gtk::TreeView());
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
    }


    layout.reset(new Gtk::Table());

    layout->attach(*calendar,       0, 1, 0, 1);
    layout->attach(*dateLabel,      0, 1, 1, 2);
    layout->attach(*entriesView,    0, 1, 2, 3);

    // register the signal handler for the button getting clicked.
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &SchedulerWindow::onCloseButtonClicked));
    // register the signal handle for when a date is selected in the calendar
    calendar->signal_day_selected().connect(sigc::mem_fun(*this,
                                    &SchedulerWindow::onDateSelected));

    // initialize the selected date for today
    selectedDate.reset(new gregorian::date(TimeConversion::now()->date()));

    add(*layout);

    show_all();

    showContents();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
SchedulerWindow :: ~SchedulerWindow (void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for a date being selected on the calendar
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onDateSelected (void)                        throw ()
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
SchedulerWindow :: showContents(void)                           throw ()
{
    calendar->select_month(selectedDate->month() - 1, selectedDate->year());
    calendar->select_day(selectedDate->day());

    dateLabel->set_text(to_simple_string(*selectedDate));

    Ptr<StorageClientInterface>::Ref                    storage;
    Ptr<SessionId>::Ref                                 sessionId;
    Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref     entries;
    std::vector<Ptr<ScheduleEntry>::Ref>::iterator      it;
    std::vector<Ptr<ScheduleEntry>::Ref>::iterator      end;
    Ptr<posix_time::ptime>::Ref                         from;
    Ptr<posix_time::ptime>::Ref                         to;
    Ptr<posix_time::time_duration>::Ref                 midnight;

    storage   = gLiveSupport->getStorage();
    sessionId = gLiveSupport->getSessionId();

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

        if (!storage->existsPlaylist(sessionId, playlistId)) {
            ++it;
            continue;
        }

        playlist = storage->getPlaylist(sessionId, playlistId);

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
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
SchedulerWindow :: onCloseButtonClicked (void)                  throw ()
{
    hide();
}


