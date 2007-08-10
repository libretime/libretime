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
#ifndef TransportList_h
#define TransportList_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/UniqueId.h"
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
 *  The list of pending transports.
 *
 *  This is a Gtk::VBox containing a TreeView with the following columns:
 *  <ul>
 *  <li>shown:
 *      <ul>
 *      <li>the direction of the transfer (upload or download) </li>
 *      <li>the title of the transported file </li>
 *      <li>transport date </li>
 *      <li>transport status (localized; contains the fault string, if any)</li>
 *      </ul></li>
 *  <li>hidden:
 *      <ul>
 *      <li>token </li>
 *      <li>transport status (not localized:
 *                                      "working" / "success" / "fault")</li>
 *      </ul></li>
 *  </ul>
 *
 *  The TransportList is contained in the SearchWindow.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class TransportList : public GuiComponent,
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
         *  @param  errorMessage    the error message (for status "fault");
         *                          optional
         *  @return true    if the status is "success", false otherwise.
         */
        bool
        setStatus(Gtk::TreeIter                         iter,
                  AsyncState                            status,
                  Ptr<const Glib::ustring>::Ref         errorMsg
                                            = Ptr<const Glib::ustring>::Ref())
                                                                throw ();

        /**
         *  Add an item with an already existing token to the list.
         *
         *  @param  title       the title of the transport.
         *  @param  date        the date of the transport.
         *  @param  token       the token for this transport.
         *  @param  isUpload    true if this is an upload transfer;
         *                      false if this is a download transfer.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        void
        add(const Glib::ustring &     title,
            const Glib::ustring &     date,
            const Glib::ustring &     token,
            bool                      isUpload)
                                                throw (XmlRpcException);
        
        /**
         *  Query the storage server about the status of the given row.
         *  If its status is 'working', call getTransportInfo
         *  on it, and change its displayed status, if needed.
         *
         *  @param  iter    points to the row to be updated.
         *  @return true    if getTransportInfo was called, and it returned
         *                  'success'; false in all other cases.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        bool
        update(Gtk::TreeIter    iter)           throw (XmlRpcException);

        /**
         *  Handle some known exception types.
         *
         *  @param  rawMessage  the error message to be processed.
         *  @return a localized error message if rawMessage contains
         *          [xxx], where xxx is a recognized error code.
         */
        Ptr<const Glib::ustring>::Ref
        processException(Ptr<const Glib::ustring>::Ref  rawMessage)
                                                            throw ();


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
                 *  The column for the direction of the transport (up/down).
                 */
                Gtk::TreeModelColumn<Glib::ustring>         directionColumn;

                /**
                 *  The column for the title of the transported file.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         titleColumn;

                /**
                 *  The column for the date of the transport.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         dateColumn;

                /**
                 *  The column for the status of the transport (not localized).
                 */
                Gtk::TreeModelColumn<Glib::ustring>         statusColumn;

                /**
                 *  The column for the status of the transport (localized).
                 */
                Gtk::TreeModelColumn<Glib::ustring>         statusDisplayColumn;

                /**
                 *  The column for the token corresponding to the transport.
                 */
                Gtk::TreeModelColumn<Ptr<Glib::ustring>::Ref>
                                                            tokenColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(directionColumn);
                    add(titleColumn);
                    add(dateColumn);
                    add(statusColumn);
                    add(statusDisplayColumn);
                    add(tokenColumn);
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
        
        /**
         *  The pop-up menu for uploads.
         */
        Ptr<Gtk::Menu>::Ref             uploadMenu;
        
        /**
         *  The pop-up menu for downloads.
         */
        Ptr<Gtk::Menu>::Ref             downloadMenu;

        /**
         *  Event handler for an entry being clicked in the list.
         *  This is used to pop up the right-click context menu.
         *
         *  @param event the button event recieved
         */
        void
        onEntryClicked(GdkEventButton *     event)                  throw ();
        
        /**
         *  Event handler for "cancel" selected from the pop-up menu.
         */
        void
        onCancelTransport(void)                                     throw ();


    public:

        /**
         *  Constructor.
         *
         *  @param  parent  the GuiObject which contains this one.
         */
        TransportList(GuiObject *         parent)                   throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~TransportList(void)                                        throw ()
        {
        }
        
        /**
         *  Add a new upload task to the list.
         *
         *  @param  playable    the playable object to be uploaded to the
         *                      network hub.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        void
        addUpload(Ptr<Playable>::Ref        playable)
                                                        throw (XmlRpcException);
        
        /**
         *  Add a new download task to the list.
         *
         *  The <i>playable</i> parameter can be an incomplete object; 
         *  all it needs to have is a unique ID, a type (audio clip or
         *  playlist), and a title.
         *
         *  @param  playable    the playable object to be downloaded from the
         *                      network hub.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        void
        addDownload(Ptr<Playable>::Ref      playable)
                                                        throw (XmlRpcException);
        
        /**
         *  Remove the currently selected item from the list.
         *
         *  The doTransportAction storage function is 
         *  called on the transport task with the cancel parameter,
         *  and it is removed from the tree model.
         *
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        void
        removeSelected(void)                            throw (XmlRpcException);

        /**
         *  Query the storage server about the status of the selected row.
         *
         *  If its status is 'working', call getTransportInfo
         *  on it, and change its displayed status, if needed.
         *
         *  @return true    if getTransportInfo was called, and it returned
         *                  'success'; false in all other cases.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        bool
        updateSelected(void)                            throw (XmlRpcException);

        /**
         *  Query the storage server about the status of the pending transport.
         *
         *  If there is a transport with status 'working', call getTransportInfo
         *  on it, and change its displayed status, if needed.
         *
         *  @return true    if getTransportInfo was called, and it returned
         *                  at least one 'success'; false in all other cases.
         *  @exception  XmlRpcException     thrown by the storage client.
         */
        bool
        update(void)                                    throw (XmlRpcException);

        /**
         *  Query the storage server about the status of the pending transport.
         *
         *  If there is a transport with status 'working', call getTransportInfo
         *  on it, and change its displayed status, if needed.
         *
         *  This is the same as update(), except it does not throw any
         *  exceptions (just ignores them).
         *
         *  @return true    if getTransportInfo was called, and it returned
         *                  at least one 'success'; false in all other cases.
         */
        bool
        updateSilently(void)                                        throw ();

        /**
         *  Return the contents of the transport list.
         *
         *  The format is a newline-separated list of transport directions,
         *  titles, dates and tokens.
         *  E.g.: "up title1 date1 token1 down title2 date2 token2".
         *
         *  @return the contents of the transport list as a string.
         */
        Ptr<Glib::ustring>::Ref
        getContents(void)                                           throw ();

        /**
         *  Restore the contents of the transport list.
         *
         *  The current contents are discarded, and replaced with the items
         *  listed in the 'contents' parameter.
         *  The format is a newline-separated list of transport directions,
         *  titles, dates and tokens.
         *  E.g.: "up title1 date1 token1 down title2 date2 token2".
         *
         *  @param contents the new contents of the transport list as a string.
         */
        void
        setContents(Ptr<const Glib::ustring>::Ref   contents)       throw ();

        /**
         *  Return the user preferences key.
         *
         *  The contents of the window will be stored in the user preferences
         *  under this key.
         *
         *  @return the user preference key.
         */
        Ptr<const Glib::ustring>::Ref
        getUserPreferencesKey(void)                                 throw ()
        {
            return userPreferencesKey;
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // TransportList_h

