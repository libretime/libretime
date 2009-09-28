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

#include "CuePlayer.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/**
 *  The string which identifies the Play stock image.
 */
const Glib::ustring     playStockImageName = "gtk-media-play";

/**
 *  The string which identifies the Pause stock image.
 */
const Glib::ustring     pauseStockImageName = "gtk-media-pause";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
CuePlayer :: CuePlayer(GuiObject *                              parent,
                       Gtk::TreeView *                          treeView,
                       const PlayableTreeModelColumnRecord &    modelColumns)
                                                                    throw ()
          : GuiComponent(parent),
            treeView(treeView),
            modelColumns(modelColumns)
{
    glade->get_widget("cuePlayButton1", playButton);
    glade->get_widget("cueStopButton1", stopButton);

    playButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &CuePlayer::onPlayButtonClicked ));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &CuePlayer::onStopButtonClicked ));

    audioState = waitingState;
    
    gLiveSupport->attachCueAudioListener(this);
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
CuePlayer :: ~CuePlayer(void)                                       throw ()
{
    try {
        gLiveSupport->detachCueAudioListener(this);
    } catch (std::invalid_argument &e) {
        std::cerr << "Could not detach cue player audio listener."
                  << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Play menu item selected from the entry context menu
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onPlayItem(void)                                       throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> 
                    selection       = treeView->get_selection();
    std::vector<Gtk::TreePath> 
                    selectedRows    = selection->get_selected_rows();
    Gtk::TreeIter   iter;
    
    if (selectedRows.size() > 0) {
        Gtk::TreePath   path = selectedRows.front();
        iter = treeView->get_model()->get_iter(path);
    } else {
        iter = treeView->get_model()->children().begin();
    }
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
        try {
            setAudioState(playingState);
            gLiveSupport->playCueAudio(playable);
        } catch (std::runtime_error &e) {
            std::cerr << "GLiveSupport::playCueAudio() error:"
                        << std::endl << e.what() << std::endl;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Pause the song.
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onPauseItem(void)                                      throw ()
{
    try {
        gLiveSupport->pauseCueAudio();
        setAudioState(pausedState);
    } catch (std::logic_error &e) {
        std::cerr << "GLiveSupport::pauseCueAudio() error:" << std::endl
                    << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Play button getting clicked
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onPlayButtonClicked(void)                              throw ()
{
    switch (audioState) {
        case waitingState:
            onPlayItem();
            break;
            
        case playingState:
            onPauseItem();
            break;
            
        case pausedState:
            try {
                gLiveSupport->pauseCueAudio();      // ie, restart
                setAudioState(playingState);
            } catch (std::logic_error &e) {
                std::cerr << "GLiveSupport::pauseCueAudio() error:" << std::endl
                            << e.what() << std::endl;
            }
            break;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Stop button getting clicked
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onStopButtonClicked(void)                              throw ()
{
    if (audioState != waitingState) {
        try {
            gLiveSupport->stopCueAudio();
        } catch (std::logic_error &e) {
            std::cerr << "GLiveSupport::stopCueAudio() error:" << std::endl
                        << e.what() << std::endl;
        }
        setAudioState(waitingState);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the "cue audio player has stopped" event.
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onStop(Ptr<const Glib::ustring>::Ref  errorMessage)    throw ()
{
    setAudioState(waitingState);
    
    if (errorMessage) {
        gLiveSupport->displayMessageWindow(*errorMessage);
    }
}

/*------------------------------------------------------------------------------
 *  Event handler for the "cue audio player has started" event.
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onStart(gint64 id)    throw ()
{
}


/*------------------------------------------------------------------------------
 *  Set the state of the widget.
 *----------------------------------------------------------------------------*/
void
CuePlayer :: setAudioState(AudioState    newState)                  throw ()
{
    if ((audioState == waitingState || audioState == pausedState)
                && newState == playingState) {
        playButton->set_label(pauseStockImageName);
        
    } else if (audioState == playingState
                && (newState == waitingState || newState == pausedState)) {
        playButton->set_label(playStockImageName);
    }
    
    audioState = newState;
}

