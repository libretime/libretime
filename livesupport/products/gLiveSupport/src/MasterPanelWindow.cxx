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
    Version  : $Revision: 1.29 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/MasterPanelWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <unicode/msgfmt.h>
#include <gtkmm/label.h>
#include <gtkmm/main.h>

#include "LiveSupport/Core/TimeConversion.h"
#include "MasterPanelWindow.h"


using namespace LiveSupport;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


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

    // TODO: remove hard-coded station logo path reference
    radioLogoWidget = Gtk::manage(new Gtk::Image("var/stationLogo.png"));
    radioLogoWidget->set_size_request(158, 104);

    // set up the layout, which is a button box
    layout = Gtk::manage(new Gtk::Table());

    // set up the time label
    timeWidget = Gtk::manage(new Gtk::Label("time"));
    timeBin = Gtk::manage(widgetFactory->createBlueBin());
    timeBin->add(*timeWidget);
    timeBin->set_size_request(153, 104);

    // set up the now playing widget
    nowPlayingWidget = Gtk::manage(new Gtk::Label(""));
    nowPlayingBin = Gtk::manage(widgetFactory->createDarkBlueBin());
    nowPlayingBin->add(*nowPlayingWidget);
    nowPlayingBin->set_size_request(-1, 104);

    // set up the VU meter widget
    vuMeterWidget = Gtk::manage(new Gtk::Label("VU meter"));
    vuMeterBin = Gtk::manage(widgetFactory->createBlueBin());
    vuMeterBin->add(*vuMeterWidget);
    vuMeterBin->set_size_request(400, 40);
    // set up the next playing widget
    nextPlayingWidget = Gtk::manage(new Gtk::Label("next playing"));
    nextPlayingBin = Gtk::manage(widgetFactory->createBlueBin());
    nextPlayingBin->add(*nextPlayingWidget);
    nextPlayingBin->set_size_request(400, 59);

    // create the bottom bar
    bottomBar = Gtk::manage(new Gtk::Table());
    bottomBar->set_size_request(-1, 30);
    buttonBar = Gtk::manage(new Gtk::Table());
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
    bottomBar->attach(*buttonBarAlignment, 0, 1, 0, 1,
                      Gtk::EXPAND|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    bottomBar->attach(*userInfoAlignment,  1, 2, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    
    // set up the main window, and show everything
    // all the localized widgets were set up in changeLanguage()
    set_border_width(10);
    layout->attach(*timeBin,         0, 1, 0, 2,
                    Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                    0, 0);
    layout->attach(*nowPlayingBin,   1, 2, 0, 2,
                   Gtk::EXPAND|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                   5, 0);
    layout->attach(*vuMeterBin,      2, 3, 0, 1,
                    Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                    0, 0);
    layout->attach(*nextPlayingBin,  2, 3, 1, 2,
                    Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                    0, 0);
    layout->attach(*radioLogoWidget, 3, 4, 0, 2,
                    Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                    5, 0);
    layout->attach(*bottomBar,       0, 4, 2, 3,
                    Gtk::EXPAND|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                    0, 0);
    add(*layout);

    // set the background to white
    bgColor = Colors::getColor(Colors::White);
    modify_bg(Gtk::STATE_NORMAL, bgColor);

    // set the size and location of the window, according to the screen size
    Glib::RefPtr<Gdk::Screen>   screen = get_screen();
    int                         width;
    int                         height;
    get_size(width, height);
    width = screen->get_width();
    set_default_size(width, height);
    move(0, 0);
    set_decorated(false);

    // set the localized resources
    liveModeButton           = 0;
    uploadFileButton         = 0;
    scratchpadButton         = 0;
    simplePlaylistMgmtButton = 0;
    schedulerButton          = 0;
    searchButton             = 0;
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

    try {
        set_title(*getResourceUstring("windowTitle"));

        Ptr<WidgetFactory>::Ref wf = WidgetFactory::getInstance();

        liveModeButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("liveModeButtonLabel")));
        uploadFileButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("uploadFileButtonLabel")));
        scratchpadButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("scratchpadButtonLabel")));
        simplePlaylistMgmtButton = Gtk::manage(wf->createButton(
                        *getResourceUstring("simplePlaylistMgmtButtonLabel")));
        schedulerButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("schedulerButtonLabel")));
        searchButton = Gtk::manage(wf->createButton(
                                *getResourceUstring("searchButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    userInfoWidget->changeLanguage(bundle);

    // re-attach the localized widgets to the layout
    buttonBar->attach(*liveModeButton,             0, 1, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*uploadFileButton,           1, 2, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*scratchpadButton,           2, 3, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*simplePlaylistMgmtButton,   3, 4, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*schedulerButton,            4, 5, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*searchButton,               5, 6, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);

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

    // set the timer to active once a second
    timer.reset(new sigc::connection(
                                  Glib::signal_timeout().connect(slot, 1000)));
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

    try {
        now = gLiveSupport->getScheduler()->getSchedulerTime();
    } catch (XmlRpcException &e) {
        // TODO: handle error
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

        liveModeWindow.reset(new LiveModeWindow(gLiveSupport, bundle));
    }
    
    if (playable) {
        liveModeWindow->addItem(playable);
    }
    
    if (!liveModeWindow->is_visible()) {
        liveModeWindow->show();
    }
}


/*------------------------------------------------------------------------------
 *  The event when the upload file button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: onUploadFileButtonClicked(void)                throw ()
{
    if (!uploadFileWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("uploadFileWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        uploadFileWindow.reset(new UploadFileWindow(gLiveSupport, bundle));
        uploadFileWindow->show();
        return;
    }

    if (!uploadFileWindow->is_visible()) {
        uploadFileWindow->show();
    } else {
        uploadFileWindow->hide();
    }
}


/*------------------------------------------------------------------------------
 *  The event when the Scratchpad button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: updateScratchpadWindow(void)                   throw ()
{
    if (!scratchpadWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("scratchpadWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        scratchpadWindow.reset(new ScratchpadWindow(gLiveSupport, bundle));
    }

    scratchpadWindow->showContents();

    if (!scratchpadWindow->is_visible()) {
        scratchpadWindow->show();
    }
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

        simplePlaylistMgmtWindow.reset(
                new SimplePlaylistManagementWindow(gLiveSupport, bundle));
    }
    
    simplePlaylistMgmtWindow->showContents();
    
    if (!simplePlaylistMgmtWindow->is_visible()) {
        simplePlaylistMgmtWindow->show();
    }
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

        schedulerWindow.reset(new SchedulerWindow(gLiveSupport, bundle));
    }
    
    if (time.get()) {
        schedulerWindow->setTime(time);
    }
    
    schedulerWindow->showContents();

    if (!schedulerWindow->is_visible()) {
        schedulerWindow->show();
    }
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

        searchWindow.reset(new SearchWindow(gLiveSupport, bundle));
    }

    if (!searchWindow->is_visible()) {
        searchWindow->show();
    }
}


/*------------------------------------------------------------------------------
 *  Show only the UI components that are visible when no one is logged in
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showAnonymousUI(void)                          throw ()
{
    show_all();
    buttonBar->hide();
    uploadFileButton->hide();
    scratchpadButton->hide();
    simplePlaylistMgmtButton->hide();
    schedulerButton->hide();
    searchButton->hide();
    
    if (liveModeWindow.get()) {
        liveModeWindow->hide();
        liveModeWindow.reset();
    }
    if (uploadFileWindow.get()) {
        uploadFileWindow->hide();
        uploadFileWindow.reset();
    }
    if (scratchpadWindow.get()) {
        scratchpadWindow->hide();
        scratchpadWindow.reset();
    }
    if (simplePlaylistMgmtWindow.get()) {
        simplePlaylistMgmtWindow->hide();
        simplePlaylistMgmtWindow.reset();
    }
    if (schedulerWindow.get()) {
        schedulerWindow->hide();
        schedulerWindow.reset();
    }
    if (searchWindow.get()) {
        searchWindow->hide();
        searchWindow.reset();
    }
    
    gLiveSupport->stopCueAudio();
}


/*------------------------------------------------------------------------------
 *  Show the UI components that are visible to a specific user.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showLoggedInUI(void)                           throw ()
{
    show_all();
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

