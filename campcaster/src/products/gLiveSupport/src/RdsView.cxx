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
    Ptr<RdsEntry>::Ref  psEntry(new RdsEntry(getBundle(), "PS", 8));
    Ptr<RdsEntry>::Ref  piEntry(new RdsEntry(getBundle(), "PI", 4));
    Ptr<RdsEntry>::Ref  rtEntry(new RdsEntry(getBundle(), "RT", 32));
    
    rdsEntryList.push_back(psEntry);
    rdsEntryList.push_back(piEntry);
    rdsEntryList.push_back(rtEntry);
    
    pack_start(*psEntry, Gtk::PACK_SHRINK, 10);
    pack_start(*piEntry, Gtk::PACK_SHRINK, 0);
    pack_start(*rtEntry, Gtk::PACK_SHRINK, 10);

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
            // there is no such RDS option; it's OK
        }
    }
}


/*------------------------------------------------------------------------------
 *  Save the changes made by the user.
 *----------------------------------------------------------------------------*/
bool
RdsView :: saveChanges(void)                                        throw ()
{
    bool    touched = false;

    RdsEntryListType::const_iterator    it;
    for (it = rdsEntryList.begin(); it != rdsEntryList.end(); ++it) {
        Ptr<RdsEntry>::Ref              rdsEntry = *it;
        touched |= rdsEntry->saveChanges(gLiveSupport);
    }
    
    return touched;
}

