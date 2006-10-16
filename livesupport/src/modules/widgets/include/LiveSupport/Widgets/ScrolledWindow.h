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
#ifndef LiveSupport_Widgets_ScrolledWindow_h
#define LiveSupport_Widgets_ScrolledWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/scrolledwindow.h>

#include "LiveSupport/Widgets/Colors.h"


namespace LiveSupport {
namespace Widgets {
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A subclass of Gtk::ScrolledWindow.  The only difference is that the
 *  background color is hard-coded to be LiveSupport::Widgets::Colors::White.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class ScrolledWindow : public Gtk::ScrolledWindow
{
    private:
        /**
         *  The shadow type of the child widget.
         */
        Gtk::ShadowType     shadowType;

        /**
         *  Whether the shadow type has been manually set on the child widget.
         */
        bool                useShadowType;


    protected:
        /**
         *  Handle the realize event.
         */
        virtual void
        on_realize()                                                throw ();


    public:
        /**
         *  Constructor.
         */
        ScrolledWindow()                                            throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~ScrolledWindow(void)                                       throw ();

        /**
         *  Set the shadow_type of the child widget.
         *  Must be called before the window is realized.
         */
        void
        setShadowType(Gtk::ShadowType   st)                         throw ()
        {
            shadowType      = st;
            useShadowType   = true;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ScrolledWindow_h

