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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/OperatorComboBoxText.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_OperatorComboBoxText_h
#define LiveSupport_Widgets_OperatorComboBoxText_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/ComboBoxText.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A combo box holding all possible search operator entries.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class OperatorComboBoxText : public ComboBoxText,
                             public LocalizedObject
{
    public:
        /**
         *  Constructor.
         *
         */
        OperatorComboBoxText(Glib::RefPtr<Gdk::Pixbuf>  leftImage, 
                             Glib::RefPtr<Gdk::Pixbuf>  centerImage, 
                             Glib::RefPtr<Gdk::Pixbuf>  rightImage,
                             Ptr<ResourceBundle>::Ref   bundle)
                                                                    throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~OperatorComboBoxText(void)                                 throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_OperatorComboBoxText_h

