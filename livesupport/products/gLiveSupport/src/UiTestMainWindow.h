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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/UiTestMainWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef UiTestMainWindow_h
#define UiTestMainWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/button.h>
#include <gtkmm/buttonbox.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, enabling interactive testing of UI components.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class UiTestMainWindow : public Gtk::Window
{
    protected:
        /**
         *  The layout used in the window.
         */
        Ptr<Gtk::VButtonBox>::Ref   layout;

        /**
         *  The to quit the applicaiton.
         */
        Ptr<Gtk::Button>::Ref       quitButton;

        /**
         *  The button invoking the LoginWindow.
         */
        Ptr<Gtk::Button>::Ref       loginButton;

        /**
         *  Signal handler for the quit button clicked.
         */
        virtual void
        onQuitButtonClicked(void)                           throw ();

        /**
         *  Signal handler for the login button clicked.
         */
        virtual void
        onLoginButtonClicked(void)                          throw ();


    public:
        /**
         *  Constructor.
         */
        UiTestMainWindow(void)                              throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~UiTestMainWindow(void)                             throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // UiTestMainWindow_h

