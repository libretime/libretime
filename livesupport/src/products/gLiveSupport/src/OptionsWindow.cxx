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
#include "LiveSupport/Widgets/EntryBin.h"
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
    Gtk::Box *      soundSectionBox     = constructSoundSection();
    Gtk::Box *      serversSectionBox   = constructServersSection();
    Gtk::Box *      aboutSectionBox     = constructAboutSection();

    try {
        mainNotebook->appendPage(*soundSectionBox,
                                 *getResourceUstring("soundSectionLabel"));
        mainNotebook->appendPage(*serversSectionBox,
                                 *getResourceUstring("serversSectionLabel"));
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
    set_default_size(500, 400);
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
    resetEntries();
    onCloseButtonClicked(false);
}


/*------------------------------------------------------------------------------
 *  Event handler for the Apply button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onApplyButtonClicked(void)                         throw ()
{
    Ptr<OptionsContainer>::Ref
            optionsContainer  = gLiveSupport->getOptionsContainer();

    bool                                changed = false;
    StringEntryListType::const_iterator it;
    for (it = stringEntryList.begin(); it != stringEntryList.end(); ++it) {
    
        OptionsContainer::OptionItemString  optionItem = it->first;
        EntryBin *                          entry      = it->second;
        
        Ptr<const Glib::ustring>::Ref
            oldValue = optionsContainer->getOptionItem(optionItem);
        Ptr<const Glib::ustring>::Ref
            newValue(new Glib::ustring(entry->get_text()));

        if (*oldValue != *newValue) {
            try {
                optionsContainer->setOptionItem(optionItem, newValue);
                changed = true;
            } catch (std::invalid_argument &e) {
                Ptr<Glib::ustring>::Ref
                        errorMessage(new Glib::ustring(
                                    *getResourceUstring("errorMsg") ));
                errorMessage->append(e.what());
                gLiveSupport->displayMessageWindow(errorMessage);
            }
        }
    }
    
    if (changed) {
        try {
            Ptr<Glib::ustring>::Ref
                    restartMessage(new Glib::ustring(
                                *getResourceUstring("needToRestartMsg") ));
            gLiveSupport->displayMessageWindow(restartMessage);
        } catch (std::invalid_argument &e) {
            // TODO: signal error
            std::cerr << e.what() << std::endl;
            std::exit(1);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onOkButtonClicked(void)                            throw ()
{
    onApplyButtonClicked();
    onCloseButtonClicked(false);
}


/*------------------------------------------------------------------------------
 *  Event handler for the Close button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onCloseButtonClicked(bool     needConfirm)         throw ()
{
    if (needConfirm) {
        // TODO: add confirmation dialog
        // and either save changes or cancel them
    }
    gLiveSupport->putWindowPosition(shared_from_this());
    hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the test button
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onTestButtonClicked(const EntryBin *    entry)
                                                                    throw ()
{
    Ptr<OptionsContainer>::Ref  optionsContainer
                                   = gLiveSupport->getOptionsContainer();

    Ptr<const Glib::ustring>::Ref
        oldDevice = optionsContainer->getOptionItem(OptionsContainer::
                                                        outputPlayerDeviceName);
    Ptr<const Glib::ustring>::Ref
        newDevice(new Glib::ustring(entry->get_text()));
    
    gLiveSupport->setCueAudioDevice(newDevice);     // NOTE: we can't use the
    gLiveSupport->playTestSoundOnCue();             // output player b/c that
    gLiveSupport->setCueAudioDevice(oldDevice);     // would trigger onStop()
}


/*------------------------------------------------------------------------------
 *  Create a new user entry field item.
 *----------------------------------------------------------------------------*/
EntryBin *
OptionsWindow :: createEntry(OptionsContainer::OptionItemString  optionItem)
                                                                    throw ()
{
    Ptr<OptionsContainer>::Ref  optionsContainer
                                   = gLiveSupport->getOptionsContainer();
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    EntryBin *  entry = Gtk::manage(wf->createEntryBin());

    try {
        entry->set_text(*optionsContainer->getOptionItem(optionItem));

    } catch (std::invalid_argument &e) {
        // TODO: signal error?
        entry->set_text("");
    }
    
    stringEntryList.push_back(std::make_pair(optionItem, entry));

    return entry;
}


/*------------------------------------------------------------------------------
 *  Construct the "Sound" section.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
OptionsWindow :: constructSoundSection(void)                        throw ()
{
    Ptr<OptionsContainer>::Ref  optionsContainer
                                   = gLiveSupport->getOptionsContainer();
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Table *    audioDeviceTable = Gtk::manage(new Gtk::Table);
    audioDeviceTable->set_row_spacings(10);
    audioDeviceTable->set_col_spacings(5);
    
    // display the settings for the cue player device
    Glib::ustring   cuePlayerLabelContents;
    try {
        cuePlayerLabelContents.append(*getResourceUstring("cueDeviceLabel"));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    Gtk::Label *    cuePlayerLabel = Gtk::manage(
                                    new Gtk::Label(cuePlayerLabelContents) );
    audioDeviceTable->attach(*cuePlayerLabel, 
                                    0, 1, 0, 1, Gtk::SHRINK, Gtk::SHRINK, 5, 0);
    
    EntryBin *      cuePlayerEntry = createEntry(
                                    OptionsContainer::cuePlayerDeviceName);
    audioDeviceTable->attach(*cuePlayerEntry, 1, 2, 0, 1);
    
    Button *        cueTestButton;
    try {
        cueTestButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("testButtonLabel") ));
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    cueTestButton->signal_clicked().connect(sigc::bind<EntryBin*>(
                sigc::mem_fun(*this,&OptionsWindow::onTestButtonClicked),
                cuePlayerEntry));
    audioDeviceTable->attach(*cueTestButton, 2, 3, 0, 1);
    
    // display the settings for the output player device
    Glib::ustring   outputPlayerLabelContents;
    try {
        outputPlayerLabelContents.append(*getResourceUstring(
                                                        "outputDeviceLabel"));
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    Gtk::Label *    outputPlayerLabel = Gtk::manage(
                                    new Gtk::Label(outputPlayerLabelContents) );
    audioDeviceTable->attach(*outputPlayerLabel, 
                                    0, 1, 1, 2, Gtk::SHRINK, Gtk::SHRINK, 5, 0);
    
    EntryBin *      outputPlayerEntry = createEntry(
                                    OptionsContainer::outputPlayerDeviceName);
    audioDeviceTable->attach(*outputPlayerEntry, 1, 2, 1, 2);

    Button *        outputTestButton;
    try {
        outputTestButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("testButtonLabel") ));
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    outputTestButton->signal_clicked().connect(sigc::bind<EntryBin*>(
                sigc::mem_fun(*this, &OptionsWindow::onTestButtonClicked),
                outputPlayerEntry));
    audioDeviceTable->attach(*outputTestButton, 2, 3, 1, 2);

    // make a new box and pack the components into it
    Gtk::VBox *     section = Gtk::manage(new Gtk::VBox);
    section->pack_start(*audioDeviceTable,   Gtk::PACK_SHRINK, 5);
    
    return section;
}


/*------------------------------------------------------------------------------
 *  Construct the "Servers" section.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
OptionsWindow :: constructServersSection(void)                      throw ()
{
    Ptr<OptionsContainer>::Ref  optionsContainer
                                   = gLiveSupport->getOptionsContainer();
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    // the settings for the authentication server
    Gtk::Table *    authenticationTable = Gtk::manage(new Gtk::Table);
    authenticationTable->set_row_spacings(5);
    authenticationTable->set_col_spacings(5);
    
    Gtk::Label *    authenticationLabel;
    Gtk::Label *    authenticationServerLabel;
    Gtk::Label *    authenticationPortLabel;
    Gtk::Label *    authenticationPathLabel;
    try {
        authenticationLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("authenticationLabel") ));
        authenticationServerLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("serverLabel") ));
        authenticationPortLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("portLabel") ));
        authenticationPathLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("pathLabel") ));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    authenticationTable->attach(*authenticationLabel,
                                    0, 1, 0, 1, Gtk::SHRINK, Gtk::SHRINK, 5, 0);
    authenticationTable->attach(*authenticationServerLabel,
                                    1, 2, 0, 1, Gtk::SHRINK, Gtk::SHRINK);
    authenticationTable->attach(*authenticationPortLabel,
                                    1, 2, 1, 2, Gtk::SHRINK, Gtk::SHRINK);
    authenticationTable->attach(*authenticationPathLabel,
                                    1, 2, 2, 3, Gtk::SHRINK, Gtk::SHRINK);
    
    EntryBin *  authenticationServerEntry = createEntry(
                                    OptionsContainer::authenticationServer);
    EntryBin *  authenticationPortEntry   = createEntry(
                                    OptionsContainer::authenticationPort);
    EntryBin *  authenticationPathEntry   = createEntry(
                                    OptionsContainer::authenticationPath);
    
    authenticationTable->attach(*authenticationServerEntry, 2, 3, 0, 1);
    authenticationTable->attach(*authenticationPortEntry,   2, 3, 1, 2);
    authenticationTable->attach(*authenticationPathEntry,   2, 3, 2, 3);
    
    // the settings for the storage server
    Gtk::Table *    storageTable = Gtk::manage(new Gtk::Table);
    storageTable->set_row_spacings(5);
    storageTable->set_col_spacings(5);
    
    Gtk::Label *    storageLabel;
    Gtk::Label *    storageServerLabel;
    Gtk::Label *    storagePortLabel;
    Gtk::Label *    storagePathLabel;
    try {
        storageLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("storageLabel") ));
        storageServerLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("serverLabel") ));
        storagePortLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("portLabel") ));
        storagePathLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("pathLabel") ));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    storageTable->attach(*storageLabel,
                                    0, 1, 0, 1, Gtk::SHRINK, Gtk::SHRINK, 5, 0);
    storageTable->attach(*storageServerLabel,
                                    1, 2, 0, 1, Gtk::SHRINK, Gtk::SHRINK);
    storageTable->attach(*storagePortLabel,
                                    1, 2, 1, 2, Gtk::SHRINK, Gtk::SHRINK);
    storageTable->attach(*storagePathLabel,
                                    1, 2, 2, 3, Gtk::SHRINK, Gtk::SHRINK);
    
    EntryBin *  storageServerEntry = createEntry(
                                            OptionsContainer::storageServer);
    EntryBin *  storagePortEntry   = createEntry(
                                            OptionsContainer::storagePort);
    EntryBin *  storagePathEntry   = createEntry(
                                            OptionsContainer::storagePath);
    
    storageTable->attach(*storageServerEntry, 2, 3, 0, 1);
    storageTable->attach(*storagePortEntry,   2, 3, 1, 2);
    storageTable->attach(*storagePathEntry,   2, 3, 2, 3);
    
    // make a new box and pack the components into it
    Gtk::VBox *     section = Gtk::manage(new Gtk::VBox);
    section->pack_start(*authenticationTable,   Gtk::PACK_SHRINK, 10);
    section->pack_start(*storageTable,          Gtk::PACK_SHRINK, 10);
    
    return section;
}


/*------------------------------------------------------------------------------
 *  Construct the "About" section.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
OptionsWindow :: constructAboutSection(void)                        throw ()
{
    Glib::ustring   aboutLabelContents;
    aboutLabelContents.append("\n\n");
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


/*------------------------------------------------------------------------------
 *  Reset all user entries to their saved state.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: resetEntries()                                     throw ()
{
    Ptr<OptionsContainer>::Ref      optionsContainer
                                        = gLiveSupport->getOptionsContainer();

    StringEntryListType::iterator   it;
    for (it = stringEntryList.begin(); it != stringEntryList.end(); ++it) {
        OptionsContainer::OptionItemString  optionItem = it->first;
        EntryBin *                          entry      = it->second;
     
        try {
            entry->set_text(*optionsContainer->getOptionItem(optionItem));

        } catch (std::invalid_argument &e) {
            // TODO: signal error?
            entry->set_text("");
        }
    }
}

