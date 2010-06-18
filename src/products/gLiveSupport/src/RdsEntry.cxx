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


#include "RdsEntry.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
RdsEntry :: RdsEntry(GuiObject *                parent,
                     int                        index,
                     const Glib::ustring &      type,
                     int                        width)
                                                                    throw ()
          : GuiComponent(parent)
{
    this->type.reset(new const Glib::ustring(type));
    
    glade->get_widget(addIndex("rdsCheckButton", index), checkButton);
    checkButton->set_label(*getResourceUstring(type + "rdsLabel"));

    glade->get_widget(addIndex("rdsEntry", index), entry);
    entry->set_width_chars(width);
}


/*------------------------------------------------------------------------------
 *  Set the state of the widget.
 *----------------------------------------------------------------------------*/
void
RdsEntry :: setOptions(bool                           enabled,
                       Ptr<const Glib::ustring>::Ref  value)        throw ()
{
    checkButton->set_active(enabled);
    entry->set_text(*value);
    
    checkButtonSaved = enabled;
    entrySaved = value;
}


/*------------------------------------------------------------------------------
 *  Save the changes made by the user.
 *----------------------------------------------------------------------------*/
bool
RdsEntry :: saveChanges(void)                                       throw ()
{
    bool            checkButtonNow = checkButton->get_active();
    Ptr<const Glib::ustring>::Ref
                    entryNow(new const Glib::ustring(entry->get_text()));
    
    if (!entrySaved || checkButtonNow != checkButtonSaved
                    || *entryNow != *entrySaved) {
        Ptr<OptionsContainer>::Ref      optionsContainer =
                                        gLiveSupport->getOptionsContainer();
        optionsContainer->setRdsOptions(type, entryNow, checkButtonNow);
        checkButtonSaved = checkButtonNow;
        entrySaved = entryNow;
        return true;
    } else {
        return false;
    }
}


/*------------------------------------------------------------------------------
 *  Clear the entries of the widget.
 *----------------------------------------------------------------------------*/
void
RdsEntry :: reset(void)                                             throw ()
{
    Ptr<const Glib::ustring>::Ref   empty(new const Glib::ustring(""));
    setOptions(false, empty);
}

