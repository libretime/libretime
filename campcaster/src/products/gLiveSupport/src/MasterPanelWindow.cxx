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
#include <gtkmm/label.h>
#include <gtkmm/main.h>
#include <gdkmm/pixbuf.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Widgets/MasterPanelBin.h"

#include "MasterPanelWindow.h"


using namespace LiveSupport;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/**
 *  The name of the window, used by the keyboard shortcuts (or by the .gtkrc).
 */
const Glib::ustring     windowName = "masterPanelWindow";

/**
 *  The name of the application, shown on the task bar.
 */
const Glib::ustring     applicationTitleSuffix = " - Campcaster";

/**
 *  Number of times per second that onUpdateTime() is called.
 *  It's a good idea to make this a divisor of 1000.
 *  If you change this, then you must change NowPlaying::blinkingConstant, too.
 */
const int               updateTimeConstant = 20;

/**
 *  The delay between two checks on the progress of an asynchronous method
 *  (in seconds).
 */
const int               asyncUpdateFrequency = 10;

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
MasterPanelWindow :: MasterPanelWindow (Ptr<GLiveSupport>::Ref    gLiveSupport,
                                        Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
                        : LocalizedObject(bundle),
                          gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref widgetFactory = WidgetFactory::getInstance();

    radioLogoWidget = Gtk::manage(gLiveSupport->getStationLogoImage());
    resizeImage(radioLogoWidget, 120, 104);
    radioLogoWidget->set_size_request(120, 104);

    // set up the layout, which is a button box
    layout = Gtk::manage(new Gtk::Table());

    // set up the time label
    timeWidget = Gtk::manage(new Gtk::Label());
    Pango::Attribute    fontDescriptionAttribute = 
                            Pango::Attribute::create_attr_font_desc(
                                Pango::FontDescription(
                                    "Bitstream Vera Sans Bold 20"));
    fontDescriptionAttribute.set_start_index(0);
    fontDescriptionAttribute.set_end_index(10);
    Pango::AttrList     timeWidgetAttributes;
    timeWidgetAttributes.insert(fontDescriptionAttribute);
    timeWidget->set_attributes(timeWidgetAttributes);
    timeBin = Gtk::manage(widgetFactory->createBlueBin());
    timeBin->add(*timeWidget);
    timeBin->set_size_request(140, 104);

    // set up the now playing widget
    nowPlayingWidget = Gtk::manage(new NowPlaying(gLiveSupport, bundle));
    Gtk::Alignment *    nowPlayingAlignment = Gtk::manage(new Gtk::Alignment(
                                                        0.0, 0.7, 1.0, 0.0 ));
    nowPlayingAlignment->add(*nowPlayingWidget);
    nowPlayingBin = Gtk::manage(widgetFactory->createDarkBlueBin());
    nowPlayingBin->add(*nowPlayingAlignment);
    nowPlayingBin->set_size_request(-1, 104);

/*  temporarily disabled
    // set up the VU meter widget
    vuMeterWidget = Gtk::manage(new Gtk::Label(""));
    vuMeterBin = Gtk::manage(widgetFactory->createBlueBin());
    vuMeterBin->add(*vuMeterWidget);
    vuMeterBin->set_size_request(200, 40);
*/
    
/*  temporarily disabled
    // set up the next playing widget
    nextPlayingWidget = Gtk::manage(new Gtk::Label(""));
    nextPlayingBin = Gtk::manage(widgetFactory->createBlueBin());
    nextPlayingBin->add(*nextPlayingWidget);
    nextPlayingBin->set_size_request(200, 59);
*/

    // create the bottom bar
    bottomBar = Gtk::manage(new Gtk::HBox());
    buttonBar = Gtk::manage(new Gtk::Table());
    buttonBar->set_homogeneous(true);
    buttonBarAlignment = Gtk::manage(new Gtk::Alignment(Gtk::ALIGN_LEFT,
                                                        Gtk::ALIGN_CENTER,
                                                        0, 0));
    buttonBarAlignment->add(*buttonBar);
    userInfoWidget = Gtk::manage(new MasterPanelUserInfoWidget(gLiveSupport,
                                                               bundle));
    userInfoAlignment = Gtk::manage(new Gtk::Alignment(Gtk::ALIGN_RIGHT,
                                                       Gtk::ALIGN_CENTER,
                                                       0, 0));
    userInfoAlignment->add(*userInfoWidget);
    bottomBar->pack_start(*buttonBarAlignment, Gtk::PACK_EXPAND_WIDGET, 0);
    bottomBar->pack_start(*userInfoAlignment,  Gtk::PACK_EXPAND_WIDGET, 0);
    
    // a bit of extra vertical space above the buttons
    Gtk::HBox *     extraSpace = Gtk::manage(new Gtk::HBox());
    
    // set up the main window, and show everything
    // all the localized widgets were set up in changeLanguage()
    layout->set_border_width(5);
    layout->attach(*timeBin,            0, 1, 0, 2,
                    Gtk::SHRINK, Gtk::SHRINK,
                    0, 0);
    layout->attach(*nowPlayingBin,      1, 2, 0, 2,
                    Gtk::EXPAND|Gtk::FILL, Gtk::SHRINK,
                    5, 0);
//    layout->attach(*vuMeterBin,         2, 3, 0, 1,
//                    Gtk::SHRINK, Gtk::SHRINK,
//                    0, 0);
//    layout->attach(*nextPlayingBin,     2, 3, 1, 2,
//                    Gtk::SHRINK, Gtk::SHRINK,
//                    0, 0);
    layout->attach(*radioLogoWidget,    3, 4, 0, 2,
                    Gtk::SHRINK, Gtk::SHRINK,
                    5, 0);
    layout->attach(*extraSpace,         0, 4, 2, 3,
                    Gtk::EXPAND|Gtk::FILL, Gtk::EXPAND|Gtk::FILL,
                    0, 2);
    layout->attach(*bottomBar,          0, 4, 3, 4,
                    Gtk::EXPAND|Gtk::FILL, Gtk::EXPAND|Gtk::FILL,
                    0, 0);
    
    // add the bottom border
    MasterPanelBin *    bin = Gtk::manage(new MasterPanelBin());
    bin->add(*layout);
    this->add(*bin);

    // register the signal handler for keyboard key presses
    this->signal_key_press_event().connect(sigc::mem_fun(*this,
                                        &MasterPanelWindow::onKeyPressed));

    // set the background to white
    bgColor = Colors::getColor(Colors::White);
    modify_bg(Gtk::STATE_NORMAL, bgColor);

    // set the size and location of the window, according to the screen size
    Glib::RefPtr<Gdk::Screen>   screen = get_screen();
    int                         width  = screen->get_width();
    set_default_size(width, -1);
    move(0, 0);
    set_decorated(false);
    set_name(windowName);

    // set the localized resources
    liveModeButton           = 0;
    uploadFileButton         = 0;
    scratchpadButton         = 0;
    simplePlaylistMgmtButton = 0;
    schedulerButton          = 0;
    searchButton             = 0;
    optionsButton            = 0;
    changeLanguage(bundle);

    // show what's there to see
    showAnonymousUI();

    // set the timer, that will update timeWidget
    setTimer();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
MasterPanelWindow :: ~MasterPanelWindow (void)                        throw ()
{
    resetTimer();
    gLiveSupport->stopOutputAudio();
    gLiveSupport->stopCueAudio();
}


/*------------------------------------------------------------------------------
 *  Change the language of the panel
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: changeLanguage(Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
{
    setBundle(bundle);

    if (liveModeButton) {
        buttonBar->remove(*liveModeButton);
    }
    if (uploadFileButton) {
        buttonBar->remove(*uploadFileButton);
    }
    if (scratchpadButton) {
        buttonBar->remove(*scratchpadButton);
    }
    if (simplePlaylistMgmtButton) {
        buttonBar->remove(*simplePlaylistMgmtButton);
    }
    if (schedulerButton) {
        buttonBar->remove(*schedulerButton);
    }
    if (searchButton) {
        buttonBar->remove(*searchButton);
    }
    if (optionsButton) {
        buttonBar->remove(*optionsButton);
    }

    try {
        Ptr<Glib::ustring>::Ref     title = getResourceUstring(
                                                    windowName.c_str(),
                                                    "windowTitle");
        title->append(applicationTitleSuffix);
        set_title(*title);

        Ptr<WidgetFactory>::Ref wf = WidgetFactory::getInstance();

        liveModeButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("liveModeButtonLabel"),
                                WidgetConstants::radioButton));
        uploadFileButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("uploadFileButtonLabel"),
                                WidgetConstants::radioButton));
        scratchpadButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("scratchpadButtonLabel"),
                                WidgetConstants::radioButton));
        simplePlaylistMgmtButton = Gtk::manage(wf->createButton(
                        *getResourceUstring("simplePlaylistMgmtButtonLabel"),
                        WidgetConstants::radioButton));
        schedulerButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("schedulerButtonLabel"),
                                WidgetConstants::radioButton));
        searchButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("searchButtonLabel"),
                                WidgetConstants::radioButton));
        optionsButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("optionsButtonLabel"),
                                WidgetConstants::radioButton));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    userInfoWidget->changeLanguage(bundle);

    // re-attach the localized widgets to the layout
    buttonBar->attach(*liveModeButton,             0, 1, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      0, 0);
    buttonBar->attach(*uploadFileButton,           1, 2, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*scratchpadButton,           2, 3, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      0, 0);
    buttonBar->attach(*simplePlaylistMgmtButton,   3, 4, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*schedulerButton,            4, 5, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      0, 0);
    buttonBar->attach(*searchButton,               5, 6, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*optionsButton,              6, 7, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      0, 0);

    if (gLiveSupport->isStorageAvailable()) {
        // re-bind events to the buttons
        liveModeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &MasterPanelWindow::onLiveModeButtonClicked));
        uploadFileButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &MasterPanelWindow::onUploadFileButtonClicked));
        scratchpadButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &MasterPanelWindow::onScratchpadButtonClicked));
        simplePlaylistMgmtButton->signal_clicked().connect(
                sigc::mem_fun(*this,
                     &MasterPanelWindow::onSimplePlaylistMgmtButtonClicked));
        schedulerButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &MasterPanelWindow::onSchedulerButtonClicked));
        searchButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &MasterPanelWindow::onSearchButtonClicked));
    } else {
        // gray out all the buttons except Options
        liveModeButton->setDisabled(true);
        uploadFileButton->setDisabled(true);
        scratchpadButton->setDisabled(true);
        simplePlaylistMgmtButton->setDisabled(true);
        schedulerButton->setDisabled(true);
        searchButton->setDisabled(true);
    }

    optionsButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &MasterPanelWindow::onOptionsButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Set the timer
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: setTimer(void)                                  throw ()
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
MasterPanelWindow :: resetTimer(void)                                throw ()
{
    timer->disconnect();
    timer.reset();
}


/*------------------------------------------------------------------------------
 *  Update the timeWidget display, with the current time
 *----------------------------------------------------------------------------*/
bool
MasterPanelWindow :: onUpdateTime(int   dummy)                       throw ()
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

        timeWidget->set_text(to_simple_string(dayTimeSec));
    }

    nowPlayingWidget->onUpdateTime();
    
    // check on the progress of the async methods
    static int      backupCounter = 0;
    if (backupCounter++ == updateTimeConstant * asyncUpdateFrequency) {
        backupCounter = 0;
    }
    
    if (backupCounter == 0) {
        if (optionsWindow) {
            BackupList *    backupList    = optionsWindow->getBackupList();
            if (backupList) {
                backupList->updateSilently();
            }
        }
        
        if (searchWindow) {
            searchWindow->onTimer();
        }
    }
    
    // refresh all windows
    gLiveSupport->runMainLoop();
    
    // refresh the RDS display
    gLiveSupport->updateRds();
    
    return true;
}


/*------------------------------------------------------------------------------
 *  The event when the Live Mode button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateLiveModeWindow(Ptr<Playable>::Ref    playable)
                                                                    throw ()
{
    if (!liveModeWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("liveModeWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        liveModeWindow.reset(new LiveModeWindow(gLiveSupport,
                                                bundle,
                                                liveModeButton));
    }
    
    liveModeWindow->present();
    
    if (playable) {
        liveModeWindow->addItem(playable);
    }
}


/*------------------------------------------------------------------------------
 *  The event when the upload file button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateUploadFileWindow(void)                   throw ()
{
    if (!uploadFileWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("uploadFileWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        uploadFileWindow.reset(new UploadFileWindow(gLiveSupport,
                                                    bundle,
                                                    uploadFileButton));
    }

    uploadFileWindow->present();
}


/*------------------------------------------------------------------------------
 *  Create the Scratchpad window.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: createScratchpadWindow(void)
                                                                     throw ()
{
    if (!scratchpadWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("scratchpadWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }
        scratchpadWindow.reset(new ScratchpadWindow(gLiveSupport,
                                                    bundle,
                                                    scratchpadButton));
        gLiveSupport->loadWindowContents(scratchpadWindow);
    }
}


/*------------------------------------------------------------------------------
 *  The event when the Scratchpad button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateScratchpadWindow(Ptr<Playable>::Ref  playable)
                                                                     throw ()
{
    createScratchpadWindow();
    
    if (playable) {
        scratchpadWindow->addItem(playable);
    }
    
    scratchpadWindow->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Simple Playlist Management button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateSimplePlaylistMgmtWindow(void)           throw ()
{
    if (!simplePlaylistMgmtWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("simplePlaylistManagementWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        simplePlaylistMgmtWindow.reset(new SimplePlaylistManagementWindow(
                                                    gLiveSupport,
                                                    bundle,
                                                    simplePlaylistMgmtButton));
    }
    
    simplePlaylistMgmtWindow->showContents();
    
    simplePlaylistMgmtWindow->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Scheduler button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateSchedulerWindow(
                        Ptr<boost::posix_time::ptime>::Ref time)
                                                                    throw ()
{
    if (!schedulerWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("schedulerWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }
        
        try {
            schedulerWindow.reset(new SchedulerWindow(gLiveSupport,
                                                      bundle,
                                                      schedulerButton));
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

    schedulerWindow->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Search button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateSearchWindow(void)                       throw ()
{
    if (!searchWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("searchWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        searchWindow.reset(new SearchWindow(gLiveSupport,
                                            bundle,
                                            searchButton));
    }
    
    searchWindow->present();
}


/*------------------------------------------------------------------------------
 *  The event when the Options button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateOptionsWindow(void)                      throw ()
{
    if (!optionsWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("optionsWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        optionsWindow.reset(new OptionsWindow(gLiveSupport,
                                              bundle,
                                              optionsButton));
        ContentsStorable *  backupList = optionsWindow->getBackupList();
        if (backupList) {
            gLiveSupport->loadWindowContents(backupList);
        }
    }

    optionsWindow->present();
}


/*------------------------------------------------------------------------------
 *  Show only the UI components that are visible when no one is logged in
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showAnonymousUI(void)                          throw ()
{
    show_all();
    liveModeButton->hide();
    uploadFileButton->hide();
    scratchpadButton->hide();
    simplePlaylistMgmtButton->hide();
    schedulerButton->hide();
    searchButton->hide();
    optionsButton->hide();
    
    if (liveModeWindow.get()) {
        if (liveModeWindow->is_visible()) {
            liveModeWindow->hide();
        }
        // the Live Mode window is not destroyed at logout, unlike the others
    }
    if (uploadFileWindow.get()) {
        if (uploadFileWindow->is_visible()) {
            uploadFileWindow->hide();
        }
        uploadFileWindow.reset();
    }
    if (scratchpadWindow.get()) {
        gLiveSupport->storeWindowContents(scratchpadWindow);
        if (scratchpadWindow->is_visible()) {
            scratchpadWindow->hide();
        }
        scratchpadWindow.reset();
    }
    if (simplePlaylistMgmtWindow.get()) {
        if (simplePlaylistMgmtWindow->is_visible()) {
            simplePlaylistMgmtWindow->hide();
        }
        simplePlaylistMgmtWindow.reset();
    }
    if (schedulerWindow.get()) {
        if (schedulerWindow->is_visible()) {
            schedulerWindow->hide();
        }
        schedulerWindow.reset();
    }
    if (searchWindow.get()) {
        if (searchWindow->is_visible()) {
            searchWindow->hide();
        }
        searchWindow.reset();
    }
    if (optionsWindow.get()) {
        ContentsStorable *  backupList = optionsWindow->getBackupList();
        if (backupList) {
            gLiveSupport->storeWindowContents(backupList);
        }
        if (optionsWindow->is_visible()) {
            optionsWindow->hide();
        }
        optionsWindow.reset();
    }
}


/*------------------------------------------------------------------------------
 *  Cancel the playlist edited in the SimplePlaylistMgmtWindow, if any.
 *----------------------------------------------------------------------------*/
bool
MasterPanelWindow :: cancelEditedPlaylist(void)                     throw ()
{
    if (simplePlaylistMgmtWindow) {
        return simplePlaylistMgmtWindow->cancelPlaylist();
    } else {
        return true;
    }
}


/*------------------------------------------------------------------------------
 *  Show the UI components that are visible to a specific user.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showLoggedInUI(void)                           throw ()
{
    show_all();
    
    if (!gLiveSupport->isStorageAvailable()) { 
        liveModeButton->setDisabled(true);
        uploadFileButton->setDisabled(true);
        scratchpadButton->setDisabled(true);
        simplePlaylistMgmtButton->setDisabled(true);
        searchButton->setDisabled(true);
    }
    
    setSchedulerAvailable(gLiveSupport->isSchedulerAvailable());
    
    if (liveModeWindow) {
        liveModeWindow->updateStrings();
        if (liveModeWindow->isNotEmpty()) {
            liveModeWindow->present();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Get the next item from the top of the Live Mode window.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
MasterPanelWindow :: getNextItemToPlay()                            throw ()
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
resizeImage(Gtk::Image* image, int width, int height)               throw ()
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
MasterPanelWindow :: onKeyPressed(GdkEventKey *    event)           throw ()
{
    if (event->type == GDK_KEY_PRESS) {
        KeyboardShortcut::Action    action = gLiveSupport->findAction(
                                                windowName,
                                                Gdk::ModifierType(event->state),
                                                event->keyval);
        switch (action) {
            case KeyboardShortcut::playAudio :
                                    nowPlayingWidget->onPlayAudio();
                                    return true;
            
            case KeyboardShortcut::pauseAudio :
                                    nowPlayingWidget->onPauseAudio();
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
MasterPanelWindow :: uploadToHub(Ptr<Playable>::Ref     playable)
                                                                    throw ()
{
    if (!searchWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("searchWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        searchWindow.reset(new SearchWindow(gLiveSupport,
                                            bundle,
                                            searchButton));
    }
    
    searchWindow->uploadToHub(playable);
    
    searchWindow->present();
}


/*------------------------------------------------------------------------------
 *  Show or hide the Scheduler button.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: setSchedulerAvailable(bool  status)            throw ()
{
    if (status == false) {
        if (schedulerWindow && schedulerWindow->is_visible()) {
            schedulerWindow->hide();
        }
    }
    
    if (schedulerButton) {
        schedulerButton->setDisabled(!status);
    }
}


/*------------------------------------------------------------------------------
 *  Update the cue player displays to show a stopped state.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showCuePlayerStopped(void)                     throw ()
{
    if (scratchpadWindow) {
        scratchpadWindow->showCuePlayerStopped();
    }
    
    if (liveModeWindow) {
        liveModeWindow->showCuePlayerStopped();
    }
}

