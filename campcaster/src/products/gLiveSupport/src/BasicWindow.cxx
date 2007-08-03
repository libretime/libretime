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
    Version  : $Revision: 3105 $
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/BasicWindow.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "GLiveSupport.h"
#include "BasicWindow.h"


using namespace LiveSupport::Core;
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
BasicWindow :: BasicWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                            Ptr<ResourceBundle>::Ref    bundle,
                            Gtk::ToggleButton *         windowOpenerButton,
                            const Glib::ustring &       gladeFileName)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport),
            windowOpenerButton(windowOpenerButton)
{
    glade = Gnome::Glade::Xml::create(gladeFileName);

    glade->get_widget("mainWindow1", mainWindow);
    setTitle(getResourceUstring("windowTitle"));
    
    mainWindow->signal_delete_event().connect(sigc::mem_fun(*this,
                                                &BasicWindow::onDeleteEvent));
}


/*------------------------------------------------------------------------------
 *  Restore the window position and show the window.
 *----------------------------------------------------------------------------*/
void
BasicWindow :: show (void)                                          throw ()
{
    preShow();
    mainWindow->show();
}


/*------------------------------------------------------------------------------
 *  Stuff to do before showing the window.
 *----------------------------------------------------------------------------*/
void
BasicWindow :: preShow (void)                                       throw ()
{
    gLiveSupport->getWindowPosition(this);
    if (windowOpenerButton) {
        windowOpenerButton->set_active(true);
    }
}


/*------------------------------------------------------------------------------
 *  Save the window position and hide the window.
 *----------------------------------------------------------------------------*/
void
BasicWindow :: hide (void)                                          throw ()
{
    preHide();
    mainWindow->hide();
}


/*------------------------------------------------------------------------------
 *  Signal handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
bool
BasicWindow :: onDeleteEvent (GdkEventAny *     event)              throw ()
{
    preHide();
    return false;
}


/*------------------------------------------------------------------------------
 *  Stuff to do before hiding the window.
 *----------------------------------------------------------------------------*/
void
BasicWindow :: preHide (void)                                       throw ()
{
    gLiveSupport->putWindowPosition(this);
    if (windowOpenerButton) {
        windowOpenerButton->set_active(false);
    }
}


/*------------------------------------------------------------------------------
 *  Set the title of the window.
 *----------------------------------------------------------------------------*/
void
BasicWindow :: setTitle (Ptr<const Glib::ustring>::Ref      title)
                                                                    throw ()
{
    windowTitle = title;

    Glib::ustring   fullTitle = applicationTitle;
    fullTitle += " - ";
    fullTitle += *title;
    mainWindow->set_title(fullTitle);
}

