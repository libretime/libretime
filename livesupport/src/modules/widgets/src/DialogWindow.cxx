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

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Colors.h"

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
          : WhiteWindow("",
                        Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners(),
                        WhiteWindow::isModal | WhiteWindow::isBornHidden),
            LocalizedObject(bundle)
{
    Ptr<WidgetFactory>::Ref  widgetFactory = WidgetFactory::getInstance();

    layout = Gtk::manage(new Gtk::VBox());

    messageLabel = Gtk::manage(new Gtk::Label(*message));
    layout->pack_start(*messageLabel, true, true);

    Gtk::ButtonBox *    buttonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
    layout->pack_start(*buttonBox, Gtk::PACK_SHRINK, 0);
    
    int     buttonCount = 0;
    try {
        if (buttonTypes & cancelButton) {
            cancelDialogButton = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("cancelButtonLabel") ));
            cancelDialogButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onCancelButtonClicked));
            buttonBox->pack_start(*cancelDialogButton);
            ++buttonCount;
        }

        if (buttonTypes & noButton) {
            noDialogButton = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("noButtonLabel") ));
            noDialogButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onNoButtonClicked));
            buttonBox->pack_start(*noDialogButton);
            ++buttonCount;
        }

        if (buttonTypes & yesButton) {
            yesDialogButton = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("yesButtonLabel") ));
            yesDialogButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onYesButtonClicked));
            buttonBox->pack_start(*yesDialogButton);
            ++buttonCount;
        }

        if (buttonTypes & okButton) {
            okDialogButton = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("okButtonLabel") ));
            okDialogButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &DialogWindow::onOkButtonClicked));
            buttonBox->pack_start(*okDialogButton);
            ++buttonCount;
        }
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    set_default_size(100*buttonCount + 50, 120);
    property_window_position().set_value(Gtk::WIN_POS_CENTER);

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

