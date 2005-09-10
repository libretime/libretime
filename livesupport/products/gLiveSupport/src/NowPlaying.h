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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/NowPlaying.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_GLiveSupport_NowPlaying_h
#define LiveSupport_GLiveSupport_NowPlaying_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"

#include "GLiveSupport.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The box displaying "now playing" in the master panel.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class NowPlaying : public Gtk::HBox,
                   public LocalizedObject
{
    private:
    
        /**
         *  Whether anything is shown in the widget.
         */
        bool                    isActive;

        /**
         *  Whether the pause button has been clicked.
         */
        bool                    isPaused;

        /**
         *  The length of the item currently playing.
         */
        Ptr<time_duration>::Ref audioLength;

        /**
         *  The label holding the title etc. of the now playing item.
         */
        Gtk::Label *            label;

        /**
         *  The label holding the elapsed time.
         */
        Gtk::Label *            elapsedTime;

        /**
         *  The label holding the remaining time.
         */
        Gtk::Label *            remainsTime;

        /**
         *  The play button.
         */
        ImageButton *           playButton;

        /**
         *  The pause button.
         */
        ImageButton *           pauseButton;

        /**
         *  The stop button.
         */
        ImageButton *           stopButton;

        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref  gLiveSupport;
         
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
         *  Event handler for the Pause button being clicked.
         */
        void
        onPauseButtonClicked(void)                      throw ();

        /**
         *  Event handler for the Stop button being clicked.
         */
        void
        onStopButtonClicked(void)                       throw ();

        /**
         *  Return a Gtk::manage'd Gtk::Label*, with the Bitstream Vera
         *  font attributes set.
         *
         *  @param  fontSize    the size of the text in the label, in points
         *  @return the new label
         */
        Gtk::Label *
        createFormattedLabel(int    fontSize)           throw ();

    
    public:
    
        /**
         *  Constructor with parent and localization parameter.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this widget
         */
        NowPlaying(Ptr<GLiveSupport>::Ref       gLiveSupport,
                   Ptr<ResourceBundle>::Ref     bundle)
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

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveSupport_GLiveSupport_NowPlaying_h

