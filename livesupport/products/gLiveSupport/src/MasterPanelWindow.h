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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/MasterPanelWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef MasterPanelWindow_h
#define MasterPanelWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/button.h>
#include <gtkmm/table.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"

#include "GtkLocalizedObject.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The master panel window.
 *
 *  The layout of the window is roughly the following:
 *  <pre><code>
 *  +--- master panel --------------------------------------------------------+
 *  | + LS logo + + time + + now ----+ + VU meter + + on-air + + radio logo + |
 *  | |         | |      | | playing | |          | |        | |            | |
 *  | |         | |      | |         | +----------+ +--------+ +------------+ |
 *  | |         | |      | |         | + next ----+ + user info ------------+ |
 *  | |         | |      | |         | | playing  | |                       | |
 *  | +---------+ +------+ +---------+ +----------+ +-----------------------+ |
 *  +-------------------------------------------------------------------------+
 *  </code></pre>
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class MasterPanelWindow : public Gtk::Window, public GtkLocalizedObject
{
    protected:
        /**
         *  The layout used in the window.
         */
        Ptr<Gtk::Table>::Ref        layout;

        /**
         *  The LiveSupport logo
         */
        Ptr<Gtk::Widget>::Ref       lsLogoWidget;

        /**
         *  The time display
         */
        Ptr<Gtk::Label>::Ref        timeWidget;

        /**
         *  The signal connection, that is notified by the GTK timer each
         *  second, and will update the time display on each wakeup.
         */
        Ptr<sigc::connection>::Ref  timer;

        /**
         *  The 'now playing' display.
         */
        Ptr<Gtk::Widget>::Ref       nowPlayingWidget;

        /**
         *  The VU meter display.
         */
        Ptr<Gtk::Widget>::Ref       vuMeterWidget;

        /**
         *  The 'next playing' display.
         */
        Ptr<Gtk::Widget>::Ref       nextPlayingWidget;

        /**
         *  The on-air indicator.
         */
        Ptr<Gtk::Widget>::Ref       onAirWidget;

        /**
         *  The user info widget.
         */
        Ptr<Gtk::Widget>::Ref       userInfoWidget;

        /**
         *  The radio logo.
         */
        Ptr<Gtk::Widget>::Ref       radioLogoWidget;

        /**
         *  The gLiveSupport object, handling the logic of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  Function that updates timeLabel with the current time.
         *  This is called by GTK at regular intervals.
         *
         *  @param dummy a dummy, unused parameter
         *  @return true if the timer should call this function again,
         *          false if the timer should be canceled
         */
        virtual bool
        onUpdateTime(int  dummy)                            throw ();

        /**
         *  Register onUpdateTime with the GTK timer.
         *
         *  @see #resetTimer
         */
        virtual void
        setTimer(void)                                      throw ();

        /**
         *  Stop the timer, which was set by setTimer().
         *
         *  @see #setTimer
         */
        virtual void
        resetTimer(void)                                    throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the gLiveSupport object, handling the
         *         logic of the application
         *  @param bundle the resource bundle holding localized resources
         */
        MasterPanelWindow(Ptr<GLiveSupport>::Ref     gLiveSupport,
                          Ptr<ResourceBundle>::Ref   bundle)
                                                                throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~MasterPanelWindow(void)                             throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // MasterPanelWindow_h

