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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/MasterPanelUserInfoWidget.h,v $

------------------------------------------------------------------------------*/
#ifndef MasterPanelUserInfoWidget_h
#define MasterPanelUserInfoWidget_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/button.h>
#include <gtkmm/table.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The user info widget of the master panel.
 *
 *  This widget handles login and login info display.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.4 $
 */
class MasterPanelUserInfoWidget : public Gtk::Table,
                                  public LocalizedObject
{
    protected:
        /**
         *  The login / logout button.
         */
        Widgets::Button           * logInOutButton;

        /**
         *  A label to display the currently logged in user.
         */
        Gtk::Label                * userInfoLabel;

        /**
         *  The gLiveSupport object, handling the logic of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The sigc connection object, that connects the button clicked
         *  event on the logInOutButton to either onLoginButtonClicked()
         *  or onLogoutButtonClicked().
         */
        sigc::connection            logInOutSignalConnection;

        /**
         *  Flag to indicate if the user is logged in or not.
         */
        bool                        loggedIn;

        /**
         *  The user id logged in as.
         */
        Ptr<const Glib::ustring>::Ref       login;

        /**
         *  Signal handler for the login button clicked.
         */
        virtual void
        onLoginButtonClicked(void)                          throw ();

        /**
         *  Signal handler for the logout button clicked.
         */
        virtual void
        onLogoutButtonClicked(void)                         throw ();

        /**
         *  Update the strings in the widget, including the localized strings.
         *
         *  @exception std::invalid_argument if some localized resources
         *             could not be attained.
         */
        void
        updateStrings(void)                     throw (std::invalid_argument);


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the gLiveSupport object, handling the
         *         logic of the application
         *  @param bundle the resource bundle holding localized resources
         */
        MasterPanelUserInfoWidget(Ptr<GLiveSupport>::Ref     gLiveSupport,
                                  Ptr<ResourceBundle>::Ref   bundle)
                                                                throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~MasterPanelUserInfoWidget(void)                             throw ();

        /**
         *  Change the user interface language of the application
         *  by providing a new resource bundle.
         *  This call assumes that only the MasterPanel is visilbe,
         *  and will only change the language of the currently open
         *  MasterPanel. No other open windows will be affected by
         *  this call, but subsequently opened windows are.
         *
         *  @param bundle the new resource bundle.
         */
        void
        changeLanguage(Ptr<ResourceBundle>::Ref     bundle)     throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // MasterPanelUserInfoWidget_h

