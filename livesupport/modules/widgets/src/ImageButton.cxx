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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/ImageButton.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/ImageButton.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ImageButton :: ImageButton(Glib::RefPtr<Gdk::Pixbuf>   image)
                                                                    throw ()
{
    set_flags(Gtk::NO_WINDOW);

    state              = passiveState;
    this->passiveImage = image;
    this->rollImage.clear();
}


/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ImageButton :: ImageButton(Glib::RefPtr<Gdk::Pixbuf>   passiveImage,
                           Glib::RefPtr<Gdk::Pixbuf>   rollImage)
                                                                    throw ()
{
    set_flags(Gtk::NO_WINDOW);

    state              = passiveState;
    this->passiveImage = passiveImage;
    this->rollImage    = rollImage;
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
ImageButton :: ~ImageButton(void)                            throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the size request event.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_size_request(Gtk::Requisition* requisition)       throw ()
{
    *requisition = Gtk::Requisition();

    requisition->width  = passiveImage->get_width();
    requisition->height = passiveImage->get_height();
}


/*------------------------------------------------------------------------------
 *  Handle the size allocate event.
 *  We will not be given heights or widths less than we have requested,
 *  though we might get more.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_size_allocate(Gtk::Allocation& allocation)        throw ()
{
    allocation.set_width(passiveImage->get_width());
    allocation.set_height(passiveImage->get_height());
    set_allocation(allocation);

    if (gdkWindow) {
        gdkWindow->move_resize( allocation.get_x(), 
                                allocation.get_y(), 
                                allocation.get_width(), 
                                allocation.get_height() );
    }

    Gtk::Button::on_size_allocate(allocation);
}


/*------------------------------------------------------------------------------
 *  Execute a function on all the children.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
ImageButton :: forall_vfunc(gboolean    includeInternals,
                            GtkCallback callback,
                            gpointer    callbackData)               throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the add child widget event.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_add(Gtk::Widget* child)                           throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the remove child widget event.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_remove(Gtk::Widget* child)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Return what kind of widgets can be added to this container.
 *  As this widget has no children, return G_TYPE_NONE always.
 *----------------------------------------------------------------------------*/
GtkType
ImageButton :: child_type_vfunc() const                             throw ()
{
    return G_TYPE_NONE;
}


/*------------------------------------------------------------------------------
 *  Handle the map event.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_map()                                             throw ()
{
    Gtk::Button::on_map();
}


/*------------------------------------------------------------------------------
 *  Handle the unmap event.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_unmap()                                           throw ()
{
    Gtk::Button::on_unmap();
}


/*------------------------------------------------------------------------------
 *  Handle the realize event.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_realize()                                         throw ()
{
    Gtk::Button::on_realize();

    if (!gdkWindow) {
        // create the Gdk::Window, if it didn't exist before

        GdkWindowAttr       attributes;
        memset(&attributes, 0, sizeof(attributes));

        Gtk::Allocation     allocation = get_allocation();

        // set initial position and size of the Gdk::Window
        attributes.x      = allocation.get_x();
        attributes.y      = allocation.get_y();
        attributes.width  = allocation.get_width();
        attributes.height = allocation.get_height();

        attributes.event_mask  = get_events () | Gdk::EXPOSURE_MASK; 
        attributes.window_type = GDK_WINDOW_CHILD;
        attributes.wclass      = GDK_INPUT_OUTPUT;


        gdkWindow = Gdk::Window::create(get_window(),
                                        &attributes,
                                        GDK_WA_X | GDK_WA_Y);
        unset_flags(Gtk::NO_WINDOW);
        set_window(gdkWindow);

        Gdk::Color  bgColor;
        bgColor.set_rgb(0xffff, 0xffff, 0xffff);
        Glib::RefPtr<Gdk::Colormap> colormap = get_default_colormap();
        colormap->alloc_color(bgColor);
        modify_bg(Gtk::STATE_NORMAL, bgColor);

        // make the widget receive expose events
        gdkWindow->set_user_data(gobj());
        
        // allocate a GC for use in on_expose_event()
        gc = Gdk::GC::create(gdkWindow);
    }
}


/*------------------------------------------------------------------------------
 *  Handle the unrealize event.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_unrealize()                                   throw ()
{
    gdkWindow.clear();
    gc.clear();

    Gtk::Button::on_unrealize();
}


/*------------------------------------------------------------------------------
 *  Handle the expose event.
 *----------------------------------------------------------------------------*/
bool
ImageButton :: on_expose_event(GdkEventExpose* event)           throw ()
{
    if (event->count > 0) {
        return false;
    }  

    if (gdkWindow) {
        gdkWindow->clear();

        Glib::RefPtr<Gdk::Pixbuf>   image;

        switch (state) {
            case passiveState:
            default:
                image = passiveImage;
                break;
                
            case rollState:
                image = rollImage ? rollImage : passiveImage;
                break;
        }

        // just draw the button image at the centre of the available space
        int x = (get_width() - image->get_width()) / 2;
        int y = (get_height() - image->get_height()) / 2;

        image->render_to_drawable(gdkWindow,
                                  get_style()->get_white_gc(),
                                  0, 0,
                                  x,
                                  y,
                                  image->get_width(),
                                  image->get_height(),
                                  Gdk::RGB_DITHER_NONE,
                                  0, 0);
    }

    return false;
}


/*------------------------------------------------------------------------------
 *  Handle the mouse enter event.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_enter(void)                                   throw ()
{
    state = rollState;

    Gtk::Button::on_enter();
}


/*------------------------------------------------------------------------------
 *  Handle the mouse leave event.
 *----------------------------------------------------------------------------*/
void
ImageButton :: on_leave(void)                                   throw ()
{
    state = passiveState;

    Gtk::Button::on_leave();
}

