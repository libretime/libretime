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
#ifndef MessageWindow_h
#define MessageWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/box.h>
#include <gtkmm/label.h>

#include "LiveSupport/Core/Ptr.h"

#include "LiveSupport/Widgets/WhiteWindow.h"

namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A message window, displaying a single line of message, with an OK
 *  button.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class MessageWindow : public WhiteWindow
{
    protected:
        /**
         *  The vertical box holding the message and the button.
         */
        Gtk::Box                  * layout;

        /**
         *  The message.
         */
        Gtk::Label                * messageLabel;

        /**
         *  The OK button.
         */
        Button                    * okButton;

        /**
         *  The event handler for the OK button clicked.
         */
        virtual void
        onOkButtonClicked(void)                             throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param message the message to display in the window
         */
        MessageWindow(Ptr<Glib::ustring>::Ref   message)        throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~MessageWindow(void)                                throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // MessageWindow_h

