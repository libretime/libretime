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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/UiTestMainWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <unicode/msgfmt.h>
#include <gtkmm/main.h>
#include <gtkmm/messagedialog.h>

#include "LoginWindow.h"
#include "UiTestMainWindow.h"


using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
UiTestMainWindow :: UiTestMainWindow (Ptr<GLiveSupport>::Ref    gLiveSupport,
                                      Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
                        : GtkLocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    // set up the status label
    statusLabel.reset(new Gtk::Label(*getResourceUstring("welcomeMsg")));

    // set up the login button
    loginButton.reset(new Gtk::Button("loginWindow"));
    loginButton->signal_clicked().connect(sigc::mem_fun(*this,
                                      &UiTestMainWindow::onLoginButtonClicked));

    // set up the logout button
    logoutButton.reset(new Gtk::Button("logout"));
    logoutButton->signal_clicked().connect(sigc::mem_fun(*this,
                                    &UiTestMainWindow::onLogoutButtonClicked));

    // set up the quit button
    quitButton.reset(new Gtk::Button("quit"));
    quitButton->signal_clicked().connect(sigc::mem_fun(*this,
                                      &UiTestMainWindow::onQuitButtonClicked));

    // set up the layout, which is a button box
    layout.reset(new Gtk::VButtonBox());

    // set up the main window, and show everything
    set_border_width(10);
    layout->add(*statusLabel);
    layout->add(*loginButton);
    layout->add(*logoutButton);
    layout->add(*quitButton);
    add(*layout);

    // show everything
    statusLabel->show();
    loginButton->show();
    logoutButton->show();
    quitButton->show();
    layout->show();
    show();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
UiTestMainWindow :: ~UiTestMainWindow (void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the logout getting clicked.
 *----------------------------------------------------------------------------*/
void
UiTestMainWindow :: onLogoutButtonClicked (void)                    throw ()
{
    gLiveSupport->logout();
    statusLabel->set_label(*getResourceUstring("welcomeMsg"));
}


/*------------------------------------------------------------------------------
 *  Event handler for the quit getting clicked.
 *----------------------------------------------------------------------------*/
void
UiTestMainWindow :: onQuitButtonClicked (void)                      throw ()
{
    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the login button getting clicked.
 *----------------------------------------------------------------------------*/
void
UiTestMainWindow :: onLoginButtonClicked (void)                     throw ()
{
    Ptr<ResourceBundle>::Ref    loginBundle;
    try {
        loginBundle = getBundle("loginWindow");
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        return;
    }

    Ptr<LoginWindow>::Ref       loginWindow(new LoginWindow(loginBundle));

    Gtk::Main::run(*loginWindow);

    Ptr<const Glib::ustring>::Ref     login    = loginWindow->getLogin();
    Ptr<const Glib::ustring>::Ref     password = loginWindow->getPassword();

    bool loggedIn = gLiveSupport->login(login->raw(), password->raw());

    if (loggedIn) {
        Ptr<UnicodeString>::Ref uLogin = ustringToUnicodeString(login);
        Formattable             arguments[] = { *uLogin };
        Ptr<Glib::ustring>::Ref msg = formatMessageUstring("loggedInMsg",
                                                           arguments, 1);

        statusLabel->set_label(*msg);
    }
}

