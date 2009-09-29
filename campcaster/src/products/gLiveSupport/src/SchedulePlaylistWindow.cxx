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

#include <boost/date_time/gregorian/gregorian.hpp>
#include "boost/date_time/posix_time/posix_time.hpp"

#include "LiveSupport/Core/TimeConversion.h"

#include "SchedulePlaylistWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "schedulePlaylistWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "SchedulePlaylistWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
SchedulePlaylistWindow :: SchedulePlaylistWindow (Ptr<Playlist>::Ref  playlist)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName),
            playlist(playlist)
{
    Gtk::Label *        playlistLabel;
    glade->get_widget("playlistLabel1", playlistLabel);
    playlistLabel->set_label(*playlist->getTitle());

    Gtk::Label *        hourLabel;
    Gtk::Label *        minuteLabel;
    Gtk::Label *        secondLabel;
    glade->get_widget("hourLabel1", hourLabel);
    glade->get_widget("minuteLabel1", minuteLabel);
    glade->get_widget("secondLabel1", secondLabel);
    hourLabel->set_label(*getResourceUstring("hourLabel"));
    minuteLabel->set_label(*getResourceUstring("minuteLabel"));
    secondLabel->set_label(*getResourceUstring("secondLabel"));

    glade->get_widget("calendar1", calendar);

    glade->get_widget("hourSpinButton1", hourEntry);
    glade->get_widget("minuteSpinButton1", minuteEntry);
    glade->get_widget("secondSpinButton1", secondEntry);
    Ptr<boost::posix_time::ptime>::Ref      now = TimeConversion::now();
    boost::posix_time::time_duration        time = now->time_of_day();
    hourEntry->set_value(time.hours());
    minuteEntry->set_value(time.minutes() + 1);
    secondEntry->set_value(0);

    Gtk::Button *       scheduleButton;
    glade->get_widget("scheduleButton1", scheduleButton);
    scheduleButton->set_label(*getResourceUstring("scheduleButtonLabel"));
    scheduleButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &SchedulePlaylistWindow::onScheduleButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Event handler for the schedule button getting clicked.
 *----------------------------------------------------------------------------*/
void
SchedulePlaylistWindow :: onScheduleButtonClicked (void)              throw ()
{
    unsigned int    year;
    unsigned int    month;
    unsigned int    day;
    calendar->get_date(year, month, day);
    ++month;    // Gtk+ months are 0-based, Boost months are 1-based

    int             hours = hourEntry->get_value_as_int();
    int             minutes = minuteEntry->get_value_as_int();
    int             seconds = secondEntry->get_value_as_int();

    Ptr<boost::posix_time::ptime>::Ref  dateTime(new boost::posix_time::ptime(
                boost::gregorian::date(year, month, day),
                boost::posix_time::time_duration(hours, minutes, seconds) ));

    try {
        gLiveSupport->schedulePlaylist(playlist, dateTime);
    } catch (XmlRpcException &e) {
        // TODO: notify user
        std::cerr << "scheduling problem: " << e.what() << std::endl;
        return;
    }

    mainWindow->hide();
}

