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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/GuiWindow.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/WidgetFactory.h"

#include "GuiWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/**
 *  The name of the application, shown on the task bar.
 */
const Glib::ustring     applicationTitle = "Campcaster";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
GuiWindow :: GuiWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                        Ptr<ResourceBundle>::Ref    bundle,
                        Button *                    windowOpenerButton,
                        int                         properties)
                                                                    throw ()
          : WhiteWindow(Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners(),
                        properties),
            LocalizedObject(bundle),
            windowOpenerButton(windowOpenerButton),
            gLiveSupport(gLiveSupport)
{
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window is shown.
 *----------------------------------------------------------------------------*/
void
GuiWindow :: on_show (void)                                         throw ()
{
    gLiveSupport->getWindowPosition(shared_from_this());
    
    if (windowOpenerButton) {
        windowOpenerButton->setSelected(true);
    }
    
    WhiteWindow::on_show();
}


/*------------------------------------------------------------------------------
 *  Event handler called when the the window gets hidden.
 *----------------------------------------------------------------------------*/
void
GuiWindow :: on_hide (void)                                         throw ()
{
    gLiveSupport->putWindowPosition(shared_from_this());
    
    if (windowOpenerButton) {
        windowOpenerButton->setSelected(false);
    }

    WhiteWindow::on_hide();
}


/*------------------------------------------------------------------------------
 *  Set the title of the window.
 *----------------------------------------------------------------------------*/
void
GuiWindow :: set_title (const Glib::ustring &   title)              throw ()
{
    WhiteWindow::setTitle(title, applicationTitle);
}

