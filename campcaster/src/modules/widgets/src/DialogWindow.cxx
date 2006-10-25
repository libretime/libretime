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

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/Button.h"

#include "LiveSupport/Widgets/DialogWindow.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
DialogWindow :: DialogWindow (Ptr<Glib::ustring>::Ref   message,
                              int                       buttonTypes,
                              Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
          : WhiteWindow(Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners(),
                        WhiteWindow::hasNoTitle || WhiteWindow::isModal),
            LocalizedObject(bundle)
{
    Ptr<WidgetFactory>::Ref  widgetFactory = WidgetFactory::getInstance();

    Gtk::Label *        messageLabel = Gtk::manage(new Gtk::Label(*message,
                                                          Gtk::ALIGN_CENTER,
                                                          Gtk::ALIGN_CENTER ));
    messageLabel->set_justify(Gtk::JUSTIFY_CENTER);
    
    Gtk::Box *          messageBox = Gtk::manage(new Gtk::HBox);
    messageBox->pack_start(*messageLabel, true, false, 10);
    
    Gtk::ButtonBox *    buttonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
                                                            
    int     buttonCount = 0;
    try {
        if (buttonTypes & cancelButton) {
            Button *    button = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("cancelButtonLabel") ));
            button->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onCancelButtonClicked));
            buttonBox->pack_start(*button);
            ++buttonCount;
        }

        if (buttonTypes & noButton) {
            Button *    button = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("noButtonLabel") ));
            button->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onNoButtonClicked));
            buttonBox->pack_start(*button);
            ++buttonCount;
        }

        if (buttonTypes & yesButton) {
            Button *    button = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("yesButtonLabel") ));
            button->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onYesButtonClicked));
            buttonBox->pack_start(*button);
            ++buttonCount;
        }

        if (buttonTypes & okButton) {
            Button *    button = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("okButtonLabel") ));
            button->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onOkButtonClicked));
            buttonBox->pack_start(*button);
            ++buttonCount;
        }
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    Gtk::Box *  bottomBox = Gtk::manage(new Gtk::HBox);
    bottomBox->pack_start(*buttonBox, true, true, 10);
    
    Gtk::Box *  layout = Gtk::manage(new Gtk::VBox);
    layout->pack_start(*messageBox, true, false, 5);
    layout->pack_start(*bottomBox, false, false, 0);

    set_default_size(100*buttonCount + 50, 120);
    property_window_position().set_value(Gtk::WIN_POS_CENTER);
    set_skip_taskbar_hint(true);    // do not show in the task bar
    set_type_hint(Gdk::WINDOW_TYPE_HINT_DIALOG);
    set_keep_above(true);

    add(*layout);
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
DialogWindow :: ~DialogWindow (void)                                throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the Cancel button clicked
 *----------------------------------------------------------------------------*/
void
DialogWindow :: onCancelButtonClicked(void)                         throw ()
{
    buttonClicked = cancelButton;
    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the No button clicked.
 *----------------------------------------------------------------------------*/
void
DialogWindow :: onNoButtonClicked(void)                             throw ()
{
    buttonClicked = noButton;
    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Yes button clicked.
 *----------------------------------------------------------------------------*/
void
DialogWindow :: onYesButtonClicked(void)                            throw ()
{
    buttonClicked = yesButton;
    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button clicked.
 *----------------------------------------------------------------------------*/
void
DialogWindow :: onOkButtonClicked(void)                             throw ()
{
    buttonClicked = okButton;
    hide();
}


/*------------------------------------------------------------------------------
 *  Show the window and return the button clicked.
 *----------------------------------------------------------------------------*/
DialogWindow::ButtonType
DialogWindow :: run(void)                                           throw ()
{
    show_all();
    Gtk::Main::run(*this);
    return buttonClicked;
}

