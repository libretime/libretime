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
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
RdsView :: RdsView (Ptr<GLiveSupport>::Ref    gLiveSupport,
                    Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    Gtk::Label *    deviceLabel;
    try {
        deviceLabel = Gtk::manage(new Gtk::Label(*getResourceUstring(
                                                    "deviceLabel" )));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    deviceEntryBin = Gtk::manage(wf->createEntryBin());
    Gtk::Box *      deviceBox = Gtk::manage(new Gtk::HBox());
    deviceBox->pack_start(*deviceLabel,     Gtk::PACK_SHRINK, 5);
    deviceBox->pack_start(*deviceEntryBin,  Gtk::PACK_EXPAND_WIDGET, 5);

    Ptr<RdsEntry>::Ref  psEntry(new RdsEntry(getBundle(), "PS", 8));
    Ptr<RdsEntry>::Ref  piEntry(new RdsEntry(getBundle(), "PI", 4));
    Ptr<RdsEntry>::Ref  rtEntry(new RdsEntry(getBundle(), "RT", 32));
    
    rdsEntryList.push_back(psEntry);
    rdsEntryList.push_back(piEntry);
    rdsEntryList.push_back(rtEntry);
    
    pack_start(*deviceBox,  Gtk::PACK_SHRINK, 5);
    pack_start(*psEntry,    Gtk::PACK_SHRINK, 5);
    pack_start(*piEntry,    Gtk::PACK_SHRINK, 5);
    pack_start(*rtEntry,    Gtk::PACK_SHRINK, 5);

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
                                        deviceEntryBin->get_text() ));
    if (*oldDevice != *newDevice) {
        options->setOptionItem(OptionsContainer::serialDeviceName, newDevice);
        touched = true;
    }
    
    RdsEntryListType::const_iterator    it;
    for (it = rdsEntryList.begin(); it != rdsEntryList.end(); ++it) {
        Ptr<RdsEntry>::Ref              rdsEntry = *it;
        touched |= rdsEntry->saveChanges(gLiveSupport);
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
    deviceEntryBin->set_text(*options->getOptionItem(
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

