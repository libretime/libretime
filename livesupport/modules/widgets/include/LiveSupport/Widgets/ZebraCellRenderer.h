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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/ZebraCellRenderer.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_ZebraCellRenderer_h
#define LiveSupport_Widgets_ZebraCellRenderer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "gtkmm/cellrenderertext.h"


namespace LiveSupport {
namespace Widgets {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A custom cell renderer for blue-gray striped TreeView's.
 *  This is not used anywhere at the moment, but it's left in here because 
 *  we will probably need (something like) this later.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class ZebraCellRenderer : public Gtk::CellRendererText
{
    public:
        /**
         *  Default constructor.
         */
        ZebraCellRenderer()                                     throw ();

        /**
         *  A virtual destructor.
         */
        virtual ~ZebraCellRenderer()                            throw ();

    protected:
        /**
         *  Calculate the size of the cell.
         */
        virtual void get_size_vfunc(Gtk::Widget& widget,
                              const Gdk::Rectangle* cell_area,
                              int* x_offset, int* y_offset,
                              int* width,    int* height) const
                                                                throw ();

        /**
         *  Draw the cell.
         */
        virtual void render_vfunc(const Glib::RefPtr<Gdk::Drawable>& window,
                            Gtk::Widget& widget,
                            const Gdk::Rectangle& background_area,
                            const Gdk::Rectangle& cell_area,
                            const Gdk::Rectangle& expose_area,
                            Gtk::CellRendererState flags)
                                                                throw ();

        /**
         *  The user clicked on the cell.
         */
        virtual bool activate_vfunc(GdkEvent* event,
                              Gtk::Widget& widget,
                              const Glib::ustring& path,
                              const Gdk::Rectangle& background_area,
                              const Gdk::Rectangle& cell_area,
                              Gtk::CellRendererState flags)
                                                                throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ZebraCellRenderer_h

