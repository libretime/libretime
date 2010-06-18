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

#include "LiveSupport/Core/TimeConversion.h"
#include "BackupList.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The localization key for the 'working' status.
 *----------------------------------------------------------------------------*/
const Glib::ustring      workingStatusKey    = "workingStatus";

/*------------------------------------------------------------------------------
 *  The localization key for the 'success' status.
 *----------------------------------------------------------------------------*/
const Glib::ustring      successStatusKey    = "successStatus";

/*------------------------------------------------------------------------------
 *  The localization key for the 'fault' status.
 *----------------------------------------------------------------------------*/
const Glib::ustring      faultStatusKey      = "faultStatus";

/*------------------------------------------------------------------------------
 *  The name of the user preference for storing the list of backups
 *----------------------------------------------------------------------------*/
const Glib::ustring      userPreferencesKeyName  = "activeBackups";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
BackupList :: BackupList (GuiObject *      parent)
                                                                    throw ()
          : GuiComponent(parent)
{
    // create the tree view
    treeModel = Gtk::ListStore::create(modelColumns);
    glade->get_widget_derived("backupListTreeView1", treeView);
    treeView->set_model(treeModel);
    treeView->connectModelSignals(treeModel);

    // Add the TreeView's view columns:
    treeView->appendColumn(*getResourceUstring("titleColumnLabel"),
                           modelColumns.titleColumn, 300);
    treeView->appendColumn(*getResourceUstring("dateColumnLabel"),
                           modelColumns.dateColumn, 180);
    treeView->appendColumn(*getResourceUstring("statusColumnLabel"),
                           modelColumns.statusDisplayColumn, 50);

    userPreferencesKey.reset(new const Glib::ustring(userPreferencesKeyName));
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
 *  Add an item with an already existing token to the list.
 *----------------------------------------------------------------------------*/
void
BackupList :: add(const Glib::ustring &     title,
                  const Glib::ustring &     date,
                  const Glib::ustring &     token)
                                                throw (XmlRpcException)
{
    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    
    Ptr<const Glib::ustring>::Ref   url;
    Ptr<const Glib::ustring>::Ref   path;
    Ptr<const Glib::ustring>::Ref   errorMessage;
    
    AsyncState  status = storage->createBackupCheck(token,
                                                    url,
                                                    path,
                                                    errorMessage);
    
    Gtk::TreeRow                row = *treeModel->append();
    row[modelColumns.titleColumn]   = title;
    row[modelColumns.dateColumn]    = date;
    row[modelColumns.tokenColumn]   = token;
    setStatus(row, status, url, errorMessage);
}


/*------------------------------------------------------------------------------
 *  Remove the currently selected item from the list.
 *----------------------------------------------------------------------------*/
void
BackupList :: removeSelected(void)              throw (XmlRpcException)
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
 *  Get the title of the currently selected item.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
BackupList :: getSelectedTitle(void)                            throw ()
{
    Ptr<Glib::ustring>::Ref     title;
    
    Glib::RefPtr<Gtk::TreeSelection>    selection = treeView->get_selection();
    Gtk::TreeIter                       iter = selection->get_selected();
    if (iter) {
        Gtk::TreeRow    row = *iter;
        title.reset(new Glib::ustring(row[modelColumns.titleColumn]));
    }
    
    return title;
}


/*------------------------------------------------------------------------------
 *  Get the URL of the currently selected item.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
BackupList :: getSelectedUrl(void)              throw (XmlRpcException)
{
    Ptr<Glib::ustring>::Ref     url;
    
    Glib::RefPtr<Gtk::TreeSelection>    selection = treeView->get_selection();
    Gtk::TreeIter                       iter = selection->get_selected();
    if (!iter) {
        return url;
    }
    
    if (iter->get_value(modelColumns.statusColumn) == workingStatusKey) {
        update(iter);
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
BackupList :: updateSelected(void)              throw (XmlRpcException)
{
    Glib::RefPtr<Gtk::TreeSelection>    selection = treeView->get_selection();
    Gtk::TreeIter                       iter = selection->get_selected();
    if (!iter) {
        return false;
    } else {
        return update(iter);
    }
}


/*------------------------------------------------------------------------------
 *  Query the storage server about the status of the pending backup.
 *----------------------------------------------------------------------------*/
bool
BackupList :: update(void)                      throw (XmlRpcException)
{
    bool    didSomething = false;
    
    for (Gtk::TreeIter  it  = treeModel->children().begin();
                        it != treeModel->children().end(); ++it) {
        didSomething |= update(it);
    }
    
    return didSomething;
}


/*------------------------------------------------------------------------------
 *  Query the storage server about the status of the pending backup.
 *----------------------------------------------------------------------------*/
bool
BackupList :: updateSilently(void)                              throw ()
{
    bool    didSomething = false;
    
    for (Gtk::TreeIter  it  = treeModel->children().begin();
                        it != treeModel->children().end(); ++it) {
        try {
            didSomething |= update(it);
        } catch (XmlRpcException &e) {
        }
    }
    
    return didSomething;
}


/*------------------------------------------------------------------------------
 *  Query the storage server about the status of the pending backup.
 *----------------------------------------------------------------------------*/
bool
BackupList :: update(Gtk::TreeIter   iter)      throw (XmlRpcException)
{
    if (iter->get_value(modelColumns.statusColumn) != workingStatusKey) {
        return false;
    }
    
    Ptr<StorageClientInterface>::Ref 
                                storage = gLiveSupport->getStorageClient();
    
    Ptr<const Glib::ustring>::Ref   url;
    Ptr<const Glib::ustring>::Ref   path;
    Ptr<const Glib::ustring>::Ref   errorMessage;
    
    AsyncState  status = storage->createBackupCheck(
                                    iter->get_value(modelColumns.tokenColumn),
                                    url,
                                    path,
                                    errorMessage);
    
    return setStatus(iter, status, url, errorMessage);
}


/*------------------------------------------------------------------------------
 *  Set the status of the row pointed to by an iterator.
 *----------------------------------------------------------------------------*/
bool
BackupList :: setStatus(Gtk::TreeIter                       iter,
                        AsyncState                          status,
                        Ptr<const Glib::ustring>::Ref       url,
                        Ptr<const Glib::ustring>::Ref       errorMessage)
                                                                throw ()
{
    if (status == AsyncState::pendingState) {
        return false;
        
    } else if (status == AsyncState::finishedState) {
        iter->set_value(modelColumns.statusColumn,
                        successStatusKey);
        iter->set_value(modelColumns.statusDisplayColumn, 
                        *getResourceUstring(successStatusKey));
        iter->set_value(modelColumns.urlColumn,
                        *url);
        return true;
        
    } else if (status == AsyncState::failedState) {
        iter->set_value(modelColumns.statusColumn,
                        faultStatusKey);
        iter->set_value(modelColumns.statusDisplayColumn, 
                        *formatMessage(faultStatusKey, *errorMessage));
        return false;
        
    } else {
        std::cerr << "Impossible status: '" << status
                  << "' in BackupList::setStatus()." << std::endl;
    }
    
    return false;
}


/*------------------------------------------------------------------------------
 *  Return the contents of the backup list.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
BackupList :: getContents(void)                                 throw ()
{
    std::ostringstream              contentsStream;
    Gtk::TreeModel::const_iterator  it;

    for (it = treeModel->children().begin(); 
                                it != treeModel->children().end(); ++it) {
        Gtk::TreeRow        row = *it;
        contentsStream << row[modelColumns.titleColumn] << '\n';
        contentsStream << row[modelColumns.dateColumn]  << '\n';
        contentsStream << row[modelColumns.tokenColumn] << '\n';
    }

    Ptr<Glib::ustring>::Ref         contents(new Glib::ustring(
                                                    contentsStream.str() ));
    return contents;
}


/*------------------------------------------------------------------------------
 *  Restore the contents of the backup list.
 *----------------------------------------------------------------------------*/
void
BackupList :: setContents(Ptr<const Glib::ustring>::Ref     contents)
                                                                throw ()
{
    std::istringstream      contentsStream(contents->raw());
    
    treeModel->clear();
    while (!contentsStream.eof()) {
        std::string   title;
        std::string   date;
        std::string   token;

        std::getline(contentsStream, title);
        if (contentsStream.fail()) {
            break;
        }
        std::getline(contentsStream, date);
        if (contentsStream.fail()) {
            break;
        }
        std::getline(contentsStream, token);
        if (contentsStream.fail()) {
            break;
        }
        
        try {
            add(title, date, token);
        
        } catch (XmlRpcException &e) {
        }
    }
}

