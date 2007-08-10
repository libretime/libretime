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
#ifndef RestoreBackupWindow_h
#define RestoreBackupWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/StorageClient/StorageClientInterface.h"
#include "GLiveSupport.h"

#include "GuiWindow.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A pop-up window displaying the progress of a restore backup task.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class RestoreBackupWindow : public GuiWindow
{
    private:

        /**
         *  The label holding the current message displayed by the window.
         */
        Gtk::Label *                            messageLabel;

        /**
         *  The OK button.
         */
        Gtk::Button *                           okButton;
        
        /**
         *  The file name of the backup file to be uploaded.
         */
        Ptr<const Glib::ustring>::Ref           fileName;
        
        /**
         *  The current state of the upload task.
         */
        AsyncState                              currentState;

        /**
         *  The token of the upload task.
         */
        Ptr<const Glib::ustring>::Ref           token;
        
        /**
         *  The connection object to the timer signal.
         */
        Ptr<sigc::connection>::Ref              timer;


    protected:

        /**
         *  Event handler for the cancel button being clicked.
         */
        virtual void
        onCancelButtonClicked(void)                                 throw ();

        /**
         *  Event handler for the ok button being clicked.
         */
        virtual void
        onOkButtonClicked(void)                                     throw ();

        /**
         *  Event handler for closing the window from the window manager.
         *  Calls StorageClientInterface::restoreBackupClose().
         *
         *  Overrides GuiWindow::onDeleteEvent().
         *
         *  @param  event   attributes of the event.
         *  @return true if handled the event, false to continue deleting.
         */
        virtual bool
        onDeleteEvent(GdkEventAny *     event)                      throw ();

        /**
         *  Set the text of the label.
         *
         *  @param  text    the new text of the label.
         */
        virtual void
        setLabelText(const Glib::ustring &  text)                   throw ()
        {
            messageLabel->set_text(text);
        }
        
        /**
         *  Display a localized message in the window.
         *
         *  @param  messageKey  the localization key of the message.
         */
        virtual void
        displayMessage(const Glib::ustring &     messageKey)        throw ();
        
        /**
         *  Display a localized message in the window, with one argument.
         *
         *  @param  messageKey  the localization key of the message.
         *  @param  argument    the string to substitute for {0}.
         */
        virtual void
        displayMessage(const Glib::ustring &     messageKey,
                       const Glib::ustring &     argument)          throw ();

        /**
         *  Signal an error.
         *  Prints the error message, sets the internal state to failedState,
         *  and re-sensitizes the OK button.
         *
         *  @param  errorMessage    the error message to be displayed.
         */
        virtual void
        signalError(const Glib::ustring &   errorMessage)           throw ();
        
        /**
         *  Call the restoreBackupOpen function in the storage client.
         */
        virtual void
        restoreBackupOpen(void)                                     throw ();

        /**
         *  Call the restoreBackupCheck function in the storage client.
         */
        virtual void
        restoreBackupCheck(void)                                    throw ();

        /**
         *  Call the restoreBackupClose function in the storage client.
         */
        virtual void
        restoreBackupClose(void)                                    throw ();
        
        /**
         *  The function which is called regularly when the timer is set.
         *
         *  This is just a wrapper for restoreBackupCheck(), with a bool
         *  return value (always true), because Glib::signal_timeout expects
         *  a sigc::slot0<bool>.
         *  TODO: figure out what this return value does.
         */
        virtual bool
        onUpdateTime(void)                                          throw ();
        
        /**
         *  Connect the timer.
         */
        virtual void
        setTimer(void)                                              throw ();
        
        /**
         *  Disconnect the timer.
         */
        virtual void
        resetTimer(void)                                            throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param  fileName        the file name of the backup to be restored.
         */
        RestoreBackupWindow(Ptr<const Glib::ustring>::Ref       fileName)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~RestoreBackupWindow(void)                                  throw ()
        {
        }

        /**
         *  Close the connection and hide the window.
         *  Calls StorageClientInterface::restoreBackupClose().
         *
         *  Overrides GuiWindow::hide().
         */
        virtual void
        hide(void)                                                  throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // RestoreBackupWindow_h

