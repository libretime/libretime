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
#ifndef ExportFormatRadioButtons_h
#define ExportFormatRadioButtons_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include "LiveSupport/StorageClient/StorageClientInterface.h"

#include "GuiComponent.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A specialized group of radio buttons, holding the 
 *  StorageClientInterface::ExportFormatType options.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class ExportFormatRadioButtons : public GuiComponent
{
    private:

        /**
         *  The radio button for the internal Campcaster format.
         */
        Gtk::RadioButton *          internalFormatRadioButton;

        /**
         *  The radio button for the SMIL format.
         */
        Gtk::RadioButton *          smilFormatRadioButton;


    public:

        /**
         *  Constructor.
         *
         *  @param  parent  the GuiObject which contains this one.
         */
        ExportFormatRadioButtons(GuiObject *        parent)
                                                                    throw ();
        
        /**
         *  Return the format which is currently selected.
         */
        StorageClientInterface::ExportFormatType
        getFormat(void)                                             throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // ExportFormatRadioButtons_h

