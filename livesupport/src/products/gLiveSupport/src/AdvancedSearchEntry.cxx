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
#include "AdvancedSearchEntry.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The localization key for "File type: " before the file type selector box.
 *----------------------------------------------------------------------------*/
const std::string    fileTypeLabelKey = "fileTypeTextLabel";

/*------------------------------------------------------------------------------
 *  The localization key for "all" in the file type selector box.
 *----------------------------------------------------------------------------*/
const std::string    allLocalizationKey = "allFileType";

/*------------------------------------------------------------------------------
 *  The localization key for "playlist" in the file type selector box.
 *----------------------------------------------------------------------------*/
const std::string    playlistLocalizationKey = "playlistFileType";

/*------------------------------------------------------------------------------
 *  The localization key for "audioClip" in the file type selector box.
 *----------------------------------------------------------------------------*/
const std::string    audioClipLocalizationKey = "audioClipFileType";

/*------------------------------------------------------------------------------
 *  The search key for "all" in the file type selector box.
 *----------------------------------------------------------------------------*/
const std::string    allSearchKey = "all";

/*------------------------------------------------------------------------------
 *  The search key for "playlist" in the file type selector box.
 *----------------------------------------------------------------------------*/
const std::string    playlistSearchKey = "playlist";

/*------------------------------------------------------------------------------
 *  The search key for "audioClip" in the file type selector box.
 *----------------------------------------------------------------------------*/
const std::string    audioClipSearchKey = "audioClip";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
AdvancedSearchEntry :: AdvancedSearchEntry(Ptr<GLiveSupport>::Ref  gLiveSupport)
                                                                    throw ()
          : gLiveSupport(gLiveSupport)
{
    Ptr<ResourceBundle>::Ref    bundle;
    try {
        bundle = gLiveSupport->getBundle("advancedSearchEntry");
    } catch (std::invalid_argument &e) {
        // TODO: signal error
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    setBundle(bundle);

    metadataTypes = gLiveSupport->getMetadataTypeContainer();
    Ptr<WidgetFactory>::Ref wf = WidgetFactory::getInstance(); 
    
    Gtk::Label *            fileTypeLabel;
    try {
        fileTypeLabel = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring(fileTypeLabelKey) ));

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    fileTypeEntry = Gtk::manage(wf->createComboBoxText());
    Ptr<Glib::ustring>::Ref allKey(new Glib::ustring(allSearchKey));
    Ptr<Glib::ustring>::Ref audioClipKey(new Glib::ustring(audioClipSearchKey));
    Ptr<Glib::ustring>::Ref playlistKey( new Glib::ustring(playlistSearchKey));
    fileTypeEntry->appendPair(getResourceUstring(allLocalizationKey),
                              allKey);
    fileTypeEntry->appendPair(getResourceUstring(audioClipLocalizationKey),
                              audioClipKey);
    fileTypeEntry->appendPair(getResourceUstring(playlistLocalizationKey),
                              playlistKey);
    fileTypeEntry->set_active(0);
        
    AdvancedSearchItem *    searchItem = Gtk::manage(new AdvancedSearchItem(
                                                                true, 
                                                                metadataTypes,
                                                                getBundle() ));
    searchItem->signalAddNew().connect(sigc::mem_fun(*this, 
                                    &AdvancedSearchEntry::onAddNewCondition ));
    
    Gtk::HBox *     fileTypeBox = Gtk::manage(new Gtk::HBox);
    fileTypeBox->pack_start(*fileTypeLabel, Gtk::PACK_SHRINK, 5);
    fileTypeBox->pack_start(*fileTypeEntry, Gtk::PACK_SHRINK, 5);
    
    searchItemsBox = Gtk::manage(new Gtk::VBox);
    searchItemsBox->pack_start(*searchItem, Gtk::PACK_SHRINK, 0);

    pack_start(*fileTypeBox,    Gtk::PACK_SHRINK, 5);
    pack_start(*searchItemsBox, Gtk::PACK_SHRINK, 5);  
}


/*------------------------------------------------------------------------------
 *  Add a new search condition entrys item.
 *----------------------------------------------------------------------------*/
void
AdvancedSearchEntry :: onAddNewCondition(void)                      throw ()
{
    AdvancedSearchItem *    searchItem = Gtk::manage(new AdvancedSearchItem(
                                                                false, 
                                                                metadataTypes,
                                                                getBundle() ));
    searchItemsBox->pack_start(*searchItem, Gtk::PACK_SHRINK, 5);

    searchItem->show_all_children();
    searchItem->show();
}


/*------------------------------------------------------------------------------
 *  Return the current state of the search fields.
 *----------------------------------------------------------------------------*/
Ptr<SearchCriteria>::Ref
AdvancedSearchEntry :: getSearchCriteria(void)                      throw ()
{
    Ptr<const Glib::ustring>::Ref   fileType = fileTypeEntry->getActiveKey();
    Ptr<SearchCriteria>::Ref        criteria(new SearchCriteria(*fileType));

    Gtk::Box_Helpers::BoxList       children = searchItemsBox->children();
    Gtk::Box_Helpers::BoxList::type_base::iterator      it;
    
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
    Gtk::Box_Helpers::BoxList       children = searchItemsBox->children();
    Gtk::Box_Helpers::BoxList::type_base::iterator      it;
    
    for (it = children.begin(); it != children.end(); ++it) {
        AdvancedSearchItem *    child = dynamic_cast<AdvancedSearchItem *>(
                                                            it->get_widget() );
        child->signal_activate().connect(callback);
    }
}

