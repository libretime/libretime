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
#ifndef NowPlaying_h
#define NowPlaying_h

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
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Core/Mutex.h"

#include "GLiveSupport.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The box displaying "now playing" in the master panel.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class NowPlaying : public LocalizedObject
{
    private:
    
        /**
         *  The Glade object, containing the visual design.
         */
        Glib::RefPtr<Gnome::Glade::Xml>     glade;

        /**
         *  Whether anything is shown in the widget.
         */
        bool                    isActive;

        /**
         *  Whether the pause button has been clicked.
         */
        bool                    isPaused;

        /**
         *  The item which is currently playing (audio clip or playlist).
         */
        Ptr<Playable>::Ref      playable;

        /**
         *  The audio clip which is currently playing (could be nested
         *  several levels inside the "playable" object).
         */
        Ptr<Playable>::Ref      currentInnerPlayable;

        /**
         *  The label holding the title of the now playing item.
         */
        Gtk::Label *            titleLabel;

        /**
         *  The label holding the creator of the now playing item.
         */
        Gtk::Label *            creatorLabel;

        /**
         *  The label holding the playlist containing the audio clip
         *  which is playing now (if any).
         */
        Gtk::Label *            playlistLabel;

        /**
         *  The label which says "elapsed time".
         */
        Gtk::Label *            elapsedTimeText;

        /**
         *  The label holding the elapsed time.
         */
        Gtk::Label *            elapsedTimeLabel;

        /**
         *  The label which says "remaining time".
         */
        Gtk::Label *            remainsTimeText;

        /**
         *  The label holding the remaining time.
         */
        Gtk::Label *            remainsTimeLabel;

        /**
         *  A box around the remaining time label, so we can modify its color.
         */
        Gtk::EventBox *         remainsTimeBox;

        /**
         *  The play button.
         */
        Gtk::Button *           playButton;

        /**
         *  The stop button.
         */
        Gtk::Button *           stopButton;

        /**
         *  The possible states of the 'time remains' label.
         */
        typedef enum { TIME_GREEN, TIME_YELLOW, TIME_RED }
                                RemainsTimeStateType;
        
        /**
         *  The current state of the 'time remains' label.
         */
        RemainsTimeStateType    remainsTimeState;
        
        /**
         *  Counter which makes the 'time remains' label blink.
         */
        int                     remainsTimeCounter;
        
        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref  gLiveSupport;

        /**
         *  A mutex to make the writing, and some reading, of the
         *  'playable' variable atomic.
         */
        Mutex                   playableMutex;

        /**
         *  Default constructor.
         */
        NowPlaying(void)                                throw ();

        /**
         *  Event handler for the Play button being clicked.
         */
        void
        onPlayButtonClicked(void)                       throw ();

        /**
         *  Event handler for the Stop button being clicked.
         */
        void
        onStopButtonClicked(void)                       throw ();

        /**
         *  Set the color of the 'remains time' label.
         *
         *  It sets the background color of the label to blue, yellow or red,
         *  depending on the remainsTimeState and the remainsTimeCounter
         *  variables.
         *
         *  @param state    the new state of the label.
         */
        void
        setRemainsTimeColor(RemainsTimeStateType  state)
                                                        throw ();

        /**
         *  Reset all remains-time-blinking related variables.
         *
         *  Sets remainsTimeState to TIME_GREEN, remainsTimeCounter to 0,
         *  and the background color of the label to blue.
         */
        void inline
        resetRemainsTimeState(void)                     throw ();
 
    
    public:
    
        /**
         *  Constructor with parent and localization parameter.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this widget
         *  @param glade    the Glade file which specifies the visual
         *                  components for this class.
         */
        NowPlaying(Ptr<GLiveSupport>::Ref           gLiveSupport,
                   Ptr<ResourceBundle>::Ref         bundle,
                   Glib::RefPtr<Gnome::Glade::Xml>  glade)
                                                        throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~NowPlaying(void)                               throw ()
        {
        }

        /**
         *  Set the title etc. of the now playing item.
         *
         *  @param playable     the playable to be displayed
         */
        void
        setPlayable(Ptr<Playable>::Ref  playable)       throw ();

        /**
         *  Function that updates the elapsed and remaining time displays.
         *  This is called by the MasterPanelWindow every second.
         */
        void
        onUpdateTime(void)                              throw ();

        /**
         *  Public interface for restarting the audio.
         *
         *  This is used by MasterPanelWindow::onKeyPressed().
         */
        void
        onPlayAudio(void)                               throw ()
        {
            onPlayButtonClicked();
        }

        /**
         *  Public interface for stopping the audio.
         *
         *  This is used by MasterPanelWindow::onKeyPressed().
         */
        void
        onStopAudio(void)                               throw ()
        {
            onStopButtonClicked();
        }

        /**
         *  Get the Playable object which is playing now.
         *  If a playlist is playing, does not return the playlist, but
         *  the audio clip inside the playlist (possibly several levels deep).
         *
         *  This is used by GLiveSupport::substituteRdsData().
         *
         *  @return the currently playing item; 0 if nothing is playing.
         */
        Ptr<Playable>::Ref
        getCurrentInnerPlayable(void)                   throw ()
        {
            return currentInnerPlayable;
        }

        /**
         *  Change the user interface language of the widget.
         *
         *  @param  bundle  the new resource bundle.
         */
        void
        changeLanguage(Ptr<ResourceBundle>::Ref     bundle)         throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // NowPlaying_h

