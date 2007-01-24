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

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Colors.h"

#include "NowPlaying.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/**
 *  This should be 1/4th of MasterPanelWindow::updateTimeConstant.
 */
const int   blinkingConstant = 5;

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
NowPlaying :: NowPlaying(Ptr<GLiveSupport>::Ref     gLiveSupport,
                         Ptr<ResourceBundle>::Ref   bundle)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    playButton = Gtk::manage(wf->createButton(
                                    WidgetConstants::masterPlayButton ));
    pauseButton = Gtk::manage(wf->createButton(
                                    WidgetConstants::masterPauseButton ));
    stopButton = Gtk::manage(wf->createButton(
                                    WidgetConstants::masterStopButton ));

    playButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onPlayButtonClicked ));
    pauseButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onPauseButtonClicked ));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onStopButtonClicked ));

    isActive = false;
    isPaused = false;

    titleLabel = createFormattedLabel(14);
    titleLabel->set_ellipsize(Pango::ELLIPSIZE_END);
    
    creatorLabel = createFormattedLabel(8);
    creatorLabel->set_ellipsize(Pango::ELLIPSIZE_END);
    
    playlistLabel = createFormattedLabel(8);
    playlistLabel->set_ellipsize(Pango::ELLIPSIZE_END);
    
    Gtk::Label *    elapsedLabel = createFormattedLabel(7);
    Gtk::Label *    remainsLabel = createFormattedLabel(7);
    elapsedTime = createFormattedLabel(16);
    remainsTime = createFormattedLabel(16);
    
    Gtk::HBox *         elapsedTimeHBox = Gtk::manage(new Gtk::HBox);
    Gtk::VBox *         elapsedTimeVBox = Gtk::manage(new Gtk::VBox);
    elapsedTimeHBox->pack_start(*elapsedTime,     Gtk::PACK_SHRINK, 5);
    elapsedTimeVBox->pack_start(*elapsedTimeHBox, Gtk::PACK_SHRINK, 2);
    
    remainsTimeBox = Gtk::manage(new Gtk::EventBox);
    Gtk::HBox *         remainsTimeHBox = Gtk::manage(new Gtk::HBox);
    Gtk::VBox *         remainsTimeVBox = Gtk::manage(new Gtk::VBox);
    remainsTimeHBox->pack_start(*remainsTime,     Gtk::PACK_SHRINK, 5);
    remainsTimeVBox->pack_start(*remainsTimeHBox, Gtk::PACK_SHRINK, 2);
    remainsTimeBox->add(*remainsTimeVBox);
    resetRemainsTimeState();

    try {
        elapsedLabel->set_text(*getResourceUstring("elapsedTimeLabel"));
        remainsLabel->set_text(*getResourceUstring("remainingTimeLabel"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    Gtk::Box *      titleBox = Gtk::manage(new Gtk::HBox);
    titleBox->pack_start(*titleLabel, Gtk::PACK_EXPAND_WIDGET, 5);
    
    Gtk::Box *      creatorBox = Gtk::manage(new Gtk::HBox);
    creatorBox->pack_start(*creatorLabel, Gtk::PACK_EXPAND_WIDGET, 5);
    
    Gtk::Box *      extraSpace = Gtk::manage(new Gtk::HBox);
    
    Gtk::Box *      playlistBox = Gtk::manage(new Gtk::HBox);
    playlistBox->pack_start(*playlistLabel, Gtk::PACK_EXPAND_WIDGET, 5);
    
    Gtk::Box *      elapsedTextBox = Gtk::manage(new Gtk::HBox);
    elapsedTextBox->pack_start(*elapsedLabel, Gtk::PACK_EXPAND_WIDGET, 5);
    elapsedTextBox->set_size_request(150);      // set a fixed width
    
    Gtk::Box *      elapsedBox = Gtk::manage(new Gtk::VBox);
    elapsedBox->pack_start(*elapsedTextBox,     Gtk::PACK_SHRINK, 0);
    elapsedBox->pack_start(*elapsedTimeVBox,    Gtk::PACK_SHRINK, 0);
    
    Gtk::Box *      remainsTextBox = Gtk::manage(new Gtk::HBox);
    remainsTextBox->pack_start(*remainsLabel, Gtk::PACK_EXPAND_WIDGET, 5);
    
    Gtk::Box *      remainsBox = Gtk::manage(new Gtk::VBox);
    remainsBox->pack_start(*remainsTextBox, Gtk::PACK_SHRINK, 0);
    remainsBox->pack_start(*remainsTimeBox, Gtk::PACK_SHRINK, 0);
    
    Gtk::Box *      timeBox = Gtk::manage(new Gtk::HBox);
    timeBox->pack_start(*elapsedBox, Gtk::PACK_SHRINK, 0);
    timeBox->pack_start(*remainsBox, Gtk::PACK_SHRINK, 0);
    
    Gtk::Box *      textBox = Gtk::manage(new Gtk::VBox);
    textBox->pack_start(*titleBox,      Gtk::PACK_SHRINK, 0);
    textBox->pack_start(*creatorBox,    Gtk::PACK_SHRINK, 0);
    textBox->pack_start(*extraSpace,    Gtk::PACK_SHRINK, 2);
    textBox->pack_start(*timeBox,       Gtk::PACK_SHRINK, 0);
    textBox->pack_start(*playlistBox,   Gtk::PACK_SHRINK, 0);
    
    pack_end(*textBox, Gtk::PACK_EXPAND_WIDGET, 0);
    pack_end(*stopButton, Gtk::PACK_SHRINK, 0);
    pack_end(*playButton, Gtk::PACK_SHRINK, 2);
}


/*------------------------------------------------------------------------------
 *  Set the title etc. of the playable shown in the widget.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: setPlayable(Ptr<Playable>::Ref  playable)             throw ()
{
    if (playable) {
        if (!isActive || isPaused) {
            remove(*playButton);
            pack_end(*pauseButton, Gtk::PACK_SHRINK, 2);
            pauseButton->show();
        }
        this->playable = playable;
        isActive = true;
        isPaused = false;
        resetRemainsTimeState();
        onUpdateTime();
        
    } else {
        if (isActive && !isPaused) {
            remove(*pauseButton);
            pack_end(*playButton, Gtk::PACK_SHRINK, 2);
            playButton->show();
            isActive = false;
        }
        titleLabel->set_text("");
        creatorLabel->set_text("");
        elapsedTime->set_text("");
        remainsTime->set_text("");
        playlistLabel->set_text("");
        resetRemainsTimeState();
        this->playable.reset();
        this->currentInnerPlayable.reset();
    }

    gLiveSupport->updateRds();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Play button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onPlayButtonClicked(void)                             throw ()
{
    if (isActive && isPaused) {
        gLiveSupport->pauseOutputAudio();       // i.e., restart

        remove(*playButton);
        pack_end(*pauseButton, Gtk::PACK_SHRINK, 2);
        pauseButton->show();
        
        isPaused = false;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Pause button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onPauseButtonClicked(void)                            throw ()
{
    if (isActive && !isPaused) {
        gLiveSupport->pauseOutputAudio();
        
        remove(*pauseButton);
        pack_end(*playButton, Gtk::PACK_SHRINK, 2);
        playButton->show();   
    
        isPaused = true;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Stop button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onStopButtonClicked(void)                             throw ()
{
    if (isActive) {
        gLiveSupport->stopOutputAudio();    // triggers a call to GLiveSupport::
    }                                       // onStop(), which in turn calls
}                                           // setPlayable() with a 0 argument


/*------------------------------------------------------------------------------
 *  Construct a label with the font attribute already set.
 *----------------------------------------------------------------------------*/
Gtk::Label *
NowPlaying :: createFormattedLabel(int    fontSize)                 throw ()
{
    Gtk::Label *    label = Gtk::manage(new Gtk::Label("", Gtk::ALIGN_LEFT,
                                                           Gtk::ALIGN_CENTER));
    
    Pango::FontDescription  fontDescription;
    fontDescription.set_family("Bitstream Vera Sans");
    fontDescription.set_weight(Pango::WEIGHT_BOLD);
    fontDescription.set_size(fontSize * Pango::SCALE);
    
    Pango::Attribute        fontDescriptionAttribute = 
                                Pango::Attribute::create_attr_font_desc(
                                    fontDescription);
    fontDescriptionAttribute.set_start_index(0);
    fontDescriptionAttribute.set_end_index(255);
    
    Pango::AttrList         attributeList;
    attributeList.insert(fontDescriptionAttribute);
    label->set_attributes(attributeList);
    
    return label;
}


/*------------------------------------------------------------------------------
 *  Update the timer displays. This is called every second by the master panel.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onUpdateTime(void)                                    throw ()
{
    if (!isActive) {
        return;
    }

    remainsTimeCounter++;
    if (remainsTimeCounter == 2*blinkingConstant) {
        remainsTimeCounter = 0;
    }

    Ptr<time_duration>::Ref     elapsed;
    try {
        elapsed = gLiveSupport->getOutputAudioPosition();
        
    } catch (std::logic_error &e) {
        // just act as if nothing has happened
        return;
    }
    
    Ptr<time_duration>::Ref     totalLength
                                = TimeConversion::roundToNearestSecond(
                                                    playable->getPlaylength());
    Ptr<time_duration>::Ref     remains(new time_duration(
                                                    *totalLength - *elapsed));
    switch (remainsTimeState) {
        case TIME_GREEN :
            if (*remains <= seconds(20)) {
                remainsTimeState = TIME_YELLOW;
            }
            break;
        
        case TIME_YELLOW :
            if (*remains <= seconds(10)) {
                remainsTimeState = TIME_RED;
            }
            break;
        
        case TIME_RED :
            break;
    }
    setRemainsTimeColor(remainsTimeState);

    Ptr<Playable>::Ref          innerPlayable   = playable;
    Ptr<time_duration>::Ref     innerElapsed    = elapsed;
    Ptr<time_duration>::Ref     innerRemains    = remains;
    Glib::ustring               playlistInfo;
    bool                        isFirst      = true;
    
    while (innerPlayable->getType() == Playable::PlaylistType) {
        if (isFirst) {
            isFirst = false;
        } else {
            playlistInfo += "   >>>   ";
        }
        playlistInfo += *innerPlayable->getTitle();
        playlistInfo += " [";
        playlistInfo += *TimeConversion::timeDurationToHhMmSsString(
                                                innerRemains);
        playlistInfo += "/";
        playlistInfo += *TimeConversion::timeDurationToHhMmSsString(
                                                innerPlayable->getPlaylength());
        playlistInfo += "]";
        
        Ptr<PlaylistElement>::Ref   element
                                    = innerPlayable->getPlaylist()
                                                   ->findAtOffset(elapsed);
        if (!element) {
            break;
        }
        innerPlayable = element->getPlayable();
        *innerElapsed -= *element->getRelativeOffset();
        *innerRemains = *TimeConversion::roundToNearestSecond(
                                                innerPlayable->getPlaylength())
                      - *innerElapsed;
    }

    playlistLabel->set_text(playlistInfo);

    titleLabel->set_text(*innerPlayable->getTitle());

    Ptr<Glib::ustring>::Ref
                    creator = innerPlayable->getMetadata("dc:creator");
    if (creator) {
        creatorLabel->set_text(*creator);
    } else {
        creatorLabel->set_text("");
    }
    
    elapsedTime->set_text(*TimeConversion::timeDurationToHhMmSsString(
                                                innerElapsed ));
    remainsTime->set_text(*TimeConversion::timeDurationToHhMmSsString(
                                                innerRemains ));

    currentInnerPlayable = innerPlayable;
}


/*------------------------------------------------------------------------------
 *  Set the background color of the "remains time" label.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: setRemainsTimeColor(RemainsTimeStateType  state)      throw ()
{
    bool        isBlinkOn = (remainsTimeCounter < blinkingConstant);

    Gdk::Color  color;
    
    if (isBlinkOn) {
        switch (state) {
            case TIME_GREEN:
                color = Colors::getColor(Colors::MasterPanelCenterBlue);
                break;
                
            case TIME_YELLOW:
                color = Colors::getColor(Colors::Yellow);
                break;
                
            case TIME_RED:
                color = Colors::getColor(Colors::Red);
                break;
        }
    } else {
        color = Colors::getColor(Colors::MasterPanelCenterBlue);
    }
    
    remainsTimeBox->modify_bg(Gtk::STATE_NORMAL, color);
}                


/*------------------------------------------------------------------------------
 *  Reset all remains-time-blinking related variables.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: resetRemainsTimeState(void)                           throw ()
{
    remainsTimeState   = TIME_GREEN;
    remainsTimeCounter = 0;
    setRemainsTimeColor(TIME_GREEN);
}

