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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/SchedulePlaylistWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Core/TimeConversion.h"
#include "SchedulePlaylistWindow.h"


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
SchedulePlaylistWindow :: SchedulePlaylistWindow (
                                    Ptr<GLiveSupport>::Ref      gLiveSupport,
                                    Ptr<ResourceBundle>::Ref    bundle,
                                    Ptr<Playlist>::Ref          playlist)
                                                                    throw ()
                    : LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;
    this->playlist     = playlist;

    try {
        set_title(*getResourceUstring("windowTitle"));
        hourLabel = Gtk::manage(new Gtk::Label(*getResourceUstring(
                                                                "hourLabel")));
        minuteLabel = Gtk::manage(new Gtk::Label(*getResourceUstring(
                                                            "minuteLabel")));
        scheduleButton = Gtk::manage(new Gtk::Button(
                                  *getResourceUstring("scheduleButtonLabel")));
        closeButton = Gtk::manage(new Gtk::Button(
                                    *getResourceUstring("closeButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    playlistLabel = Gtk::manage(new Gtk::Label(*playlist->getTitle()));
    calendar      = Gtk::manage(new Gtk::Calendar());
    hourEntry     = Gtk::manage(new Gtk::Entry());
    minuteEntry   = Gtk::manage(new Gtk::Entry());


    layout = Gtk::manage(new Gtk::Table());

    layout->attach(*playlistLabel,  0, 4, 0, 1);
    layout->attach(*calendar,       0, 4, 1, 2);
    layout->attach(*hourLabel,      0, 1, 2, 3);
    layout->attach(*hourEntry,      1, 2, 2, 3);
    layout->attach(*minuteLabel,    2, 3, 2, 3);
    layout->attach(*minuteEntry,    3, 4, 2, 3);
    layout->attach(*scheduleButton, 2, 4, 3, 4);
    layout->attach(*closeButton   , 2, 4, 4, 5);

    // register the signal handler for the schedule getting clicked.
    scheduleButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &SchedulePlaylistWindow::onScheduleButtonClicked));
    // register the signal handler for the button getting clicked.
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                              &SchedulePlaylistWindow::onCloseButtonClicked));

    add(*layout);

    show_all();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
SchedulePlaylistWindow :: ~SchedulePlaylistWindow (void)              throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the schedule button getting clicked.
 *----------------------------------------------------------------------------*/
void
SchedulePlaylistWindow :: onScheduleButtonClicked (void)              throw ()
{
    // get the date from the calendar
    guint   year;
    guint   month;
    guint   day;

    calendar->get_date(year, month, day);

    // get the hour and minute from the entries
    // and construct an HH:MM:00.00 string from it
    Glib::ustring   timeStr = hourEntry->get_text();
    timeStr += ":";
    timeStr += minuteEntry->get_text();
    timeStr += ":00.00";

    Ptr<posix_time::ptime>::Ref selectedTime;

    try {
        gregorian::date             date(year, month+1, day);
        posix_time::time_duration   time(duration_from_string(timeStr.raw()));

        selectedTime.reset(new posix_time::ptime(date, time));
    } catch (std::exception &e) {
        // most probably duration_from_string failed
        // TODO: notify user
        std::cerr << "date format problem: " << e.what() << std::endl;
        return;
    }

    try {
        gLiveSupport->schedulePlaylist(playlist, selectedTime);
    } catch (XmlRpcException &e) {
        // TODO: notify user
        std::cerr << "scheduling problem: " << e.what() << std::endl;
        return;
    }

    hide();
}

/*------------------------------------------------------------------------------
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
SchedulePlaylistWindow :: onCloseButtonClicked (void)                  throw ()
{
    hide();
}


