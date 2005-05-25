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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/AdvancedSearchEntry.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "AdvancedSearchItem.h"
#include "AdvancedSearchEntry.h"


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
AdvancedSearchEntry :: AdvancedSearchEntry(
                                Ptr<MetadataTypeContainer>::Ref  metadataTypes,
                                Ptr<ResourceBundle>::Ref         bundle)
                                                                    throw ()
          : LocalizedObject(bundle),
            metadataTypes(metadataTypes)
{
    AdvancedSearchItem *    searchOptionsBox = Gtk::manage(new 
                                    AdvancedSearchItem(true, 
                                                       metadataTypes,
                                                       getBundle() ));
    pack_start(*searchOptionsBox, Gtk::PACK_SHRINK, 5);

    searchOptionsBox->signal_add_new().connect(sigc::mem_fun(*this, 
                                    &AdvancedSearchEntry::onAddNewCondition ));
}


/*------------------------------------------------------------------------------
 *  Add a new search condition entrys item.
 *----------------------------------------------------------------------------*/
void
AdvancedSearchEntry :: onAddNewCondition(void)                      throw ()
{
    AdvancedSearchItem *    searchOptionsBox = Gtk::manage(new 
                                    AdvancedSearchItem(false, 
                                                       metadataTypes,
                                                       getBundle() ));
    pack_start(*searchOptionsBox, Gtk::PACK_SHRINK, 5);

    searchOptionsBox->signal_add_new().connect(sigc::mem_fun(*this, 
                                    &AdvancedSearchEntry::onAddNewCondition ));
    searchOptionsBox->show_all_children();
    searchOptionsBox->show();
}


/*------------------------------------------------------------------------------
 *  Return the current state of the search fields.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria>::Ref
AdvancedSearchEntry :: getSearchCriteria(void)                      throw ()
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria("all", "and"));

    Gtk::Box_Helpers::BoxList                       children = this->children();
    Gtk::Box_Helpers::BoxList::type_base::iterator  it;
    
    for (it = children.begin(); it != children.end(); ++it) {
        AdvancedSearchItem *    child = dynamic_cast<AdvancedSearchItem *>(
                                                            it->get_widget() );
        criteria->addCondition(child->getSearchCondition());
    }
    
    return criteria;
}


/*------------------------------------------------------------------------------
 *  Connect a callback to the "enter key pressed" event.
 *----------------------------------------------------------------------------*/
void
AdvancedSearchEntry :: connectCallback(const sigc::slot<void> &  callback)
                                                                    throw ()
{
    Gtk::Box_Helpers::BoxList                       children = this->children();
    Gtk::Box_Helpers::BoxList::type_base::iterator  it;
    
    for (it = children.begin(); it != children.end(); ++it) {
        AdvancedSearchItem *    child = dynamic_cast<AdvancedSearchItem *>(
                                                            it->get_widget() );
        child->signal_activate().connect(callback);
    }
}

