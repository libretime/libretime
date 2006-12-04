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

#include <gtkmm.h>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ScrolledNotebook.h"
#include "LiveSupport/Widgets/EntryBin.h"

#include "OptionsWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/**
 *  The name of the window, used by the keyboard shortcuts (or by the .gtkrc).
 */
const Glib::ustring     windowName = "optionsWindow";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
OptionsWindow :: OptionsWindow (Ptr<GLiveSupport>::Ref    gLiveSupport,
                                Ptr<ResourceBundle>::Ref  bundle,
                                Button *                  windowOpenerButton)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle,
                      windowOpenerButton),
            backupView(0)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    try {
        set_title(*getResourceUstring("windowTitle"));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    bool    canBackup = (gLiveSupport->getSessionId()
                            && gLiveSupport->isStorageAvailable());

    // build up the notepad for the various sections
    mainNotebook = Gtk::manage(new ScrolledNotebook);
    Gtk::Box *      soundSectionBox         = constructSoundSection();
    Gtk::Box *      keyBindingsSectionBox   = constructKeyBindingsSection();
    Gtk::Box *      serversSectionBox       = constructServersSection();
    Gtk::Box *      schedulerSectionBox     = constructSchedulerSection();
    Gtk::Box *      backupSectionBox        = 0;
    if (canBackup) {
                    backupSectionBox        = constructBackupSection();
    }
    Gtk::Box *      aboutSectionBox         = constructAboutSection();

    try {
        mainNotebook->appendPage(*soundSectionBox,
                            *getResourceUstring("soundSectionLabel"));
        mainNotebook->appendPage(*keyBindingsSectionBox,
                            *getResourceUstring("keyBindingsSectionLabel"));
        mainNotebook->appendPage(*serversSectionBox,
                            *getResourceUstring("serversSectionLabel"));
        mainNotebook->appendPage(*schedulerSectionBox,
                            *getResourceUstring("schedulerSectionLabel"));
        if (canBackup) {
            mainNotebook->appendPage(*backupSectionBox,
                            *getResourceUstring("backupSectionLabel"));
        }
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
    set_default_size(700, 500);
    set_modal(false);
    
    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Cancel button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onCancelButtonClicked(void)                        throw ()
{
    resetEntries();
    resetKeyBindings();
    onCloseButtonClicked(false);
}


/*------------------------------------------------------------------------------
 *  Event handler for the Apply button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onApplyButtonClicked(void)                         throw ()
{
    bool changed = saveChangesInStringEntryFields();
    saveChangesInKeyBindings();                     // no need to restart

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
 *  Save the changes in the string entry fields.
 *----------------------------------------------------------------------------*/
bool
OptionsWindow :: saveChangesInStringEntryFields(void)               throw ()
{
    Ptr<OptionsContainer>::Ref
            optionsContainer    = gLiveSupport->getOptionsContainer();

    bool    changed             = false;
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
                try {
                    Ptr<Glib::ustring>::Ref
                            errorMessage(new Glib::ustring(
                                        *getResourceUstring("errorMsg") ));
                    errorMessage->append(e.what());
                    gLiveSupport->displayMessageWindow(errorMessage);
                } catch (std::invalid_argument &e) {
                    std::cerr << e.what() << std::endl;
                    std::exit(1);
                }
            }
        }
    }
    
    return changed;
}


/*------------------------------------------------------------------------------
 *  Save the changes in the key bindings.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: saveChangesInKeyBindings(void)                     throw ()
{
    Ptr<OptionsContainer>::Ref
            optionsContainer    = gLiveSupport->getOptionsContainer();
    Ptr<const KeyboardShortcutList>::Ref
            list                = gLiveSupport->getKeyboardShortcutList();

    KeyboardShortcutList::iterator  listIt;
    Gtk::TreeModel::iterator        modelIt = keyBindingsModel
                                                        ->children().begin();
    int                             containerNo = 1;
    for (listIt = list->begin(); listIt != list->end();
                                        ++listIt, ++modelIt, ++containerNo) {
        Ptr<const KeyboardShortcutContainer>::Ref   
                        container   = *listIt;
        Gtk::TreeRow    parent      = *modelIt;
        
        KeyboardShortcutContainer::iterator     containerIt;
        Gtk::TreeModel::iterator                childIt = parent.children()
                                                                    .begin();
        int                                     shortcutNo = 1;
        for (containerIt = container->begin(); containerIt != container->end();
                                    ++containerIt, ++childIt, ++shortcutNo) {
            Ptr<KeyboardShortcut>::Ref
                            shortcut        = *containerIt;
            Ptr<const Glib::ustring>::Ref
                            oldKeyString    = shortcut->getKeyString();
            Gtk::TreeRow    child           = *childIt;
            Ptr<const Glib::ustring>::Ref
                            newKeyString(new const Glib::ustring(
                                    child[keyBindingsColumns.keyNameColumn] ));
            if (*oldKeyString != *newKeyString) {
                try {
                    shortcut->setKey(*newKeyString);
                    optionsContainer->setKeyboardShortcutItem(containerNo,
                                                              shortcutNo,
                                                              newKeyString);
                } catch (std::invalid_argument &e) {
                    try {
                        Ptr<Glib::ustring>::Ref
                                errorMessage(new Glib::ustring(
                                            *getResourceUstring("errorMsg") ));
                        errorMessage->append(e.what());
                        gLiveSupport->displayMessageWindow(errorMessage);
                    } catch (std::invalid_argument &e) {
                        std::cerr << e.what() << std::endl;
                        std::exit(1);
                    }
                }
            }
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
                                                        cuePlayerDeviceName);
    Ptr<const Glib::ustring>::Ref
        newDevice(new Glib::ustring(entry->get_text()));
    
    // NOTE: we can't use the output player b/c that would trigger onStop()
    gLiveSupport->playTestSoundOnCue(oldDevice, newDevice);
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
 *  Construct the "Key bindings" section.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
OptionsWindow :: constructKeyBindingsSection(void)                  throw ()
{
    // create the TreeView
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    keyBindingsModel = Gtk::TreeStore::create(keyBindingsColumns);
    keyBindingsView  = Gtk::manage(wf->createTreeView(keyBindingsModel));
    
    keyBindingsView->appendColumn("", keyBindingsColumns.actionColumn);
    keyBindingsView->appendColumn("", keyBindingsColumns.keyDisplayColumn);
    
    // fill in the data
    fillKeyBindingsModel();
        
    // set TreeView properties
    keyBindingsView->set_headers_visible(false);
    keyBindingsView->set_enable_search(false);
    keyBindingsView->columns_autosize();
    keyBindingsView->expand_all();
    
    // connect the callbacks
    keyBindingsView->signal_row_activated().connect(sigc::mem_fun(*this,
                                &OptionsWindow::onKeyBindingsRowActivated ));
    keyBindingsView->signal_key_press_event().connect(sigc::mem_fun(*this,
                                &OptionsWindow::onKeyBindingsKeyPressed ));
    keyBindingsView->signal_focus_out_event().connect_notify(sigc::mem_fun(
                                *this,
                                &OptionsWindow::onKeyBindingsFocusOut ));
    
    // add instructions
    Ptr<const Glib::ustring>::Ref   instructionsText;
    try {
        instructionsText = getResourceUstring("keyBindingsInstructionsText");
        
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    Gtk::Label *    instructionsLabel = Gtk::manage(new Gtk::Label(
                                                            *instructionsText,
                                                            Gtk::ALIGN_CENTER,
                                                            Gtk::ALIGN_CENTER));
    instructionsLabel->set_justify(Gtk::JUSTIFY_CENTER);
    
    // make a new box and pack the components into it
    Gtk::VBox *     section = Gtk::manage(new Gtk::VBox);
    section->pack_start(*instructionsLabel, Gtk::PACK_SHRINK, 5);
    section->pack_start(*keyBindingsView,   Gtk::PACK_SHRINK, 5);
    
    return section;
}


/*------------------------------------------------------------------------------
 *  Fill the key bindings model from the KeyboardShortcutList.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: fillKeyBindingsModel(void)                         throw ()
{
    Ptr<const KeyboardShortcutList>::Ref
                            list    = gLiveSupport->getKeyboardShortcutList();

    try {
        KeyboardShortcutList::iterator it;
        for (it = list->begin(); it != list->end(); ++it) {
            Ptr<const KeyboardShortcutContainer>::Ref   
                            container   = *it;
            Ptr<const Glib::ustring>::Ref
                            windowName  = container->getWindowName();
            Gtk::TreeRow    parent      = *keyBindingsModel->append();
            parent[keyBindingsColumns.actionColumn]   
                    = *gLiveSupport->getLocalizedWindowName(windowName);
            
            KeyboardShortcutContainer::iterator iter;
            for (iter = container->begin(); iter != container->end(); ++iter) {
                Ptr<const KeyboardShortcut>::Ref
                            shortcut    = *iter;
                Ptr<const Glib::ustring>::Ref
                            actionString    = shortcut->getActionString();
                Ptr<const Glib::ustring>::Ref
                            keyString       = shortcut->getKeyString();
                Gtk::TreeRow    child
                                = *keyBindingsModel->append(parent.children());
                child[keyBindingsColumns.actionColumn]
                    = *gLiveSupport->getLocalizedKeyboardActionName(
                                                                actionString);
                child[keyBindingsColumns.keyNameColumn]
                    = *keyString;                       // TODO: localize this?
                child[keyBindingsColumns.keyDisplayColumn]
                    = Glib::Markup::escape_text(*keyString);
            }
        }
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
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
    
    // the settings for the scheduler
    Gtk::Table *    schedulerTable = Gtk::manage(new Gtk::Table);
    schedulerTable->set_row_spacings(5);
    schedulerTable->set_col_spacings(5);
    
    Gtk::Label *    schedulerLabel;
    Gtk::Label *    schedulerServerLabel;
    Gtk::Label *    schedulerPortLabel;
    Gtk::Label *    schedulerPathLabel;
    try {
        schedulerLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("schedulerLabel") ));
        schedulerServerLabel = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("serverLabel") ));
        schedulerPortLabel   = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("portLabel") ));
        schedulerPathLabel   = Gtk::manage(new Gtk::Label(
                            *getResourceUstring("pathLabel") ));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    schedulerTable->attach(*schedulerLabel,
                                    0, 1, 0, 1, Gtk::SHRINK, Gtk::SHRINK, 5, 0);
    schedulerTable->attach(*schedulerServerLabel,
                                    1, 2, 0, 1, Gtk::SHRINK, Gtk::SHRINK);
    schedulerTable->attach(*schedulerPortLabel,
                                    1, 2, 1, 2, Gtk::SHRINK, Gtk::SHRINK);
    schedulerTable->attach(*schedulerPathLabel,
                                    1, 2, 2, 3, Gtk::SHRINK, Gtk::SHRINK);
    
    EntryBin *  schedulerServerEntry = createEntry(
                                            OptionsContainer::schedulerServer);
    EntryBin *  schedulerPortEntry   = createEntry(
                                            OptionsContainer::schedulerPort);
    EntryBin *  schedulerPathEntry   = createEntry(
                                            OptionsContainer::schedulerPath);
    
    schedulerTable->attach(*schedulerServerEntry, 2, 3, 0, 1);
    schedulerTable->attach(*schedulerPortEntry,   2, 3, 1, 2);
    schedulerTable->attach(*schedulerPathEntry,   2, 3, 2, 3);
    
    // make a new box and pack the components into it
    Gtk::VBox *     section = Gtk::manage(new Gtk::VBox);
    section->pack_start(*authenticationTable,   Gtk::PACK_SHRINK, 10);
    section->pack_start(*storageTable,          Gtk::PACK_SHRINK, 10);
    section->pack_start(*schedulerTable,        Gtk::PACK_SHRINK, 10);
    
    return section;
}


/*------------------------------------------------------------------------------
 *  Construct the "Scheduler" section.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
OptionsWindow :: constructSchedulerSection(void)                    throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Label *    statusTextLabel;
    Button *        startButton;
    Button *        stopButton;
    try {
        statusTextLabel = Gtk::manage(new Gtk::Label(*getResourceUstring(
                                                "schedulerStatusText")));
        startButton = Gtk::manage(wf->createButton(*getResourceUstring(
                                                "schedulerStartButtonLabel")));
        stopButton = Gtk::manage(wf->createButton(*getResourceUstring(
                                                "schedulerStopButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    startButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &OptionsWindow::onSchedulerStartButtonClicked));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &OptionsWindow::onSchedulerStopButtonClicked));
    
    Gtk::HBox *         statusReportBox = Gtk::manage(new Gtk::HBox);
    statusReportBox->pack_start(*statusTextLabel,      Gtk::PACK_SHRINK, 5);
    schedulerStatusLabel = Gtk::manage(new Gtk::Label);
    statusReportBox->pack_start(*schedulerStatusLabel, Gtk::PACK_SHRINK, 0);
    
    Gtk::ButtonBox *    startStopButtons = Gtk::manage(new Gtk::HButtonBox(
                                                    Gtk::BUTTONBOX_SPREAD, 20));
    startStopButtons->pack_start(*startButton);
    startStopButtons->pack_start(*stopButton);

    Gtk::VBox *         section = Gtk::manage(new Gtk::VBox);
    section->pack_start(*statusReportBox,  Gtk::PACK_SHRINK, 20);
    section->pack_start(*startStopButtons, Gtk::PACK_SHRINK);
    
    updateSchedulerStatus();
    return section;
}


/*------------------------------------------------------------------------------
 *  Construct the "Backup" section.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
OptionsWindow :: constructBackupSection(void)                       throw ()
{
    Ptr<ResourceBundle>::Ref    backupBundle;
    try {
        backupBundle = gLiveSupport->getBundle("backupView");
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    backupView = Gtk::manage(new BackupView(gLiveSupport, backupBundle));
    return backupView;
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
    try {
        aboutLabelContents.append(" (");
        aboutLabelContents.append(*getResourceUstring("webSiteUrlText"));
        aboutLabelContents.append(")\n\n");
        aboutLabelContents.append(*formatMessage("reportBugsToText",
                                                 PACKAGE_BUGREPORT ));
        aboutLabelContents.append("\n\n");
        aboutLabelContents.append(*getResourceUstring("creditsText"));
    
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


/*------------------------------------------------------------------------------
 *  Reset the key bindings to their saved state.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: resetKeyBindings(void)                             throw ()
{
    keyBindingsModel->clear();
    fillKeyBindingsModel();
    keyBindingsView->expand_all();
}


/*------------------------------------------------------------------------------
 *  Event handler for clicking on a row in the key bindings table.
 *----------------------------------------------------------------------------*/
void
OptionsWindow ::  onKeyBindingsRowActivated(const Gtk::TreePath &     path,
                                            Gtk::TreeViewColumn *     column)
                                                                    throw ()
{
    resetEditedKeyBinding();
    
    Gtk::TreeIter       iter = keyBindingsModel->get_iter(path);
    if (iter) {
        Gtk::TreeRow    row = *iter;
        editedKeyName.reset(new const Glib::ustring(
                                        row[keyBindingsColumns.keyNameColumn]));
        editedKeyRow = row;
        try {
            row[keyBindingsColumns.keyDisplayColumn]
                                        = *getResourceUstring("pressAKeyMsg");
        } catch (std::invalid_argument &e) {
            // TODO: signal error
            std::cerr << e.what() << std::endl;
            std::exit(1);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for clicking outside the key bindings table.
 *----------------------------------------------------------------------------*/
bool
OptionsWindow ::  onKeyBindingsKeyPressed(GdkEventKey *  event)     throw ()
{
    // TODO: remove this ugly hack
    switch (event->keyval) {
        case GDK_Shift_L :              // ignore the event if only
        case GDK_Shift_R :              // a shift key has been pressed
        case GDK_Control_L :
        case GDK_Control_R :
        case GDK_Alt_L :
        case GDK_Alt_R :
        case GDK_Super_L :
        case GDK_Super_R :
        case GDK_Hyper_L :
        case GDK_Hyper_R :
        case GDK_Meta_L :
        case GDK_Meta_R :   return false;
    }
        
    if (editedKeyName) {
        Ptr<const Glib::ustring>::Ref
            newKeyName = KeyboardShortcut::modifiedKeyToString(
                                        Gdk::ModifierType(event->state),
                                        event->keyval);
        if (newKeyName && *newKeyName != "Escape") {
            editedKeyName = newKeyName;
        }
        resetEditedKeyBinding();
        return true;
    }
    
    return false;
}


/*------------------------------------------------------------------------------
 *  Event handler for clicking outside the key bindings table.
 *----------------------------------------------------------------------------*/
void
OptionsWindow ::  onKeyBindingsFocusOut(GdkEventFocus *     event)
                                                                    throw ()
{
    resetEditedKeyBinding();
}


/*------------------------------------------------------------------------------
 *  Reset the key binding to its pre-editing value.
 *----------------------------------------------------------------------------*/
void
OptionsWindow ::  resetEditedKeyBinding(void)                       throw ()
{
    if (editedKeyName) {
        editedKeyRow[keyBindingsColumns.keyNameColumn]
                            = *editedKeyName;
        editedKeyRow[keyBindingsColumns.keyDisplayColumn]
                            = Glib::Markup::escape_text(*editedKeyName);
        editedKeyName.reset();
    }
}


/*------------------------------------------------------------------------------
 *  Show the window and return when the user hides it.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: run(void)                                          throw ()
{
    mainNotebook->setActivePage(2);      // "Servers"
    property_window_position().set_value(Gtk::WIN_POS_CENTER_ALWAYS);
    show_all();
    Gtk::Main::run(*this);
}


/*------------------------------------------------------------------------------
 *  Signal handler for the scheduler Start button getting clicked.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onSchedulerStartButtonClicked(void)                throw ()
{
    gLiveSupport->checkSchedulerClient();
    if (!gLiveSupport->isSchedulerAvailable()) {
        gLiveSupport->startSchedulerClient();
    }
    updateSchedulerStatus();
}


/*------------------------------------------------------------------------------
 *  Signal handler for the scheduler Stop button getting clicked.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onSchedulerStopButtonClicked(void)                 throw ()
{
    gLiveSupport->checkSchedulerClient();
    if (gLiveSupport->isSchedulerAvailable()) {
        gLiveSupport->stopSchedulerClient();
    }
    updateSchedulerStatus();
}


/*------------------------------------------------------------------------------
 *  Update the status display in the Status tab.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: updateSchedulerStatus(void)                        throw ()
{
    gLiveSupport->checkSchedulerClient();
    try {
        if (gLiveSupport->isSchedulerAvailable()) {
            schedulerStatusLabel->set_text(
                                *getResourceUstring("schedulerRunningStatus"));
        } else {
            schedulerStatusLabel->set_text(
                                *getResourceUstring("schedulerStoppedStatus"));
        }
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
}

