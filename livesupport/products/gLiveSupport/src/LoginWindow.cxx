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
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/LoginWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LoginWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
LoginWindow :: LoginWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                            Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
          : WhiteWindow("",
                        0xffffff,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    set_default_size(350, 265);

    try {
        set_title(*getResourceUstring("windowTitle"));
        loginLabel = Gtk::manage(
                            new Gtk::Label(*getResourceUstring("loginLabel")));
        passwordLabel = Gtk::manage(
                        new Gtk::Label( *getResourceUstring("passwordLabel")));
        loginEntryBin    = Gtk::manage(widgetFactory->createEntryBin());
        loginEntry       = loginEntryBin->getEntry();
        passwordEntryBin = Gtk::manage(widgetFactory->createEntryBin());
        passwordEntry    = passwordEntryBin->getEntry();
        languageList     = Gtk::manage(widgetFactory->createComboBoxText());
        okButton = Gtk::manage(widgetFactory->createButton(
                                        *getResourceUstring("okButtonLabel")));
        cancelButton = Gtk::manage(widgetFactory->createButton(
                                    *getResourceUstring("cancelButtonLabel")));
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
    loginEntry->set_visibility(true);
    loginEntry->set_activates_default(true);

    // set up the password text entry area
    passwordEntry->set_visibility(false);
    passwordEntry->set_activates_default(true);

    // set up the drop down list for available languages
    languageList->set_name("languageList");
    languageList->set_flags(Gtk::CAN_FOCUS);

    // fill up the language list with the list of available languages
    Ptr<const GLiveSupport::LanguageMap>::Ref   languages;
    languages = gLiveSupport->getSupportedLanguages();
    GLiveSupport::LanguageMap::const_iterator  lang = languages->begin();
    GLiveSupport::LanguageMap::const_iterator  end  = languages->end();

    // insert the inital, 'default' language
    languageList->set_active_text("");
    selectedLocale.reset(new std::string(""));

    while (lang != end) {
        const Glib::ustring   & language = (*lang).first;
        languageList->append_text(language);

        lang++;
    }

    // set up the OK button
    okButton->set_name("okButton");
    okButton->set_flags(Gtk::CAN_FOCUS|Gtk::CAN_DEFAULT|Gtk::HAS_DEFAULT);
    okButton->set_relief(Gtk::RELIEF_NORMAL);
    // Register the signal handler for the button getting clicked.
    okButton->signal_clicked().connect(sigc::mem_fun(*this,
                                            &LoginWindow::onOkButtonClicked));

    // set up the Cancel button
    cancelButton->set_name("cancelButton");
    cancelButton->set_flags(Gtk::CAN_FOCUS);
    cancelButton->set_relief(Gtk::RELIEF_NORMAL);
    // Register the signal handler for the button getting clicked.
    cancelButton->signal_clicked().connect(sigc::mem_fun(*this,
                                         &LoginWindow::onCancelButtonClicked));

    // set up the box for the buttons
    buttonBox = Gtk::manage(new Gtk::HButtonBox());
    buttonBox->set_layout(Gtk::BUTTONBOX_END);
    buttonBox->set_spacing(5);
    buttonBox->add(*cancelButton);
    buttonBox->add(*okButton);
    
    // set up the table, which provides the layout, and place the widgets
    // inside the table
    table = Gtk::manage(new Gtk::Table(2, 2, false));
    table->set_name("table");
    table->set_row_spacings(0);
    table->set_col_spacings(0);
    table->attach(*loginLabel,
                  0, 1, 0, 1,
                  Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*loginEntryBin,
                  0, 1, 1, 2,
                  Gtk::EXPAND|Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*passwordLabel,
                  0, 1, 2, 3,
                  Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*passwordEntryBin,
                  0, 1, 3, 4,
                  Gtk::EXPAND|Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*languageList,
                  0, 1, 4, 5,
                  Gtk::EXPAND|Gtk::FILL, Gtk::AttachOptions(), 0, 0);
    table->attach(*buttonBox,
                  0, 1, 5, 6,
                  Gtk::FILL, Gtk::AttachOptions(), 0, 0);

    // set up the window itself
    set_name("loginWindow");
    set_modal(true);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    set_resizable(false);
    property_destroy_with_parent().set_value(false);
    set_default(*okButton);

    // add the table to the window, and show everything
    add(*table);
    show_all();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
LoginWindow :: ~LoginWindow (void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button getting clicked.
 *----------------------------------------------------------------------------*/
void
LoginWindow :: onOkButtonClicked (void)                  throw ()
{
    loginText.reset(new Glib::ustring(loginEntry->get_text()));
    passwordText.reset(new Glib::ustring(passwordEntry->get_text()));
    
    Ptr<const GLiveSupport::LanguageMap>::Ref   languages;
    languages = gLiveSupport->getSupportedLanguages();

    GLiveSupport::LanguageMap::const_iterator  end     = languages->end();
    GLiveSupport::LanguageMap::const_iterator  langSel =
                            languages->find(languageList->get_active_text());
    if (langSel != end) {
        selectedLocale.reset(new std::string((*langSel).second));
    } else {
        selectedLocale.reset(new std::string(""));
    }

    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the cancel button getting clicked.
 *----------------------------------------------------------------------------*/
void
LoginWindow :: onCancelButtonClicked (void)                  throw ()
{
    hide();
}

