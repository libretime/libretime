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
#ifndef LoginWindow_h
#define LoginWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <unicode/resbund.h>
#include <gtkmm.h>
#include <libglademm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, handling user login.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class LoginWindow : public LocalizedObject
{
    private:

        /**
         *  The Glade object, containing the visual design.
         */
        Glib::RefPtr<Gnome::Glade::Xml>     glade;

        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref  gLiveSupport;

        /**
         *  The window itself.
         */
        Gtk::Dialog *           loginWindow;

        /**
         *  The user name text entry area.
         */
        Gtk::Entry *            userNameEntry;

        /**
         *  The password text entry area.
         */
        Gtk::Entry *            passwordEntry;

        /**
         *  The drop-down list to select the desired language.
         */
        ComboBoxText *          languageEntry;

        /**
         *  The status bar.
         */
        Gtk::Label *            statusBar;

        /**
         *  The user name text entered by the user.
         */
        Ptr<Glib::ustring>::Ref     userNameText;

        /**
         *  The password text entered by the user.
         */
        Ptr<Glib::ustring>::Ref     passwordText;

        /**
         *  The locale / language selected by the user.
         */
        Ptr<std::string>::Ref       selectedLocale;

        /**
         *  Flag to show that the user has successfully logged in.
         */
        bool                        loggedIn;

        /**
         *  Signal handler for the Enter key pressed in the user name entry.
         */
        virtual void
        onUserNameEntryActivated(void)                      throw ();

        /**
         *  Signal handler for the Enter key pressed in the password entry.
         */
        virtual void
        onPasswordEntryActivated(void)                      throw ();

        /**
         *  Signal handler for the ok button clicked.
         */
        virtual void
        onOkButtonClicked(void)                             throw ();

        /**
         *  Signal handler for the cancel button clicked.
         */
        virtual void
        onCancelButtonClicked(void)                         throw ();

        /**
         *  Get the password entered by the user.
         *
         *  @return the password entered by the user.
         */
        Ptr<const Glib::ustring>::Ref
        getPassword(void) const                             throw ()
        {
            return passwordText;
        }

    public:

        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         *  @param  gladeDir        the directory where the glade file is.
         */
        LoginWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                    Ptr<ResourceBundle>::Ref    bundle,
                    const Glib::ustring &       gladeDir)
                                                            throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~LoginWindow(void)                                  throw ();

        /**
         *  Get the login entered by the user.
         *
         *  @return the login entered by the user.
         */
        Ptr<const Glib::ustring>::Ref
        getLogin(void) const                                throw ()
        {
            return userNameText;
        }

        /**
         *  Show the window, and return whether the login was successful.
         *
         *  @return true if the login was successful.
         */
        bool
        run(void)                                           throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LoginWindow_h

