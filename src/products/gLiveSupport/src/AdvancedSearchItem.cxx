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

#include <iostream>

#include "AdvancedSearchItem.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
AdvancedSearchItem :: AdvancedSearchItem(
                            GuiObject *                         parent,
                            int                                 index,
                            Ptr<MetadataTypeContainer>::Ref     metadataTypes)
                                                                    throw ()
          : GuiComponent(parent)
{
    glade->get_widget(addIndex("advancedSearchItem", index), enclosingBox);

    Gtk::Label *    searchByLabel;
    glade->get_widget(addIndex("advancedSearchByLabel", index), searchByLabel);
    searchByLabel->set_label(*getResourceUstring("searchByTextLabel"));

    glade->get_widget_derived(addIndex("advancedMetadataEntry", index),
                              metadataEntry);
    metadataEntry->setContents(metadataTypes);

    glade->get_widget_derived(addIndex("advancedOperatorEntry", index),
                              operatorEntry);
    operatorEntry->setContents(getBundle());

    glade->get_widget(addIndex("advancedValueEntry", index), valueEntry);
    
    if (index == 0) {
        glade->get_widget(addIndex("advancedPlusMinusButton", index),
                                   plusButton);
        plusButton->signal_clicked().connect(sigc::mem_fun(*this, 
                                    &AdvancedSearchItem::onPlusButtonClicked));
    } else {
        glade->get_widget(addIndex("advancedPlusMinusButton", index),
                                   closeButton);
        closeButton->signal_clicked().connect(sigc::mem_fun(*this, 
                                    &AdvancedSearchItem::onCloseButtonClicked));
    }
}


/*------------------------------------------------------------------------------
 *  Return the current state of the search fields.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria::SearchConditionType>::Ref
AdvancedSearchItem :: getSearchCondition(void)                      throw ()
{
    Ptr<const Glib::ustring>::Ref  metadataKey = metadataEntry->getActiveKey();
    Ptr<const Glib::ustring>::Ref  operatorKey = operatorEntry->getActiveKey();
    std::string                    value       = valueEntry->get_text();
    
    Ptr<SearchCriteria::SearchConditionType>::Ref
            condition(new SearchCriteria::SearchConditionType(*metadataKey,
                                                              *operatorKey,
                                                              value));
    
    return condition;
}

