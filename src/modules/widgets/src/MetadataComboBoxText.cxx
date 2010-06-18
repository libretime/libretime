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

#include "LiveSupport/Widgets/MetadataComboBoxText.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
MetadataComboBoxText :: MetadataComboBoxText(
                        GtkComboBox *                             baseClass,
                        const Glib::RefPtr<Gnome::Glade::Xml> &   glade)
                                                                    throw ()
          : ComboBoxText(baseClass, glade)
{
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
MetadataComboBoxText :: ~MetadataComboBoxText(void)                 throw ()
{
}


/*------------------------------------------------------------------------------
 *  Set up the contents of the combo box.
 *----------------------------------------------------------------------------*/
void
MetadataComboBoxText :: setContents(
                        Ptr<const MetadataTypeContainer>::Ref   metadataTypes)
                                                                    throw ()
{
    this->metadataTypes = metadataTypes;

    MetadataTypeContainer::Vector::const_iterator   it;
    for (it = metadataTypes->begin(); it != metadataTypes->end(); ++it) {
        Ptr<const MetadataType>::Ref  metadata = *it;
        append_text(*metadata->getLocalizedName());
    }
    set_active(0);  // select the first item
}


/*------------------------------------------------------------------------------
 *  Set up the contents of the combo box.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
MetadataComboBoxText :: getActiveKey(void)
                                                                    throw ()
{
    Ptr<const MetadataType>::Ref    metadata = metadataTypes->getByIndex(
                                                    get_active_row_number());
    return metadata->getDcName();
}

