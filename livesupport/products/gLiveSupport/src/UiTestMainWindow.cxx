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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/UiTestMainWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include <gtkmm/main.h>

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
UiTestMainWindow :: UiTestMainWindow (void)                         throw ()
{
    // set up the quit button
    quitButton.reset(new Gtk::Button("quit"));
    quitButton->signal_clicked().connect(sigc::mem_fun(*this,
                                      &UiTestMainWindow::onQuitButtonClicked));

    // set up the login button
    loginButton.reset(new Gtk::Button("loginWindow"));
    loginButton->signal_clicked().connect(sigc::mem_fun(*this,
                                      &UiTestMainWindow::onLoginButtonClicked));

    // set up the layout, which is a button box
    layout.reset(new Gtk::VButtonBox());

    // set up the main window, and show everything
    set_border_width(10);
    layout->add(*loginButton);
    layout->add(*quitButton);
    add(*layout);

    // show everything
    loginButton->show();
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
    std::cout << "invoking loginWindow" << std::endl;

    Ptr<LoginWindow>::Ref       loginWindow(new LoginWindow());

    Gtk::Main::run(*loginWindow);

    std::cout << "login: " << *loginWindow->getLogin() << std::endl;
    std::cout << "password: " << *loginWindow->getPassword() << std::endl;
}

