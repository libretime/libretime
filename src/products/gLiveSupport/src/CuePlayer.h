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
#ifndef CuePlayer_h
#define CuePlayer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerEventListener.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"
#include "GLiveSupport.h"

#include "GuiComponent.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A box displaying a play/pause and a stop button, which control the cue
 *  (preview) audio player.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class CuePlayer : public GuiComponent,
                  public PlaylistExecutor::AudioPlayerEventListener
{
    private:

        /**
         *  The possible states of the (cue) audio player.
         */
        enum AudioState { waitingState, playingState, pausedState };

        /**
         *  The current state of the player.
         */
        AudioState                  audioState;

        /**
         *  The play button.
         */
        Gtk::Button *               playButton;

        /**
         *  The stop button.
         */
        Gtk::Button *               stopButton;

        /**
         *  The Gtk::TreeView of the parent.
         */
        Gtk::TreeView *             treeView;
         
        /**
         *  The Gtk::TreeModelColumnRecord of the parent.
         */
        const PlayableTreeModelColumnRecord &
                                    modelColumns;
         
        /**
         *  Default constructor.
         */
        CuePlayer(void)                                throw ();

        /**
         *  Pause the song.
         */
        void
        onPauseItem(void)                               throw ();

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
         *  Set the state of the widget.
         *  It sets the value of the audioState variable, and changes the
         *  play/pause button if necessary.
         *
         *  @param  newState    the new value of the audioState variable.
         */
        void
        setAudioState(AudioState    newState)           throw ();

    
    public:

        /**
         *  Constructor with parent parameters.
         *
         *  @param  parent          the GuiObject which contains this one.
         *  @param  treeView        the TreeView object showing the selection.
         *  @param  modelColumns    the object holding the types of the columns.
         */
        CuePlayer(GuiObject *                               parent,
                  Gtk::TreeView *                           treeView,
                  const PlayableTreeModelColumnRecord &     modelColumns)
                                                        throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~CuePlayer(void)                                throw ();

        /**
         *  Signal handler for the "play item" menu item selected
         *  from the entry context menu.
         */
        void
        onPlayItem(void)                                throw ();

        /**
         *  Event handler for the "cue audio player has stopped" event.
         *
         *  @param errorMessage is a 0 pointer if the player stopped normally
         */
        virtual void
        onStop(Ptr<const Glib::ustring>::Ref  errorMessage
                                              = Ptr<const Glib::ustring>::Ref())
                                                        throw ();

        /**
         *  Event handler for the "cue audio player has started" event.
         *
         *  @param fileName
         */
        virtual void
        onStart(gint64 id)
                                                        throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // CuePlayer_h

