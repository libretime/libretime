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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/LoginWindow.h,v $

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

#include <gtkmm/window.h>
#include <gtkmm/button.h>
#include <gtkmm/label.h>
#include <gtkmm/entry.h>
#include <gtkmm/table.h>

#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, handling user login.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class LoginWindow : public Gtk::Window
{

    protected:
        /**
         *  The table, which provides the layout for the window.
         */
        Ptr<Gtk::Table>::Ref        table;

        /**
         *  The login label in the window.
         */
        Ptr<Gtk::Label>::Ref        loginLabel;

        /**
         *  The password label in the window.
         */
        Ptr<Gtk::Label>::Ref        passwordLabel;

        /**
         *  The login text entry area.
         */
        Ptr<Gtk::Entry>::Ref        loginEntry;

        /**
         *  The password text entry area.
         */
        Ptr<Gtk::Entry>::Ref        passwordEntry;

        /**
         *  The OK button.
         */
        Ptr<Gtk::Button>::Ref       okButton;

        /**
         *  The login text, that was entered by the user.
         */
        Ptr<std::string>::Ref       loginText;

        /**
         *  The password text, that was entered by the user.
         */
        Ptr<std::string>::Ref       passwordText;

        /**
         *  Signal handler for the ok button clicked.
         */
        virtual void
        onOkButtonClicked(void)                             throw ();


    public:
        /**
         *  Constructor.
         */
        LoginWindow(void)                                   throw ();

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
        Ptr<const std::string>::Ref
        getLogin(void) const                                throw ()
        {
            return loginText;
        }

        /**
         *  Get the password entered by the user.
         *
         *  @return the password entered by the user.
         */
        Ptr<const std::string>::Ref
        getPassword(void) const                             throw ()
        {
            return passwordText;
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LoginWindow_h

