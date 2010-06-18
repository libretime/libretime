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
#include <glibmm.h>

#include "BrowseItem.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
BrowseItem :: BrowseItem(GuiObject *      parent,
                         int              index,
                         int              defaultIndex)
                                                                    throw ()
          : GuiComponent(parent)
{
    parentCriteria.reset(new SearchCriteria);
    
    glade->get_widget_derived(addIndex("browseMetadataEntry", index),
                              metadataEntry);
    metadataEntry->setContents(gLiveSupport->getMetadataTypeContainer());
    metadataEntry->set_active(defaultIndex);
    metadataEntry->signal_changed().connect(sigc::mem_fun(*this,
                                                        &BrowseItem::onShow ));

    treeModel = Gtk::ListStore::create(modelColumns);
    
    glade->get_widget_derived(addIndex("browseMetadataValues", index),
                              metadataValues);
    metadataValues->set_model(treeModel);
    metadataValues->connectModelSignals(treeModel);
    metadataValues->appendColumn("", modelColumns.displayedColumn, 200);
    metadataValues->signal_cursor_changed().connect(sigc::mem_fun(*this,
                                    &BrowseItem::emitSignalChanged ));
    
    allString = Glib::Markup::escape_text(
                                    *getResourceUstring("allStringForBrowse"));

    onShow();
}


/*------------------------------------------------------------------------------
 *  Return the search criteria selected by the user.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria>::Ref
BrowseItem :: getSearchCriteria(void)           throw (std::invalid_argument)
{
    Ptr<const Glib::ustring>::Ref   metadataKey = metadataEntry->getActiveKey();

    Glib::ustring                   metadataValue;
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                metadataValues->get_selection();
    bool    found = false;
    if (refSelection) {
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        if (iter) {
            found = true;
            metadataValue = (*iter)[modelColumns.originalColumn];
        }
    }
    
    if (!found) {
        return parentCriteria;      // should never happen, but für alle Fälle
    }
    
    if (metadataValue == allString) {
        return parentCriteria;
        
    } else {
        Ptr<SearchCriteria>::Ref  criteria(new SearchCriteria(*parentCriteria));
        criteria->addCondition(*metadataKey, "=", metadataValue);
        return criteria;
    }
}


/*------------------------------------------------------------------------------
 *  Fill in the column with the possible values.
 *----------------------------------------------------------------------------*/
void
BrowseItem :: onShow(void)                                          throw ()
{
    Ptr<const Glib::ustring>::Ref   metadataKey = metadataEntry->getActiveKey();
    
    Ptr<StorageClientInterface>::Ref 
                                storage   = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId = gLiveSupport->getSessionId();
    
    Ptr<std::vector<Glib::ustring> >::Ref   values;
    try {
        values = storage->browse(sessionId, metadataKey, parentCriteria);
    } catch (XmlRpcException &e) {
        std::cerr << "Error in BrowseItem::onShow(): " 
                  << e.what() << std::endl;
        return;
    }
    
    treeModel->clear();
    Gtk::TreeModel::Row     row = *treeModel->append();
    row[modelColumns.originalColumn]        = allString;
    row[modelColumns.displayedColumn]       = allString;
    metadataValues->get_selection()->select(*row);

    std::vector<Glib::ustring>::const_iterator valuesIt;
    for (valuesIt = values->begin(); valuesIt != values->end(); ++valuesIt) {
        row = *treeModel->append();
        row[modelColumns.originalColumn]    = *valuesIt;
        row[modelColumns.displayedColumn]   = Glib::Markup::escape_text(
                                                                    *valuesIt);
    }
    
    emitSignalChanged();
}

