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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/BackupView.h $

------------------------------------------------------------------------------*/
#ifndef BackupView_h
#define BackupView_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/box.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ScrolledWindow.h"
#include "AdvancedSearchEntry.h"
#include "BackupList.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The backup view, a subclass of Gtk::VBox.
 *  This will be contained in another window, most likely
 *  as the contents of a notebook tab.
 *
 *  The layout of the view is roughly the following:
 *  <pre><code>
 *  +--- backup view -------------------+
 *  | +-- criteria selector pane -----+ |
 *  | +-- [[ AdvancedSearchEntry ]] --+ |
 *  | +-------------------(Backup)----+ |
 *  | +===============================+ |
 *  | +-- pending backups pane -------+ |
 *  | +-- [[ BackupList ]] -----------+ |
 *  | +---------------(Delete)-(Save)-+ |
 *  +-------------------------(Close)---+
 *  </code></pre>
 *
 *  @author $Author: fgerlits $
 *  @version $Revision$
 */
class BackupView : public Gtk::VBox,
                   public LocalizedObject
{
    private:
        /**
         *  The object for entering the backup criteria.
         */
        AdvancedSearchEntry *       criteriaEntry;
        
        /**
         *  The list of pending backups.
         */
        BackupList *                backupList;
        
        /**
         *  Construct the box for entering the backup criteria.
         *
         *  @return the constructed box, already Gtk::manage()'ed.
         */
        Gtk::Box *
        constructCriteriaView(void)                                 throw ();
    
        /**
         *  Construct the box for listing the pending backups.
         *
         *  @return the constructed box, already Gtk::manage()'ed.
         */
        Gtk::Box *
        constructBackupListView(void)                               throw ();
        
        /**
         *  Fetch the backup file from an URL and save it to a local file.
         *
         *  @param  url         the URL to fetch.
         *  @param  fileName    the local file to save as.
         *  @return     the status reported by curl (true if everything is OK).
         */
        bool
        copyUrlToFile(Ptr<Glib::ustring>::Ref   url,
                      Ptr<Glib::ustring>::Ref   fileName)           throw ();
    
    
    protected:
        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;
        
        /**
         *  Initiate the creation of a new backup.
         */
        void
        onCreateBackup(void)                                        throw ();
        
        /**
         *  Event handler for the Delete button being clicked.
         */
        void
        onDeleteButtonClicked(void)                                 throw ();
        
        /**
         *  Event handler for the Save button being clicked.
         */
        void
        onSaveButtonClicked(void)                                   throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         */
        BackupView(Ptr<GLiveSupport>::Ref     gLiveSupport,
                   Ptr<ResourceBundle>::Ref   bundle)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~BackupView(void)                                           throw ()
        {
        }

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // BackupView_h

