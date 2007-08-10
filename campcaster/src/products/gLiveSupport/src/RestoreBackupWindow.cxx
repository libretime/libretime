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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "RestoreBackupWindow.h"


using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {
/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "restoreBackupWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "RestoreBackupWindow.glade";

/*------------------------------------------------------------------------------
 *  The interval between two calls to restoreBackupCheck(), in milliseconds.
 *----------------------------------------------------------------------------*/
const unsigned int      timerInterval = 10000;

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
RestoreBackupWindow :: RestoreBackupWindow (
                            Ptr<const Glib::ustring>::Ref       fileName)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName),
            fileName(fileName),
            currentState(AsyncState::pendingState)
{
    Gtk::Button *       cancelButton;
    glade->get_widget("restoreBackupMessageLabel1", messageLabel);
    glade->get_widget("restoreBackupCancelButton1", cancelButton);
    glade->get_widget("restoreBackupOkButton1", okButton);
    
    cancelButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &RestoreBackupWindow::onCancelButtonClicked));
    okButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &RestoreBackupWindow::onOkButtonClicked));

    restoreBackupOpen();
}


/*------------------------------------------------------------------------------
 *  The event when the cancel button has been clicked.
 *----------------------------------------------------------------------------*/
void
RestoreBackupWindow :: onCancelButtonClicked(void)                  throw ()
{
    // TODO: add confirmation dialog
    hide();
}


/*------------------------------------------------------------------------------
 *  The event when the OK button has been clicked.
 *----------------------------------------------------------------------------*/
void
RestoreBackupWindow :: onOkButtonClicked(void)                      throw ()
{
    hide();
}


/*------------------------------------------------------------------------------
 *  Display a localized message in the window.
 *----------------------------------------------------------------------------*/
inline void
RestoreBackupWindow :: displayMessage(const Glib::ustring &     messageKey)
                                                                    throw ()
{
    setLabelText(*getResourceUstring(messageKey));
}


/*------------------------------------------------------------------------------
 *  Display a localized message in the window, with one argument.
 *----------------------------------------------------------------------------*/
inline void
RestoreBackupWindow :: displayMessage(const Glib::ustring &     messageKey,
                                      const Glib::ustring &     argument)
                                                                    throw ()
{
    setLabelText(*formatMessage(messageKey, argument));
}


/*------------------------------------------------------------------------------
 *  Display an error message in the window.
 *----------------------------------------------------------------------------*/
inline void
RestoreBackupWindow :: signalError(const Glib::ustring &    errorMessage)
                                                                    throw ()
{
    currentState = AsyncState::failedState;
    displayMessage("errorMessage", errorMessage);
    restoreBackupClose();
}


/*------------------------------------------------------------------------------
 *  Start the upload.
 *----------------------------------------------------------------------------*/
void
RestoreBackupWindow :: restoreBackupOpen(void)                      throw ()
{
    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    
    try {
        token = storage->restoreBackupOpen(sessionId, fileName);
        
    } catch (XmlRpcException &e) {
        signalError(e.what());
        return;
    }
    
    currentState = AsyncState::pendingState;
    displayMessage("pendingMessage", *fileName);
    setTimer();
}


/*------------------------------------------------------------------------------
 *  Check on the upload.
 *----------------------------------------------------------------------------*/
void
RestoreBackupWindow :: restoreBackupCheck(void)                     throw ()
{
    Ptr<StorageClientInterface>::Ref 
                            storage     = gLiveSupport->getStorageClient();

    Ptr<const Glib::ustring>::Ref           errorMessage;
    try {
        currentState = storage->restoreBackupCheck(*token, errorMessage);
        
    } catch (XmlRpcException &e) {
        signalError(e.what());
        return;
    }
    
    if (currentState == AsyncState::finishedState) {
        displayMessage("finishedMessage");
        restoreBackupClose();
        
    } else if (currentState == AsyncState::failedState) {
        displayMessage("errorMessage",
                        *errorMessage);
        restoreBackupClose();
    }
}


/*------------------------------------------------------------------------------
 *  Close the upload.
 *----------------------------------------------------------------------------*/
void
RestoreBackupWindow :: restoreBackupClose(void)                     throw ()
{
    if (token) {
        Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
        
        try {
            storage->restoreBackupClose(*token);
            token.reset();
            
        } catch (XmlRpcException &e) {
            signalError(e.what());
            return;
        }
    }
    
    resetTimer();
    okButton->set_sensitive(true);
}


/*------------------------------------------------------------------------------
 *  The function which is called regularly when the timer is set.
 *----------------------------------------------------------------------------*/
bool
RestoreBackupWindow :: onUpdateTime(void)                           throw ()
{
    if (currentState == AsyncState::pendingState) {
        restoreBackupCheck();
    }
    return true;
}


/*------------------------------------------------------------------------------
 *  Connect the timer.
 *----------------------------------------------------------------------------*/
inline void
RestoreBackupWindow :: setTimer(void)                               throw ()
{
    timer.reset(new sigc::connection(Glib::signal_timeout().connect(
                            sigc::mem_fun(
                                *this,
                                &RestoreBackupWindow::onUpdateTime),
                            timerInterval)));
}


/*------------------------------------------------------------------------------
 *  Disconnect the timer.
 *----------------------------------------------------------------------------*/
inline void
RestoreBackupWindow :: resetTimer(void)                             throw ()
{
    timer->disconnect();
}


/*------------------------------------------------------------------------------
 *  Close the connection and hide the window.
 *----------------------------------------------------------------------------*/
void
RestoreBackupWindow :: hide(void)                                   throw ()
{
    restoreBackupClose();
    GuiWindow::hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for closing the window from the window manager.
 *----------------------------------------------------------------------------*/
bool
RestoreBackupWindow :: onDeleteEvent(GdkEventAny *     event)       throw ()
{
    restoreBackupClose();
    return false;
}

