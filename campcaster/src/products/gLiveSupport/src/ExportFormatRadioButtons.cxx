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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/ExportFormatRadioButtons.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "ExportFormatRadioButtons.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ExportFormatRadioButtons :: ExportFormatRadioButtons(
                                    Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
          : RadioButtons(),
            LocalizedObject(bundle)
{
    try {
        add(getResourceUstring("internalFormatName"));
        add(getResourceUstring("smilFormatName"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
}


/*------------------------------------------------------------------------------
 *  Return the format which is currently selected.
 *----------------------------------------------------------------------------*/
StorageClientInterface::ExportFormatType
ExportFormatRadioButtons :: getFormat(void)                         throw ()
{
    int button  = getActiveButton();
    return StorageClientInterface::ExportFormatType(button);
}

