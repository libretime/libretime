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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/DialogWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef DialogWindow_h
#define DialogWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/box.h>
#include <gtkmm/label.h>
#include <gtkmm/main.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

#include "LiveSupport/Widgets/WhiteWindow.h"

namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A dialog window, displaying a single line of message, 
 *  with a pair of buttons: Yes and No, or OK and Cancel etc.
 *
 *  The constructor is called with a message, a list of ButtonType values
 *  (e.g. cancelButton|okButton), and a resource bundle.  The resource bundle
 *  is expected to contain keys named cancelButtonLabel, noButtonLabel, 
 *  yesButtonLabel and okButtonLabel.
 *
 *  The return value of the run() method is a single ButtonType value.
 *  The DialogWindow object is not destroyed when it returns from run();
 *  it is the responsibility of the caller to delete it (or it can be
 *  reused a few times first).
 *
 *  @author $Author$
 *  @version $Revision$
 */
class DialogWindow : public WhiteWindow,
                     public LocalizedObject
{
    public:
        /**
         *  The types of possible buttons.
         */
        typedef enum  { cancelButton = 1,
                        noButton     = 2,
                        yesButton    = 4,
                        okButton     = 8 }      ButtonType;


    private:
        /**
         *  The type of the button clicked.
         */
        ButtonType                  buttonClicked;


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
         *  The Cancel button.
         */
        Button                    * cancelDialogButton;

        /**
         *  The No button.
         */
        Button                    * noDialogButton;

        /**
         *  The Yes button.
         */
        Button                    * yesDialogButton;

        /**
         *  The OK button.
         */
        Button                    * okDialogButton;

        /**
         *  The event handler for the Cancel button clicked.
         */
        virtual void
        onCancelButtonClicked(void)                         throw ();

        /**
         *  The event handler for the No button clicked.
         */
        virtual void
        onNoButtonClicked(void)                             throw ();

        /**
         *  The event handler for the Yes button clicked.
         */
        virtual void
        onYesButtonClicked(void)                            throw ();

        /**
         *  The event handler for the OK button clicked.
         */
        virtual void
        onOkButtonClicked(void)                             throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param message  the message to display in the window
         *  @param buttonTypes  a list of button types, e.g., 
         *                      <code>noButton|yesButton</code>
         *  @param bundle   a resource bundle containing the localized
         *                  button labels
         */
        DialogWindow(Ptr<Glib::ustring>::Ref    message,
                     int                        buttonTypes,
                     Ptr<ResourceBundle>::Ref   bundle)     throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~DialogWindow(void)                                 throw ();

        /**
         *  Run the window and return the button pressed.
         */
        virtual ButtonType
        run(void)                                           throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // DialogWindow_h

