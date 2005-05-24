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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/CuePlayer.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"

#include "CuePlayer.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
CuePlayer :: CuePlayer(Ptr<GLiveSupport>::Ref                   gLiveSupport,
                       Gtk::TreeView *                          treeView,
                       const PlayableTreeModelColumnRecord &    modelColumns)
                                                                    throw ()
          : gLiveSupport(gLiveSupport),
            treeView(treeView),
            modelColumns(modelColumns)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    playButton = Gtk::manage(wf->createButton(
                                    WidgetFactory::smallPlayButton ));
    pauseButton = Gtk::manage(wf->createButton(
                                    WidgetFactory::smallPauseButton ));
    stopButton = Gtk::manage(wf->createButton(
                                    WidgetFactory::smallStopButton ));

    playButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &CuePlayer::onPlayButtonClicked ));
    pauseButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &CuePlayer::onPauseButtonClicked ));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &CuePlayer::onStopButtonClicked ));

    pack_end(*stopButton, Gtk::PACK_SHRINK, 3);
    pack_end(*playButton, Gtk::PACK_SHRINK, 3);

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
 *  Event handler for the Play menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onPlayItem(void)                                       throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> 
                    selection       = treeView->get_selection();
    std::vector<Gtk::TreePath> 
                    selectedRows    = selection->get_selected_rows();

    if (selectedRows.size() == 1) {
        Gtk::TreePath   path = selectedRows.front();
        Gtk::TreeIter   iter = treeView->get_model()->get_iter(path);
        if (iter) {
            Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];
            try {
                gLiveSupport->playCueAudio(playable);
            } catch (std::logic_error &e) {
                std::cerr << "GLiveSupport::playCueAudio() error:"
                          << std::endl << e.what() << std::endl;
            }
            audioState = playingState;
            remove(*playButton);
            pack_end(*pauseButton, Gtk::PACK_SHRINK, 3);
            pauseButton->show();
        }
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
        case pausedState:
            try {
                gLiveSupport->pauseCueAudio();      // ie, restart
                audioState = playingState;
                remove(*playButton);
                pack_end(*pauseButton, Gtk::PACK_SHRINK, 3);
                pauseButton->show();
            } catch (std::logic_error &e) {
                std::cerr << "GLiveSupport::pauseCueAudio() error:" << std::endl
                            << e.what() << std::endl;
            }
            break;
        case playingState:      // should never happen
            std::cerr << "Assertion failed in CuePlayer:" << std::endl
                      << "play button clicked when it should not be visible."
                      << std::endl;
            break;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Pause button getting clicked
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onPauseButtonClicked(void)                             throw ()
{
    try {
        gLiveSupport->pauseCueAudio();
        audioState = pausedState;
        remove(*pauseButton);
        pack_end(*playButton, Gtk::PACK_SHRINK, 3);
        playButton->show();
    } catch (std::logic_error &e) {
        std::cerr << "GLiveSupport::pauseCueAudio() error:" << std::endl
                    << e.what() << std::endl;
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
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the "cue audio player has stopped" event.
 *----------------------------------------------------------------------------*/
void
CuePlayer :: onStop(void)                                           throw ()
{
    switch (audioState) {
        case pausedState:
            remove(*playButton);
            break;
        case playingState:
            remove(*pauseButton);
            break;
        case waitingState:      // sometimes onStop() is called twice
            return;
    }
    audioState = waitingState;
    pack_end(*playButton, Gtk::PACK_SHRINK, 3);
    playButton->show();
}

