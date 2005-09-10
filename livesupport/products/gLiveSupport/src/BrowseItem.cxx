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

#include "LiveSupport/Widgets/WidgetFactory.h"

#include "BrowseItem.h"


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
BrowseItem :: BrowseItem(
        Ptr<LiveSupport::GLiveSupport::GLiveSupport>::Ref   gLiveSupport,
        Ptr<ResourceBundle>::Ref                            bundle,
        int                                                 defaultIndex)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    parentCriteria.reset(new SearchCriteria);
    
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    metadataEntry = Gtk::manage(wf->createMetadataComboBoxText(
                                    gLiveSupport->getMetadataTypeContainer()));
    metadataEntry->set_active(defaultIndex);
    metadataEntry->signalSelectionChanged().connect(sigc::mem_fun(*this,
                                                        &BrowseItem::onShow ));
    pack_start(*metadataEntry, Gtk::PACK_SHRINK, 5);

    treeModel = Gtk::ListStore::create(modelColumns);
    
    metadataValues = Gtk::manage(wf->createTreeView(treeModel));
    metadataValues->appendColumn("", modelColumns.column, 200);
    metadataValues->set_size_request(230,150);
    metadataValues->set_headers_visible(false);
    metadataValues->signal_cursor_changed().connect(sigc::mem_fun(*this,
                                    &BrowseItem::emitSignalSelectionChanged ));
    
    Gtk::ScrolledWindow * scrolledWindow = Gtk::manage(new Gtk::ScrolledWindow);
    scrolledWindow->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
    scrolledWindow->add(*metadataValues);
    pack_start(*scrolledWindow, Gtk::PACK_SHRINK, 5);

    try {
        allString = Glib::Markup::escape_text(
                                    *getResourceUstring("allStringForBrowse"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

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
            metadataValue = (*iter)[modelColumns.column];
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
    
    treeModel->clear();
    int rowNumber = 1;
    Gtk::TreeModel::Row     row = *treeModel->append();
    row[modelColumns.column]                = allString;
    row[modelColumns.rowNumberColumn]       = rowNumber++;
    metadataValues->get_selection()->select(*row);

    Ptr<std::vector<Glib::ustring> >::Ref
            values = gLiveSupport->browse(metadataKey, parentCriteria);
    std::vector<Glib::ustring>::const_iterator valuesIt;
    for (valuesIt = values->begin(); valuesIt != values->end(); ++valuesIt) {
        row = *treeModel->append();
        row[modelColumns.column]            = Glib::Markup::escape_text(
                                                                    *valuesIt);
        row[modelColumns.rowNumberColumn]   = rowNumber++;
    }
    
    emitSignalSelectionChanged();
}

