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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/LoginWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LoginWindow.h"


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
LoginWindow :: LoginWindow (Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
                    : GtkLocalizedObject(bundle)
{
    try {
        table.reset(new Gtk::Table(2, 2, false));
        loginLabel.reset(new Gtk::Label(*getResourceUstring("loginLabel")));
        passwordLabel.reset(new Gtk::Label(
                                        *getResourceUstring("passwordLabel")));
        loginEntry.reset(new Gtk::Entry());
        passwordEntry.reset(new Gtk::Entry());
        okButton.reset(new Gtk::Button(*getResourceUstring("okButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    // set up the login label
    loginLabel->set_name("loginLabel");
    loginLabel->set_alignment(0, 0.5);
    loginLabel->set_padding(0, 0);
    loginLabel->set_justify(Gtk::JUSTIFY_RIGHT);
    loginLabel->set_line_wrap(false);
    loginLabel->set_use_markup(false);
    loginLabel->set_selectable(false);

    // set up the password label
    passwordLabel->set_name("passwordLabel");
    passwordLabel->set_alignment(0, 0.5);
    passwordLabel->set_padding(0, 0);
    passwordLabel->set_justify(Gtk::JUSTIFY_RIGHT);
    passwordLabel->set_line_wrap(false);
    passwordLabel->set_use_markup(false);
    passwordLabel->set_selectable(false);

    // set up the login text entry area
    loginEntry->set_name("loginEntry");
    loginEntry->set_flags(Gtk::CAN_FOCUS|Gtk::HAS_FOCUS);
    loginEntry->set_visibility(true);
    loginEntry->set_editable(true);
    loginEntry->set_max_length(0);
    loginEntry->set_text("");
    loginEntry->set_has_frame(true);
    loginEntry->set_activates_default(true);

    // set up the password text entry area
    passwordEntry->set_name("passwordEntry");
    passwordEntry->set_flags(Gtk::CAN_FOCUS);
    passwordEntry->set_visibility(false);
    passwordEntry->set_editable(true);
    passwordEntry->set_max_length(0);
    passwordEntry->set_text("");
    passwordEntry->set_has_frame(true);
    passwordEntry->set_activates_default(true);

    // set up the OK button
    okButton->set_name("okButton");
    okButton->set_flags(Gtk::CAN_FOCUS|Gtk::CAN_DEFAULT|Gtk::HAS_DEFAULT);
    okButton->set_relief(Gtk::RELIEF_NORMAL);
    // Register the signal handler for the button getting clicked.
    okButton->signal_clicked().connect(sigc::mem_fun(*this,
                                            &LoginWindow::onOkButtonClicked));

    // set up the table, which provides the layout, and place the widgets
    // inside the table
    table->set_name("table");
    table->set_row_spacings(0);
    table->set_col_spacings(0);
    table->attach(*loginLabel,
                  0, 1, 0, 1,
                  Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*passwordLabel,
                  0, 1, 1, 2,
                  Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*loginEntry,
                  1, 2, 0, 1,
                  Gtk::EXPAND|Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*passwordEntry,
                  1, 2, 1, 2,
                  Gtk::EXPAND|Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*okButton,
                  1, 2, 2, 3,
                  Gtk::FILL, Gtk::AttachOptions(), 0, 0);

    // set up the window itself
    set_name("loginWindow");
    set_title("LiveSupport login");
    set_modal(true);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    set_resizable(false);
    property_destroy_with_parent().set_value(false);
    set_default(*okButton);

    // add the table to the window, and show everything
    add(*table);
    loginLabel->show();
    passwordLabel->show();
    loginEntry->show();
    passwordEntry->show();
    okButton->show();
    table->show();
    show();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
LoginWindow :: ~LoginWindow (void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the button getting clicked.
 *----------------------------------------------------------------------------*/
void
LoginWindow :: onOkButtonClicked (void)                  throw ()
{
    loginText.reset(new std::string(loginEntry->get_text()));
    passwordText.reset(new std::string(passwordEntry->get_text()));

    hide();
}


