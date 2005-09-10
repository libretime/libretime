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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

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
                                Glib::RefPtr<Gdk::Pixbuf>   leftImage, 
                                Glib::RefPtr<Gdk::Pixbuf>   centerImage, 
                                Glib::RefPtr<Gdk::Pixbuf>   rightImage,
                                Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
        : ComboBoxText(leftImage, centerImage, rightImage),
          LocalizedObject(bundle)
{
    Ptr<Glib::ustring>::Ref   partialOperator(new Glib::ustring("partial"));
    appendPair(getResourceUstring("partialOperatorDisplay"), partialOperator);

    Ptr<Glib::ustring>::Ref   prefixOperator(new Glib::ustring("prefix"));
    appendPair(getResourceUstring("prefixOperatorDisplay"), prefixOperator);

    Ptr<Glib::ustring>::Ref   equalsOperator(new Glib::ustring("="));
    appendPair(getResourceUstring("=OperatorDisplay"), equalsOperator);

    Ptr<Glib::ustring>::Ref   lessOrEqualOperator(new Glib::ustring("<="));
    appendPair(getResourceUstring("<=OperatorDisplay"), lessOrEqualOperator);

    Ptr<Glib::ustring>::Ref   greaterOrEqualOperator(new Glib::ustring(">="));
    appendPair(getResourceUstring(">=OperatorDisplay"), greaterOrEqualOperator);

    set_active(0);  // select the first item
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
OperatorComboBoxText :: ~OperatorComboBoxText(void)                            throw ()
{
}

