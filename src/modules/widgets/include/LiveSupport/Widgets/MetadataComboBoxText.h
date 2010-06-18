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
#ifndef LiveSupport_Widgets_MetadataComboBoxText_h
#define LiveSupport_Widgets_MetadataComboBoxText_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <libglademm.h>

#include "LiveSupport/Core/MetadataTypeContainer.h"
#include "LiveSupport/Widgets/ComboBoxText.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A combo box holding all possible metadata type entries.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class MetadataComboBoxText : public ComboBoxText
{
    private:

        /**
         *  The list of metadata types.
         */
        Ptr<const MetadataTypeContainer>::Ref   metadataTypes;


    public:

        /**
         *  Constructor to be used with Glade::Xml::get_widget_derived().
         *
         *  @param baseClass    widget of the parent class, created by Glade.
         *  @param glade        the Glade object.
         */
        MetadataComboBoxText(
                    GtkComboBox *                              baseClass,
                    const Glib::RefPtr<Gnome::Glade::Xml> &    glade)
                                                                    throw ();
        /**
         *  A virtual destructor.
         */
        virtual
        ~MetadataComboBoxText(void)                                 throw ();

        /**
         *  Set up the contents of the combo box.
         *
         *  @param  metadataTypes   contains the metadata keys and values.
         */
        void
        setContents(Ptr<const MetadataTypeContainer>::Ref   metadataTypes)
                                                                    throw ();

        /**
         *  Get the DC name for the currently selected metadata type.
         *  This is either a standard Dublin Core metadata type like
         *  "dc:title" or "dc:creator", or one of the Campcaster
         *  (née LiveSupport) extensions like "ls:year" or "ls:bpm".
         *  See the Studio config files for a list of all metadata types.
         *
         *  @return the DC name for the current selection.
         */
        Ptr<const Glib::ustring>::Ref
        getActiveKey(void)
                                                                    throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_MetadataComboBoxText_h

