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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/Button.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/Button.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The font definition used by the button.
 *----------------------------------------------------------------------------*/
const std::string Button :: fontDefinition = "Bitstream Vera 10";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
Button :: Button(const Glib::ustring          & label,
                 Ptr<ButtonImages>::Ref         buttonImages)
                                                                    throw ()
{
    set_flags(Gtk::NO_WINDOW);

    state                    = passiveState;
    stationaryState          = passiveState;
    this->buttonImages       = buttonImages;

    this->child = Gtk::manage(new Gtk::Label(label));
    this->child->modify_font(Pango::FontDescription(fontDefinition));
    this->child->set_parent(*this);
}


/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
Button :: Button(Gtk::Widget                  * child,
                 Ptr<ButtonImages>::Ref         buttonImages)
                                                                    throw ()
{
    set_flags(Gtk::NO_WINDOW);

    state                    = passiveState;
    this->buttonImages       = buttonImages;

    this->child = Gtk::manage(child);
    this->child->set_parent(*this);
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
Button :: ~Button(void)                            throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the size request event.
 *----------------------------------------------------------------------------*/
void
Button :: on_size_request(Gtk::Requisition* requisition)       throw ()
{
    *requisition = Gtk::Requisition();

    Gtk::Requisition    childRequisition = child->size_request();;

    requisition->width  = buttonImages->passiveImageLeft->get_width()
                        + childRequisition.width
                        + buttonImages->passiveImageRight->get_width();
    requisition->height = buttonImages->passiveImageCenter->get_height();
}


/*------------------------------------------------------------------------------
 *  Handle the size allocate event.
 *  We will not be given heights or widths less than we have requested,
 *  though we might get more.
 *----------------------------------------------------------------------------*/
void
Button :: on_size_allocate(Gtk::Allocation& allocation)        throw ()
{
    allocation.set_height(buttonImages->passiveImageCenter->get_height());
    set_allocation(allocation);

    if (gdkWindow) {
        gdkWindow->move_resize( allocation.get_x(), 
                                allocation.get_y(), 
                                allocation.get_width(), 
                                allocation.get_height() );
    }

    Gtk::Allocation     childAlloc;

    childAlloc.set_x(buttonImages->passiveImageLeft->get_width());
    childAlloc.set_y((allocation.get_height()
                    - buttonImages->passiveImageCenter->get_height())
                    / 2);
    childAlloc.set_width(allocation.get_width()
                       - buttonImages->passiveImageLeft->get_width()
                       - buttonImages->passiveImageRight->get_width());
    childAlloc.set_height(buttonImages->passiveImageCenter->get_height());

    child->size_allocate(childAlloc);

    Gtk::Button::on_size_allocate(allocation);
}


/*------------------------------------------------------------------------------
 *  Execute a function on all the children.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
Button :: forall_vfunc(gboolean    includeInternals,
                       GtkCallback callback,
                       gpointer    callbackData)               throw ()
{
    callback((GtkWidget*) child->gobj(), callbackData);
}


/*------------------------------------------------------------------------------
 *  Handle the add child widget event.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
Button :: on_add(Gtk::Widget* child)                           throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the remove child widget event.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
Button :: on_remove(Gtk::Widget* child)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Return what kind of widgets can be added to this container.
 *  As this widget has no children, return G_TYPE_NONE always.
 *----------------------------------------------------------------------------*/
GtkType
Button :: child_type_vfunc() const                             throw ()
{
    return G_TYPE_NONE;
}


/*------------------------------------------------------------------------------
 *  Handle the map event.
 *----------------------------------------------------------------------------*/
void
Button :: on_map()                                             throw ()
{
    Gtk::Button::on_map();
}


/*------------------------------------------------------------------------------
 *  Handle the unmap event.
 *----------------------------------------------------------------------------*/
void
Button :: on_unmap()                                           throw ()
{
    Gtk::Button::on_unmap();
}


/*------------------------------------------------------------------------------
 *  Handle the realize event.
 *----------------------------------------------------------------------------*/
void
Button :: on_realize()                                         throw ()
{
    // trick to make GTK-- allocate a window for the later get_window() call
    set_flags(Gtk::NO_WINDOW);
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
Button :: on_unrealize()                                   throw ()
{
    gdkWindow.clear();
    gc.clear();

    Gtk::Button::on_unrealize();
}


/*------------------------------------------------------------------------------
 *  Handle the expose event.
 *----------------------------------------------------------------------------*/
bool
Button :: on_expose_event(GdkEventExpose* event)           throw ()
{
    if (event->count > 0) {
        return false;
    }  

    if (gdkWindow) {
        gdkWindow->clear();

        Glib::RefPtr<Gdk::Pixbuf>   leftImage;
        Glib::RefPtr<Gdk::Pixbuf>   centerImage;
        Glib::RefPtr<Gdk::Pixbuf>   rightImage;

        switch (state) {
            case passiveState:
            default:
                leftImage   = buttonImages->passiveImageLeft;
                centerImage = buttonImages->passiveImageCenter;
                rightImage  = buttonImages->passiveImageRight;
                break;

            case rollState:
                leftImage   = buttonImages->rollImageLeft
                                ? buttonImages->rollImageLeft
                                : buttonImages->passiveImageLeft;
                centerImage = buttonImages->rollImageCenter
                                ? buttonImages->rollImageCenter
                                : buttonImages->passiveImageCenter;
                rightImage  = buttonImages->rollImageRight
                                ? buttonImages->rollImageRight
                                : buttonImages->passiveImageRight;
                break;

            case selectedState:
                leftImage   = buttonImages->selectedImageLeft
                                ? buttonImages->selectedImageLeft
                                : buttonImages->passiveImageLeft;
                centerImage = buttonImages->selectedImageCenter
                                ? buttonImages->selectedImageCenter
                                : buttonImages->passiveImageCenter;
                rightImage  = buttonImages->selectedImageRight
                                ? buttonImages->selectedImageRight
                                : buttonImages->passiveImageRight;
                break;
        }

        // draw everything vertically centered, but horizontally stretched
        // out
        int x    = 0;
        int y    = (get_height() - centerImage->get_height()) / 2;
        int maxX = get_width() - rightImage->get_width();

        // draw the left image
        leftImage->render_to_drawable(gdkWindow,
                                      get_style()->get_black_gc(),
                                      0, 0,
                                      x,
                                      y,
                                      leftImage->get_width(),
                                      leftImage->get_height(),
                                      Gdk::RGB_DITHER_NONE,
                                      0, 0);

        // draw as many center images, as necessary
        for (x = leftImage->get_width();
             x < maxX;
             x += centerImage->get_width()) {
            
            centerImage->render_to_drawable(gdkWindow,
                                            get_style()->get_black_gc(),
                                            0, 0,
                                            x,
                                            y,
                                            centerImage->get_width(),
                                            centerImage->get_height(),
                                            Gdk::RGB_DITHER_NONE,
                                            0, 0);
        }

        // draw the right image
        rightImage->render_to_drawable(gdkWindow,
                                       get_style()->get_black_gc(),
                                       0, 0,
                                       maxX,
                                       y,
                                       rightImage->get_width(),
                                       rightImage->get_height(),
                                       Gdk::RGB_DITHER_NONE,
                                       0, 0);
    }

    Gtk::Container::on_expose_event(event);

    return false;
}


/*------------------------------------------------------------------------------
 *  Handle the mouse enter event.
 *----------------------------------------------------------------------------*/
void
Button :: on_enter(void)                                   throw ()
{
    state = rollState;

    Gtk::Button::on_enter();
}


/*------------------------------------------------------------------------------
 *  Handle the mouse leave event.
 *----------------------------------------------------------------------------*/
void
Button :: on_leave(void)                                   throw ()
{
    state = stationaryState;

    Gtk::Button::on_leave();
}

