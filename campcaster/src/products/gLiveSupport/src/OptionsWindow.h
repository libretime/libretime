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
#ifndef OptionsWindow_h
#define OptionsWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Core/OptionsContainer.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "GuiWindow.h"
#include "GLiveSupport.h"
#include "BackupView.h"
#include "RdsView.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The options window.
 *
 *  The layout of the window is roughly the following:
 *  <pre><code>
 *  +--- options window ----------------+
 *  | +- tab1 -+ ... +- tabN -+         |
 *  | +-------------------------------+ |
 *  | +-- contents of the ------------+ |
 *  | +-- currently ------------------+ |
 *  | +-- selected tab ---------------+ |
 *  | +-------------------------------+ |
 *  +------------(Apply)-(Cancel)-(OK)--+
 *  </code></pre>
 *
 *  @author $Author$
 *  @version $Revision$
 */
class OptionsWindow : public GuiWindow
{
    private:

        /**
         *  The notepad holding the different sections.
         */
        Gtk::Notebook *             mainNotebook;

        /**
         *  The label showing the current status of the scheduler.
         */
        Gtk::Label *                schedulerStatusLabel;
        
        /**
         *  The type for the list of user entry fields of string type.
         */
        typedef std::vector<std::pair<OptionsContainer::OptionItemString,
                                      Gtk::Entry*> >    StringEntryListType;

        /**
         *  The list of user entry fields of string type.
         */
        StringEntryListType         stringEntryList;

        /**
         *  Create a new user entry field item.
         *
         *  This gets a reference to the Gtk::Entry from the Glade file, and
         *  sets its text to the current value of the option.
         *  The Gtk::Entry is then added to the list of user entry fields.
         *
         *  @param  entryName   the name of the Entry in the Glade file.
         *  @param  optionItem  the name of the option item for this entry.
         *  @return the text entry field just processed.
         */
        Gtk::Entry *
        createEntry(const Glib::ustring &               entryName,
                    OptionsContainer::OptionItemString  optionItem)
                                                                    throw ();

        /**
         *  Reset all user entries to their saved state.
         */
        void
        resetEntries(void)                                          throw ();

        /**
         *  Reset the key bindings to their saved state.
         */
        void
        resetKeyBindings(void)                                      throw ();

        /**
         *  Reset the RDS settings to their saved state.
         */
        void
        resetRds(void)                                              throw ();

        /**
         *  Fill the key bindings model from the KeyboardShortcutList.
         */
        void
        fillKeyBindingsModel(void)                                  throw ();

        /**
         *  The row of the currently edited key binding.
         */
        Gtk::TreeRow                    editedKeyRow;
        
        /**
         *  The value of the currently edited key binding
         *  (as a user-readable modifiers - key name combo).
         */
        Ptr<const Glib::ustring>::Ref   editedKeyName;
        
        /**
         *  Reset the key binding to its pre-editing value.
         */
        void
        resetEditedKeyBinding(void)                                 throw ();

        /**
         *  Save the changes in the string entry fields.
         *
         *  @return true if some changes were detected and saved.
         */
        bool
        saveChangesInStringEntryFields(void)                        throw ();

        /**
         *  Save the changes in the key bindings.
         */
        void
        saveChangesInKeyBindings(void)                              throw ();

        /**
         *  Save the changes in the RDS settings.
         */
        void
        saveChangesInRds(void)                                      throw ();

        /**
         *  Construct the "Sound" section.
         */
        void
        constructSoundSection(void)                                 throw ();

        /**
         *  Construct the "Key bindings" section.
         */
        void
        constructKeyBindingsSection(void)                           throw ();

        /**
         *  Construct the "Servers" section.
         */
        void
        constructServersSection(void)                               throw ();

        /**
         *  Construct the "Scheduler" section.
         *  This section contains the scheduler start and stop buttons.
         */
        void
        constructSchedulerSection(void)                             throw ();

        /**
         *  Construct the "Backup" section.
         */
        void
        constructBackupSection(void)                                throw ();

        /**
         *  Construct the "RDS" section.
         */
        void
        constructRdsSection(void)                                   throw ();

        /**
         *  Construct the "About" section.
         */
        void
        constructAboutSection(void)                                 throw ();

        /**
         *  Update the scheduler status display in the Scheduler tab.
         */
        void
        updateSchedulerStatus(void)                                 throw ();


    protected:

        /**
         *  Event handler for the Cancel button.
         */
        virtual void
        onCancelButtonClicked(void)                                 throw ();

        /**
         *  Event handler for the Apply button.
         */
        virtual void
        onApplyButtonClicked(void)                                  throw ();

        /**
         *  Event handler for the OK button.
         */
        virtual void
        onOkButtonClicked(void)                                     throw ();

        /**
         *  Event handler for the Close button.
         *
         *  @param  needConfirm     if true, we check if changes has been
         *                          made to the input fields, and if yes, then
         *                          a "save changes?" dialog is displayed
         */
        virtual void
        onCloseButtonClicked(bool   needConfirm = true)             throw ();

        /**
         *  Event handler for the test button.
         *
         *  @param  entry   the text entry field containing the new device name
         *  @see    GLiveSupport::setCueAudioDevice()
         *  @see    GLiveSupport::playTestSoundOnCue()
         */
        virtual void
        onTestButtonClicked(const Gtk::Entry *    entry)            throw ();

        /**
         *  Event handler for double-clicking a row in the key bindings table.
         *
         *  @param  event   the button event
         */
        virtual void
        onKeyBindingsRowActivated(const Gtk::TreePath &     path,
                                  Gtk::TreeViewColumn *     column)
                                                                    throw ();

        /**
         *  Signal handler for a key pressed in the key bindings table.
         *
         *  @param  event the button event received
         *  @return true if the key press was fully handled, false if not
         */
        virtual bool
        onKeyBindingsKeyPressed(GdkEventKey *   event)              throw ();

        /**
         *  Event handler for clicking outside the key bindings table.
         *
         *  @param  event   the focus event
         */
        virtual void
        onKeyBindingsFocusOut(GdkEventFocus *   event)              throw ();

        /**
         *  Signal handler for the scheduler Start button getting clicked.
         */
        virtual void
        onSchedulerStartButtonClicked(void)                         throw ();

        /**
         *  Signal handler for the scheduler Stop button getting clicked.
         */
        virtual void
        onSchedulerStopButtonClicked(void)                          throw ();

        /**
         *  The columns model containing the data for the Key bindings section.
         *
         *  @author $Author$
         *  @version $Revision$
         */
        class ModelColumns : public ZebraTreeModelColumnRecord
        {
            public:
            
                /**
                 *  The column for the name of the action.
                 *  This contains the name of the window (for parent rows),
                 *  or the name of the action (for child rows).
                 */
                Gtk::TreeModelColumn<Glib::ustring>       actionColumn;

                /**
                 *  The column for the user readable name of the key bound
                 *  (plain version, with '<' and '>').
                 */
                Gtk::TreeModelColumn<Glib::ustring>       keyNameColumn;

                /**
                 *  The column for the user readable name of the key bound
                 *  (escaped version, with '&lt;' and '&gt;').
                 */
                Gtk::TreeModelColumn<Glib::ustring>       keyDisplayColumn;

                /**
                 *  The column for the gdkkeytypes.h code of the modifiers.
                 */
                Gtk::TreeModelColumn<unsigned int>        modifiersColumn;

                /**
                 *  The column for the gdkkeysyms.h code of the key.
                 */
                Gtk::TreeModelColumn<unsigned int>        keyValueColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(actionColumn);
                    add(keyNameColumn);
                    add(keyDisplayColumn);
                    add(modifiersColumn);
                    add(keyValueColumn);
                }
        };


        /**
         *  The column model for the key bindings.
         */
        ModelColumns                    keyBindingsColumns;

        /**
         *  The tree model, as a GTK reference, for the key bindings.
         */
        Glib::RefPtr<Gtk::TreeStore>    keyBindingsModel;

        /**
         *  The tree view for the key bindings.
         */
        ZebraTreeView *                 keyBindingsTreeView;

        /**
         *  The backup view shown in the backup section.
         */
        Ptr<BackupView>::Ref            backupView;

        /**
         *  The RdsView shown in the RDS section.
         */
        Ptr<RdsView>::Ref               rdsView;


    public:

        /**
         *  Constructor.
         *
         *  @param windowOpenerButton   the button which was pressed to open
         *                              this window.
         */
        OptionsWindow(Gtk::ToggleButton *        windowOpenerButton)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~OptionsWindow(void)                                        throw ()
        {
        }

        /**
         *  Return the BackupList object shown by the widget.
         */
        Ptr<BackupList>::Ref
        getBackupList(void)                                         throw ()
        {
            return backupView ? backupView->getBackupList()
                              : Ptr<BackupList>::Ref();
        }
        
        /**
         *  Show the window and return when the user hides it.
         *  This is used by GLiveSupport when the authentication server
         *  address is wrong.  It opens the window to the "Servers" tab.
         */
        void
        run(void)                                                   throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // OptionsWindow_h

