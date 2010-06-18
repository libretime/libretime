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
NowPlaying :: NowPlaying (GuiObject *      parent)
                                                                    throw ()
          : GuiComponent(parent)
{
    glade->get_widget("playButton1", playButton);
    glade->get_widget("stopButton1", stopButton);

    playButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onPlayButtonClicked ));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onStopButtonClicked ));

    isActive = false;
    isPaused = false;

    glade->get_widget("titleLabel1", titleLabel);
    glade->get_widget("creatorLabel1", creatorLabel);
    glade->get_widget("elapsedTimeLabel1", elapsedTimeLabel);
    glade->get_widget("remainsTimeEventBox1", remainsTimeBox);
    glade->get_widget("remainsTimeLabel1", remainsTimeLabel);
    glade->get_widget("playlistLabel1", playlistLabel);
    setStyle(titleLabel, 14);
    setStyle(creatorLabel, 8);
    setStyle(elapsedTimeLabel, 16);
    setStyle(remainsTimeLabel, 16);
    setStyle(playlistLabel, 8);

    glade->get_widget("elapsedTimeText1", elapsedTimeText);
    glade->get_widget("remainsTimeText1", remainsTimeText);
    setStyle(elapsedTimeText, 7);
    setStyle(remainsTimeText, 7);
    elapsedTimeText->set_text(*getResourceUstring("elapsedTimeLabel"));
    remainsTimeText->set_text(*getResourceUstring("remainingTimeLabel"));

    glade->get_widget("progressBar1", progressBar);

    Ptr<Playable>::Ref      nullPointer;
    setPlayable(nullPointer);
}


/*------------------------------------------------------------------------------
 *  Set the font and size of a label.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: setStyle (Gtk::Label *       label,
                        int                fontSize)                throw ()
{
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
}

void
NowPlaying :: setCurrentInnerPlayable (gint64 id)                              throw ()
{
    playableMutex.lock();
	//keep this for future use
	if((gint64)currentInnerPlayable->getId()->getId() != id)
	{
		//we are not playing a correct file, must have had an error - adjust the playlist
//std::cout << "NowPlaying :: setCurrentInnerPlayable ERROR DETECTED! called = " << id << ", current = " << (gint64)currentInnerPlayable->getId()->getId() << std::endl;
	}
	else{
//std::cout << "NowPlaying :: setCurrentInnerPlayable CORRECT!" << std::endl;
	}
    playableMutex.unlock();
}

/*------------------------------------------------------------------------------
 *  Set the title etc. of the playable shown in the widget.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: setPlayable (Ptr<Playable>::Ref  playable)            throw ()
{
    playableMutex.lock();
    // BEGIN synchronized block

    if (playable) {
        if (!isActive || isPaused) {
            playButton->set_label(pauseStockImageName);
        }
        playButton->set_sensitive(true);
        stopButton->set_sensitive(true);

        this->playable = playable;
        isActive = true;
        isPaused = false;
        resetRemainsTimeState();

		remainsTimeCounter++;
		if (remainsTimeCounter == 2*blinkingConstant) {
			remainsTimeCounter = 0;
		}

		Ptr<time_duration>::Ref     elapsed;
		try {
			elapsed = gLiveSupport->getOutputAudioPosition();
		} catch (std::logic_error &e) {
			elapsed.reset(new time_duration(microseconds(0)));
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
			playlistInfo += *TimeConversion::timeDurationToHhMmSsString(innerRemains);
			playlistInfo += "/";
			playlistInfo += *TimeConversion::timeDurationToHhMmSsString(innerPlayable->getPlaylength());
			playlistInfo += "]";
			
			Ptr<PlaylistElement>::Ref   element = innerPlayable->getPlaylist()->findAtOffset(elapsed);
			 if (!element) {
				break;
			}
			innerPlayable = element->getPlayable();
			*innerElapsed -= *element->getRelativeOffset();
			*innerRemains = *TimeConversion::roundToNearestSecond(
													innerPlayable->getPlaylength()) - *innerElapsed;
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
		
		elapsedTimeLabel->set_text(*TimeConversion::timeDurationToHhMmSsString(innerElapsed ));
		remainsTimeLabel->set_text(*TimeConversion::timeDurationToHhMmSsString(innerRemains ));

		long    elapsedMilliSec = innerElapsed->total_milliseconds();
		long    totalMilliSec = elapsedMilliSec
								+ innerRemains->total_milliseconds();
		double  fraction = double(elapsedMilliSec) / double(totalMilliSec);
		if (fraction < 0.0) {
			fraction = 0.0;     // can't happen afaik
		}
		if (fraction > 1.0) {
			fraction = 1.0;     // can and does happen!
		}
		progressBar->set_fraction(fraction);

		currentInnerPlayable = innerPlayable;
    } else {
        if (isActive && !isPaused) {
            playButton->set_label(playStockImageName);
            isActive = false;
        }
        playButton->set_sensitive(false);
        stopButton->set_sensitive(false);

        titleLabel->set_text("");
        creatorLabel->set_text("");
        elapsedTimeLabel->set_text("");
        remainsTimeLabel->set_text("");
        playlistLabel->set_text("");
        progressBar->set_fraction(0);
        resetRemainsTimeState();
        this->playable.reset();
        this->currentInnerPlayable.reset();
    }
    
    // END synchronized block
    playableMutex.unlock();
}


/*------------------------------------------------------------------------------
 *  Event handler for the Play button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onPlayButtonClicked (void)                            throw ()
{
    if (isActive) {
        if (isPaused) {
            gLiveSupport->pauseOutputAudio();       // i.e., restart
            playButton->set_label(pauseStockImageName);
            isPaused = false;
        } else {
            gLiveSupport->pauseOutputAudio();
            playButton->set_label(playStockImageName);
            isPaused = true;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Stop button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onStopButtonClicked (void)                            throw ()
{
    if (isActive) {
        gLiveSupport->stopOutputAudio();    // triggers a call to GLiveSupport::
    }                                       // onStop(), which in turn calls
}                                           // setPlayable() with a 0 argument


/*------------------------------------------------------------------------------
 *  Update the timer displays. This is called every second by the master panel.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onUpdateTime (void)                                   throw ()
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

    if (!playableMutex.tryLock()) {     // if the 'playable' variable is being
        return;                         // written to, then just give up for now
    }
    // BEGIN synchronized block
    
    if (!playable) {
        playableMutex.unlock();
        return;
    }
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
    
    elapsedTimeLabel->set_text(*TimeConversion::timeDurationToHhMmSsString(
                                                        innerElapsed ));
    remainsTimeLabel->set_text(*TimeConversion::timeDurationToHhMmSsString(
                                                        innerRemains ));

    long    elapsedMilliSec = innerElapsed->total_milliseconds();
    long    totalMilliSec = elapsedMilliSec
                            + innerRemains->total_milliseconds();
    double  fraction = double(elapsedMilliSec) / double(totalMilliSec);
    if (fraction < 0.0) {
        fraction = 0.0;     // can't happen afaik
    }
    if (fraction > 1.0) {
        fraction = 1.0;     // can and does happen!
    }
    progressBar->set_fraction(fraction);

    currentInnerPlayable = innerPlayable;
    
    // END synchronized block
    playableMutex.unlock();
}


/*------------------------------------------------------------------------------
 *  Set the background color of the "remains time" label.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: setRemainsTimeColor (RemainsTimeStateType  state)     throw ()
{
    bool        isBlinkOn = (remainsTimeCounter < blinkingConstant);

    if (isBlinkOn) {
        switch (state) {
            case TIME_GREEN:
                remainsTimeBox->unset_bg(Gtk::STATE_NORMAL);
                break;
                
            case TIME_YELLOW:
                remainsTimeBox->modify_bg(Gtk::STATE_NORMAL,
                                          Colors::getColor(Colors::Yellow));
                break;
                
            case TIME_RED:
                remainsTimeBox->modify_bg(Gtk::STATE_NORMAL,
                                          Colors::getColor(Colors::Red));
                break;
        }
    } else {
        remainsTimeBox->unset_bg(Gtk::STATE_NORMAL);
    }

    gLiveSupport->runMainLoop();
}


/*------------------------------------------------------------------------------
 *  Reset all remains-time-blinking related variables.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: resetRemainsTimeState (void)                          throw ()
{
    remainsTimeState   = TIME_GREEN;
    remainsTimeCounter = 0;
    setRemainsTimeColor(TIME_GREEN);
}


/*------------------------------------------------------------------------------
 *  Change the language of the widget.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: changeLanguage (void)
                                                                    throw ()
{
    setBundle(parent->getBundle());

    elapsedTimeText->set_text(*getResourceUstring("elapsedTimeLabel"));
    remainsTimeText->set_text(*getResourceUstring("remainingTimeLabel"));
}


