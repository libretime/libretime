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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/BrowseItem.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_GLiveSupport_BrowseItem_h
#define LiveSupport_GLiveSupport_BrowseItem_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <vector>
#include <utility>

#include <gtkmm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Core/SearchCriteria.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"

#include "GLiveSupport.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A single browse input field.
 *
 *  It consists of a Widgets::ComboBoxText and a Widgets::ZebraTreeView
 *  (without header).  It stores a "parent search criteria", and shows all
 *  possible metadata values of the type selected in the ComboBoxText which
 *  match this condition.  The parent search criteria should be conjunction
 *  of all search conditions selected in BrowseItem objects to the left of
 *  this one.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.2 $
 */
class BrowseItem : public Gtk::VBox,
                   public LocalizedObject
{
    private:
    
        /**
         *  The type for storing both the metadata and the comparison operator
         *  localizations.
         */
        typedef std::vector<std::pair<Glib::ustring, Glib::ustring> >
                                MapVector;

        /**
         *  The list of possible metadata field names.
         */
        Ptr<MapVector>::Ref     metadataTypes;
    
        /**
         *  The list of possible comparison operators.
         */
        Ptr<MapVector>::Ref     operatorTypes;
           
        /**
         *  The metadata field.
         */
        ComboBoxText *          metadataEntry;
        
        /**
         *  The selection field.
         */
        ZebraTreeView *         metadataValues;
        
        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one clip per row.
         *
         *  @author $Author: fgerlits $
         *  @version $Revision: 1.2 $
         */
        class ModelColumns : public ZebraTreeModelColumnRecord
        {
            public:
                /**
                 *  The single displayed column.
                 */
                Gtk::TreeModelColumn<Glib::ustring>     column;
                
                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(column);
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
         *  This is pretty lame, but we store the localized version of the 
         *  "--- all ---" string here.
         */
        Glib::ustring                   allString;

        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<LiveSupport::GLiveSupport::GLiveSupport>::Ref   gLiveSupport;
         
        /**
         *  The criteria from the browse items to the left of this one.
         */
        Ptr<SearchCriteria>::Ref        parentCriteria;
         
        /**
         *  Default constructor.
         */
        BrowseItem(void)                               throw ();

        /**
         *  Read the localized metadata field names.
         *
         *  @exception std::invalid_argument if some keys are not found in
         *                                   the resource bundle
         */
        void
        readMetadataTypes(void)                 throw (std::invalid_argument);

        /**
         *  Read the localized comparison operator names.
         *
         *  @exception std::invalid_argument if some keys are not found in
         *                                   the resource bundle
         */
        void
        readOperatorTypes(void)                 throw (std::invalid_argument);

        /**
         *  Emit the "selection changed" signal.
         */
        void
        emitSignalSelectionChanged(void)        throw ()
        {
            signalSelectionChanged().emit();
        }


    protected:
    
        /**
         *  A signal object to notify people that the selection has changed.
         */
        sigc::signal<void>              signalSelectionChangedObject;


    public:
    
        /**
         *  Constructor with parent and localization parameter.
         *
         *  @param isFirst  true if this is the first search condition
         *                  (so it does not need a Close button)
         *  @param bundle   the resource bundle for localization
         */
        BrowseItem(
            Ptr<LiveSupport::GLiveSupport::GLiveSupport>::Ref   gLiveSupport,
            const Glib::ustring &                               metadata,
            Ptr<ResourceBundle>::Ref                            bundle)
                                                       throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~BrowseItem(void)                              throw ()
        {
        }

        /**
         *  Return the search criteria selected by the user.
         *  This is the parent criteria (assumed to have operator "and")
         *  with the search condition showing the current selection added
         *  (if any).
         *
         *  @return a new LiveSupport::Storage::SearchCriteria instance,
         *          which contains the data entered by the user
         */
        Ptr<SearchCriteria>::Ref
        getSearchCriteria(void)                 throw (std::invalid_argument);
        
        /**
         *  Fill in the column with the possible values (limited by the
         *  parent criteria), and set the selection to "all".
         */
        void
        onShow(void)                                    throw ();

        /**
         *  The signal handler for refreshing the treeview of metadata values,
         *  if we also need to change the parent criteria.  Same as onShow(),
         *  plus changing the parent criteria.
         *
         *  @param criteria     the new parent search criteria
         */
        void
        onParentChangedShow(BrowseItem *    leftNeighbor)
                                                        throw ()
        {
            parentCriteria = leftNeighbor->getSearchCriteria();
            onShow();
        }
        
        /**
         *  The signal raised when either the combo box or the tree view
         *  selection has changed.
         *
         *  @return the signal object (a protected member of this class)
         */
        sigc::signal<void>
        signalSelectionChanged(void)                        throw ()
        {
            return signalSelectionChangedObject;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveSupport_GLiveSupport_BrowseItem_h

