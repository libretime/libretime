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
    Version  : $Revision: 1.15 $
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
#include "UploadFileWindow.h"
#include "DjBagWindow.h"
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
                        : LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;

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
    nowPlayingWidget = Gtk::manage(new Gtk::Label("now playing"));
    nowPlayingBin = Gtk::manage(widgetFactory->createDarkBlueBin());
    nowPlayingBin->add(*nowPlayingWidget);
    timeBin->set_size_request(-1, 104);

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
    gLiveSupport->stopAudio();
}


/*------------------------------------------------------------------------------
 *  Change the language of the panel
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: changeLanguage(Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
{
    setBundle(bundle);

    try {
        set_title(*getResourceUstring("windowTitle"));

        Ptr<WidgetFactory>::Ref wf = WidgetFactory::getInstance();

        uploadFileButton = wf->createButton(
                                *getResourceUstring("uploadFileButtonLabel"));
        djBagButton = wf->createButton(
                                *getResourceUstring("djBagButtonLabel"));
        simplePlaylistMgmtButton = wf->createButton(
                        *getResourceUstring("simplePlaylistMgmtButtonLabel"));
        schedulerButton = wf->createButton(
                                *getResourceUstring("schedulerButtonLabel"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
    }

    userInfoWidget->changeLanguage(bundle);

    // re-attach the localized widgets to the layout
    buttonBar->attach(*uploadFileButton,           0, 1, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*djBagButton,                1, 2, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*simplePlaylistMgmtButton,   2, 3, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);
    buttonBar->attach(*schedulerButton,            3, 4, 0, 1,
                      Gtk::SHRINK|Gtk::FILL, Gtk::SHRINK|Gtk::FILL,
                      5, 0);

    // re-bind events to the buttons
    uploadFileButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &MasterPanelWindow::onUploadFileButtonClicked));
    djBagButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &MasterPanelWindow::onDjBagButtonClicked));
    simplePlaylistMgmtButton->signal_clicked().connect(
            sigc::mem_fun(*this,
                 &MasterPanelWindow::onSimplePlaylistMgmtButtonClicked));
    schedulerButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &MasterPanelWindow::onSchedulerButtonClicked));

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
    Ptr<const ptime>::Ref   now = gLiveSupport->getScheduler()
                                              ->getSchedulerTime();
    
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
 *  The event when the upload file button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: onUploadFileButtonClicked(void)                 throw ()
{
    Ptr<ResourceBundle>::Ref    bundle;
    try {
        bundle       = getBundle("uploadFileWindow");
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        return;
    }

    Ptr<UploadFileWindow>::Ref  uploadWindow(new UploadFileWindow(gLiveSupport,
                                                                  bundle));

    Gtk::Main::run(*uploadWindow);
}


/*------------------------------------------------------------------------------
 *  The event when the DJ Bag button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: onDjBagButtonClicked(void)                     throw ()
{
    if (!djBagWindow.get()) {
        Ptr<ResourceBundle>::Ref    bundle;
        try {
            bundle       = getBundle("djBagWindow");
        } catch (std::invalid_argument &e) {
            std::cerr << e.what() << std::endl;
            return;
        }

        djBagWindow.reset(new DjBagWindow(gLiveSupport, bundle));
    }

    if (!djBagWindow->is_visible()) {
        djBagWindow->show();
    }

    djBagWindow->showContents();
}


/*------------------------------------------------------------------------------
 *  The event when the Simple Playlist Management button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: onSimplePlaylistMgmtButtonClicked(void)        throw ()
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
    
    if (!simplePlaylistMgmtWindow->is_visible()) {
        simplePlaylistMgmtWindow->show();
    }
}


/*------------------------------------------------------------------------------
 *  The event when the Scheduler button has been clicked.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: onSchedulerButtonClicked(void)                 throw ()
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

    if (!schedulerWindow->is_visible()) {
        schedulerWindow->show();
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
    djBagButton->hide();
    simplePlaylistMgmtButton->hide();
    schedulerButton->hide();
}


/*------------------------------------------------------------------------------
 *  Show the UI components that are visible to a specific user.
 *----------------------------------------------------------------------------*/
void
MasterPanelWindow :: showLoggedInUI(void)                           throw ()
{
    show_all();
}

