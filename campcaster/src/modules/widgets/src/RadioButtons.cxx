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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/widgets/src/RadioButtons.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/RadioButtons.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Add a new radio button.
 *----------------------------------------------------------------------------*/
void
RadioButtons :: add(Ptr<const Glib::ustring>::Ref   label)          throw ()
{
    Gtk::RadioButton *      button;
    
    if (buttons.size() == 0) {
        button = Gtk::manage(new Gtk::RadioButton(*label));
    } else {
        Gtk::RadioButton *          firstButton = buttons.front();
        Gtk::RadioButton::Group     group = firstButton->get_group();
        button = Gtk::manage(new Gtk::RadioButton(group, *label));
    }
    
    buttons.push_back(button);
    pack_start(*button, Gtk::PACK_SHRINK, 5);
}


/*------------------------------------------------------------------------------
 *  Return the number of the active (selected) button.
 *----------------------------------------------------------------------------*/
int
RadioButtons :: getActiveButton(void) const                         throw ()
{
    int i = -1;
    
    for (i = 0; i < int(buttons.size()); ++i) {
        if (buttons[i]->get_active()) {
            break;
        }
    }
    
    return i;
}

