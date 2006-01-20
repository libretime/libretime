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
#ifndef OptionsWindow_h
#define OptionsWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/button.h>
#include <gtkmm/table.h>
#include <gtkmm/entry.h>
#include <gtkmm/alignment.h>
#include <gtkmm/box.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/EntryBin.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/Notebook.h"
#include "LiveSupport/Widgets/WhiteWindow.h"
#include "LiveSupport/Widgets/ScrolledWindow.h"

#include "GLiveSupport.h"
#include "MasterPanelUserInfoWidget.h"

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
 *  +------------(Cancel)-(Apply)-(OK)--+
 *  </code></pre>
 *
 *  @author $Author$
 *  @version $Revision$
 */
class OptionsWindow : public WhiteWindow, public LocalizedObject
{
    private:
        /**
         *  The notepad holding the different sections.
         */
        Notebook                  * mainNotebook;

        /**
         *  The button box.
         */
        Gtk::ButtonBox            * buttonBox;

        /**
         *  The Cancel button.
         */
        Gtk::Button               * cancelButton;

        /**
         *  The Apply button.
         */
        Gtk::Button               * applyButton;

        /**
         *  The OK button.
         */
        Gtk::Button               * okButton;

        /**
         *  The entry field for the cue player device's name.
         */
        EntryBin                  * cuePlayerEntry;

        /**
         *  The entry field for the output player device's name.
         */
        EntryBin                  * outputPlayerEntry;

        /**
         *  The gLiveSupport object, handling the logic of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  Signals if any changes have been made.
         */
        bool                        isChanged;

        /**
         *  Construct the "About" section.
         *
         *  @return a pointer to the new box (already Gtk::manage()'ed)
         */
        Gtk::VBox*
        constructAboutSection(void)                         throw ();

        /**
         *  Construct the "Sound" section.
         *
         *  @return a pointer to the new box (already Gtk::manage()'ed)
         */
        Gtk::VBox*
        constructSoundSection(void)                         throw ();


    protected:
        /**
         *  Event handler for the Cancel button.
         */
        virtual void
        onCancelButtonClicked(void)                         throw ();

        /**
         *  Event handler for the Apply button.
         */
        virtual void
        onApplyButtonClicked(void)                          throw ();

        /**
         *  Event handler for the OK button.
         */
        virtual void
        onOkButtonClicked(void)                             throw ();

        /**
         *  Event handler for the Close button.
         *
         *  @param  needConfirm     if true, we check if changes has been
         *                          made to the input fields, and if yes, then
         *                          a "save changes?" dialog is displayed
         *  @see    WhiteWindow::onCloseButtonClicked()
         */
        virtual void
        onCloseButtonClicked(bool   needConfirm = true)     throw ();

    
    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the gLiveSupport object, handling the
         *         logic of the application
         *  @param bundle the resource bundle holding localized resources
         */
        OptionsWindow(Ptr<GLiveSupport>::Ref     gLiveSupport,
                      Ptr<ResourceBundle>::Ref   bundle)
                                                            throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~OptionsWindow(void)                                throw ()
        {
        }

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // OptionsWindow_h

