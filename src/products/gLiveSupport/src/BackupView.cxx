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

#ifdef HAVE_PWD_H
#include <pwd.h>
#else
#error need pwd.h
#endif

#include "LiveSupport/Core/FileTools.h"

#include "BackupView.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;
using namespace boost::posix_time;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "backupView";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
BackupView :: BackupView (GuiObject *       parent)
                                                                    throw ()
          : GuiComponent(parent,
                         bundleName)
{
    Gtk::Label *        backupTitleLabel;
    Gtk::Label *        mtimeLabel;
    Gtk::Button *       chooseTimeButton;
    Gtk::Button *       resetTimeButton;
    glade->get_widget("backupTitleLabel1", backupTitleLabel);
    glade->get_widget("backupMtimeLabel1", mtimeLabel);
    glade->get_widget("backupMtimeChooseButton1", chooseTimeButton);
    glade->get_widget("backupMtimeResetButton1", resetTimeButton);
    backupTitleLabel->set_label(*getResourceUstring("backupTitleLabel"));
    mtimeLabel->set_label(*getResourceUstring("mtimeTextLabel"));
    chooseTimeButton->set_label(*getResourceUstring("chooseTimeButtonLabel"));
    resetTimeButton->set_label(*getResourceUstring("resetTimeButtonLabel"));
    
    chooseTimeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &BackupView::onChooseTimeButtonClicked));
    resetTimeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                &BackupView::onResetTimeButtonClicked));

    glade->get_widget("backupTitleEntry1", backupTitleEntry);
    glade->get_widget("backupMtimeEntry1", mtimeEntry);
    
    writeMtimeEntry();

    constructCriteriaView();
    constructBackupListView();
    
    dateTimeChooserWindow.reset(new DateTimeChooserWindow());
}


/*------------------------------------------------------------------------------
 *  Construct the box for entering the backup criteria.
 *----------------------------------------------------------------------------*/
void
BackupView :: constructCriteriaView(void)                           throw ()
{
    criteriaEntry.reset(new AdvancedSearchEntry(this));
    criteriaEntry->connectCallback(sigc::mem_fun(*this,
                                            &BackupView::onCreateBackup));

    Gtk::Button *       backupButton;
    glade->get_widget("backupButton1", backupButton);
    backupButton->set_label(*getResourceUstring("backupButtonLabel"));
    backupButton->signal_clicked().connect(sigc::mem_fun(*this,
                                            &BackupView::onCreateBackup));
}


/*------------------------------------------------------------------------------
 *  Construct the box for listing the pending backups.
 *----------------------------------------------------------------------------*/
void
BackupView :: constructBackupListView(void)                         throw ()
{
    backupList.reset(new BackupList(this));
    
    glade->connect_clicked("backupDeleteButton1", sigc::mem_fun(*this,
                                        &BackupView::onDeleteButtonClicked));
    glade->connect_clicked("backupSaveButton1", sigc::mem_fun(*this,
                                        &BackupView::onSaveButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Event handler for the time chooser button being clicked.
 *----------------------------------------------------------------------------*/
void
BackupView :: onChooseTimeButtonClicked(void)                       throw ()
{
    Ptr<const ptime>::Ref   userMtime = dateTimeChooserWindow->run();
    
    if (userMtime && *userMtime != not_a_date_time) {
        mtime = userMtime;
        writeMtimeEntry();
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the "reset time" button being clicked.
 *----------------------------------------------------------------------------*/
void
BackupView :: onResetTimeButtonClicked(void)                        throw ()
{
    mtime.reset();
    writeMtimeEntry();
}


/*------------------------------------------------------------------------------
 *  Initiate the creation of a new backup.
 *----------------------------------------------------------------------------*/
void
BackupView :: onCreateBackup(void)                                  throw ()
{
    Ptr<Glib::ustring>::Ref     title    = readTitle();
    Ptr<SearchCriteria>::Ref    criteria = criteriaEntry->getSearchCriteria();
    
    if (mtime) {
        criteria->addMtimeCondition(">=", mtime);
    }
    
    try {
        backupList->add(title, criteria);
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     errorMsg
                                    = getResourceUstring("backupErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(*errorMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Delete button being clicked.
 *----------------------------------------------------------------------------*/
void
BackupView :: onDeleteButtonClicked(void)                           throw ()
{
    try {
        backupList->removeSelected();
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     errorMsg
                                    = getResourceUstring("backupErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(*errorMsg);
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
        url = backupList->getSelectedUrl();
        
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     errorMsg
                                    = getResourceUstring("backupErrorMsg");
        errorMsg->append(e.what());
        gLiveSupport->displayMessageWindow(*errorMsg);
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
    
    struct passwd *             pwd = getpwuid(getuid());
    if (pwd) {
        dialog->set_current_folder(pwd->pw_dir);
    }
    
    Ptr<Glib::ustring>::Ref     fileName = backupList->getSelectedTitle();
    fileName->append(".tar");
    dialog->set_current_name(*fileName);

    dialog->add_button(Gtk::Stock::CANCEL,  Gtk::RESPONSE_CANCEL);
    dialog->add_button(Gtk::Stock::SAVE,    Gtk::RESPONSE_OK);

    int result = dialog->run();

    if (result != Gtk::RESPONSE_OK) {
        return;
    }
    
    fileName->assign(dialog->get_filename());
    try {
        FileTools::copyUrlToFile(*url, *fileName);
    
    } catch (std::runtime_error &e) {
        // TODO: handle error
    }
}


/*------------------------------------------------------------------------------
 *  Read the title of the backup from the entry field.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
BackupView :: readTitle(void)                                       throw ()
{
    Ptr<Glib::ustring>::Ref     title(new Glib::ustring(
                                                backupTitleEntry->get_text() ));
    if (*title == "") {
        title = getResourceUstring("defaultBackupTitle");
    }
    
    return title;
}


/*------------------------------------------------------------------------------
 *  Format and write the contents of mtime into the mtimeEntry.
 *----------------------------------------------------------------------------*/
void
BackupView :: writeMtimeEntry(void)                                 throw ()
{
    if (mtime) {
        mtimeEntry->set_text(to_simple_string(*mtime));
    } else {
        mtimeEntry->set_text("-");
    }
}

