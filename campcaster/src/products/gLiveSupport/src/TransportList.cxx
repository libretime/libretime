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
#include "TransportList.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring      bundleName          = "transportList";

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
 *  The name of the user preference for storing the list of transports.
 *----------------------------------------------------------------------------*/
const Glib::ustring      userPreferencesKeyName  = "activeTransports";

/*------------------------------------------------------------------------------
 *  The symbol for an upload.
 *----------------------------------------------------------------------------*/
const Glib::ustring      uploadSymbol  = "⇧";

/*------------------------------------------------------------------------------
 *  The symbol for a download.
 *----------------------------------------------------------------------------*/
const Glib::ustring      downloadSymbol  = "⇩";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
TransportList :: TransportList(GuiObject *      parent)
                                                                    throw ()
          : GuiComponent(parent,
                         bundleName)
{
    // create the tree view
    treeModel = Gtk::ListStore::create(modelColumns);
    glade->get_widget_derived("transportsTreeView1", treeView);
    treeView->set_model(treeModel);
    treeView->connectModelSignals(treeModel);

    // Add the TreeView's view columns:
    treeView->appendColumn("",
                           modelColumns.directionColumn, 20);
    treeView->appendColumn(*getResourceUstring("titleColumnLabel"),
                           modelColumns.titleColumn, 300);
    treeView->appendColumn(*getResourceUstring("dateColumnLabel"),
                           modelColumns.dateColumn, 180);
    treeView->appendColumn(*getResourceUstring("statusColumnLabel"),
                           modelColumns.statusDisplayColumn, 50);
    
    // register the signal handler for treeview entries being clicked
    treeView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                        &TransportList::onEntryClicked));

    // create the right-click entry context menu
    uploadMenu.reset(new Gtk::Menu());
    downloadMenu.reset(new Gtk::Menu());
    Gtk::Menu::MenuList&    uploadMenuList      = uploadMenu->items();
    Gtk::Menu::MenuList&    downloadMenuList    = downloadMenu->items();
    
    uploadMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("cancelUploadMenuItem"),
                            sigc::mem_fun(*this,
                                    &TransportList::onCancelTransport)));
    downloadMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                            *getResourceUstring("cancelDownloadMenuItem"),
                            sigc::mem_fun(*this,
                                    &TransportList::onCancelTransport)));
    
    Gtk::Window *       mainWindow;
    glade->get_widget("mainWindow1", mainWindow);
    uploadMenu->accelerate(*mainWindow);
    downloadMenu->accelerate(*mainWindow);

    userPreferencesKey.reset(new const Glib::ustring(userPreferencesKeyName));
}


/*------------------------------------------------------------------------------
 *  Add a new upload task to the list.
 *----------------------------------------------------------------------------*/
void
TransportList :: addUpload(Ptr<Playable>::Ref       playable)
                                                        throw (XmlRpcException)
{
    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    
    Ptr<Glib::ustring>::Ref     token = storage->uploadToHub(sessionId,
                                                             playable->getId());
    
    Gtk::TreeRow    row = *treeModel->append();
    row[modelColumns.directionColumn]   = uploadSymbol;
    row[modelColumns.titleColumn]       = *playable->getTitle();
    row[modelColumns.dateColumn]        = *TimeConversion::nowString();
    row[modelColumns.statusColumn]      = workingStatusKey;
    row[modelColumns.statusDisplayColumn] 
                                        = *getResourceUstring(workingStatusKey);
    row[modelColumns.tokenColumn]       = token;
}


/*------------------------------------------------------------------------------
 *  Add a new download task to the list.
 *----------------------------------------------------------------------------*/
void
TransportList :: addDownload(Ptr<Playable>::Ref     playable)
                                                        throw (XmlRpcException)
{
    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    
    Ptr<Glib::ustring>::Ref     token = storage->downloadFromHub(
                                                            sessionId,
                                                            playable->getId());
    
    Gtk::TreeRow    row = *treeModel->append();
    row[modelColumns.directionColumn]   = downloadSymbol;
    row[modelColumns.titleColumn]       = *playable->getTitle();
    row[modelColumns.dateColumn]        = *TimeConversion::nowString();
    row[modelColumns.statusColumn]      = workingStatusKey;
    row[modelColumns.statusDisplayColumn] 
                                        = *getResourceUstring(workingStatusKey);
    row[modelColumns.tokenColumn]       = token;
}


/*------------------------------------------------------------------------------
 *  Add an item with an already existing token to the list.
 *----------------------------------------------------------------------------*/
void
TransportList :: add(const Glib::ustring &          title,
                     const Glib::ustring &          date,
                     const Glib::ustring &          token,
                     bool                           isUpload)
                                                        throw (XmlRpcException)
{
    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    
    Ptr<Glib::ustring>::Ref     tokenPtr(new Glib::ustring(token));
    Ptr<Glib::ustring>::Ref     errorMsg(new Glib::ustring);
    AsyncState                  state = storage->checkTransport(tokenPtr,
                                                                errorMsg);
    
    Gtk::TreeRow    row = *treeModel->append();
    row[modelColumns.directionColumn]   = isUpload ? uploadSymbol
                                                   : downloadSymbol;
    row[modelColumns.titleColumn]       = title;
    row[modelColumns.dateColumn]        = date;
    row[modelColumns.tokenColumn]       = tokenPtr;
    setStatus(row, state, errorMsg);
}


/*------------------------------------------------------------------------------
 *  Remove the currently selected item from the list.
 *----------------------------------------------------------------------------*/
void
TransportList :: removeSelected(void)                   throw (XmlRpcException)
{
    Glib::RefPtr<Gtk::TreeSelection>    selection = treeView->get_selection();
    Gtk::TreeIter                       iter = selection->get_selected();
    if (!iter) {
        return;
    }

    Ptr<StorageClientInterface>::Ref 
                                storage     = gLiveSupport->getStorageClient();
    Ptr<SessionId>::Ref         sessionId   = gLiveSupport->getSessionId();
    
    storage->cancelTransport(sessionId, 
                             iter->get_value(modelColumns.tokenColumn));
    
    treeModel->erase(iter);
}


/*------------------------------------------------------------------------------
 *  Query the storage server about the status of the pending transport.
 *----------------------------------------------------------------------------*/
bool
TransportList :: updateSelected(void)                   throw (XmlRpcException)
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
 *  Query the storage server about the status of the pending transport.
 *----------------------------------------------------------------------------*/
bool
TransportList :: update(void)                           throw (XmlRpcException)
{
    bool    didSomething = false;
    
    for (Gtk::TreeIter  it  = treeModel->children().begin();
                        it != treeModel->children().end(); ++it) {
        didSomething |= update(it);
    }
    
    return didSomething;
}


/*------------------------------------------------------------------------------
 *  Query the storage server about the status of the pending transport.
 *----------------------------------------------------------------------------*/
bool
TransportList :: updateSilently(void)                               throw ()
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
 *  Query the storage server about the status of the pending transport.
 *----------------------------------------------------------------------------*/
bool
TransportList :: update(Gtk::TreeIter   iter)           throw (XmlRpcException)
{
    if (iter->get_value(modelColumns.statusColumn) != workingStatusKey) {
        return false;
    }
    
    Ptr<StorageClientInterface>::Ref 
                                storage = gLiveSupport->getStorageClient();
    Ptr<Glib::ustring>::Ref     errorMsg(new Glib::ustring);
    AsyncState                  status = storage->checkTransport(
                                    iter->get_value(modelColumns.tokenColumn),
                                    errorMsg);
    
    return setStatus(iter, status, errorMsg);
}


/*------------------------------------------------------------------------------
 *  Set the status of the row pointed to by an iterator.
 *----------------------------------------------------------------------------*/
bool
TransportList :: setStatus(Gtk::TreeIter                        iter,
                           AsyncState                           status,
                           Ptr<const Glib::ustring>::Ref        errorMsg)
                                                                    throw ()
{
    if (status == AsyncState::initState
                    || status == AsyncState::pendingState) {
        iter->set_value(modelColumns.statusColumn,
                        workingStatusKey);
        iter->set_value(modelColumns.statusDisplayColumn, 
                        *getResourceUstring(workingStatusKey));
        return false;
        
    } else if (status == AsyncState::finishedState
                    || status == AsyncState::closedState) {
        iter->set_value(modelColumns.statusColumn,
                        successStatusKey);
        iter->set_value(modelColumns.statusDisplayColumn, 
                        *getResourceUstring(successStatusKey));
        return true;
        
    } else if (status == AsyncState::failedState) {
        iter->set_value(modelColumns.statusColumn,
                        faultStatusKey);
        iter->set_value(modelColumns.statusDisplayColumn, 
                        *formatMessage(faultStatusKey,
                                       *processException(errorMsg)));
        return false;
        
    } else {
        std::cerr << "Impossible status: '" << status
                  << "' in TransportList::setStatus()."
                  << std::endl;
    }
    
    return false;
}


/*------------------------------------------------------------------------------
 *  Return the contents of the transport list.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
TransportList :: getContents(void)                                  throw ()
{
    std::ostringstream              contentsStream;
    Gtk::TreeModel::const_iterator  it;
    Ptr<Glib::ustring>::Ref         token;
    
    for (it = treeModel->children().begin(); 
                                it != treeModel->children().end(); ++it) {
        Gtk::TreeRow        row = *it;
        if (row[modelColumns.statusColumn] == workingStatusKey) {
            if (row[modelColumns.directionColumn] == uploadSymbol) {
                contentsStream <<  "up\n";
            } else {
                contentsStream <<  "down\n";
            }
            contentsStream << row[modelColumns.titleColumn]     << '\n';
            contentsStream << row[modelColumns.dateColumn]      << '\n';
            token           = row[modelColumns.tokenColumn];
            contentsStream << *token  << '\n';
        }
    }

    Ptr<Glib::ustring>::Ref         contents(new Glib::ustring(
                                                    contentsStream.str() ));
    return contents;
}


/*------------------------------------------------------------------------------
 *  Restore the contents of the transport list.
 *----------------------------------------------------------------------------*/
void
TransportList :: setContents(Ptr<const Glib::ustring>::Ref     contents)
                                                                    throw ()
{
    std::istringstream      contentsStream(contents->raw());
    
    treeModel->clear();
    while (!contentsStream.eof()) {
        std::string   direction;
        std::string   title;
        std::string   date;
        std::string   token;

        std::getline(contentsStream, direction);
        if (contentsStream.fail()) {
            break;
        }
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
            add(title, date, token, (direction == "up"));
        
        } catch (XmlRpcException &e) {
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list.
 *----------------------------------------------------------------------------*/
void
TransportList :: onEntryClicked(GdkEventButton *    event)          throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        Gtk::TreePath           currentPath;
        Gtk::TreeViewColumn *   column;
        int     cell_x,
                cell_y;
        bool foundValidRow = treeView->get_path_at_pos(
                                            int(event->x), int(event->y),
                                            currentPath, column,
                                            cell_x, cell_y);

        if (foundValidRow) {
            Gtk::TreeIter   iter = treeModel->get_iter(currentPath);
            if (iter) {
                Gtk::TreeRow    row = *iter;
                if (row[modelColumns.directionColumn] == uploadSymbol) {
                    uploadMenu->popup(event->button, event->time);
                } else {
                    downloadMenu->popup(event->button, event->time);
                }
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for "cancel" selected from the pop-up menu.
 *----------------------------------------------------------------------------*/
void
TransportList :: onCancelTransport(void)                            throw ()
{
    try {
        removeSelected();

    } catch (XmlRpcException &e) {
        gLiveSupport->displayMessageWindow(*formatMessage(
                                                "cannotCancelTransportMsg",
                                                e.what() ));
    }    
}


/*------------------------------------------------------------------------------
 *  Handle some known exception types.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
TransportList :: processException(Ptr<const Glib::ustring>::Ref  rawMessage)
                                                                    throw ()
{
    if (rawMessage->find("[888]") != Glib::ustring::npos) {
        return getResourceUstring("duplicateFileMsg");
    } else {
        return rawMessage;
    }
}

