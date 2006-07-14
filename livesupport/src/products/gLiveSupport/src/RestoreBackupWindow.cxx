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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/RestoreBackupWindow.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include "RestoreBackupWindow.h"


using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

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
                                Ptr<GLiveSupport>::Ref          gLiveSupport,
                                Ptr<ResourceBundle>::Ref        bundle,
                                Ptr<const Glib::ustring>::Ref   fileName)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle,
                      ""),
            fileName(fileName),
            currentState(AsyncState::pendingState)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    messageLabel        = Gtk::manage(new Gtk::Label());
    try {
        set_title(*getResourceUstring("windowTitle"));
        cancelButton    = Gtk::manage(wf->createButton(
                                *gLiveSupport->getResourceUstring(
                                                        "cancelButtonLabel")));
        okButton        = Gtk::manage(wf->createButton(
                                *gLiveSupport->getResourceUstring(
                                                        "okButtonLabel")));
        
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    // pack the widgets
    Gtk::Box *          messageBox = Gtk::manage(new Gtk::HBox());
    messageBox->pack_start(*messageLabel, Gtk::PACK_EXPAND_WIDGET, 10);
    
    Gtk::ButtonBox *    buttonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
    buttonBox->pack_start(*cancelButton);
    buttonBox->pack_start(*okButton);
    
    Gtk::Box *          layout = Gtk::manage(new Gtk::VBox());
    layout->pack_start(*messageBox, Gtk::PACK_EXPAND_PADDING, 10);
    layout->pack_start(*buttonBox,  Gtk::PACK_SHRINK,         0);
    
    add(*layout);
    
    // set widget properties
    messageLabel->set_justify(Gtk::JUSTIFY_CENTER);
    okButton->set_sensitive(false);
    
    //connect callbacks
    cancelButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &RestoreBackupWindow::onCancelButtonClicked));
    okButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &RestoreBackupWindow::onOkButtonClicked));

    // start the restore backup operation
    restoreBackupOpen();
    
    // set name, size, etc. and show the widgets (not the window itself yet)
    set_name("restoreBackupWindow");
    show_all_children();
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
    try {
        setLabelText(*getResourceUstring(messageKey));
        
    } catch (std::invalid_argument &e) {
        messageLabel->set_text(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Display a localized message in the window, with one argument.
 *----------------------------------------------------------------------------*/
inline void
RestoreBackupWindow :: displayMessage(const Glib::ustring &     messageKey,
                                      const Glib::ustring &     argument)
                                                                    throw ()
{
    try {
        setLabelText(*formatMessage(messageKey, argument));
        
    } catch (std::invalid_argument &e) {
        messageLabel->set_text(e.what());
    }
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
    Ptr<SessionId>::Ref     sessionId   = gLiveSupport->getSessionId();

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
        Ptr<SessionId>::Ref     sessionId   = gLiveSupport->getSessionId();
        
        try {
            storage->restoreBackupClose(*token);
            
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
 *  Detach the timer when the window is hidden.
 *----------------------------------------------------------------------------*/
void
RestoreBackupWindow :: on_hide(void)                                throw ()
{
    restoreBackupClose();
    GuiWindow::on_hide();
}

