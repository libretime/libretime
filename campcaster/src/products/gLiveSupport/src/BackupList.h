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
#ifndef BackupList_h
#define BackupList_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/XmlRpcException.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "GLiveSupport.h"

#include "GuiComponent.h"


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
 *  @author $Author$
 *  @version $Revision$
 */
class BackupList : public GuiComponent,
                   public ContentsStorable
{
    private:

        /**
         *  The user preferences key.
         */
        Ptr<const Glib::ustring>::Ref       userPreferencesKey;

        /**
         *  Set the status of the row pointed to by an iterator.
         *
         *  @param  iter    points to the row we want to set the status of.
         *  @param  status  the new status ("working", "success" or "fault").
         *  @param  url     must be non-0 if the status is "success".
         *  @param  errorMessage    must be non-0 if the status is "fault".
         *  @return true    if the status is "success", false otherwise.
         */
        bool
        setStatus(Gtk::TreeIter                         iter,
                  AsyncState                            status,
                  Ptr<const Glib::ustring>::Ref         url,
                  Ptr<const Glib::ustring>::Ref         errorMessage)
                                                                throw ();

        /**
         *  Add an item with an already existing token to the list.
         *
         *  @param  title       the title of the backup.
         *  @param  date        the date of the backup.
         *  @param  token       the token for this backup.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        void
        add(const Glib::ustring &     title,
            const Glib::ustring &     date,
            const Glib::ustring &     token)
                                                throw (XmlRpcException);
        
        /**
         *  Query the storage server about the status of the given row.
         *  If its status is 'working', call createBackupCheck
         *  on it, and change its displayed status, if needed.
         *
         *  @param  iter    points to the row to be updated.
         *  @return true    if createBackupCheck was called, and it returned
         *                  'success'; false in all other cases.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        bool
        update(Gtk::TreeIter    iter)           throw (XmlRpcException);


    protected:

        /**
         *  The columns model needed by ZebraTreeView.
         *
         *  @author $Author$
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
         *  @param parent         the GuiObject which contains this one.
         */
        BackupList(GuiObject *          parent)
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
        removeSelected(void)                    throw (XmlRpcException);

        /**
         *  Get the title of the currently selected item.
         *  If no item is selected, then a 0 pointer is returned.
         */
        Ptr<Glib::ustring>::Ref
        getSelectedTitle(void)                                  throw ();
        
        /**
         *  Get the URL of the currently selected item.
         *  If the status of the item is 'working', then an update() is
         *  done first.
         *  If no item is selected, or the URL for the backup is not available
         *  yet, then a 0 pointer is returned.
         *
         *  @exception  XmlRpcException     can be thrown by update().
         */
        Ptr<Glib::ustring>::Ref
        getSelectedUrl(void)                    throw (XmlRpcException);
        
        /**
         *  Query the storage server about the status of the selected row.
         *  If its status is 'working', call createBackupCheck
         *  on it, and change its displayed status, if needed.
         *
         *  @return true    if createBackupCheck was called, and it returned
         *                  'success'; false in all other cases.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        bool
        updateSelected(void)                    throw (XmlRpcException);

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

        /**
         *  Query the storage server about the status of the pending backup.
         *  If there is a backup with status 'working', call createBackupCheck
         *  on it, and change its displayed status, if needed.
         *  This is the same as update(), except it does not throw any
         *  exceptions (just ignores them).
         *
         *  @return true    if createBackupCheck was called, and it returned
         *                  'success'; false in all other cases.
         */
        bool
        updateSilently(void)                                    throw ();

        /**
         *  Return the contents of the backup list.
         *  The format is a space-separated list of backup titles, dates
         *  and tokens.  E.g.: "title1 date1 token1 title2 date2 token2".
         *
         *  @return the contents of the backup list as a string.
         */
        Ptr<Glib::ustring>::Ref
        getContents(void)                                       throw ();

        /**
         *  Restore the contents of the backup list.
         *  The current contents are discarded, and replaced with the items
         *  listed in the 'contents' parameter.
         *  The format is a space-separated list of backup titles, dates
         *  and tokens.  E.g.: "title1 date1 token1 title2 date2 token2".
         *
         *  @param contents the new contents of the backup list as a string.
         */
        void
        setContents(Ptr<const Glib::ustring>::Ref   contents)   throw ();

        /**
         *  Return the user preferences key.
         *  The contents of the window will be stored in the user preferences
         *  under this key.
         *
         *  @return the user preference key.
         */
        Ptr<const Glib::ustring>::Ref
        getUserPreferencesKey(void)                              throw ()
        {
            return userPreferencesKey;
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // BackupList_h

