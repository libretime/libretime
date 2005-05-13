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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/CuePlayer.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_GLiveSupport_CuePlayer_h
#define LiveSupport_GLiveSupport_CuePlayer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerEventListener.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/PlayableTreeModelColumnRecord.h"

#include "GLiveSupport.h"


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
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class CuePlayer : public Gtk::HBox,
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
        ImageButton *               playButton;

        /**
         *  The pause button.
         */
        ImageButton *               pauseButton;

        /**
         *  The stop button.
         */
        ImageButton *               stopButton;

        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;
         
        /**
         *  The Gtk::TreeView of the parent.
         */
        Gtk::TreeView *       treeView;
         
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
         *  Event handler for the Play button being clicked.
         */
        void
        onPlayButtonClicked(void)                       throw ();

        /**
         *  Event handler for the Pause button being clicked.
         */
        void
        onPauseButtonClicked(void)                      throw ();

        /**
         *  Event handler for the Stop button being clicked.
         */
        void
        onStopButtonClicked(void)                       throw ();

    
    public:
    
        /**
         *  Constructor with parent parameters.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param treeView     the TreeView object showing the selection.
         *  @param modelColumns the object holding the types of the columns.
         */
        CuePlayer(Ptr<GLiveSupport>::Ref                    gLiveSupport,
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
         */
        virtual void
        onStop(void)                                    throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveSupport_GLiveSupport_CuePlayer_h

