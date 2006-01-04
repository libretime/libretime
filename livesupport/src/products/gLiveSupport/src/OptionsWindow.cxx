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

#include <gtkmm.h>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ScrolledNotebook.h"
#include "OptionsWindow.h"


using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the window, used by the keyboard shortcuts (or by the .gtkrc).
 */
static const Glib::ustring  windowName = "optionsWindow";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
OptionsWindow :: OptionsWindow (Ptr<GLiveSupport>::Ref    gLiveSupport,
                                Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
          : WhiteWindow("",
                        Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    isChanged = false;

    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    try {
        set_title(*getResourceUstring("windowTitle"));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // build up the notepad for the various sections
    mainNotebook = Gtk::manage(new ScrolledNotebook);
    Gtk::Box *      aboutSectionBox = constructAboutSection();

    try {
        mainNotebook->appendPage(*aboutSectionBox,
                                *getResourceUstring("aboutSectionLabel"));

    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // build up the button box
    buttonBox = Gtk::manage(new Gtk::HButtonBox);
    buttonBox->set_layout(Gtk::BUTTONBOX_END);
    buttonBox->set_spacing(5);

    try {
        cancelButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("cancelButtonLabel") ));
        applyButton  = Gtk::manage(wf->createButton(
                                *getResourceUstring("applyButtonLabel") ));
        okButton     = Gtk::manage(wf->createButton(
                                *getResourceUstring("okButtonLabel") ));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    buttonBox->pack_start(*cancelButton);
    buttonBox->pack_start(*applyButton);
    buttonBox->pack_start(*okButton);

    // set up the main window
    Gtk::Box *      layout = Gtk::manage(new Gtk::VBox);
    layout->pack_start(*mainNotebook, Gtk::PACK_EXPAND_WIDGET, 5);
    layout->pack_start(*buttonBox,  Gtk::PACK_SHRINK, 5);
    add(*layout);

    // bind events
    cancelButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &OptionsWindow::onCancelButtonClicked));
    applyButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &OptionsWindow::onApplyButtonClicked));
    okButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &OptionsWindow::onOkButtonClicked));

    // show everything
    set_name(windowName);
    set_default_size(350, 250);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    
    show_all();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Cancel button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onCancelButtonClicked(void)                        throw ()
{
    onCloseButtonClicked();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Apply button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onApplyButtonClicked(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onOkButtonClicked(void)                            throw ()
{
    onCloseButtonClicked();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Close button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onCloseButtonClicked(void)                         throw ()
{
    gLiveSupport->putWindowPosition(shared_from_this());
    hide();
}


/*------------------------------------------------------------------------------
 *  Construct the "About" section.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
OptionsWindow :: constructAboutSection(void)                        throw ()
{
    Glib::ustring   aboutLabelContents;
    aboutLabelContents.append(PACKAGE_NAME);
    aboutLabelContents.append(" ");
    aboutLabelContents.append(PACKAGE_VERSION);
    aboutLabelContents.append("\n\n");
    try {
        aboutLabelContents.append(*formatMessage("reportBugsToText",
                                                 PACKAGE_BUGREPORT ));
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    Gtk::Label *    aboutLabel = Gtk::manage(
                                    new Gtk::Label(aboutLabelContents) );

    // make a new box and pack the components into it
    Gtk::VBox *     section = Gtk::manage(new Gtk::VBox);
    section->pack_start(*aboutLabel, Gtk::PACK_SHRINK, 5);
    
    return section;
}

