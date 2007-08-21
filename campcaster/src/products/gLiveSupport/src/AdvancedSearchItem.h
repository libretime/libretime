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
#ifndef AdvancedSearchItem_h
#define AdvancedSearchItem_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <vector>
#include <utility>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/NumericTools.h"
#include "LiveSupport/Core/MetadataTypeContainer.h"
#include "LiveSupport/Core/SearchCriteria.h"
#include "LiveSupport/Widgets/MetadataComboBoxText.h"
#include "LiveSupport/Widgets/OperatorComboBoxText.h"

#include "GuiComponent.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A single search input field.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class AdvancedSearchItem : public  GuiComponent,
                           private NumericTools
{
    private:

        /**
         *  The enclosing box.
         */
        Gtk::Box *                  enclosingBox;
        
        /**
         *  The metadata field.
         */
        MetadataComboBoxText *      metadataEntry;

        /**
         *  The operator field.
         */
        OperatorComboBoxText *      operatorEntry;

        /**
         *  The "search for this value" field.
         */
        Gtk::Entry *                valueEntry;
        
        /**
         *  The "add new search item" button.
         */
        Gtk::Button *               plusButton;
        
        /**
         *  The "remove this item" button.
         */
        Gtk::Button *               closeButton;
        
        /**
         *  A signal object emitted when the plus button is pressed.
         */
        sigc::signal<void>          signalAddNewObject;

        /**
         *  Event handler for the Plus button getting clicked.
         */
        void
        onPlusButtonClicked()                                   throw ()
        {
            signalAddNew().emit();
        }

        /**
         *  Event handler for the Close button getting clicked.
         */
        void
        onCloseButtonClicked()                                  throw ()
        {
            hide();
        }


    public:

        /**
         *  Constructor.
         *
         *  @param parent         the GuiObject which contains this one.
         *  @param index          the position of this item in the list of
         *                        advanced search items.
         *  @param metadataTypes  container holding all known metadata types
         */
        AdvancedSearchItem(GuiObject *                        parent,
                           int                                index,
                           Ptr<MetadataTypeContainer>::Ref    metadataTypes)
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
         *  @return a new LiveSupport::StorageClient::SearchCriteria instance,
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
         *  @return a signal emitted when the Plus button is clicked.
         */
        sigc::signal<void>
        signalAddNew(void)                                      throw ()
        {
            return signalAddNewObject;
        }

        /**
         *  Is the widget visible?
         *
         *  return true if visible, false if not.
         */
        bool
        is_visible(void)                                        throw ()
        {
            return enclosingBox->is_visible();
        }

        /**
         *  Show the widget.
         */
        void
        show(void)                                              throw ()
        {
            if (!enclosingBox->is_visible()) {
                enclosingBox->show();
            }
        }

        /**
         *  Hide the widget.
         */
        void
        hide(void)                                              throw ()
        {
            if (enclosingBox->is_visible()) {
                enclosingBox->hide();
            }
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // AdvancedSearchItem_h

