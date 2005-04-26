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
    Version  : $Revision: 1.1 $
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
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
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
         *  @version $Revision: 1.1 $
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
            Ptr<Glib::ustring>::Ref                             metadata,
            Ptr<SearchCriteria>::Ref                            parentCriteria,
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
         *  Return the current state of the search fields.
         *
         *  @return a new LiveSupport::Storage::SearchCriteria instance,
         *          which contains the data entered by the user
         */
        Ptr<SearchCriteria::SearchConditionType>::Ref
        getSearchCondition(void)               throw (std::invalid_argument);
        
        /**
         *  Fill in the column with the possible values (limited by the
         *  parent criteria), and set the selection to "all".
         */
        void
        reset(void)                                     throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveSupport_GLiveSupport_BrowseItem_h

