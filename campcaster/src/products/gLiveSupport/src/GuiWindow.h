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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/GuiWindow.h $

------------------------------------------------------------------------------*/
#ifndef GuiWindow_h
#define GuiWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <unicode/resbund.h>
#include <glibmm.h>

#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/WhiteWindow.h"
#include "LiveSupport/Widgets/WidgetConstants.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The common ancestor of all openable and closable windows in the GUI.
 *
 *  @author $Author: fgerlits $
 *  @version $Revision$
 */
class GuiWindow : public WhiteWindow,
                  public LocalizedObject
{
    private:
        /**
         *  The button which was pressed to open this window.
         */
        Gtk::ToggleButton *         windowOpenerButton;

    protected:
        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  Event handler called when the the window is shown.
         *
         *  This overrides WhiteWindow::on_show(), inherited from Gtk::Widget.
         *  It reads and restores the saved window position, if any.
         *
         *  @see LiveSupport::GLiveSupport::GLiveSupport::getWindowPosition()
         */
        virtual void
        on_show(void)                                               throw ();

        /**
         *  Event handler called when the the window gets hidden.
         *
         *  This overrides WhiteWindow::on_hide(), inherited from Gtk::Widget.
         *  It stores the window position, and 'pops out' the window opener 
         *  button.
         *
         *  @see LiveSupport::GLiveSupport::GLiveSupport::putWindowPosition()
         */
        virtual void
        on_hide(void)                                               throw ();

    public:
        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the GLiveSupport application object.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         *  @param  windowOpenerButton  the button which was pressed to open
         *                              this window (optional).
         *  @param  properties      see WhiteWindow::WindowProperties
         *                          (optional).
         */
        GuiWindow(Ptr<GLiveSupport>::Ref        gLiveSupport,
                  Ptr<ResourceBundle>::Ref      bundle,
                  Gtk::ToggleButton *           windowOpenerButton = 0,
                  int                           properties = 0)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~GuiWindow(void)                                            throw ()
        {
        }

        /**
         *  Set the title of the window.
         *
         *  Overrides WhiteWindow::set_title() (inherited from Gtk::Window).
         *  Adds the application's title to the title of the window shown
         *  on the task bar.
         *
         *  @param  title   the title of the window.
         */
        virtual void
        set_title(const Glib::ustring &     title)                  throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // GuiWindow_h

