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
    Version  : $Revision: 1.18 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/MasterPanelWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef MasterPanelWindow_h
#define MasterPanelWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/button.h>
#include <gtkmm/table.h>
#include <gtkmm/image.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

#include "GLiveSupport.h"
#include "MasterPanelUserInfoWidget.h"
#include "LiveModeWindow.h"
#include "UploadFileWindow.h"
#include "ScratchpadWindow.h"
#include "SimplePlaylistManagementWindow.h"
#include "SchedulerWindow.h"
#include "SearchWindow.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The master panel window.
 *
 *  The layout of the window is roughly the following:
 *  <pre><code>
 *  +--- master panel ---------------------------------+
 *  | + time + + now ----+ + VU meter + + radio logo + |
 *  | |      | | playing | |          | |            | |
 *  | |      | |         | +----------+ |            | |
 *  | |      | |         | + next ----+ |            | |
 *  | |      | |         | | playing  | |            | |
 *  | +------+ +---------+ +----------+ +------------+ |
 *  | +-- bottom bar --------------------------------+ |
 *  | | +-- button bar -----------+ +-- user info -+ | |
 *  | +----------------------------------------------+ |
 *  +--------------------------------------------------+
 *  </code></pre>
 *
 *  @author $Author: fgerlits $
 *  @version $Revision: 1.18 $
 */
class MasterPanelWindow : public Gtk::Window, public LocalizedObject
{
    protected:
        /**
         *  The layout used in the window.
         */
        Gtk::Table                * layout;

        /**
         *  The background color.
         */
        Gdk::Color                  bgColor;

        /**
         *  The container for the time widget
         */
        BlueBin                   * timeBin;

        /**
         *  The time display
         */
        Gtk::Label                * timeWidget;

        /**
         *  The signal connection, that is notified by the GTK timer each
         *  second, and will update the time display on each wakeup.
         */
        Ptr<sigc::connection>::Ref  timer;

        /**
         *  The container for the now playing widget
         */
        BlueBin                   * nowPlayingBin;

        /**
         *  The 'now playing' display.
         */
        Gtk::Widget               * nowPlayingWidget;

        /**
         *  The container for the VU meter widget
         */
        BlueBin                   * vuMeterBin;

        /**
         *  The VU meter display.
         */
        Gtk::Widget               * vuMeterWidget;

        /**
         *  The container for the next playing widget.
         */
        BlueBin                   * nextPlayingBin;

        /**
         *  The 'next playing' display.
         */
        Gtk::Widget               * nextPlayingWidget;

        /**
         *  The user info alignment helper.
         */
        Gtk::Alignment            * userInfoAlignment;

        /**
         *  The user info widget.
         */
        MasterPanelUserInfoWidget     * userInfoWidget;

        /**
         *  The radio logo.
         */
        Gtk::Image                * radioLogoWidget;

        /**
         *  The bottom bar.
         */
        Gtk::Table                * bottomBar;

        /**
         *  The button bar alignment helper
         */
        Gtk::Alignment            * buttonBarAlignment;

        /**
         *  The button bar.
         */
        Gtk::Table                * buttonBar;

        /**
         *  The button to invoke the live mode window.
         */
        Button                    * liveModeButton;

        /**
         *  The button to invoke the upload file window.
         */
        Button                    * uploadFileButton;

        /**
         *  The button to invoke the Scratchpad window.
         */
        Button                    * scratchpadButton;

        /**
         *  The button to invoke the Simple Playlist Management Window.
         */
        Button                    * simplePlaylistMgmtButton;

        /**
         *  The button to invoke the Scheduler Window.
         */
        Button                    * schedulerButton;

        /**
         *  The button to invoke the Search Window.
         */
        Button                    * searchButton;

        /**
         *  The gLiveSupport object, handling the logic of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The one and only Live Mode window.
         */
        Ptr<LiveModeWindow>::Ref    liveModeWindow;

        /**
         *  The one and only Upload File window.
         */
        Ptr<UploadFileWindow>::Ref  uploadFileWindow;

        /**
         *  The one and only Scratchpad window.
         */
        Ptr<ScratchpadWindow>::Ref  scratchpadWindow;

        /**
         *  The one and only simple playlist management window.
         */
        Ptr<SimplePlaylistManagementWindow>::Ref    simplePlaylistMgmtWindow;

        /**
         *  The one and only scheduler window.
         */
        Ptr<SchedulerWindow>::Ref   schedulerWindow;

        /**
         *  The one and only search window.
         */
        Ptr<SearchWindow>::Ref   searchWindow;

        /**
         *  Function that updates timeLabel with the current time.
         *  This is called by GTK at regular intervals.
         *
         *  @param dummy a dummy, unused parameter
         *  @return true if the timer should call this function again,
         *          false if the timer should be canceled
         */
        virtual bool
        onUpdateTime(int  dummy)                            throw ();

        /**
         *  Register onUpdateTime with the GTK timer.
         *
         *  @see #resetTimer
         */
        virtual void
        setTimer(void)                                      throw ();

        /**
         *  Stop the timer, which was set by setTimer().
         *
         *  @see #setTimer
         */
        virtual void
        resetTimer(void)                                    throw ();

        /**
         *  Function to catch the event of the file upload button being
         *  pressed.
         */
        virtual void
        onUploadFileButtonClicked(void)                     throw ();

        /**
         *  Function to catch the event of the live mode button being
         *  pressed.
         */
        virtual void
        onLiveModeButtonClicked(void)                       throw ()
        {
            if (!liveModeWindow ||
                    liveModeWindow && !liveModeWindow->is_visible()) {
                updateLiveModeWindow();
            } else {
                liveModeWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the Scratchpad button being
         *  pressed.
         */
        virtual void
        onScratchpadButtonClicked(void)                     throw ()
        {
            if (!scratchpadWindow ||
                    scratchpadWindow && !scratchpadWindow->is_visible()) {
                updateScratchpadWindow();
            } else {
                scratchpadWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the Simple Playlist
         *  Management button being pressed.
         */
        virtual void
        onSimplePlaylistMgmtButtonClicked(void)         throw ()
        {
            if (!simplePlaylistMgmtWindow ||
                    simplePlaylistMgmtWindow &&
                                 !simplePlaylistMgmtWindow->is_visible()) {
                updateSimplePlaylistMgmtWindow();
            } else {
                simplePlaylistMgmtWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the Scheduler button
         *  button being pressed.
         */
        virtual void
        onSchedulerButtonClicked(void)                  throw ()
        {
            if (!schedulerWindow ||
                    schedulerWindow && !schedulerWindow->is_visible()) {
                updateSchedulerWindow();
            } else {
                schedulerWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the Search button
         *  button being pressed.
         */
        virtual void
        onSearchButtonClicked(void)                             throw ()
        {
            if (!searchWindow ||
                    searchWindow && !searchWindow->is_visible()) {
                updateSearchWindow();
            } else {
                searchWindow->hide();
            }
        }


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the gLiveSupport object, handling the
         *         logic of the application
         *  @param bundle the resource bundle holding localized resources
         */
        MasterPanelWindow(Ptr<GLiveSupport>::Ref     gLiveSupport,
                          Ptr<ResourceBundle>::Ref   bundle)
                                                                throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~MasterPanelWindow(void)                             throw ();

        /**
         *  Change the user interface language of the application
         *  by providing a new resource bundle.
         *  This call assumes that only the MasterPanel is visilbe,
         *  and will only change the language of the currently open
         *  MasterPanel. No other open windows will be affected by
         *  this call, but subsequently opened windows are.
         *
         *  @param bundle the new resource bundle.
         */
        void
        changeLanguage(Ptr<ResourceBundle>::Ref     bundle)     throw ();

        /**
         *  Show the UI components that are visible when no one is logged in.
         */
        void
        showAnonymousUI(void)                                   throw ();

        /**
         *  Show the UI components that are visible when someone is logged in.
         */
        void
        showLoggedInUI(void)                                    throw ();

        /**
         *  Update the Live Mode window.
         */
        void
        updateLiveModeWindow(Ptr<Playable>::Ref     playable
                                                    = Ptr<Playable>::Ref())
                                                                throw ();

        /**
         *  Update the Scratchpad window.
         */
        void
        updateScratchpadWindow(void)                            throw ();

        /**
         *  Update the Simple Playlist Management Window
         */
        void
        updateSimplePlaylistMgmtWindow(void)                    throw ();

        /**
         *  Update the Scheduler Window, optionally to display a new time.
         *
         *  @param time the time to display in the scheduler window.
         */
        void
        updateSchedulerWindow(Ptr<boost::posix_time::ptime>::Ref time =
                                        Ptr<boost::posix_time::ptime>::Ref())
                                                                throw ();

        /**
         *  Update the Search Window
         */
        void
        updateSearchWindow(void)                                throw ();

        /**
         *  Get the next item from the top of the Live Mode window.
         *  The item is removed from the Live Mode window.
         *
         *  @return the item at the top of the Live Mode window, a 0 pointer
         *          if there is no Live Mode window, or it is empty.
         */
        Ptr<Playable>::Ref
        getNextItemToPlay()                                     throw ();

        /**
         *  Set the "now playing" display.
         */
        void
        setNowPlaying(Ptr<Playable>::Ref    playable)           throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // MasterPanelWindow_h

