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

#include "GLiveSupport.h"
#include "LoginWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "loginWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "LoginWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
LoginWindow :: LoginWindow (void)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName),
            loggedIn(false)
{
    // localize everything
    Gtk::Label *    userNameLabel;
    Gtk::Label *    passwordLabel;
    Gtk::Label *    languageLabel;
    glade->get_widget("userNameLabel1", userNameLabel);
    glade->get_widget("passwordLabel1", passwordLabel);
    glade->get_widget("languageLabel1", languageLabel);
    userNameLabel->set_text(*getResourceUstring("userNameLabel"));
    passwordLabel->set_text(*getResourceUstring("passwordLabel"));
    languageLabel->set_text(*getResourceUstring("languageLabel"));

    // fill up the language list with the list of available languages
    glade->get_widget_derived("languageEntry1", languageEntry);

    Ptr<const GLiveSupport::LanguageMap>::Ref
                        languages = gLiveSupport->getSupportedLanguages();
    for (GLiveSupport::LanguageMap::const_iterator
            it = languages->begin(); it != languages->end(); ++it) {
        Glib::ustring   language = it->first;
        languageEntry->append_text(language);
    }
    languageEntry->set_active(0);

    // connect signal handlers
    glade->connect_clicked("okButton1", sigc::mem_fun(*this,
                                        &LoginWindow::onOkButtonClicked));
    glade->connect_clicked("cancelButton1", sigc::mem_fun(*this,
                                        &LoginWindow::onCancelButtonClicked));

    glade->get_widget("userNameEntry1", userNameEntry);
    userNameEntry->signal_activate().connect(sigc::mem_fun(*this,
                                    &LoginWindow::onUserNameEntryActivated));
    glade->get_widget("passwordEntry1", passwordEntry);
    passwordEntry->signal_activate().connect(sigc::mem_fun(*this,
                                    &LoginWindow::onPasswordEntryActivated));
    
    // clear the status bar
    glade->get_widget("statusBar1", statusBar);
    statusBar->set_text("");
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
LoginWindow :: ~LoginWindow (void)                                  throw ()
{
}


/*------------------------------------------------------------------------------
 *  Signal handler for the Enter key pressed in the user name entry.
 *----------------------------------------------------------------------------*/
void
LoginWindow :: onUserNameEntryActivated (void)                      throw ()
{
    passwordEntry->grab_focus();
}


/*------------------------------------------------------------------------------
 *  Signal handler for the Enter key pressed in the password entry.
 *----------------------------------------------------------------------------*/
void
LoginWindow :: onPasswordEntryActivated (void)                      throw ()
{
    onOkButtonClicked();
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button getting clicked.
 *----------------------------------------------------------------------------*/
void
LoginWindow :: onOkButtonClicked (void)                             throw ()
{
    statusBar->set_text(*getResourceUstring("pleaseWaitMsg"));
    mainWindow->set_sensitive(false);
    gLiveSupport->runMainLoop();    // redraw the window
    
    userNameText.reset(new Glib::ustring(userNameEntry->get_text()));
    passwordText.reset(new Glib::ustring(passwordEntry->get_text()));
    
    Ptr<const GLiveSupport::LanguageMap>::Ref   languages;
    languages = gLiveSupport->getSupportedLanguages();

    GLiveSupport::LanguageMap::const_iterator
        langSel = languages->find(languageEntry->get_active_text());
    selectedLocale.reset(new std::string(langSel->second));
    
    loggedIn = gLiveSupport->login(*getLogin(), *getPassword());
    
    if (loggedIn) {
        if (selectedLocale->size() > 0) {
            gLiveSupport->changeLanguage(selectedLocale);
        } else {
            // TODO: get and set default locale for user
        }
        
        gLiveSupport->createScratchpadWindow();
    }
    
    mainWindow->hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the cancel button getting clicked.
 *----------------------------------------------------------------------------*/
void
LoginWindow :: onCancelButtonClicked (void)                         throw ()
{
    mainWindow->hide();
}


/*------------------------------------------------------------------------------
 *  Show the window, and return whether the login was successful.
 *----------------------------------------------------------------------------*/
bool
LoginWindow :: run(void)                                            throw ()
{
    Gtk::Main::run(*mainWindow);
    return loggedIn;
}


