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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/WhiteWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/WhiteWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
WhiteWindow :: WhiteWindow(unsigned int                 backgroundColor,
                           Ptr<CornerImages>::Ref       cornerImages)
                                                                    throw ()
                : Gtk::Window(Gtk::WINDOW_TOPLEVEL)
{
    set_decorated(false);

    Ptr<WidgetFactory>::Ref   wf = WidgetFactory::getInstance();

    layout.reset(new Gtk::Table());

    // create the background color, as it is needed by the event box
    Gdk::Color      bgColor = Gdk::Color();
    unsigned int    red   = (backgroundColor & 0xff0000) >> 8;
    unsigned int    green = (backgroundColor & 0x00ff00);
    unsigned int    blue  = (backgroundColor & 0x0000ff) << 8;
    bgColor.set_rgb(red, green, blue);
    Glib::RefPtr<Gdk::Colormap> colormap = get_default_colormap();
    colormap->alloc_color(bgColor);

    // set the window title
    title.reset(new Gtk::Label("Window Title"));
    titleAlignment.reset(new Gtk::Alignment(Gtk::ALIGN_LEFT,
                                            Gtk::ALIGN_CENTER,
                                            0, 0));
    titleEventBox.reset(new Gtk::EventBox());
    titleEventBox->set_visible_window();
    titleEventBox->modify_bg(Gtk::STATE_NORMAL, bgColor);
    titleAlignment->add(*title);
    titleEventBox->add(*titleAlignment);
    layout->attach(*titleEventBox, 0, 1, 0, 1, Gtk::FILL, Gtk::SHRINK);

    // create the close button
    closeButton = wf->createButton(WidgetFactory::deleteButton);
    closeButtonAlignment.reset(new Gtk::Alignment(Gtk::ALIGN_RIGHT,
                                                  Gtk::ALIGN_CENTER,
                                                  0, 0));
    closeButtonAlignment->add(*closeButton);
    layout->attach(*closeButtonAlignment, 1, 2, 0, 1, Gtk::FILL, Gtk::SHRINK);

    // add the child container
    childContainer.reset(new Gtk::Alignment(Gtk::ALIGN_CENTER));
    layout->attach(*childContainer, 0, 2, 1, 2);

    // add the corners
    blueBin.reset(new BlueBin(backgroundColor, cornerImages));
    blueBin->add(*layout);
    Gtk::Window::add(*blueBin);

    // show all
    show_all();

    // register signal handlers
    titleEventBox->add_events(Gdk::BUTTON_PRESS_MASK);
    titleEventBox->signal_button_press_event().connect(sigc::mem_fun(*this,
                                                &WhiteWindow::onTitleClicked));

    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                          &WhiteWindow::onCloseButtonClicked));

}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
WhiteWindow :: ~WhiteWindow(void)                            throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the size request event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_size_request(Gtk::Requisition* requisition)       throw ()
{
    Gtk::Window::on_size_request(requisition);
}


/*------------------------------------------------------------------------------
 *  Handle the size allocate event.
 *  We will not be given heights or widths less than we have requested,
 *  though we might get more.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_size_allocate(Gtk::Allocation& allocation)        throw ()
{
    Gtk::Window::on_size_allocate(allocation);
}


/*------------------------------------------------------------------------------
 *  Execute a function on all the children.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: forall_vfunc(gboolean    includeInternals,
                            GtkCallback callback,
                            gpointer    callbackData)               throw ()
{
    Gtk::Window::forall_vfunc(includeInternals, callback, callbackData);
}


/*------------------------------------------------------------------------------
 *  Handle the add child widget event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_add(Gtk::Widget* child)                           throw ()
{
    if (child == blueBin.get()) {
        Gtk::Window::on_add(child);
    } else {
        childContainer->add(*child);
    }
}


/*------------------------------------------------------------------------------
 *  Handle the remove child widget event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_remove(Gtk::Widget* child)                        throw ()
{
    if (child == blueBin.get()) {
        Gtk::Window::on_remove(child);
    } else {
        childContainer->remove();
    }
}


/*------------------------------------------------------------------------------
 *  Return what kind of widgets can be added to this container.
 *----------------------------------------------------------------------------*/
GtkType
WhiteWindow :: child_type_vfunc() const                             throw ()
{
    return Gtk::Window::child_type_vfunc();
}


/*------------------------------------------------------------------------------
 *  Handle the map event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_map()                                             throw ()
{
    Gtk::Window::on_map();
}


/*------------------------------------------------------------------------------
 *  Handle the unmap event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_unmap()                                           throw ()
{
    Gtk::Window::on_unmap();
}


/*------------------------------------------------------------------------------
 *  Handle the realize event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_realize()                                         throw ()
{
    Gtk::Window::on_realize();
}


/*------------------------------------------------------------------------------
 *  Handle the unrealize event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_unrealize()                                   throw ()
{
    Gtk::Window::on_unrealize();
}


/*------------------------------------------------------------------------------
 *  Handle the expose event.
 *----------------------------------------------------------------------------*/
bool
WhiteWindow :: on_expose_event(GdkEventExpose* event)           throw ()
{
    return Gtk::Window::on_expose_event(event);
}


/*------------------------------------------------------------------------------
 *  The event of the title being clicked
 *----------------------------------------------------------------------------*/
bool
WhiteWindow :: onTitleClicked(GdkEventButton     * event)          throw ()
{
    if (event->button == 1) {
        begin_move_drag(event->button,
                        (int) event->x_root,
                        (int) event->y_root,
                        event->time);
    }

    return false;
}


/*------------------------------------------------------------------------------
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: onCloseButtonClicked (void)                  throw ()
{
    hide();
}


