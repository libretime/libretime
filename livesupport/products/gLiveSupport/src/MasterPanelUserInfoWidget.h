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
    Version  : $Revision: 1.1 $
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

#include "GtkLocalizedObject.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

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
 *  @version $Revision: 1.1 $
 */
class MasterPanelUserInfoWidget : public Gtk::Table,
                                  public GtkLocalizedObject
{
    protected:
        /**
         *  The login / logout button.
         */
        Ptr<Gtk::Button>::Ref       logInOutButton;

        /**
         *  A label to display the currently logged in user.
         */
        Ptr<Gtk::Label>::Ref        userInfoLabel;

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
         *  Signal handler for the login button clicked.
         */
        virtual void
        onLoginButtonClicked(void)                          throw ();

        /**
         *  Signal handler for the logout button clicked.
         */
        virtual void
        onLogoutButtonClicked(void)                         throw ();


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

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // MasterPanelUserInfoWidget_h

