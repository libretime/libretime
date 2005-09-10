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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/NowPlaying.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Widgets/WidgetFactory.h"

#include "NowPlaying.h"


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
NowPlaying :: NowPlaying(Ptr<GLiveSupport>::Ref     gLiveSupport,
                         Ptr<ResourceBundle>::Ref   bundle)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    playButton = Gtk::manage(wf->createButton(
                                    WidgetFactory::masterPlayButton ));
    pauseButton = Gtk::manage(wf->createButton(
                                    WidgetFactory::masterPauseButton ));
    stopButton = Gtk::manage(wf->createButton(
                                    WidgetFactory::masterStopButton ));

    playButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onPlayButtonClicked ));
    pauseButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onPauseButtonClicked ));
    stopButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &NowPlaying::onStopButtonClicked ));

    isActive = false;
    isPaused = false;

    label = Gtk::manage(new Gtk::Label);
    label->set_use_markup(true);
    label->set_ellipsize(Pango::ELLIPSIZE_END);
    label->set_markup("");
    
    Gtk::Label *    elapsedLabel = createFormattedLabel(8);
    Gtk::Label *    remainsLabel = createFormattedLabel(8);
    elapsedTime = createFormattedLabel(12);
    remainsTime = createFormattedLabel(12);

    try {
        elapsedLabel->set_text(*getResourceUstring("elapsedTimeLabel"));
        remainsLabel->set_text(*getResourceUstring("remainingTimeLabel"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    Gtk::Box *      elapsedBox = Gtk::manage(new Gtk::VBox);
    elapsedBox->pack_start(*elapsedLabel, Gtk::PACK_EXPAND_WIDGET, 2);
    elapsedBox->pack_start(*elapsedTime,  Gtk::PACK_EXPAND_WIDGET, 2);
    
    Gtk::Box *      remainsBox = Gtk::manage(new Gtk::VBox);
    remainsBox->pack_start(*remainsLabel, Gtk::PACK_EXPAND_WIDGET, 2);
    remainsBox->pack_start(*remainsTime,  Gtk::PACK_EXPAND_WIDGET, 2);
    
    Gtk::Box *      timeBox = Gtk::manage(new Gtk::HBox);
    timeBox->pack_start(*elapsedBox, Gtk::PACK_EXPAND_WIDGET, 2);
    timeBox->pack_start(*remainsBox, Gtk::PACK_EXPAND_WIDGET, 2);
    
    Gtk::Box *      textBox = Gtk::manage(new Gtk::VBox);
    textBox->pack_start(*label,   Gtk::PACK_EXPAND_PADDING, 2);
    textBox->pack_start(*timeBox, Gtk::PACK_EXPAND_PADDING, 2);
    
    pack_end(*textBox, Gtk::PACK_EXPAND_WIDGET, 5);
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
        isActive = true;
        isPaused = false;
    
        Ptr<Glib::ustring>::Ref     infoString(new Glib::ustring);
    
        infoString->append("<span font_desc='Bitstream Vera Sans"
                           " Bold 16'>");
        infoString->append(Glib::Markup::escape_text(*playable->getTitle()));
        infoString->append("</span>    ");

        // TODO: rewrite this using the Core::Metadata class

        Ptr<Glib::ustring>::Ref 
                        creator = playable->getMetadata("dc:creator");
        if (creator) {
            infoString->append("<span font_desc='Bitstream Vera Sans"
                               " Bold 16'>");
            infoString->append(Glib::Markup::escape_text(*creator));
            infoString->append("</span>");
        }
        label->set_markup(*infoString);
        
        audioLength = playable->getPlaylength();
        
    } else {
        if (isActive && !isPaused) {
            remove(*pauseButton);
            pack_end(*playButton, Gtk::PACK_SHRINK, 2);
            playButton->show();
            isActive = false;
        }
        label->set_markup("");
        elapsedTime->set_text("");
        remainsTime->set_text("");
        audioLength.reset();
    }
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
    Gtk::Label *    label = Gtk::manage(new Gtk::Label);
    
    Pango::FontDescription  fontDescription;
    fontDescription.set_family("Bitstream Vera Sans");
    fontDescription.set_weight(Pango::WEIGHT_BOLD);
    fontDescription.set_size(fontSize * Pango::SCALE);
    
    Pango::Attribute        fontDescriptionAttribute = 
                                Pango::Attribute::create_attr_font_desc(
                                    fontDescription);
    fontDescriptionAttribute.set_start_index(0);
    fontDescriptionAttribute.set_end_index(100);
    
    Pango::AttrList         attributeList;
    attributeList.insert(fontDescriptionAttribute);
    label->set_attributes(attributeList);
    
    return label;
}


/*------------------------------------------------------------------------------
 *  Update the timer displays. This is called every second by the master panel.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onUpdateTime(void)
                                                                    throw ()
{
    if (isActive) {
        try {
            Ptr<time_duration>::Ref     elapsed = gLiveSupport->
                                                      getOutputAudioPosition();
            Ptr<time_duration>::Ref     remains(new time_duration(
                                                    *audioLength - *elapsed ));
            elapsedTime->set_text(*TimeConversion::timeDurationToHhMmSsString(
                                                        elapsed ));
            remainsTime->set_text(*TimeConversion::timeDurationToHhMmSsString(
                                                        remains ));
        } catch (std::logic_error &e) {
            // just act as if nothing has happened
        }
    }
}

