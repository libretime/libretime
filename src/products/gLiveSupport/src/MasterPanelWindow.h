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
#ifndef MasterPanelWindow_h
#define MasterPanelWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm.h>
#include <libglademm.h>

#include "LiveSupport/Core/Ptr.h"

#include "GuiWindow.h"
#include "GLiveSupport.h"
#include "NowPlaying.h"
#include "LiveModeWindow.h"
#include "UploadFileWindow.h"
#include "ScratchpadWindow.h"
#include "PlaylistWindow.h"
#include "SchedulerWindow.h"
#include "SearchWindow.h"
#include "OptionsWindow.h"

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
 *  The layout of the window is contained in the file
 *  "var/glade/MasterPanelWindow.glade".
 *
 *  @author $Author$
 *  @version $Revision$
 */
class MasterPanelWindow : public GuiWindow
{
    private:

        /**
         *  Whether a user is currently logged in.
         */
        bool                                userIsLoggedIn;

        /**
         *  Log in.
         */
        void
        login (void)                                                throw ();

        /**
         *  Log out.
         */
        void
        logout (void)                                               throw ();


    protected:

        /**
         *  The time display
         */
        Gtk::Label *                        timeLabel;

        /**
         *  The signal connection, that is notified by the GTK timer each
         *  second, and will update the time display on each wakeup.
         */
        Ptr<sigc::connection>::Ref          timer;

        /**
         *  The 'now playing' display.
         */
        Ptr<NowPlaying>::Ref                nowPlayingWidget;

        /**
         *  The button to invoke the Live Mode window.
         */
        Gtk::ToggleButton *                 liveModeButton;

        /**
         *  The button to invoke the Upload File window.
         */
        Gtk::ToggleButton *                 uploadFileButton;

        /**
         *  The button to invoke the Scratchpad window.
         */
        Gtk::ToggleButton *                 scratchpadButton;

        /**
         *  The button to invoke the Playlist Window.
         */
        Gtk::ToggleButton *                 playlistButton;

        /**
         *  The button to invoke the Scheduler Window.
         */
        Gtk::ToggleButton *                 schedulerButton;

        /**
         *  The button to invoke the Search Window.
         */
        Gtk::ToggleButton *                 searchButton;

        /**
         *  The button to invoke the Options window.
         */
        Gtk::ToggleButton *                 optionsButton;

        /**
         *  The box containing the window opener buttons.
         */
        Gtk::ButtonBox *                    mainButtonBox;

        /**
         *  The label for the "logged in as" info.
         */
        Gtk::Label *                        userInfoLabel;

        /**
         *  The button to log in or log out.
         */
        Gtk::Button *                       loginButton;

        /**
         *  The one and only Live Mode window.
         */
        Ptr<LiveModeWindow>::Ref            liveModeWindow;

        /**
         *  The one and only Upload File window.
         */
        Ptr<UploadFileWindow>::Ref          uploadFileWindow;

        /**
         *  The one and only Scratchpad window.
         */
        Ptr<ScratchpadWindow>::Ref          scratchpadWindow;

        /**
         *  The one and only simple playlist management window.
         */
        Ptr<PlaylistWindow>::Ref            playlistWindow;

        /**
         *  The one and only scheduler window.
         */
        Ptr<SchedulerWindow>::Ref           schedulerWindow;

        /**
         *  The one and only search window.
         */
        Ptr<SearchWindow>::Ref              searchWindow;

        /**
         *  The one and only options window.
         */
        Ptr<OptionsWindow>::Ref             optionsWindow;

        /**
         *  Function that updates timeLabel with the current time.
         *  This is called by GTK at regular intervals.
         *
         *  @param dummy a dummy, unused parameter
         *  @return true if the timer should call this function again,
         *          false if the timer should be canceled
         */
        virtual bool
        onUpdateTime (int  dummy)                                   throw ();

        /**
         *  Register onUpdateTime with the GTK timer.
         *
         *  @see #resetTimer
         */
        virtual void
        setTimer (void)                                             throw ();

        /**
         *  Stop the timer, which was set by setTimer().
         *
         *  @see #setTimer
         */
        virtual void
        resetTimer (void)                                           throw ();

        /**
         *  Function to catch the event of the file upload button being
         *  pressed.
         */
        virtual void
        onUploadFileButtonClicked (void)                            throw ()
        {
            if (!uploadFileWindow ||
                    uploadFileWindow && !uploadFileWindow->getWindow()
                                                         ->is_visible()) {
                updateUploadFileWindow();
            } else {
                uploadFileWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the live mode button being
         *  pressed.
         */
        virtual void
        onLiveModeButtonClicked (void)                              throw ()
        {
            if (!liveModeWindow ||
                    liveModeWindow && !liveModeWindow->getWindow()
                                                     ->is_visible()) {
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
        onScratchpadButtonClicked (void)                            throw ()
        {
            if (!scratchpadWindow ||
                    scratchpadWindow && !scratchpadWindow->getWindow()
                                                         ->is_visible()) {
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
        onPlaylistButtonClicked (void)                              throw ()
        {
            if (!playlistWindow ||
                    playlistWindow && !playlistWindow->getWindow()
                                                     ->is_visible()) {
                updatePlaylistWindow();
            } else {
                playlistWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the Scheduler button
         *  button being pressed.
         */
        virtual void
        onSchedulerButtonClicked (void)                             throw ()
        {
            if (!schedulerWindow ||
                    schedulerWindow && !schedulerWindow->getWindow()
                                                       ->is_visible()) {
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
        onSearchButtonClicked (void)                                throw ()
        {
            if (!searchWindow ||
                    searchWindow && !searchWindow->getWindow()
                                                 ->is_visible()) {
                updateSearchWindow();
            } else {
                searchWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the Options button
         *  button being pressed.
         */
        virtual void
        onOptionsButtonClicked (void)                               throw ()
        {
            if (!optionsWindow ||
                    optionsWindow && !optionsWindow->getWindow()
                                                   ->is_visible()) {
                updateOptionsWindow();
            } else {
                optionsWindow->hide();
            }
        }

        /**
         *  Function to catch the event of the Login/Logout button
         *  button being pressed.
         */
        virtual void
        onLoginButtonClicked (void)                                 throw ();

        /**
         *  Signal handler for a key pressed at one of the entries.
         *  The keys can be customized by the keyboardShortcutContainer
         *  element in the gLiveSupport configuration file.
         *
         *  The action handled is: playAudio, pauseAudio, stopAudio, 
         *  and nextTrack.
         *
         *  @param  event the button event received
         *  @return true if the key press was fully handled, false if not
         */
        bool
        onKeyPressed (GdkEventKey *         event)                  throw ();

        /**
         *  Event handler for when the user closes the master panel.
         *  It pops up a confirmation dialog.
         *
         *  Overrides GuiWindow::onDeleteEvent().
         *
         *  @param  event   attributes of the event.
         *  @return true if handled the event, false to continue deleting.
         */
        virtual bool
        onDeleteEvent (GdkEventAny *    event)                      throw ();
        

    public:

        /**
         *  Constructor.
         */
        MasterPanelWindow (void)                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~MasterPanelWindow (void)                                   throw ();

        /**
         *  Change the user interface language of the application.
         *
         *  This is called by GLiveSupport, when its own locale changes.
         *
         *  This method assumes that only the MasterPanelWindow is visible,
         *  and will only change the language of the currently open
         *  MasterPanelWindow. No other open windows will be affected by
         *  this call, but subsequently opened windows are.
         */
        void
        changeLanguage (void)                                       throw ();

        /**
         *  Show the UI components that are visible when no one is logged in.
         */
        void
        showAnonymousUI (void)                                      throw ();

        /**
         *  Cancel the playlist edited in the PlaylistWindow.
         *
         *  @return true if the canceling worked (or if there was nothing 
         *          to cancel); false if the user canceled the canceling
         *  @see PlaylistWindow::cancelPlaylist()
         */
        bool
        cancelEditedPlaylist (void)                                 throw ();

        /**
         *  Show the UI components that are visible when someone is logged in.
         */
        void
        showLoggedInUI (void)                                       throw ();

        /**
         *  Update the Live Mode window.
         *
         *  @param  playable    (optional) add this item to the bottom of
         *                      the live mode window.
         */
        void
        updateLiveModeWindow (Ptr<Playable>::Ref    playable
                                                    = Ptr<Playable>::Ref())
                                                                    throw ();

        /**
         *  Refresh the playlist in the Live Mode window.
         *  Updates the playlist to the new copy supplied in the argument,
         *  if it is present in the Live Mode window.
         *  This is called by GLiveSupport::savePlaylist() after the playlist
         *  has been edited.
         *
         *  @param  playlist    the new version of the playlist.
         */
        void
        refreshPlaylistInLiveMode (Ptr<Playlist>::Ref    playlist)
                                                                    throw ()
        {
            if (liveModeWindow) {
                liveModeWindow->refreshPlaylist(playlist);
            }
        }

        /**
         *  Create the Scratchpad window.
         */
        void
        createScratchpadWindow (void)                               throw ();

        /**
         *  Update the Upload File window.
         */
        void
        updateUploadFileWindow (void)                               throw ();

        /**
         *  Update the Scratchpad window.
         */
        void
        updateScratchpadWindow (Ptr<Playable>::Ref  playable
                                                    = Ptr<Playable>::Ref())
                                                                    throw ();

        /**
         *  Update the Simple Playlist Management Window
         */
        void
        updatePlaylistWindow (void)                                 throw ();

        /**
         *  Update the Scheduler Window, optionally to display a new time.
         *
         *  @param time the time to display in the scheduler window.
         */
        void
        updateSchedulerWindow (Ptr<boost::posix_time::ptime>::Ref   time
                                        = Ptr<boost::posix_time::ptime>::Ref())
                                                                    throw ();

        /**
         *  Update the Search Window.
         */
        void
        updateSearchWindow (void)                                   throw ();

        /**
         *  Update the Options Window
         */
        void
        updateOptionsWindow (void)                                  throw ();

        /**
         *  Update the User info.
         *
         *  @param  loginName   the login name (only when userIsLoggedIn).
         */
        void
        updateUserInfo (Ptr<const Glib::ustring>::Ref   loginName
                                            = Ptr<const Glib::ustring>::Ref())
                                                                    throw ();

        /**
         *  Get the next item from the top of the Live Mode window.
         *  The item is removed from the Live Mode window.
         *
         *  @return the item at the top of the Live Mode window, a 0 pointer
         *          if there is no Live Mode window, or it is empty.
         */
        Ptr<Playable>::Ref
        getNextItemToPlay (void)                                    throw ();

        /**
         *  Set the "now playing" display.
         *
         *  @param  playable    the Playable whose data is to be displayed.
         */
        void
        setNowPlaying (Ptr<Playable>::Ref   playable)               throw ()
        {
            nowPlayingWidget->setPlayable(playable);
            gLiveSupport->updateRds();
        }

        /**
         *  Get the Playable currently shown in the "now playing" display.
         *
         *  @return the currently playing item; 0 if nothing is playing.
         */
        Ptr<Playable>::Ref
        getCurrentInnerPlayable (void)                              throw ()
        {
            return nowPlayingWidget->getCurrentInnerPlayable();
        }

        /**
         *  Set the Playable currently shown in the "now playing" display.
         *
         *  @return the currently playing item; 0 if nothing is playing.
         */
        void
        setCurrentInnerPlayable (gint64 id)                              throw ()
        {
            nowPlayingWidget->setCurrentInnerPlayable(id);
        }

        /**
         *  Upload a Playable object to the network hub.
         *  And display it in the Transports tab of the Search Window.
         *
         *  @param  playable    the audio clip or playlist to be uploaded.
         */
        void
        uploadToHub (Ptr<Playable>::Ref     playable)               throw ();

        /**
         *  Show or hide the Scheduler button.
         *
         *  @param  status  true means show the button, false means hide.
         */
        void
        setSchedulerAvailable (bool     status)                     throw ();

        /**
         *  Update the cue player displays to show a stopped state.
         *  Two cue player displays are updated by this method:
         *  one in the Scratchpad, and one in the Live Mode window.
         */
        void
        showCuePlayerStopped (void)                                 throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */

/**
 *  Resize an image to fit in a box, preserving its aspect ratio.
 *
 *  @param image    the image to resize (modified by the method)
 *  @param width    the width of the box
 *  @param height   the height of the box
 */
void
resizeImage(Gtk::Image* image, int width, int height)           throw ();

} // namespace GLiveSupport
} // namespace LiveSupport

#endif // MasterPanelWindow_h

