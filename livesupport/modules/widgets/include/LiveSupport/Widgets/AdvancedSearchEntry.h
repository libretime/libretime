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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/Attic/AdvancedSearchEntry.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_AdvancedSearchEntry_h
#define LiveSupport_Widgets_AdvancedSearchEntry_h

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
#include "LiveSupport/Core/SearchCriteria.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/EntryBin.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A Gtk::VBox with one or more search input fields in it.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.3 $
 */
class AdvancedSearchEntry : public Gtk::VBox,
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
         *  The operator field.
         */
        ComboBoxText *          operatorEntry;

        /**
         *  The "search for this value" field.
         */
        EntryBin *              valueEntry;
        
        /**
         *  Default constructor.
         */
        AdvancedSearchEntry(void)                               throw ();

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
         */
        AdvancedSearchEntry(Ptr<ResourceBundle>::Ref    bundle)
                                                                throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~AdvancedSearchEntry(void)                              throw ()
        {
        }

        /**
         *  Return the current state of the search fields.
         *
         *  @return a new LiveSupport::Storage::SearchCriteria instance,
         *          which contains the data entered by the user
         */
        Ptr<SearchCriteria>::Ref
        getSearchCriteria(void)                                 throw ();

        /**
         *  Connect a callback to the "enter key pressed" event.
         *
         *  @param callback the function to execute when enter is pressed.
         */
        void
        connectCallback(const sigc::slot<void> &    callback)   throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_AdvancedSearchEntry_h

