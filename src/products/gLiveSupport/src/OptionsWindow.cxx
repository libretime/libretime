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

#include "OptionsWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "optionsWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "OptionsWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
OptionsWindow :: OptionsWindow (Gtk::ToggleButton *       windowOpenerButton)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName,
                      windowOpenerButton)
{
    bool    canBackup = (gLiveSupport->getSessionId()
                            && gLiveSupport->isStorageAvailable());

    // build up the notepad for the various sections
    glade->get_widget("mainNotebook1", mainNotebook);
    constructSoundSection();
    constructKeyBindingsSection();
    constructServersSection();
    constructSchedulerSection();
    if (canBackup) {
        constructBackupSection();
    }
    constructRdsSection();
    constructAboutSection();

    Gtk::Label *        soundTabLabel;
    Gtk::Label *        keyBindingsTabLabel;
    Gtk::Label *        serversTabLabel;
    Gtk::Label *        schedulerTabLabel;
    Gtk::Label *        backupTabLabel;
    Gtk::Label *        rdsTabLabel;
    Gtk::Label *        aboutTabLabel;
    glade->get_widget("soundTabLabel1", soundTabLabel);
    glade->get_widget("keyBindingsTabLabel1", keyBindingsTabLabel);
    glade->get_widget("serversTabLabel1", serversTabLabel);
    glade->get_widget("schedulerTabLabel1", schedulerTabLabel);
    glade->get_widget("backupTabLabel1", backupTabLabel);
    glade->get_widget("rdsTabLabel1", rdsTabLabel);
    glade->get_widget("aboutTabLabel1", aboutTabLabel);
    soundTabLabel->set_label(*getResourceUstring("soundSectionLabel"));
    keyBindingsTabLabel->set_label(*getResourceUstring(
                                                 "keyBindingsSectionLabel"));
    serversTabLabel->set_label(*getResourceUstring("serversSectionLabel"));
    schedulerTabLabel->set_label(*getResourceUstring("schedulerSectionLabel"));
    if (canBackup) {
        backupTabLabel->set_label(*getResourceUstring("backupSectionLabel"));
    }
    rdsTabLabel->set_label(*getResourceUstring("rdsSectionLabel"));
    aboutTabLabel->set_label(*getResourceUstring("aboutSectionLabel"));

    // bind events
    glade->connect_clicked("applyButton1", sigc::mem_fun(*this,
                                &OptionsWindow::onApplyButtonClicked));
    glade->connect_clicked("cancelButton1", sigc::mem_fun(*this,
                                &OptionsWindow::onCancelButtonClicked));
    glade->connect_clicked("okButton1", sigc::mem_fun(*this,
                                &OptionsWindow::onOkButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Event handler for the Cancel button.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: onCancelButtonClicked(void)                        throw ()
{
    resetEntries();
    resetKeyBindings();
    resetRds();
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
    saveChangesInRds();                             // no need to restart

    if (changed) {
        gLiveSupport->displayMessageWindow(*getResourceUstring(
                                                    "needToRestartMsg"));
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
        Gtk::Entry *                        entry      = it->second;
        
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
                    gLiveSupport->displayMessageWindow(*errorMessage);
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
                        gLiveSupport->displayMessageWindow(*errorMessage);
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
 *  Save the changes in the RDS settings.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: saveChangesInRds(void)                             throw ()
{
    rdsView->saveChanges();
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
OptionsWindow :: onTestButtonClicked(const Gtk::Entry *     entry)
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
Gtk::Entry *
OptionsWindow :: createEntry(const Glib::ustring &                  entryName,
                             OptionsContainer::OptionItemString     optionItem)
                                                                    throw ()
{
    Ptr<OptionsContainer>::Ref  optionsContainer
                                   = gLiveSupport->getOptionsContainer();

    Gtk::Entry *    entry;
    glade->get_widget(entryName, entry);

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
void
OptionsWindow :: constructSoundSection(void)                        throw ()
{
    Ptr<OptionsContainer>::Ref  optionsContainer
                                   = gLiveSupport->getOptionsContainer();
    
    Gtk::Label *        cueDeviceLabel;
    Gtk::Label *        outputDeviceLabel;
    glade->get_widget("cueDeviceLabel1", cueDeviceLabel);
    glade->get_widget("outputDeviceLabel1", outputDeviceLabel);
    cueDeviceLabel->set_label(*getResourceUstring("cueDeviceLabel"));
    outputDeviceLabel->set_label(*getResourceUstring("outputDeviceLabel"));

    Gtk::Entry *        cueDeviceEntry;
    Gtk::Entry *        outputDeviceEntry;
    cueDeviceEntry = createEntry("cueDeviceEntry1",
                                 OptionsContainer::cuePlayerDeviceName);
    outputDeviceEntry = createEntry("outputDeviceEntry1",
                                    OptionsContainer::outputPlayerDeviceName);

    Gtk::Button *       cueTestButton;
    Gtk::Button *       outputTestButton;
    glade->get_widget("cueTestButton1", cueTestButton);
    glade->get_widget("outputTestButton1", outputTestButton);
    cueTestButton->set_label(*getResourceUstring("testButtonLabel"));
    outputTestButton->set_label(*getResourceUstring("testButtonLabel"));
    cueTestButton->signal_clicked().connect(sigc::bind<Gtk::Entry*>(
                sigc::mem_fun(*this, &OptionsWindow::onTestButtonClicked),
                cueDeviceEntry));
    outputTestButton->signal_clicked().connect(sigc::bind<Gtk::Entry*>(
                sigc::mem_fun(*this, &OptionsWindow::onTestButtonClicked),
                outputDeviceEntry));
}


/*------------------------------------------------------------------------------
 *  Construct the "Key bindings" section.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: constructKeyBindingsSection(void)                  throw ()
{
    // create the TreeView
    keyBindingsModel = Gtk::TreeStore::create(keyBindingsColumns);
    glade->get_widget_derived("keyBindingsTreeView1", keyBindingsTreeView);
    keyBindingsTreeView->set_model(keyBindingsModel);
    keyBindingsTreeView->connectModelSignals(keyBindingsModel);
    
    keyBindingsTreeView->appendColumn("", keyBindingsColumns.actionColumn);
    keyBindingsTreeView->appendColumn("", keyBindingsColumns.keyDisplayColumn);
    
    fillKeyBindingsModel();
    
    keyBindingsTreeView->columns_autosize();
    keyBindingsTreeView->expand_all();
    
    // connect the callbacks
    keyBindingsTreeView->signal_row_activated().connect(sigc::mem_fun(*this,
                                &OptionsWindow::onKeyBindingsRowActivated));
    keyBindingsTreeView->signal_key_press_event().connect(sigc::mem_fun(*this,
                                &OptionsWindow::onKeyBindingsKeyPressed));
    keyBindingsTreeView->signal_focus_out_event().connect_notify(sigc::mem_fun(
                                *this,
                                &OptionsWindow::onKeyBindingsFocusOut));
    
    // add instructions
    Gtk::Label *    instructionsLabel;
    glade->get_widget("keyBindingsInstructionsLabel1", instructionsLabel);
    instructionsLabel->set_label(*getResourceUstring(
                                            "keyBindingsInstructionsText"));
}


/*------------------------------------------------------------------------------
 *  Fill the key bindings model from the KeyboardShortcutList.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: fillKeyBindingsModel(void)                         throw ()
{
    Ptr<const KeyboardShortcutList>::Ref
                            list    = gLiveSupport->getKeyboardShortcutList();

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
}


/*------------------------------------------------------------------------------
 *  Construct the "Servers" section.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: constructServersSection(void)                      throw ()
{
    Ptr<OptionsContainer>::Ref  optionsContainer
                                   = gLiveSupport->getOptionsContainer();
    
    // authentication server
    Gtk::Label *    authenticationLabel;
    Gtk::Label *    authenticationServerLabel;
    Gtk::Label *    authenticationPortLabel;
    Gtk::Label *    authenticationPathLabel;
    glade->get_widget("authenticationServerLabel1", authenticationLabel);
    glade->get_widget("authenticationServerServerLabel1",
                                                    authenticationServerLabel);
    glade->get_widget("authenticationServerPortLabel1",
                                                    authenticationPortLabel);
    glade->get_widget("authenticationServerPathLabel1",
                                                    authenticationPathLabel);
    authenticationLabel->set_label(*getResourceUstring("authenticationLabel"));
    authenticationServerLabel->set_label(*getResourceUstring("serverLabel"));
    authenticationPortLabel->set_label(*getResourceUstring("portLabel"));
    authenticationPathLabel->set_label(*getResourceUstring("pathLabel"));
    
    createEntry("authenticationServerServerEntry1",
                                    OptionsContainer::authenticationServer);
    createEntry("authenticationServerPortEntry1",
                                    OptionsContainer::authenticationPort);
    createEntry("authenticationServerPathEntry1",
                                    OptionsContainer::authenticationPath);

    // storage server
    Gtk::Label *    storageLabel;
    Gtk::Label *    storageServerLabel;
    Gtk::Label *    storagePortLabel;
    Gtk::Label *    storagePathLabel;
    glade->get_widget("storageServerLabel1", storageLabel);
    glade->get_widget("storageServerServerLabel1", storageServerLabel);
    glade->get_widget("storageServerPortLabel1", storagePortLabel);
    glade->get_widget("storageServerPathLabel1", storagePathLabel);
    storageLabel->set_label(*getResourceUstring("storageLabel"));
    storageServerLabel->set_label(*getResourceUstring("serverLabel"));
    storagePortLabel->set_label(*getResourceUstring("portLabel"));
    storagePathLabel->set_label(*getResourceUstring("pathLabel"));
    
    createEntry("storageServerServerEntry1", OptionsContainer::storageServer);
    createEntry("storageServerPortEntry1", OptionsContainer::storagePort);
    createEntry("storageServerPathEntry1", OptionsContainer::storagePath);

    // scheduler server
    Gtk::Label *    schedulerLabel;
    Gtk::Label *    schedulerServerLabel;
    Gtk::Label *    schedulerPortLabel;
    Gtk::Label *    schedulerPathLabel;
    glade->get_widget("schedulerServerLabel1", schedulerLabel);
    glade->get_widget("schedulerServerServerLabel1", schedulerServerLabel);
    glade->get_widget("schedulerServerPortLabel1", schedulerPortLabel);
    glade->get_widget("schedulerServerPathLabel1", schedulerPathLabel);
    schedulerLabel->set_label(*getResourceUstring("schedulerLabel"));
    schedulerServerLabel->set_label(*getResourceUstring("serverLabel"));
    schedulerPortLabel->set_label(*getResourceUstring("portLabel"));
    schedulerPathLabel->set_label(*getResourceUstring("pathLabel"));
    
    createEntry("schedulerServerServerEntry1",
                                             OptionsContainer::schedulerServer);
    createEntry("schedulerServerPortEntry1", OptionsContainer::schedulerPort);
    createEntry("schedulerServerPathEntry1", OptionsContainer::schedulerPath);
}


/*------------------------------------------------------------------------------
 *  Construct the "Scheduler" section.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: constructSchedulerSection(void)                    throw ()
{
    Gtk::Label *    schedulerTextLabel;
    Gtk::Button *   startButton;
    Gtk::Button *   stopButton;
    glade->get_widget("schedulerTextLabel1", schedulerTextLabel);
    glade->get_widget("schedulerStatusLabel1", schedulerStatusLabel);
    glade->get_widget("schedulerStartButton1", startButton);
    glade->get_widget("schedulerStopButton1", stopButton);
    schedulerTextLabel->set_label(*getResourceUstring("schedulerStatusText"));
    updateSchedulerStatus();    // sets the schedulerStatusLabel
    startButton->set_label(*getResourceUstring("schedulerStartButtonLabel"));
    stopButton->set_label(*getResourceUstring("schedulerStopButtonLabel"));

    startButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &OptionsWindow::onSchedulerStartButtonClicked));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &OptionsWindow::onSchedulerStopButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Construct the "Backup" section.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: constructBackupSection(void)                       throw ()
{
    backupView.reset(new BackupView(this));
}


/*------------------------------------------------------------------------------
 *  Construct the "RDS" section.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: constructRdsSection(void)                          throw ()
{
    rdsView.reset(new RdsView(this));
}


/*------------------------------------------------------------------------------
 *  Construct the "About" section.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: constructAboutSection(void)                        throw ()
{
    Glib::ustring   aboutLabelContents;

    aboutLabelContents.append("\n");
    aboutLabelContents.append(PACKAGE_NAME);
    aboutLabelContents.append(" ");
    aboutLabelContents.append(PACKAGE_VERSION);
    aboutLabelContents.append("\n\n");
    aboutLabelContents.append(*formatMessage("reportBugsToText",
                                                PACKAGE_BUGREPORT ));
    aboutLabelContents.append("\n\n");
    aboutLabelContents.append(*getBinaryResourceAsUstring("creditsText"));

    Gtk::Label *    aboutLabel;
    glade->get_widget("aboutLabel1", aboutLabel);
    aboutLabel->set_label(aboutLabelContents);
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
        Gtk::Entry *                        entry      = it->second;
        
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
    keyBindingsTreeView->expand_all();
}


/*------------------------------------------------------------------------------
 *  Reset the RDS settings to their saved state.
 *----------------------------------------------------------------------------*/
void
OptionsWindow :: resetRds(void)                                     throw ()
{
    rdsView->reset();
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
        row[keyBindingsColumns.keyDisplayColumn]
                                        = *getResourceUstring("pressAKeyMsg");
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
    mainNotebook->set_current_page(2);      // "Servers"
    mainWindow->property_window_position().set_value(
                                                Gtk::WIN_POS_CENTER_ALWAYS);
    mainWindow->show_all();
    Gtk::Main::run(*mainWindow);
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
    if (gLiveSupport->isSchedulerAvailable()) {
        schedulerStatusLabel->set_text(
                            *getResourceUstring("schedulerRunningStatus"));
    } else {
        schedulerStatusLabel->set_text(
                            *getResourceUstring("schedulerStoppedStatus"));
    }
}

