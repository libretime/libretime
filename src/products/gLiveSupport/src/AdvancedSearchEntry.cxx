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

#include "AdvancedSearchEntry.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "advancedSearchEntry";

/*------------------------------------------------------------------------------
 *  The maximum number of AdvancedSearchItem children.
 *----------------------------------------------------------------------------*/
const int               maxChildren = 5;

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
AdvancedSearchEntry :: AdvancedSearchEntry(GuiObject *      parent)
                                                                    throw ()
          : GuiComponent(parent,
                         bundleName)
{
    metadataTypes = gLiveSupport->getMetadataTypeContainer();
    
    Gtk::Label *            fileTypeLabel;
    glade->get_widget("advancedFileTypeLabel1", fileTypeLabel);
    fileTypeLabel->set_label(*getResourceUstring("fileTypeTextLabel"));
    
    glade->get_widget_derived("advancedFileTypeEntry1", fileTypeEntry);
    fileTypeEntry->append_text(*getResourceUstring("allFileType"));
    fileTypeEntry->append_text(*getResourceUstring("audioClipFileType"));
    fileTypeEntry->append_text(*getResourceUstring("playlistFileType"));
    fileTypeEntry->set_active(0);
    
    for (int i = 0; i < maxChildren; ++i) {
        Ptr<AdvancedSearchItem>::Ref    searchItem(new AdvancedSearchItem(
                                                                this,
                                                                i,
                                                                metadataTypes));
        children.push_back(searchItem);
    }
    
    children.at(0)->signalAddNew().connect(sigc::mem_fun(*this,
                                    &AdvancedSearchEntry::onAddNewCondition ));
}


/*------------------------------------------------------------------------------
 *  Add a new search condition entrys item.
 *----------------------------------------------------------------------------*/
void
AdvancedSearchEntry :: onAddNewCondition(void)                      throw ()
{
    bool                            foundAvailableChild = false;
    Ptr<AdvancedSearchItem>::Ref    child;
    
    for (int i = 1; i < maxChildren; ++i) {
        child = children.at(i);
        if (!child->is_visible()) {
            foundAvailableChild = true;
            break;
        }
    }
    
    if (foundAvailableChild) {
        child->show();
    }
}


/*------------------------------------------------------------------------------
 *  Return the current state of the search fields.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria>::Ref
AdvancedSearchEntry :: getSearchCriteria(void)                      throw ()
{
    Glib::ustring   fileType;
    switch (fileTypeEntry->get_active_row_number()) {
        case 0:     fileType = "all";
                    break;
        case 1:     fileType = "audioClip";
                    break;
        case 2:     fileType = "playlist";
                    break;
        default:    std::cerr << "impossible value in AdvancedSearchEntry::"
                              << "getSearchCriteria()" << std::endl;
                    break;
    }

    Ptr<SearchCriteria>::Ref        criteria(new SearchCriteria(fileType));

    for (int i = 0; i < maxChildren; ++i) {
        Ptr<AdvancedSearchItem>::Ref    child = children.at(i);
        if (child->is_visible()) {
            criteria->addCondition(child->getSearchCondition());
        }
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
    for (int i = 0; i < maxChildren; ++i) {
        Ptr<AdvancedSearchItem>::Ref    child = children.at(i);
        child->signal_activate().connect(callback);
    }
}

