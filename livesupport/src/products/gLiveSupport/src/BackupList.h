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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/BackupList.h $

------------------------------------------------------------------------------*/
#ifndef BackupList_h
#define BackupList_h

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
#include "LiveSupport/Core/XmlRpcException.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ScrolledWindow.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The list of pending backups.
 *
 *  This is a Gtk::VBox containing a TreeView with the following columns:
 *  <ul>
 *  <li>shown:
 *      <ul>
 *      <li>backup title </li>
 *      <li>backup date </li>
 *      <li>backup status (localized; contains the fault string, if any)</li>
 *      </ul></li>
 *  <li>hidden:
 *      <ul>
 *      <li>token </li>
 *      <li>backup status (not localized: "working" / "success" / "fault")</li>
 *      <li>URL (if the status is "success") </li>
 *      </ul></li>
 *  </ul>
 *  
 *  The BackupList is contained in the BackupView.
 *
 *  @author $Author: fgerlits $
 *  @version $Revision$
 */
class BackupList : public Gtk::VBox,
                   public LocalizedObject
{
    private:
    
    
    protected:
        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The columns model needed by ZebraTreeView.
         *
         *  @author $Author: fgerlits $
         *  @version $Revision$
         */
        class ModelColumns : public ZebraTreeModelColumnRecord
        {
            public:
                /**
                 *  The column for the title of the backup.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         titleColumn;

                /**
                 *  The column for the date of the backup.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         dateColumn;

                /**
                 *  The column for the status of the backup (not localized).
                 */
                Gtk::TreeModelColumn<Glib::ustring>         statusColumn;

                /**
                 *  The column for the status of the backup (localized).
                 */
                Gtk::TreeModelColumn<Glib::ustring>         statusDisplayColumn;

                /**
                 *  The column for the token corresponding to the backup.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         tokenColumn;

                /**
                 *  The column for the URL of the backup.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         urlColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(titleColumn);
                    add(dateColumn);
                    add(statusColumn);
                    add(statusDisplayColumn);
                    add(tokenColumn);
                    add(urlColumn);
                }
        };


        /**
         *  The column model.
         */
        ModelColumns                    modelColumns;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The tree view.
         */
        ZebraTreeView *                 treeView;


    public:
        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         */
        BackupList(Ptr<GLiveSupport>::Ref     gLiveSupport,
                   Ptr<ResourceBundle>::Ref   bundle)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~BackupList(void)                                           throw ()
        {
        }
        
        /**
         *  Add a new item to the list.
         *
         *  @param  title       the title of the backup.
         *  @param  criteria    the search criteria for the backup.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        void
        add(Ptr<Glib::ustring>::Ref     title,
            Ptr<SearchCriteria>::Ref    criteria)
                                                throw (XmlRpcException);
        
        /**
         *  Remove the currently selected item from the list.
         *  The createBackupClose storage function is called on the backup task,
         *  and it is removed from the tree model.
         *
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        void
        remove(void)                            throw (XmlRpcException);

        /**
         *  Get the URL of the currently selected item.
         *  If the status of the item is 'working', then an update() is
         *  done first.
         *  If no item is selected, or the URL for the backup is not available
         *  yet, then a 0 pointer is returned.
         */
        Ptr<Glib::ustring>::Ref
        getUrl(void)                                                throw ();
        
        /**
         *  Query the storage server about the status of the pending backup.
         *  If there is a backup with status 'working', call createBackupCheck
         *  on it, and change its displayed status, if needed.
         *
         *  @return true    if createBackupCheck was called, and it returned
         *                  'success'; false in all other cases.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        bool
        update(void)                            throw (XmlRpcException);
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // BackupList_h

