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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/widgets/include/LiveSupport/Widgets/RadioButtons.h $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_RadioButtons_h
#define LiveSupport_Widgets_RadioButtons_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <vector>
#include <gtkmm/box.h>
#include <gtkmm/radiobutton.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A group of radio buttons.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision$
 */
class RadioButtons : public Gtk::VBox
{
    private:
        /**
         *  The list of radio buttons contained in the widget.
         */
        std::vector<Gtk::RadioButton*>      buttons;


    public:
        /**
         *  Default constructor.
         */
        RadioButtons(void)                                          throw ()
        {
        }

        /**
         *  A virtual destructor.
         */
        virtual
        ~RadioButtons(void)                                         throw ()
        {
        }

        /**
         *  Add a new radio button.
         *
         *  @param label    the label of the new button.
         */
        void
        add(Ptr<const Glib::ustring>::Ref   label)                  throw ();

        /**
         *  Return the number of the active (selected) button.
         *  The buttons are numbered in the order they were added, starting
         *  with 0.
         *  It returns -1 if no radio buttons have been added, and returns
         *  the number of radio buttons if none of them is active [this 
         *  should never happen].
         *
         *  @return the number of the active button.
         */
        int
        getActiveButton(void) const                                 throw ();

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_RadioButtons_h

