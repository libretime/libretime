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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.6 $
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
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

#include "GLiveSupport.h"
#include "MasterPanelUserInfoWidget.h"
#include "DjBagWindow.h"
#include "SimplePlaylistManagementWindow.h"
#include "SchedulerWindow.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The master panel window.
 *
 *  The layout of the window is roughly the following:
 *  <pre><code>
 *  +--- master panel --------------------------------------------------------+
 *  | + LS logo + + time + + now ----+ + VU meter + + on-air + + radio logo + |
 *  | |         | |      | | playing | |          | |        | |            | |
 *  | |         | |      | |         | +----------+ +--------+ +------------+ |
 *  | |         | |      | |         | + next ----+ + user info ------------+ |
 *  | |         | |      | |         | | playing  | |                       | |
 *  | +---------+ +------+ +---------+ +----------+ +-----------------------+ |
 *  | +-- button bar -------------------------------------------------------+ |
 *  +-------------------------------------------------------------------------+
 *  </code></pre>
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.6 $
 */
class MasterPanelWindow : public Gtk::Window, public LocalizedObject
{
    protected:
        /**
         *  The layout used in the window.
         */
        Ptr<Gtk::Table>::Ref        layout;

        /**
         *  The LiveSupport logo
         */
        Ptr<Gtk::Widget>::Ref       lsLogoWidget;

        /**
         *  The time display
         */
        Ptr<Gtk::Label>::Ref        timeWidget;

        /**
         *  The signal connection, that is notified by the GTK timer each
         *  second, and will update the time display on each wakeup.
         */
        Ptr<sigc::connection>::Ref  timer;

        /**
         *  The 'now playing' display.
         */
        Ptr<Gtk::Widget>::Ref       nowPlayingWidget;

        /**
         *  The VU meter display.
         */
        Ptr<Gtk::Widget>::Ref       vuMeterWidget;

        /**
         *  The 'next playing' display.
         */
        Ptr<Gtk::Widget>::Ref       nextPlayingWidget;

        /**
         *  The on-air indicator.
         */
        Ptr<Gtk::Widget>::Ref       onAirWidget;

        /**
         *  The user info widget.
         */
        Ptr<MasterPanelUserInfoWidget>::Ref     userInfoWidget;

        /**
         *  The radio logo.
         */
        Ptr<Gtk::Widget>::Ref       radioLogoWidget;

        /**
         *  The button to invoke the upload file window.
         */
        Ptr<Gtk::Button>::Ref       uploadFileButton;

        /**
         *  The button to invoke the DJ Bag window.
         */
        Ptr<Gtk::Button>::Ref       djBagButton;

        /**
         *  The button to invoke the Simple Playlist Management Window.
         */
        Ptr<Gtk::Button>::Ref       simplePlaylistMgmtButton;

        /**
         *  The button to invoke the Scheduler Window.
         */
        Ptr<Gtk::Button>::Ref       schedulerButton;

        /**
         *  The gLiveSupport object, handling the logic of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The one and only DJ Bag window.
         */
        Ptr<DjBagWindow>::Ref       djBagWindow;

        /**
         *  The one and only simple playlist management window.
         */
        Ptr<SimplePlaylistManagementWindow>::Ref    simplePlaylistMgmtWindow;

        /**
         *  The one and only scheduler window.
         */
        Ptr<SchedulerWindow>::Ref   schedulerWindow;

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
         *  Function to catch the event of the DJ Bag button being
         *  pressed.
         */
        virtual void
        onDjBagButtonClicked(void)                          throw ();

        /**
         *  Function to catch the event of the Simple Playlist
         *  Management button being pressed.
         */
        virtual void
        onSimplePlaylistMgmtButtonClicked(void)         throw ();

        /**
         *  Function to catch the event of the Scheduler button
         *  button being pressed.
         */
        virtual void
        onSchedulerButtonClicked(void)                  throw ();


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
         *  Update the DJ Bag window.
         */
        void
        updateDjBagWindow(void)                                 throw ()
        {
            // this will create, open and display the window.
            onDjBagButtonClicked();
            djBagWindow->showContents();
        }

        /**
         *  Update the Simple Playlist Management Window
         */
        void
        updateSimplePlaylistMgmtWindow(void)                    throw ()
        {
            // this will create, open and display the window.
            onSimplePlaylistMgmtButtonClicked();
            simplePlaylistMgmtWindow->showContents();
        }

        /**
         *  Update the Scheduler Window
         */
        void
        updateSchedulerWindow(void)                             throw ()
        {
            // this will create, open and display the window.
            onSchedulerButtonClicked();
            schedulerWindow->showContents();
        }

        /**
         *  Update the Scheduler Window to display a new time.
         *
         *  @param time the time to display in the scheduler window.
         */
        void
        updateSchedulerWindow(Ptr<boost::posix_time::ptime>::Ref    time)
                                                                    throw ()
        {
            // this will create, open and display the window.
            onSchedulerButtonClicked();
            schedulerWindow->setTime(time);
            schedulerWindow->showContents();
        }

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // MasterPanelWindow_h

