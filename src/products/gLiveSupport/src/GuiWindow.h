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
#ifndef GuiWindow_h
#define GuiWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "GuiObject.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The common ancestor of all standalone windows in the GUI.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class GuiWindow : public GuiObject
{
    private:

        /**
         *  The title of the window.
         */
        Ptr<const Glib::ustring>::Ref       windowTitle;

        /**
         *  Stuff to do before showing the window.
         */
        void
        preShow(void)                                               throw ();

        /**
         *  Stuff to do before hiding the window.
         */
        void
        preHide(void)                                               throw ();


    protected:

        /**
         *  The button which was used to open this window.
         */
        Gtk::ToggleButton *                 windowOpenerButton;

        /**
         *  The window itself.
         */
        Gtk::Window *                       mainWindow;

        /**
         *  Signal handler for the close button getting clicked.
         */
        virtual bool
        onDeleteEvent(GdkEventAny *     event)                      throw ();

        /**
         *  Protected constructor.
         *
         *  @param  bundleName      the name of the sub-bundle for this object.
         *  @param  gladeFileName   the name of the Glade file for this window.
         *  @param  windowOpenerButton  the button which was pressed to open
         *                              this window (optional).
         */
        GuiWindow(const Glib::ustring &         bundleName,
                  const Glib::ustring &         gladeFileName,
                  Gtk::ToggleButton *           windowOpenerButton = 0)
                                                                    throw ();


    public:

        /**
         *  Virtual destructor.
         */
        virtual
        ~GuiWindow(void)                                          throw ()
        {
        }

        /**
         *  Restore the window position and show the window.
         */
        virtual void
        show(void)                                                  throw ();

        /**
         *  Save the window position and hide the window.
         */
        virtual void
        hide(void)                                                  throw ();

        /**
         *  Set the title of the window.
         *
         *  Adds the application's title to the title of the window shown
         *  on the task bar.
         *
         *  @param  title   the title of the window.
         */
        virtual void
        setTitle(Ptr<const Glib::ustring>::Ref  title)              throw ();

        /**
         *  A replacement for Gtk::Window::get_name().
         *
         *  @return the (localized) title of the window.
         */
        virtual Ptr<const Glib::ustring>::Ref
        getTitle(void) const                                        throw ()
        {
            return windowTitle;
        }

        /**
         *  Get the underlying Gtk::Window.
         */
        virtual Gtk::Window *
        getWindow(void)                                             throw ()
        {
            return mainWindow;
        }

        /**
         *  Get the underlying Gtk::Window.
         */
        virtual const Gtk::Window *
        getWindow(void) const                                       throw ()
        {
            return mainWindow;
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // GuiWindow_h

