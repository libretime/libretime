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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/widgets/src/DateTimeChooserWindow.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/Button.h"

#include "LiveSupport/Widgets/DateTimeChooserWindow.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
DateTimeChooserWindow :: DateTimeChooserWindow(Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
          : WhiteWindow(Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners(),
                        WhiteWindow::hasNoTitle || WhiteWindow::isModal),
            LocalizedObject(bundle)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Label *    hourLabel;
    Gtk::Label *    minuteLabel;
    Button *        cancelButton;
    Button *        okButton;
    
    try {
        set_title(*getResourceUstring("windowTitle"));
        hourLabel       = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("hourLabel")));
        minuteLabel     = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("minuteLabel")));
        cancelButton    = Gtk::manage(wf->createButton(
                                    *getResourceUstring("cancelButtonLabel")));
        okButton        = Gtk::manage(wf->createButton(
                                    *getResourceUstring("okButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    calendar      = Gtk::manage(new Gtk::Calendar());
    hourEntry     = Gtk::manage(wf->createEntryBin());
    minuteEntry   = Gtk::manage(wf->createEntryBin());

    cancelButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &DateTimeChooserWindow::onCancelButtonClicked));
    okButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &DateTimeChooserWindow::onOkButtonClicked));

    Gtk::ButtonBox *    buttonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
    buttonBox->pack_start(*cancelButton);
    buttonBox->pack_start(*okButton);
    
    Gtk::Box *          entryBox = Gtk::manage(new Gtk::HBox());
    entryBox->pack_start(*hourLabel,   Gtk::PACK_SHRINK,        5);
    entryBox->pack_start(*hourEntry,   Gtk::PACK_EXPAND_WIDGET, 5);
    entryBox->pack_start(*minuteLabel, Gtk::PACK_SHRINK,        5);
    entryBox->pack_start(*minuteEntry, Gtk::PACK_EXPAND_WIDGET, 5);
    
    Gtk::Box *          layout = Gtk::manage(new Gtk::VBox());
    layout->pack_start(*calendar,   Gtk::PACK_EXPAND_WIDGET, 5);
    layout->pack_start(*entryBox,   Gtk::PACK_SHRINK,        5);
    layout->pack_start(*buttonBox,  Gtk::PACK_SHRINK,        5);

    set_default_size(200, 300);

    add(*layout);
}


/*------------------------------------------------------------------------------
 *  Event handler for the Cancel button clicked
 *----------------------------------------------------------------------------*/
void
DateTimeChooserWindow :: onCancelButtonClicked(void)                throw ()
{
    chosenDateTime.reset();
    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button clicked.
 *----------------------------------------------------------------------------*/
void
DateTimeChooserWindow :: onOkButtonClicked(void)                    throw ()
{
    std::stringstream   dateTime;
    
    guint   year;
    guint   month;
    guint   day;
    calendar->get_date(year, month, day);
    dateTime << std::setfill('0') << std::setw(4) 
             << year
             << std::setfill('0') << std::setw(2) 
             << month + 1
             << std::setfill('0') << std::setw(2) 
             << day;
    
    Glib::ustring   hour   = hourEntry  ->get_text().substr(0,2);
    Glib::ustring   minute = minuteEntry->get_text().substr(0,2);
    dateTime << "T"
             << std::setfill('0') << std::setw(2) 
             << hour
             << std::setfill('0') << std::setw(2) 
             << minute
             << "00";
    
    chosenDateTime.reset(new ptime(from_iso_string(dateTime.str())));
    
    hide();
}


/*------------------------------------------------------------------------------
 *  Show the window and return the button clicked.
 *----------------------------------------------------------------------------*/
Ptr<const ptime>::Ref
DateTimeChooserWindow :: run(void)                                  throw ()
{
    show_all();
    Gtk::Main::run(*this);
    return chosenDateTime;
}

