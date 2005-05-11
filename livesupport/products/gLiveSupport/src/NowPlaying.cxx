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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/NowPlaying.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

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
    label->set_ellipsize(Pango::ELLIPSIZE_END);
    pack_end(*label, Gtk::PACK_EXPAND_WIDGET, 5);
}


/*------------------------------------------------------------------------------
 *  Set the title etc. of the playable shown in the widget.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: setPlayable(Ptr<Playable>::Ref  playable)             throw ()
{
    if (playable) {
        if (!isActive) {
            pack_end(*stopButton,  Gtk::PACK_SHRINK, 0);
            pack_end(*pauseButton, Gtk::PACK_SHRINK, 2);
            stopButton->show();
            pauseButton->show();
            isActive = true;
            isPaused = false;
        }
    
        Ptr<Glib::ustring>::Ref     infoString(new Glib::ustring);
    
        infoString->append("<span size=\"larger\" weight=\"bold\">");
        infoString->append(Glib::Markup::escape_text(*playable->getTitle()));
        infoString->append("</span>        ");

        // TODO: rewrite this using the Core::Metadata class

        Ptr<Glib::ustring>::Ref 
                        creator = playable->getMetadata("dc:creator");
        if (creator) {
            infoString->append("<span size=\"larger\" weight=\"bold\">");
            infoString->append(Glib::Markup::escape_text(*creator));
            infoString->append("</span>");
        }
        label->set_markup(*infoString);
        this->playable = playable;
    } else {
        label->set_text("");
        if (isActive) {
            remove(*stopButton);
            if (isPaused) {
                remove(*playButton);
            } else {
                remove(*pauseButton);
            }
            isActive = false;
        }
        this->playable.reset();
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Play button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onPlayButtonClicked(void)                             throw ()
{
    gLiveSupport->pauseOutputAudio();       // i.e., restart

    remove(*playButton);
    pack_end(*pauseButton, Gtk::PACK_SHRINK, 2);
    pauseButton->show();
    
    isPaused = false;
}


/*------------------------------------------------------------------------------
 *  Event handler for the Pause button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onPauseButtonClicked(void)                            throw ()
{
    gLiveSupport->pauseOutputAudio();

    remove(*pauseButton);
    pack_end(*playButton, Gtk::PACK_SHRINK, 2);
    playButton->show();   
    
    isPaused = true;
}


/*------------------------------------------------------------------------------
 *  Event handler for the Stop button being clicked.
 *----------------------------------------------------------------------------*/
void
NowPlaying :: onStopButtonClicked(void)                             throw ()
{
    gLiveSupport->stopOutputAudio();
}

