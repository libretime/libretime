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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/TransportList.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TimeConversion.h"
#include "TransportList.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Widgets;
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
TransportList :: TransportList (Ptr<GLiveSupport>::Ref    gLiveSupport,
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
        treeView->appendColumn("",
                               modelColumns.directionColumn, 20);
        treeView->appendColumn(*getResourceUstring("titleColumnLabel"),
                               modelColumns.titleColumn, 300);
        treeView->appendColumn(*getResourceUstring("dateColumnLabel"),
                               modelColumns.dateColumn, 180);
        treeView->appendColumn(*getResourceUstring("statusColumnLabel"),
                               modelColumns.statusDisplayColumn, 50);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    // register the signal handler for treeview entries being clicked
    treeView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                        &TransportList::onEntryClicked));

    // create the right-click entry context menu
    uploadMenu      = Gtk::manage(new Gtk::Menu());
    downloadMenu    = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList&    uploadMenuList      = uploadMenu->items();
    Gtk::Menu::MenuList&    downloadMenuList    = downloadMenu->items();
    
    try{
        uploadMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cancelUploadMenuItem"),
                                sigc::mem_fun(*this,
                                        &TransportList::onCancelTransport)));
        downloadMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                *getResourceUstring("cancelDownloadMenuItem"),
                                sigc::mem_fun(*this,
                                        &TransportList::onCancelTransport)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    uploadMenu->accelerate(*this);
    downloadMenu->accelerate(*this);

    // add the tree view to this widget
    Gtk::VBox::pack_start(*treeView);

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
    StorageClientInterface::TransportState
                                state = storage->checkTransport(tokenPtr,
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
    StorageClientInterface::TransportState
                                status = storage->checkTransport(
                                    iter->get_value(modelColumns.tokenColumn),
                                    errorMsg);
    
    return setStatus(iter, status, errorMsg);
}


/*------------------------------------------------------------------------------
 *  Set the status of the row pointed to by an iterator.
 *----------------------------------------------------------------------------*/
bool
TransportList :: setStatus(Gtk::TreeIter                            iter,
                           StorageClientInterface::TransportState   status,
                           Ptr<const Glib::ustring>::Ref            errorMsg)
                                                                    throw ()
{
    switch (status) {
        case StorageClientInterface::initState:
        
        case StorageClientInterface::pendingState:
                    iter->set_value(modelColumns.statusColumn,
                                    workingStatusKey);
                    iter->set_value(modelColumns.statusDisplayColumn, 
                                    *getResourceUstring(workingStatusKey));
                    return false;
        
        case StorageClientInterface::finishedState:
        
        case StorageClientInterface::closedState:
                    iter->set_value(modelColumns.statusColumn,
                                    successStatusKey);
                    iter->set_value(modelColumns.statusDisplayColumn, 
                                    *getResourceUstring(successStatusKey));
                    return true;
        
        case StorageClientInterface::failedState:
                    iter->set_value(modelColumns.statusColumn,
                                    faultStatusKey);
                    iter->set_value(modelColumns.statusDisplayColumn, 
                                    *formatMessage(faultStatusKey, *errorMsg));
                    return false;
        
        default:    std::cerr << "Impossible status: '" << status
                              << "' in TransportList::setStatus()."
                              << std::endl;
                    return false;
    }
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
        gLiveSupport->displayMessageWindow(formatMessage(
                                                "cannotCancelTransportMsg",
                                                e.what() ));
    }    
}

