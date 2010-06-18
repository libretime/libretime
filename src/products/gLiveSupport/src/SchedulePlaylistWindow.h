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
#ifndef SchedulePlaylistWindow_h
#define SchedulePlaylistWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>

#include "LiveSupport/Core/Ptr.h"
#include "GLiveSupport.h"

#include "GuiWindow.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The window, for scheduling a specific playlist.
 *
 *  The rough layout of the window is:
 *  <code><pre>
 *  +--- scheduler window ----------------------------+
 *  | +--- plalyist name ---------------------------+ |
 *  | +--- calendar --------------------------------+ |
 *  | |                                             | |
 *  | +---------------------------------------------+ |
 *  | +--- time input ------------------------------+ |
 *  | | hour: +- hour -+  minute: +- minute -+      | |
 *  | +---------------------------------------------+ |
 *  | +-- schedule button --------------------------+ |
 *  | +-- close button -----------------------------+ |
 *  +-------------------------------------------------+
 *  </pre></code>
 *
 *  @author $Author$
 *  @version $Revision$
 */
class SchedulePlaylistWindow : public GuiWindow
{
    private:

        /**
         *  The playlist to schedule.
         */
        Ptr<Playlist>::Ref                      playlist;

        /**
         *  The calendar to select a specific date from.
         */
        Gtk::Calendar *                         calendar;

        /**
         *  The entry field for hours.
         */
        Gtk::SpinButton *                       hourEntry;

        /**
         *  The entry field for minutes.
         */
        Gtk::SpinButton *                       minuteEntry;

        /**
         *  The entry field for seconds.
         */
        Gtk::SpinButton *                       secondEntry;


    protected:

        /**
         *  Event handler for the schedule button clicked.
         */
        virtual void
        onScheduleButtonClicked(void)                           throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param  playlist        the playlist to schedule.
         */
        SchedulePlaylistWindow(Ptr<Playlist>::Ref           playlist)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~SchedulePlaylistWindow(void)                               throw ()
        {
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // SchedulePlaylistWindow_h

