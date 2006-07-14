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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/RestoreBackupWindow.h $

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
#include "LiveSupport/Widgets/Button.h"
#include "GuiWindow.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A pop-up window displaying the progress of a restore backup task.
 *
 *  @author $Author: fgerlits $
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
         *  The cancel button.
         */
        Button *                                cancelButton;

        /**
         *  The OK button.
         */
        Button *                                okButton;
        
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
        
        /**
         *  Hide the window.
         *  
         *  This overrides GuiWindow::on_hide(), and adds a call to
         *  restoreBackupClose() before calling the parent GuiWindow's
         *  on_hide() function.
         */
        virtual void
        on_hide(void)                                               throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         *  @param windowOpenerButton   the button which was pressed to open
         *                              this window.
         */
        RestoreBackupWindow(Ptr<GLiveSupport>::Ref          gLiveSupport,
                            Ptr<ResourceBundle>::Ref        bundle,
                            Ptr<const Glib::ustring>::Ref   fileName)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~RestoreBackupWindow(void)                                  throw ()
        {
        }

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // RestoreBackupWindow_h

