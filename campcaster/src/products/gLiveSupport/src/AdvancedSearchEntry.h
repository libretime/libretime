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
#ifndef AdvancedSearchEntry_h
#define AdvancedSearchEntry_h

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
#include "LiveSupport/Core/MetadataTypeContainer.h"
#include "LiveSupport/Core/SearchCriteria.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A Gtk::VBox with one or more search input fields in it.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class AdvancedSearchEntry : public Gtk::VBox, 
                            public LocalizedObject
{
    private:
        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref              gLiveSupport;
        
        /**
         *  A container holding all known metadata types.
         */
        Ptr<MetadataTypeContainer>::Ref     metadataTypes;
        
        /**
         *  The combo box for selecting the file types to search for.
         */
        ComboBoxText *                      fileTypeEntry;
        
        /**
         *  The box containing the AdvancedSearchItem widgets.
         */
        Gtk::VBox *                         searchItemsBox;
        
        
    public:
        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         */
        AdvancedSearchEntry(Ptr<GLiveSupport>::Ref  gLiveSupport)
                                                                throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~AdvancedSearchEntry(void)                              throw ()
        {
        }

        /**
         *  Add a new search condition entry item.
         */
        void
        onAddNewCondition(void)                                 throw ();

        /**
         *  Return the current state of the search fields.
         *
         *  @return a new LiveSupport::StorageClient::SearchCriteria instance,
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


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // AdvancedSearchEntry_h

