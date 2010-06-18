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
#ifndef LiveSupport_Widgets_ComboBoxText_h
#define LiveSupport_Widgets_ComboBoxText_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/comboboxtext.h>
#include <libglademm.h>

namespace LiveSupport {
namespace Widgets {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A combo box holding text entries.
 *  This just adds another constructor to its parent class, so that it can
 *  be used with Libglade.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class ComboBoxText : public Gtk::ComboBoxText
{
    public:

        /**
         *  Constructor to be used with Glade::Xml::get_widget_derived().
         *
         *  @param baseClass    widget of the parent class, created by Glade.
         *  @param glade        the Glade object.
         */
        ComboBoxText(GtkComboBox *                              baseClass,
                     const Glib::RefPtr<Gnome::Glade::Xml> &    glade)
                                                                    throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~ComboBoxText(void)                                         throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ComboBoxText_h

