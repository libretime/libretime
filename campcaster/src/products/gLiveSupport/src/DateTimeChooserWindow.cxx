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

#include "DateTimeChooserWindow.h"


using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "dateTimeChooserWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "DateTimeChooserWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
DateTimeChooserWindow :: DateTimeChooserWindow (void)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName)
{
    Gtk::Label *    hourLabel;
    Gtk::Label *    minuteLabel;
    glade->get_widget("hourLabel1", hourLabel);
    glade->get_widget("minuteLabel1", minuteLabel);
    hourLabel->set_label(*getResourceUstring("hourLabel"));
    minuteLabel->set_label(*getResourceUstring("minuteLabel"));
    
    glade->get_widget("calendar1", calendar);
    glade->get_widget("hourSpinButton1", hourEntry);
    glade->get_widget("minuteSpinButton1", minuteEntry);
    
    glade->get_widget("okButton1", okButton);
    okButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &DateTimeChooserWindow::onOkButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button clicked.
 *----------------------------------------------------------------------------*/
void
DateTimeChooserWindow :: onOkButtonClicked(void)                    throw ()
{
    unsigned int    year;
    unsigned int    month;
    unsigned int    day;
    calendar->get_date(year, month, day);
    ++month;    // Gtk+ months are 0-based, Boost months are 1-based

    int             hours = hourEntry->get_value_as_int();
    int             minutes = minuteEntry->get_value_as_int();

    chosenDateTime.reset(new boost::posix_time::ptime(
                boost::gregorian::date(year, month, day),
                boost::posix_time::time_duration(hours, minutes, 0) ));

    mainWindow->hide();
}


/*------------------------------------------------------------------------------
 *  Show the window and return the button clicked.
 *----------------------------------------------------------------------------*/
Ptr<const ptime>::Ref
DateTimeChooserWindow :: run(void)                                  throw ()
{
    chosenDateTime.reset();
    Gtk::Main::run(*mainWindow);
    return chosenDateTime;
}

