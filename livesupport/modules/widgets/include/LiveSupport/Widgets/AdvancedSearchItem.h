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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/Attic/AdvancedSearchItem.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_AdvancedSearchItem_h
#define LiveSupport_Widgets_AdvancedSearchItem_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <vector>
#include <utility>

#include <gtkmm/box.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Core/SearchCriteria.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/EntryBin.h"
#include "LiveSupport/Widgets/ImageButton.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A single search input field.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class AdvancedSearchItem : public Gtk::HBox,
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
        Ptr<MapVector>::Ref  metadataTypes;
    
        /**
         *  The list of possible comparison operators.
         */
        Ptr<MapVector>::Ref  operatorTypes;
           
        /**
         *  The metadata field.
         */
        ComboBoxText *          metadataEntry;

        /**
         *  The operator field.
         */
        ComboBoxText *          operatorEntry;

        /**
         *  The "search for this value" field.
         */
        EntryBin *              valueEntry;
        
        /**
         *  The "add new search item" button.
         */
        ImageButton *           plusButton;
        
        /**
         *  The "remove this item" button.
         */
        ImageButton *           closeButton;
        
        /**
         *  Default constructor.
         */
        AdvancedSearchItem(void)                               throw ();

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
         *  Constructor with localization parameter.
         *
         *  @param isFirst  true if this is the first search condition
         *                  (so it does not need a Close button)
         *  @param bundle   the resource bundle for localization
         */
        AdvancedSearchItem(bool                        isFirst,
                           Ptr<ResourceBundle>::Ref    bundle)
                                                                throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~AdvancedSearchItem(void)                              throw ()
        {
        }

        /**
         *  Return the current state of the search fields.
         *
         *  @return a new LiveSupport::Storage::SearchCriteria instance,
         *          which contains the data entered by the user
         */
        Ptr<SearchCriteria::SearchConditionType>::Ref
        getSearchCondition(void)                                throw ();

        /**
         *  The signal proxy for pressing enter in the entry field.
         *
         *  @return the signal_activate() proxy of the EntryBin.
         */
        Glib::SignalProxy0<void>
        signal_activate(void)                                   throw ()
        {
            return valueEntry->signal_activate();
        }
        
        /**
         *  The signal proxy for pressing the add new condition button.
         *
         *  @return the signal_activate() proxy of the Plus button.
         */
        Glib::SignalProxy0<void>
        signal_add_new(void)                                    throw ()
        {
            return plusButton->signal_clicked();
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_AdvancedSearchItem_h

