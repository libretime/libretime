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

#include "RdsView.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "rdsView";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
RdsView :: RdsView (GuiObject *      parent)
                                                                    throw ()
          : GuiComponent(parent,
                         bundleName)
{
    Gtk::Label *    deviceLabel;
    glade->get_widget("rdsDeviceLabel1", deviceLabel);
    deviceLabel->set_label(*getResourceUstring("deviceLabel"));

    glade->get_widget("rdsDeviceEntry1", deviceEntry);

    Ptr<RdsEntry>::Ref  psEntry(new RdsEntry(this, 0, "PS", 8));
    Ptr<RdsEntry>::Ref  piEntry(new RdsEntry(this, 1, "PI", 4));
    Ptr<RdsEntry>::Ref  rtEntry(new RdsEntry(this, 2, "RT", 32));
    
    rdsEntryList.push_back(psEntry);
    rdsEntryList.push_back(piEntry);
    rdsEntryList.push_back(rtEntry);

    reset();
}


/*------------------------------------------------------------------------------
 *  Save the changes made by the user.
 *----------------------------------------------------------------------------*/
bool
RdsView :: saveChanges(void)                                        throw ()
{
    bool    touched = false;

    Ptr<OptionsContainer>::Ref  options = gLiveSupport->getOptionsContainer();
    Ptr<const Glib::ustring>::Ref   oldDevice = options->getOptionItem(
                                        OptionsContainer::serialDeviceName);
    Ptr<const Glib::ustring>::Ref   newDevice(new const Glib::ustring(
                                        deviceEntry->get_text() ));
    if (*oldDevice != *newDevice) {
        options->setOptionItem(OptionsContainer::serialDeviceName, newDevice);
        touched = true;
    }
    
    RdsEntryListType::const_iterator    it;
    for (it = rdsEntryList.begin(); it != rdsEntryList.end(); ++it) {
        Ptr<RdsEntry>::Ref              rdsEntry = *it;
        touched |= rdsEntry->saveChanges();
    }
    
    return touched;
}


/*------------------------------------------------------------------------------
 *  Reset the widget to its saved state.
 *----------------------------------------------------------------------------*/
void
RdsView :: reset(void)                                              throw ()
{
    Ptr<OptionsContainer>::Ref  options = gLiveSupport->getOptionsContainer();
    deviceEntry->set_text(*options->getOptionItem(
                                        OptionsContainer::serialDeviceName));
    
    RdsEntryListType::const_iterator    it;
    for (it = rdsEntryList.begin(); it != rdsEntryList.end(); ++it) {
        fillEntry(*it);
    }
}


/*------------------------------------------------------------------------------
 *  Fill in the entry from the OptionsContainer.
 *----------------------------------------------------------------------------*/
void
RdsView :: fillEntry(Ptr<RdsEntry>::Ref     entry)                  throw ()
{
    Ptr<OptionsContainer>::Ref  options = gLiveSupport->getOptionsContainer();
    
    if (options) {
        Ptr<const Glib::ustring>::Ref   type    = entry->getType();
        try {
            bool                        enabled = options->getRdsEnabled(type);
            Ptr<const Glib::ustring>::Ref
                                        value   = options->getRdsValue(type);
            entry->setOptions(enabled, value);
        } catch (std::invalid_argument &e) {
            entry->reset();
        }
    }
}

