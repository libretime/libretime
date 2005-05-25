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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/AdvancedSearchItem.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"

#include "AdvancedSearchItem.h"


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
AdvancedSearchItem :: AdvancedSearchItem(
                            bool                             isFirst,
                            Ptr<MetadataTypeContainer>::Ref  metadataTypes,
                            Ptr<ResourceBundle>::Ref         bundle)
                                                                    throw ()
          : LocalizedObject(bundle)
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    Gtk::Label *    searchByLabel;
    try {
        searchByLabel = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("searchByTextLabel") ));

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    pack_start(*searchByLabel, Gtk::PACK_SHRINK, 5);

    metadataEntry = Gtk::manage(wf->createMetadataComboBoxText(metadataTypes));
    pack_start(*metadataEntry, Gtk::PACK_EXPAND_WIDGET, 5);

    operatorEntry = Gtk::manage(wf->createOperatorComboBoxText(bundle));
    pack_start(*operatorEntry,  Gtk::PACK_EXPAND_WIDGET, 5);

    valueEntry = Gtk::manage(wf->createEntryBin());
    pack_start(*valueEntry,     Gtk::PACK_EXPAND_WIDGET, 5);
    
    plusButton = Gtk::manage(wf->createButton(WidgetFactory::plusButton));
    pack_start(*plusButton,     Gtk::PACK_SHRINK, 5);
    
    if (!isFirst) {
        closeButton = Gtk::manage(wf->createButton(WidgetFactory::deleteButton));
        closeButton->signal_clicked().connect(sigc::mem_fun(*this, 
                                            &AdvancedSearchItem::destroy_ ));
        pack_start(*closeButton,    Gtk::PACK_SHRINK, 5);
    }
}


/*------------------------------------------------------------------------------
 *  Return the current state of the search fields.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria::SearchConditionType>::Ref
AdvancedSearchItem :: getSearchCondition(void)                  throw ()
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

