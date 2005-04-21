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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/Attic/AdvancedSearchEntry.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"

#include "LiveSupport/Widgets/AdvancedSearchEntry.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
AdvancedSearchEntry :: AdvancedSearchEntry(Ptr<ResourceBundle>::Ref    bundle)
                                                                throw ()
          : LocalizedObject(bundle)
{using namespace LiveSupport::Storage;

    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    // only one option for now
    Gtk::Box *      searchOptionsBox = Gtk::manage(new Gtk::HBox);
    pack_start(*searchOptionsBox, Gtk::PACK_SHRINK, 5);

    try {
        Gtk::Label *    searchByLabel = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("searchByTextLabel") ));
        searchOptionsBox->pack_start(*searchByLabel, Gtk::PACK_SHRINK, 0);

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    metadataType = Gtk::manage(wf->createComboBoxText());
    metadataType->append_text("Title");
    metadataType->append_text("Creator");
    metadataType->append_text("Length");
    metadataType->set_active_text("Title");
    searchOptionsBox->pack_start(*metadataType, Gtk::PACK_EXPAND_WIDGET, 0);

    operatorType = Gtk::manage(wf->createComboBoxText());
    operatorType->append_text("contains");
    operatorType->append_text("starts with");
    operatorType->append_text("equals");
    operatorType->append_text(">=");
    operatorType->append_text("<=");
    operatorType->set_active_text("contains");
    searchOptionsBox->pack_start(*operatorType, Gtk::PACK_EXPAND_WIDGET, 0);

    entryBin = Gtk::manage(wf->createEntryBin());
    searchOptionsBox->pack_start(*entryBin,     Gtk::PACK_EXPAND_WIDGET, 0);
}


/*------------------------------------------------------------------------------
 *  Return the current state of the search fields.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria>::Ref
AdvancedSearchEntry :: getSearchCriteria(void)                  throw ()
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria("all"));
    
    std::string     key;
    if (metadataType->get_active_text() == "Title") {
        key = "dc:title";
    } else if (metadataType->get_active_text() == "Creator") {
        key = "dc:creator";
    } else if (metadataType->get_active_text() == "Length") {
        key = "dcterms:extent";
    } else {
        std::cerr << "unknown metadata type in advanced search entry" 
                                                                    << std::endl
                  << "(this should never happen)" << std::endl;
        std::exit(1);
    }

    std::string     comparisonOperator;
    if (operatorType->get_active_text() == "contains") {
        comparisonOperator = "partial";
    } else if (operatorType->get_active_text() == "starts with") {
        comparisonOperator = "prefix";
    } else if (operatorType->get_active_text() == "equals") {
        comparisonOperator = "=";
    } else if (operatorType->get_active_text() == ">=") {
        comparisonOperator = ">=";
    } else if (operatorType->get_active_text() == "<=") {
        comparisonOperator = "<=";
    } else {
        std::cerr << "unknown comparison operator in advanced search entry"
                                                                    << std::endl
                  << "(this should never happen)" << std::endl;
        std::exit(1);
    }

    std::string     value = entryBin->get_text();

    criteria->addCondition(key, comparisonOperator, value);
    return criteria;
}


/*------------------------------------------------------------------------------
 *  Connect a callback to the "enter key pressed" event.
 *----------------------------------------------------------------------------*/
void
AdvancedSearchEntry :: connectCallback(const sigc::slot<void> &     callback)
                                                                throw ()
{
    entryBin->signal_activate().connect(callback);
}

