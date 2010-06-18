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
#ifndef BackupView_h
#define BackupView_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/TimeConversion.h"
#include "DateTimeChooserWindow.h"
#include "AdvancedSearchEntry.h"
#include "BackupList.h"
#include "GLiveSupport.h"

#include "GuiComponent.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace boost::posix_time;

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
 *  @author $Author$
 *  @version $Revision$
 */
class BackupView : public GuiComponent
{
    private:

        /**
         *  The text entry field for entering the title of the backup.
         */
        Gtk::Entry *                backupTitleEntry;
        
        /**
         *  The "modified since" time for the backup.
         */
        Ptr<const ptime>::Ref       mtime;
        
        /**
         *  The entry field holding the "modified since" time for the backup.
         */
        Gtk::Entry *                mtimeEntry;
        
        /**
         *  The window for entering the "modified since" time.
         */
        Ptr<DateTimeChooserWindow>::Ref     dateTimeChooserWindow;
        
        /**
         *  The object for entering the backup criteria.
         */
        Ptr<AdvancedSearchEntry>::Ref       criteriaEntry;
        
        /**
         *  The list of pending backups.
         */
        Ptr<BackupList>::Ref                backupList;
        
        /**
         *  Construct the box for entering the backup criteria.
         *
         *  @return the constructed box, already Gtk::manage()'ed.
         */
        void
        constructCriteriaView(void)                                 throw ();
    
        /**
         *  Construct the box for listing the pending backups.
         *
         *  @return the constructed box, already Gtk::manage()'ed.
         */
        void
        constructBackupListView(void)                               throw ();
        
        /**
         *  Read the title of the backup from the entry field.
         *  If the entry is blank, a default title is used.
         *
         *  @return the title.
         */
        Ptr<Glib::ustring>::Ref
        readTitle(void)                                             throw ();

        /**
         *  Format and write the contents of mtime into the mtimeEntry.
         */
        void
        writeMtimeEntry(void)                                       throw ();

    
    protected:

        /**
         *  Event handler for the time chooser button being clicked.
         */
        void
        onChooseTimeButtonClicked(void)                             throw ();
        
        /**
         *  Event handler for the "reset time" button being clicked.
         */
        void
        onResetTimeButtonClicked(void)                              throw ();
        
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
         *  @param  parent  the GuiObject which contains this one.
         */
        BackupView(GuiObject *         parent)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~BackupView(void)                                           throw ()
        {
        }

        /**
         *  Return the BackupList object shown by the widget.
         */
        Ptr<BackupList>::Ref
        getBackupList(void)                                         throw ()
        {
            return backupList;
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // BackupView_h

