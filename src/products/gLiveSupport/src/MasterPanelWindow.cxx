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

#include <iostream>
#include <unicode/msgfmt.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "LoginWindow.h"

#include "MasterPanelWindow.h"


using namespace LiveSupport;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "masterPanelWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName   = "MasterPanelWindow.glade";

/*------------------------------------------------------------------------------
 *  The name of the application, shown on the task bar.
 *----------------------------------------------------------------------------*/
const Glib::ustring     applicationTitleSuffix = " - Campcaster";

/*------------------------------------------------------------------------------
 *  Number of times per second that onUpdateTime() is called.
 *  It's a good idea to make this a divisor of 1000.
 *----------------------------------------------------------------------------*/
const int               updateTimeConstant = 20;

/*------------------------------------------------------------------------------
 *  The delay between two checks on the progress of an asynchronous method
 *  (in seconds).
 *----------------------------------------------------------------------------*/
const int               asyncUpdateFrequency = 10;

/**
 *  The delay between two RDS updates (in seconds).
 */
const int               rdsUpdateFrequency = 10;

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
MasterPanelWindow :: MasterPanelWindow (void)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName),
            userIsLoggedIn(false)                          
{
    // load the station logo image
    Gtk::Image *        stationLogoImage;
    glade->get_widget("stationLogoImage1", stationLogoImage);
    stationLogoImage->set(gLiveSupport->getStationLogoPixbuf());

    // set up the time label
    glade->get_widget("timeLabel1", timeLabel);
    Pango::Attribute    fontDescriptionAttribute = 
                            Pango::Attribute::create_attr_font_desc(
                                Pango::FontDescription(
                                    "Bitstream Vera Sans Bold 20"));
    fontDescriptionAttribute.set_start_index(0);
    fontDescriptionAttribute.set_end_index(10);
    Pango::AttrList     timeLabelAttributes;
    timeLabelAttributes.insert(fontDescriptionAttribute);
    timeLabel->set_attributes(timeLabelAttributes);

    // register the signal handlers for the main window
    glade->get_widget("mainWindow1", mainWindow);
    mainWindow->signal_key_press_event().connect(sigc::mem_fun(
                                        *this,
                                        &MasterPanelWindow::onKeyPressed));

    // create the Now Playing widget
    Gtk::Box *      nowPlayingBox;
    glade->get_widget("nowPlayingWidget1", nowPlayingBox);
    nowPlayingWidget.reset(new NowPlaying(this));

    // get a reference for the window-opener buttons
    glade->get_widget("liveModeButton1", liveModeButton);
    glade->get_widget("uploadFileButton1", uploadFileButton);
    glade->get_widget("scratchpadButton1", scratchpadButton);
    glade->get_widget("playlistButton1", playlistButton);
    glade->get_widget("schedulerButton1", schedulerButton);
    glade->get_widget("searchButton1", searchButton);
    glade->get_widget("optionsButton1", optionsButton);

    // get a reference for some other widgets
    glade->get_widget("mainButtonBox1", mainButtonBox);
    glade->get_widget("userInfoLabel1", userInfoLabel);
    glade->get_widget("loginButton1", loginButton);

    // bind events to the buttons
    liveModeButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onLiveModeButtonClicked));
    uploadFileButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onUploadFileButtonClicked));
    scratchpadButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onScratchpadButtonClicked));
    playlistButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onPlaylistButtonClicked));
    schedulerButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onSchedulerButtonClicked));
    searchButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onSearchButtonClicked));
    optionsButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onOptionsButtonClicked));

    loginButton->signal_clicked().connect(sigc::mem_fun(
                                *this,
                                &MasterPanelWindow::onLoginButtonClicked));

    // set the size and location of the window, according to the screen size
    Glib::RefPtr<Gdk::Screen>   screen = mainWindow->get_screen();
    mainWindow->set_default_size(screen->get_width(), -1);
    mainWindow->move(0, 0);

    // show what's there to see
    showAnonymousUI();
    updateUserInfo();

    // set the timer, that will update timeLabel
    setTimer();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
MasterPanelWindow :: ~MasterPanelWindow (void)                      throw ()
{
    resetTimer();
    gLiveSupport->stopOutputAudio();
    gLiveSupport->stopCueAudio();
}


/*------------------------------------------------------------------------------
 *  Change the language of the panel
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: changeLanguage (void)
                                                                    throw ()
{
    Ptr<ResourceBundle>::Ref    newBundle = gLiveSupport->getBundle(
                                                                bundleName);
    setBundle(newBundle);
    nowPlayingWidget->changeLanguage();

    setTitle(getResourceUstring("windowTitle"));

    liveModeButton->set_label(*getResourceUstring(
                                        "liveModeButtonLabel"));
    uploadFileButton->set_label(*getResourceUstring(
                                        "uploadFileButtonLabel"));
    scratchpadButton->set_label(*getResourceUstring(
                                        "scratchpadButtonLabel"));
    playlistButton->set_label(*getResourceUstring(
                                        "playlistButtonLabel"));
    schedulerButton->set_label(*getResourceUstring(
                                        "schedulerButtonLabel"));
    searchButton->set_label(*getResourceUstring(
                                        "searchButtonLabel"));
    optionsButton->set_label(*getResourceUstring(
                                        "optionsButtonLabel"));

    updateUserInfo();

    if (!gLiveSupport->isStorageAvailable()) {
        // gray out all the buttons except Options
        liveModeButton->set_sensitive(false);
        uploadFileButton->set_sensitive(false);
        scratchpadButton->set_sensitive(false);
        playlistButton->set_sensitive(false);
        schedulerButton->set_sensitive(false);
        searchButton->set_sensitive(false);
    }
}


/*------------------------------------------------------------------------------
 *  Set the timer
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: setTimer (void)                                throw ()
{
    sigc::slot<bool>    slot = sigc::bind(sigc::mem_fun(*this,
                                            &MasterPanelWindow::onUpdateTime),
                                          0);

    // set the timer to activate every 1/somethingth of a second
    timer.reset(new sigc::connection(
            Glib::signal_timeout().connect(slot, 1000/updateTimeConstant)));
}


/*------------------------------------------------------------------------------
 *  Clear the timer
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: resetTimer (void)                              throw ()
{
    timer->disconnect();
    timer.reset();
}


/*------------------------------------------------------------------------------
 *  Update the timeLabel display, with the current time
 *----------------------------------------------------------------------------*/
bool
MasterPanelWindow :: onUpdateTime (int   dummy)                     throw ()
{
    Ptr<const ptime>::Ref   now;

    if (gLiveSupport->isSchedulerAvailable()) {
        try {
            now = gLiveSupport->getScheduler()->getSchedulerTime();
        } catch (XmlRpcException &e) {
            std::cerr << "Scheduler time is not available; "
                      << "switching to local time." << std::endl;
            gLiveSupport->checkSchedulerClient();
        }
    } else {
        now = TimeConversion::now();
    }

    if (now.get()) {
        time_duration           dayTime = now->time_of_day();
        // get the time of day, only up to a second precision
        time_duration           dayTimeSec(dayTime.hours(),
                                           dayTime.minutes(),
                                           dayTime.seconds(),
                                           0);

        timeLabel->set_text(to_simple_string(dayTimeSec));
    }

    nowPlayingWidget->onUpdateTime();
    
    // check on the progress of the async methods
    static int      backupCounter = 0;
    if (backupCounter == 0) {
        if (optionsWindow) {
            Ptr<BackupList>::Ref    backupList = optionsWindow->getBackupList();
            if (backupList) {
                backupList->updateSilently();
            }
        }
        
        if (searchWindow) {
            searchWindow->onTimer();
        }
    }
    if (++backupCounter == updateTimeConstant * asyncUpdateFrequency) {
        backupCounter = 0;
    }
    
    // refresh all windows
    gLiveSupport->runMainLoop();
    
    // refresh the RDS display
    static int      rdsCounter = 0;
    if (rdsCounter == 0) {
        gLiveSupport->updateRds();
    }
    if (++rdsCounter == updateTimeConstant * rdsUpdateFrequency) {
        rdsCounter = 0;
    }
    
    return true;
}


/*------------------------------------------------------------------------------
 *  The event when the Live Mode button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateLiveModeWindow (Ptr<Playable>::Ref    playable)
                                                                    throw ()
{
    if (!liveModeWindow.get()) {
        liveModeWindow.reset(new LiveModeWindow(liveModeButton));
        gLiveSupport->loadWindowContents(liveModeWindow);
    }
    
    liveModeWindow->show();
    liveModeWindow->getWindow()->present();
    
    if (playable) {
        liveModeWindow->addItem(playable);
    }
}


/*------------------------------------------------------------------------------
 *  The event when the upload file button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateUploadFileWindow (void)                  throw ()
{
    if (!uploadFileWindow.get()) {
        uploadFileWindow.reset(new UploadFileWindow(uploadFileButton));
    }

    uploadFileWindow->show();
    uploadFileWindow->getWindow()->present();
}


/*------------------------------------------------------------------------------
 *  Create the Scratchpad window.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: createScratchpadWindow (void)
                                                                    throw ()
{
    if (!scratchpadWindow.get()) {
        scratchpadWindow.reset(new ScratchpadWindow(scratchpadButton));
        gLiveSupport->loadWindowContents(scratchpadWindow);
    }
}


/*------------------------------------------------------------------------------
 *  The event when the Scratchpad button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateScratchpadWindow (Ptr<Playable>::Ref  playable)
                                                                    throw ()
{
    createScratchpadWindow();
    
    if (playable) {
        scratchpadWindow->addItem(playable);
    }
    
    scratchpadWindow->show();
    scratchpadWindow->getWindow()->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Playlist button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updatePlaylistWindow (void)                    throw ()
{
    if (!playlistWindow.get()) {
        playlistWindow.reset(new PlaylistWindow(playlistButton));
    }
    
    playlistWindow->showContents();
    
    playlistWindow->show();
    playlistWindow->getWindow()->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Scheduler button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateSchedulerWindow (
                        Ptr<boost::posix_time::ptime>::Ref time)
                                                                    throw ()
{
    if (!schedulerWindow.get()) {
        try {
            schedulerWindow.reset(new SchedulerWindow(schedulerButton));
            
        } catch (XmlRpcException &e) {
            std::cerr << e.what() << std::endl;
            return;
        }
    }
    
    if (time.get()) {
        schedulerWindow->setTime(time);
    }
    
    try {
        schedulerWindow->showContents();
    } catch (XmlRpcException &e) {
        std::cerr << e.what() << std::endl;
        return;
    }

    schedulerWindow->show();
    schedulerWindow->getWindow()->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Search button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateSearchWindow (void)                      throw ()
{
    if (!searchWindow.get()) {
        searchWindow.reset(new SearchWindow(searchButton));
    }
    
    searchWindow->show();
    searchWindow->getWindow()->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Options button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateOptionsWindow (void)                     throw ()
{
    if (!optionsWindow.get()) {
        optionsWindow.reset(new OptionsWindow(optionsButton));
        Ptr<BackupList>::Ref    backupList = optionsWindow->getBackupList();    
        if (backupList) {
            gLiveSupport->loadWindowContents(backupList.get());
        }
    }

    optionsWindow->show();
    optionsWindow->getWindow()->present();
}


/*------------------------------------------------------------------------------
 *  Show only the UI components that are visible when no one is logged in
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showAnonymousUI (void)                         throw ()
{
    mainButtonBox->hide();
    
    if (liveModeWindow.get()) {
        gLiveSupport->storeWindowContents(liveModeWindow);
        if (liveModeWindow->getWindow()->is_visible()) {
            liveModeWindow->hide();
        }
        // the Live Mode window is not destroyed at logout, unlike the others
    }
    if (uploadFileWindow.get()) {
        if (uploadFileWindow->getWindow()->is_visible()) {
            uploadFileWindow->hide();
        }
        uploadFileWindow.reset();
    }
    if (scratchpadWindow.get()) {
        gLiveSupport->storeWindowContents(scratchpadWindow);
        if (scratchpadWindow->getWindow()->is_visible()) {
            scratchpadWindow->hide();
        }
        scratchpadWindow.reset();
    }
    if (playlistWindow.get()) {
        if (playlistWindow->getWindow()->is_visible()) {
            playlistWindow->hide();
        }
        playlistWindow.reset();
    }
    if (schedulerWindow.get()) {
        if (schedulerWindow->getWindow()->is_visible()) {
            schedulerWindow->hide();
        }
        schedulerWindow.reset();
    }
    if (searchWindow.get()) {
        if (searchWindow->getWindow()->is_visible()) {
            searchWindow->hide();
        }
        searchWindow.reset();
    }
    if (optionsWindow.get()) {
        Ptr<BackupList>::Ref    backupList = optionsWindow->getBackupList();
        if (backupList) {
            gLiveSupport->storeWindowContents(backupList.get());
        }
        if (optionsWindow->getWindow()->is_visible()) {
            optionsWindow->hide();
        }
        optionsWindow.reset();
    }
}


/*------------------------------------------------------------------------------
 *  Cancel the playlist edited in the PlaylistWindow, if any.
 *----------------------------------------------------------------------------*/
bool
MasterPanelWindow :: cancelEditedPlaylist (void)                    throw ()
{
    if (playlistWindow) {
        return playlistWindow->cancelPlaylist();
    } else {
        return true;
    }
}


/*------------------------------------------------------------------------------
 *  Show the UI components that are visible to a specific user.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showLoggedInUI (void)                          throw ()
{
    mainButtonBox->show();
    
    if (!gLiveSupport->isStorageAvailable()) { 
        liveModeButton->set_sensitive(false);
        uploadFileButton->set_sensitive(false);
        scratchpadButton->set_sensitive(false);
        playlistButton->set_sensitive(false);
        searchButton->set_sensitive(false);
    }
    
    setSchedulerAvailable(gLiveSupport->isSchedulerAvailable());
    
    if (liveModeWindow) {
        liveModeWindow->updateStrings();
        if (liveModeWindow->isNotEmpty()) {
            liveModeWindow->show();
            liveModeWindow->getWindow()->present();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Get the next item from the top of the Live Mode window.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
MasterPanelWindow :: getNextItemToPlay (void)                       throw ()
{
    if (liveModeWindow) {
        return liveModeWindow->popTop();
    } else {
        Ptr<Playable>::Ref      nullPointer;
        return nullPointer;
    }
}


/*------------------------------------------------------------------------------
 *  Resize an image to fit in a box, preserving its aspect ratio.
 *----------------------------------------------------------------------------*/
void
LiveSupport::GLiveSupport::
resizeImage (Gtk::Image* image, int width, int height)              throw ()
{
    Glib::RefPtr<Gdk::Pixbuf>   pixbuf = image->get_pixbuf();
    int     imageWidth  = pixbuf->get_width();
    int     imageHeight = pixbuf->get_height();

    if (imageWidth > width || imageHeight > height) {
        if (imageWidth * height > imageHeight * width) {
            // image is wide: squash horizontally
            image->set(pixbuf->scale_simple(width,
                                            (imageHeight * width)/imageWidth,
                                            Gdk::INTERP_HYPER ));
        } else {
            // image is tall: squash vertically
            image->set(pixbuf->scale_simple((imageWidth * height)/imageHeight,
                                            height,
                                            Gdk::INTERP_HYPER ));
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for a key pressed.
 *----------------------------------------------------------------------------*/
bool
MasterPanelWindow :: onKeyPressed (GdkEventKey *    event)          throw ()
{
    if (event->type == GDK_KEY_PRESS) {
        KeyboardShortcut::Action    action = gLiveSupport->findAction(
                                                "masterPanelWindow",
                                                Gdk::ModifierType(event->state),
                                                event->keyval);
        switch (action) {
            case KeyboardShortcut::playAudio :

            case KeyboardShortcut::pauseAudio :
                                    nowPlayingWidget->onPlayAudio();
                                    return true;
            
            case KeyboardShortcut::stopAudio :
                                    nowPlayingWidget->onStopAudio();
                                    return true;
            
            case KeyboardShortcut::nextTrack :
                                    gLiveSupport->stopOutputAudio();
                                    gLiveSupport->onStop();
                                    return true;
            
            default :               break;
        }
    }
    
    return false;
}


/*------------------------------------------------------------------------------
 *  The event when the Search button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: uploadToHub (Ptr<Playable>::Ref     playable)
                                                                    throw ()
{
    if (!searchWindow.get()) {
        searchWindow.reset(new SearchWindow(searchButton));
    }
    
    searchWindow->uploadToHub(playable);
    
    searchWindow->show();
    searchWindow->getWindow()->present();
}


/*------------------------------------------------------------------------------
 *  Show or hide the Scheduler button.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: setSchedulerAvailable (bool  status)           throw ()
{
    if (status == false) {
        if (schedulerWindow && schedulerWindow->getWindow()->is_visible()) {
            schedulerWindow->hide();
        }
    }
    
    if (schedulerButton) {
        schedulerButton->set_sensitive(status);
    }
}


/*------------------------------------------------------------------------------
 *  Update the cue player displays to show a stopped state.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showCuePlayerStopped (void)                    throw ()
{
    if (scratchpadWindow) {
        scratchpadWindow->showCuePlayerStopped();
    }
    
    if (liveModeWindow) {
        liveModeWindow->showCuePlayerStopped();
    }
}


/*------------------------------------------------------------------------------
 *  Handle the event of the Login/Logout button being clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: onLoginButtonClicked (void)                    throw ()
{
    if (userIsLoggedIn) {
        logout();
    } else {
        login();
    }
}


/*------------------------------------------------------------------------------
 *  Let the user log in.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: login (void)                                   throw ()
{
    Ptr<LoginWindow>::Ref       loginWindow(new LoginWindow());
    userIsLoggedIn = loginWindow->run();

    if (userIsLoggedIn) {
        Ptr<const Glib::ustring>::Ref   loginName = loginWindow->getLogin();
        updateUserInfo(loginName);
        showLoggedInUI();

    } else {
        // TODO: display an "incorrect login" dialog
    }
}


/*------------------------------------------------------------------------------
 *  Let the user log out.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: logout (void)                                  throw ()
{
    bool userCanceledTheLogout = !gLiveSupport->logout();
    if (userCanceledTheLogout) {
        return;
    }

    userIsLoggedIn = false;
    updateUserInfo();
}


/*------------------------------------------------------------------------------
 *  Show the user info and the login button.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateUserInfo (Ptr<const Glib::ustring>::Ref   loginName)
                                                                    throw ()
{
    if (userIsLoggedIn) {
        Ptr<Glib::ustring>::Ref         logoutButtonLabel;
        Ptr<Glib::ustring>::Ref         loggedInMsg;

        logoutButtonLabel   = getResourceUstring("logoutButtonLabel");
        loggedInMsg         = formatMessage("loggedInMsg", *loginName);

        loginButton->set_label(*logoutButtonLabel);
        userInfoLabel->set_label(*loggedInMsg);

    } else {
        Ptr<Glib::ustring>::Ref         loginButtonLabel;
        Ptr<Glib::ustring>::Ref         notLoggedInMsg;

        loginButtonLabel = getResourceUstring("loginButtonLabel");
        notLoggedInMsg   = getResourceUstring("notLoggedInMsg");

        loginButton->set_label(*loginButtonLabel);
        userInfoLabel->set_label(*notLoggedInMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for when the user closes the master panel.
 *----------------------------------------------------------------------------*/
bool
MasterPanelWindow :: onDeleteEvent (GdkEventAny *    event)         throw ()
{
    Gtk::ResponseType   response = gLiveSupport->runNoYesDialog(
                                        *getResourceUstring("sureToExitMsg"));
    if (response != Gtk::RESPONSE_YES) {
        return true;
    }

    gLiveSupport->logout();
    gLiveSupport->stopOutputAudio();

    Ptr<OptionsContainer>::Ref  optionsContainer
                                = gLiveSupport->getOptionsContainer();
    if (optionsContainer && optionsContainer->isTouched()) {
        optionsContainer->writeToFile();
    }

    return false;
}

