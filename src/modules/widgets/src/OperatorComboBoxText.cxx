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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <libglademm.h>

#include "LiveSupport/Widgets/OperatorComboBoxText.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
OperatorComboBoxText :: OperatorComboBoxText(
                        GtkComboBox *                             baseClass,
                        const Glib::RefPtr<Gnome::Glade::Xml> &   glade)
                                                                    throw ()
          : ComboBoxText(baseClass, glade)
{
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
OperatorComboBoxText :: ~OperatorComboBoxText(void)                 throw ()
{
}


/*------------------------------------------------------------------------------
 *  Set up the contents of the combo box.
 *----------------------------------------------------------------------------*/
void
OperatorComboBoxText :: setContents(Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
{
    setBundle(bundle);
    append_text(*getResourceUstring("partialOperatorDisplay"));
    append_text(*getResourceUstring("prefixOperatorDisplay"));
    append_text(*getResourceUstring("=OperatorDisplay"));
    append_text(*getResourceUstring("<=OperatorDisplay"));
    append_text(*getResourceUstring(">=OperatorDisplay"));
    set_active(0);
}


/*------------------------------------------------------------------------------
 *  Set up the contents of the combo box.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
OperatorComboBoxText :: getActiveKey(void)                          throw ()
{
    Ptr<Glib::ustring>::Ref   selectedOperator(new Glib::ustring);
    int                       selectedRow = get_active_row_number();
    
    switch (selectedRow) {
        case 0:         selectedOperator->assign("partial");
                        break;
        
        case 1:         selectedOperator->assign("prefix");
                        break;
        
        case 2:         selectedOperator->assign("=");
                        break;
        
        case 3:         selectedOperator->assign("<=");
                        break;
        
        case 4:         selectedOperator->assign(">=");
                        break;
        
        default:        std::cerr << "impossible value '"
                                  << selectedRow
                                  << "' in OperatorComboBoxText::getActiveKey"
                                  << std::endl;
                        std::exit(1);
                        break;
    }
    
    return selectedOperator;
}

