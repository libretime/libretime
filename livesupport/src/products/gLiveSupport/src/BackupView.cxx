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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/BackupView.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <curl/curl.h>
#include <curl/easy.h>
#include <gtkmm/filechooserdialog.h>
#include <gtkmm/stock.h>
#include <gtkmm/paned.h>

#include "LiveSupport/Widgets/ScrolledWindow.h"
#include "BackupView.h"


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
BackupView :: BackupView (Ptr<GLiveSupport>::Ref    gLiveSupport,
                          Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Gtk::Label *        backupTitleLabel;
    try {
        backupTitleLabel = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("backupTitleLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    backupTitleEntry = Gtk::manage(wf->createEntryBin());
    
    Gtk::Box *          backupTitleBox  = Gtk::manage(new Gtk::HBox);
    backupTitleBox->pack_start(*backupTitleLabel, Gtk::PACK_SHRINK, 5);
    backupTitleBox->pack_start(*backupTitleEntry, Gtk::PACK_SHRINK, 5);
    
    Gtk::Box *          criteriaView    = constructCriteriaView();
    
    Gtk::Box *          topPane         = Gtk::manage(new Gtk::VBox);
    topPane->pack_start(*backupTitleBox, Gtk::PACK_SHRINK,        5);
    topPane->pack_start(*criteriaView,   Gtk::PACK_EXPAND_WIDGET, 5);
    
    Gtk::Box *          bottomPane      = constructBackupListView();
    
    Gtk::VPaned *       twoPanedView    = Gtk::manage(new Gtk::VPaned);
    twoPanedView->pack1(*topPane,    Gtk::PACK_EXPAND_WIDGET, 5);
    twoPanedView->pack2(*bottomPane, Gtk::PACK_EXPAND_WIDGET, 5);
    
    add(*twoPanedView);
}


/*------------------------------------------------------------------------------
 *  Construct the box for entering the backup criteria.
 *----------------------------------------------------------------------------*/
Gtk::Box *
BackupView :: constructCriteriaView(void)                           throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    criteriaEntry = Gtk::manage(new AdvancedSearchEntry(gLiveSupport));
    criteriaEntry->connectCallback(sigc::mem_fun(
                                    *this, &BackupView::onCreateBackup ));

    Button *    backupButton;
    try {
        backupButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("backupButtonLabel") ));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    backupButton->signal_clicked().connect(sigc::mem_fun(
                                    *this, &BackupView::onCreateBackup ));
    
    Gtk::Box *  criteriaButtonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END ));
    criteriaButtonBox->pack_start(*backupButton, Gtk::PACK_SHRINK, 5);
    
    ScrolledWindow *    criteriaWindow    = Gtk::manage(new ScrolledWindow);
    criteriaWindow->add(*criteriaEntry);
    // NOTE: criteriaWindow->setShadowType() causes Gtk warnings here
    // TODO: find out why and fix it
    criteriaWindow->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
    
    Gtk::Box *  criteriaView = Gtk::manage(new Gtk::VBox);
    criteriaView->pack_start(*criteriaWindow,    Gtk::PACK_EXPAND_WIDGET, 0);
    criteriaView->pack_start(*criteriaButtonBox, Gtk::PACK_SHRINK,        5);
    
    return criteriaView;
}


/*------------------------------------------------------------------------------
 *  Construct the box for listing the pending backups.
 *----------------------------------------------------------------------------*/
Gtk::Box *
BackupView :: constructBackupListView(void)                         throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    backupList = Gtk::manage(new BackupList(gLiveSupport, getBundle()));
    // TODO: connect callbacks
    
    Button *    deleteButton;
    Button *    saveButton;
    try {
        deleteButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("deleteButtonLabel") ));
        saveButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("saveButtonLabel") ));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    deleteButton->signal_clicked().connect(sigc::mem_fun(
                                    *this, &BackupView::onDeleteButtonClicked));
    saveButton->signal_clicked().connect(sigc::mem_fun(
                                    *this, &BackupView::onSaveButtonClicked));
    
    Gtk::Box *  backupListButtonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END ));
    backupListButtonBox->pack_start(*deleteButton, Gtk::PACK_SHRINK, 5);
    backupListButtonBox->pack_start(*saveButton, Gtk::PACK_SHRINK, 5);

    ScrolledWindow *    backupListWindow  = Gtk::manage(new ScrolledWindow);
    backupListWindow->add(*backupList);
    backupListWindow->setShadowType(Gtk::SHADOW_NONE);
    backupListWindow->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
    
    Gtk::Box *  backupListView = Gtk::manage(new Gtk::VBox);
    backupListView->pack_start(*backupListWindow, Gtk::PACK_EXPAND_WIDGET, 5);
    backupListView->pack_start(*backupListButtonBox,     Gtk::PACK_SHRINK, 5);
    
    return backupListView;
}


/*------------------------------------------------------------------------------
 *  Initiate the creation of a new backup.
 *----------------------------------------------------------------------------*/
void
BackupView :: onCreateBackup(void)                                  throw ()
{
    Ptr<Glib::ustring>::Ref     title    = readTitle();
    Ptr<SearchCriteria>::Ref    criteria = criteriaEntry->getSearchCriteria();
    
    try {
        backupList->add(title, criteria);
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     errorMsg
                                    = getResourceUstring("backupErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(errorMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Delete button being clicked.
 *----------------------------------------------------------------------------*/
void
BackupView :: onDeleteButtonClicked(void)                           throw ()
{
    try {
        backupList->remove();
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     errorMsg
                                    = getResourceUstring("backupErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(errorMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Save button being clicked.
 *----------------------------------------------------------------------------*/
void
BackupView :: onSaveButtonClicked(void)                             throw ()
{
    Ptr<Glib::ustring>::Ref         url;
    try {
        url = backupList->getUrl();
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     errorMsg
                                    = getResourceUstring("backupErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(errorMsg);
    }
    
    if (!url) {
        return;
    }

    Ptr<Gtk::FileChooserDialog>::Ref    dialog;
    try {
        dialog.reset(new Gtk::FileChooserDialog(
                                *getResourceUstring("fileChooserDialogTitle"),
                                Gtk::FILE_CHOOSER_ACTION_SAVE));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
//    dialog->set_transient_for(*this);

    dialog->add_button(Gtk::Stock::CANCEL,  Gtk::RESPONSE_CANCEL);
    dialog->add_button(Gtk::Stock::SAVE,    Gtk::RESPONSE_OK);

    int result = dialog->run();

    if (result != Gtk::RESPONSE_OK) {
        return;
    }
    
    Ptr<Glib::ustring>::Ref     fileName(new Glib::ustring(
                                                    dialog->get_filename() ));
    copyUrlToFile(url, fileName);
}


/*------------------------------------------------------------------------------
 *  Fetch the backup file from an URL and save it to a local file.
 *----------------------------------------------------------------------------*/
bool
BackupView :: copyUrlToFile(Ptr<Glib::ustring>::Ref   url,
                            Ptr<Glib::ustring>::Ref   fileName)     throw ()
{
    FILE*   localFile      = fopen(fileName->c_str(), "wb");
    if (!localFile) {
        return false;
    }

    CURL*    handle     = curl_easy_init();
    if (!handle) {
        fclose(localFile);
        return false;
    }
    
    int    status =   curl_easy_setopt(handle, CURLOPT_URL, url->c_str()); 
    status |=   curl_easy_setopt(handle, CURLOPT_WRITEDATA, localFile);
    status |=   curl_easy_setopt(handle, CURLOPT_HTTPGET);

    if (status) {
        fclose(localFile);
        return false;
    }

    status =    curl_easy_perform(handle);

    if (status) {
        fclose(localFile);
        return false;
    }

    curl_easy_cleanup(handle);
    fclose(localFile);
    return true;
}


/*------------------------------------------------------------------------------
 *  Read the title of the backup from the entry field.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
BackupView :: readTitle(void)                                       throw ()
{
    Ptr<Glib::ustring>::Ref     title(new Glib::ustring(
                                                backupTitleEntry->get_text() ));
    if (*title != "") {
        return title;
    }
    
    try {
        title = getResourceUstring("defaultBackupTitle");
        
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    return title;
}

