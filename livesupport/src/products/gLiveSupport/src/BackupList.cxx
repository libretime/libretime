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
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/BackupList.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TimeConversion.h"
#include "BackupList.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The localization key for the 'working' status.
 *----------------------------------------------------------------------------*/
static const Glib::ustring      workingStatusKey    = "workingStatus";

/*------------------------------------------------------------------------------
 *  The localization key for the 'success' status.
 *----------------------------------------------------------------------------*/
static const Glib::ustring      successStatusKey    = "successStatus";

/*------------------------------------------------------------------------------
 *  The localization key for the 'fault' status.
 *----------------------------------------------------------------------------*/
static const Glib::ustring      faultStatusKey      = "faultStatus";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
BackupList :: BackupList (Ptr<GLiveSupport>::Ref    gLiveSupport,
                          Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    // create the tree view
    treeModel = Gtk::ListStore::create(modelColumns);
    treeView = Gtk::manage(widgetFactory->createTreeView(treeModel));
    treeView->set_enable_search(false);

    // Add the TreeView's view columns:
    try {
        treeView->appendColumn(*getResourceUstring("titleColumnLabel"),
                               modelColumns.titleColumn, 200);
        treeView->appendColumn(*getResourceUstring("dateColumnLabel"),
                               modelColumns.dateColumn, 80);
        treeView->appendColumn(*getResourceUstring("statusColumnLabel"),
                               modelColumns.statusDisplayColumn, 50);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    // add the tree view to this widget
    Gtk::VBox::add(*treeView);
}


/*------------------------------------------------------------------------------
 *  Add a new item to the list.
 *----------------------------------------------------------------------------*/
void
BackupList :: add(Ptr<Glib::ustring>::Ref     title,
                  Ptr<SearchCriteria>::Ref    criteria)
                                                throw (XmlRpcException)
{
    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    
    Ptr<Glib::ustring>::Ref     token = storage->createBackupOpen(sessionId,
                                                                  criteria);
    
    Gtk::TreeRow                row = *treeModel->append();
    row[modelColumns.titleColumn]   = *title;
    row[modelColumns.dateColumn]    = *TimeConversion::nowString();
    row[modelColumns.statusColumn]  = workingStatusKey;
    row[modelColumns.statusDisplayColumn] 
                                    = *getResourceUstring(workingStatusKey);
    row[modelColumns.tokenColumn]   = *token;
}


/*------------------------------------------------------------------------------
 *  Remove the currently selected item from the list.
 *----------------------------------------------------------------------------*/
void
BackupList :: remove(void)                      throw (XmlRpcException)
{
    Glib::RefPtr<Gtk::TreeSelection>    selection = treeView->get_selection();
    Gtk::TreeIter                       iter = selection->get_selected();
    if (!iter) {
        return;
    }

    Ptr<StorageClientInterface>::Ref 
                                storage = gLiveSupport->getStorageClient();
    storage->createBackupClose(iter->get_value(modelColumns.tokenColumn));
    
    treeModel->erase(iter);
}


/*------------------------------------------------------------------------------
 *  Get the URL of the currently selected item.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
BackupList :: getUrl(void)                                          throw ()
{
    Ptr<Glib::ustring>::Ref     url;
    
    Glib::RefPtr<Gtk::TreeSelection>    selection = treeView->get_selection();
    Gtk::TreeIter                       iter = selection->get_selected();
    if (!iter) {
        return url;
    }
    
    if (iter->get_value(modelColumns.statusColumn) == workingStatusKey) {
        update();
    }

    if (iter->get_value(modelColumns.statusColumn) == successStatusKey) {
        url.reset(new Glib::ustring(iter->get_value(modelColumns.urlColumn)));
    }
    
    return url;
}


/*------------------------------------------------------------------------------
 *  Query the storage server about the status of the pending backup.
 *----------------------------------------------------------------------------*/
bool
BackupList :: update(void)                      throw (XmlRpcException)
{
    Glib::RefPtr<Gtk::TreeSelection>    selection = treeView->get_selection();
    Gtk::TreeIter                       iter = selection->get_selected();
    if (!iter) {
        return false;
    }
    
    if (iter->get_value(modelColumns.statusColumn) != workingStatusKey) {
        return false;
    }
    
    Ptr<StorageClientInterface>::Ref 
                                storage = gLiveSupport->getStorageClient();
    Ptr<Glib::ustring>::Ref     urlOrErrorMsg(new Glib::ustring);
    Ptr<Glib::ustring>::Ref     status = storage->createBackupCheck(
                                    iter->get_value(modelColumns.tokenColumn),
                                    urlOrErrorMsg);
    
    if (*status == "working") {
        return false;
    
    } else if (*status == "success") {
        iter->set_value(modelColumns.statusColumn,
                        successStatusKey);
        iter->set_value(modelColumns.statusDisplayColumn, 
                        *getResourceUstring(successStatusKey));
        iter->set_value(modelColumns.urlColumn,
                        *urlOrErrorMsg);
        return true;
    
    } else if (*status == "fault") {
        iter->set_value(modelColumns.statusColumn,
                        faultStatusKey);
        iter->set_value(modelColumns.statusDisplayColumn, 
                        *formatMessage(faultStatusKey, *urlOrErrorMsg));
        return false;
    }
    
    std::cerr << "Impossible status: '" << *status
              << "' in BackupList::update()." << std::endl;
    return false;
}

