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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/BlueBin.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_BlueBin_h
#define LiveSupport_Widgets_BlueBin_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/bin.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A container holding exactly one child, habing a light blue border to it.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class BlueBin : public Gtk::Bin
{
    private:
        /**
         *  The Gdk::Window object, used to draw inside this button.
         */
        Glib::RefPtr<Gdk::Window>       gdkWindow;

        /**
         *   The Graphics Context, used to draw.
         */
        Glib::RefPtr<Gdk::GC>           gc;

        /**
         *  The widget contained inside this container.
         */
        Gtk::Widget                   * child;

        /**
         *  The background color of the widget.
         */
        Gdk::Color                      bgColor;

        /**
         *  The top left image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       topLeftImage;

        /**
         *  The left image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       leftImage;

        /**
         *  The top image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       topImage;

        /**
         *  The top right image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       topRightImage;

        /**
         *  The right image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       rightImage;

        /**
         *  The bottom left image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       bottomLeftImage;

        /**
         *  The bottom image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       bottomImage;

        /**
         *  The bottom right image of the border.
         */
        Glib::RefPtr<Gdk::Pixbuf>       bottomRightImage;

        /**
         *  Default constructor.
         */
        BlueBin(void)                                   throw ()
        {
        }


    protected:
        /**
         *  Handle the size request event.
         *
         *  @param requisition the size request, also being the ouptut
         *         parameter.
         */
        virtual void
        on_size_request(Gtk::Requisition* requisition)
                                                                throw ();

        /**
         *  Handle the size allocate event.
         *
         *  @param allocation the allocated size.
         */
        virtual void
        on_size_allocate(Gtk::Allocation& allocation)
                                                                throw ();

        /**
         *  Handle the map event.
         */
        virtual void
        on_map()                                            throw ();

        /**
         *  Handle the unmap event.
         */
        virtual void
        on_unmap()                                          throw ();

        /**
         *  Handle the realize event.
         */
        virtual void
        on_realize()                                        throw ();

        /**
         *  Handle the unrealize event.
         */
        virtual void
        on_unrealize()                                      throw ();

        /**
         *  Handle the expose event.
         *
         *  @param event the actual expose event recieved.
         *  @return true if something was drawn (?)
         */
        virtual bool
        on_expose_event(GdkEventExpose* event)              throw ();

        /**
         *  Execute a function on all children of this container.
         *
         *  @param includeInternals true if the callback function should
         *         also be called on the internals, false otherwise.
         *  @param callback the callback function to execute on the children.
         *  @param callbackData the data passed to the callback function.
         */
        virtual void
        forall_vfunc(gboolean      includeInternals,
                     GtkCallback   callback,
                     gpointer      callbackData)
                                                            throw ();

        /**
         *  Handle the add event.
         *
         *  @param child the child being added to the widget.
         */
        virtual void
        on_add(Gtk::Widget* child)                          throw ();

        /**
         *  Handle the remove event.
         *
         *  @param child the child to remove from the widget.
         */
        virtual void
        on_remove(Gtk::Widget* child)                       throw ();

        /**
         *  Tell what kind of children this container accepts.
         *
         *  @return the type of children this container accepts.
         */
        virtual GtkType
        child_type_vfunc() const                            throw ();


    public:
        /**
         *  Constructor, with only one state.
         *
         *  @param backgroundColor the RGB value for the background color.
         *  @param topLeftImage the top left image of the border
         *  @param leftImage the left image of the border
         *  @param topImage the top image of the border
         *  @param topRightImage the top right image of the border
         *  @param rightImage the right image of the border
         *  @param bottomLeftImage the bottom left image of the border
         *  @param bottomImage the bottom image of the border
         *  @param bottomRightImage the bottom right image of the border
         */
        BlueBin(unsigned int                backgroundColor,
                Glib::RefPtr<Gdk::Pixbuf>   topLeftImage,
                Glib::RefPtr<Gdk::Pixbuf>   leftImage,
                Glib::RefPtr<Gdk::Pixbuf>   topImage,
                Glib::RefPtr<Gdk::Pixbuf>   topRightImage,
                Glib::RefPtr<Gdk::Pixbuf>   rightImage,
                Glib::RefPtr<Gdk::Pixbuf>   bottomLeftImage,
                Glib::RefPtr<Gdk::Pixbuf>   bottomImage,
                Glib::RefPtr<Gdk::Pixbuf>   bottomRightImage)
                                                            throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~BlueBin(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_BlueBin_h

