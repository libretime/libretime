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

namespace {

/*------------------------------------------------------------------------------
 *  The font used for the window title.
 *----------------------------------------------------------------------------*/
const Glib::ustring     windowTitleFont = "Bitstream Vera Sans Bold 8";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
WhiteWindow :: WhiteWindow(Colors::ColorName            backgroundColor,
                           Ptr<CornerImages>::Ref       cornerImages,
                           int                          properties)
                                                                    throw ()
                : Gtk::Window(Gtk::WINDOW_TOPLEVEL),
                  isMaximized(false)
{
    Ptr<WidgetFactory>::Ref   wf = WidgetFactory::getInstance();

    set_decorated(false);
    defaultWidth  = -1;
    defaultHeight = -1;
    set_resizable(!(properties & isNotResizable));
    set_modal(properties & isModal);

    layout = Gtk::manage(new Gtk::Table());

    // create the background color, as it is needed by the event box
    Gdk::Color      bgColor = Colors::getColor(backgroundColor);

    // create the title
    Gtk::Box *      titleBox = Gtk::manage(new Gtk::VBox());
    titleLabel = 0;
    
    if (!(properties & hasNoTitle)) {
        Gtk::Image *    titleImage  = Gtk::manage(wf->createImage(
                                        WidgetConstants::windowTitleLogoImage));
        
        titleLabel      = Gtk::manage(new Gtk::Label());
        titleLabel->modify_font(Pango::FontDescription(windowTitleFont));
        titleLabel->modify_fg(Gtk::STATE_NORMAL, Colors::getColor(
                                        Colors::Orange));
        
        titleBox->pack_start(*titleImage, Gtk::PACK_SHRINK, 0);
        titleBox->pack_start(*titleLabel, Gtk::PACK_SHRINK, 2);
    }
    
    titleBox->modify_bg(Gtk::STATE_NORMAL, bgColor);
    titleAlignment = Gtk::manage(new Gtk::Alignment(Gtk::ALIGN_LEFT,
                                                    Gtk::ALIGN_CENTER,
                                                    0, 0));
    titleAlignment->add(*titleBox);
    layout->attach(*titleAlignment, 0, 1, 0, 1, Gtk::FILL, Gtk::SHRINK);

    // create the minimize, maximize and close buttons
    Gtk::Box *  cornerButtonBox = Gtk::manage(new Gtk::HBox);

    int     padding = 5;
    if (!(properties & isModal)) {
        closeButton = Gtk::manage(wf->createButton(
                                        WidgetConstants::windowCloseButton));
        cornerButtonBox->pack_end(*closeButton, Gtk::PACK_SHRINK, padding);
        padding = 0;
        closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &WhiteWindow::onCloseButtonClicked));
    }
    if (!(properties & isNotResizable)) {
        maximizeButton = Gtk::manage(wf->createButton(
                                        WidgetConstants::windowMaximizeButton));
        cornerButtonBox->pack_end(*maximizeButton, Gtk::PACK_SHRINK, padding);
        padding = (padding == 0) ? 5 : 0;
        maximizeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &WhiteWindow::onMaximizeButtonClicked));

        minimizeButton = Gtk::manage(wf->createButton(
                                        WidgetConstants::windowMinimizeButton));
        cornerButtonBox->pack_end(*minimizeButton, Gtk::PACK_SHRINK, padding);
        minimizeButton->signal_clicked().connect(sigc::mem_fun(*this,
                                        &WhiteWindow::onMinimizeButtonClicked));
    }

    cornerButtonAlignment = Gtk::manage(new Gtk::Alignment(Gtk::ALIGN_RIGHT,
                                                           Gtk::ALIGN_CENTER,
                                                           0, 0));
    cornerButtonAlignment->add(*cornerButtonBox);
    layout->attach(*cornerButtonAlignment, 1, 2, 0, 1, 
                                           Gtk::FILL, Gtk::SHRINK);

    // add the child container
    childContainer = Gtk::manage(new Gtk::Alignment(Gtk::ALIGN_CENTER));
    layout->attach(*childContainer, 0, 2, 1, 2);

    // create the resize image
    if (!(properties & isNotResizable)) {
        resizeImage = Gtk::manage(wf->createImage(WidgetConstants::resizeImage));
        resizeEventBox = Gtk::manage(new Gtk::EventBox());
        resizeEventBox->modify_bg(Gtk::STATE_NORMAL, bgColor);
        resizeEventBox->add(*resizeImage);
        resizeAlignment = Gtk::manage(new Gtk::Alignment(Gtk::ALIGN_RIGHT,
                                                         Gtk::ALIGN_CENTER,
                                                         0, 0));
        resizeAlignment->add(*resizeEventBox);
        layout->attach(*resizeAlignment, 1, 2, 2, 3, Gtk::FILL, Gtk::SHRINK);

        resizeEventBox->add_events(Gdk::BUTTON_PRESS_MASK);
        resizeEventBox->signal_button_press_event().connect(sigc::mem_fun(*this,
                                                &WhiteWindow::onResizeClicked));
    }

    // add the corners
    blueBin = Gtk::manage(new BlueBin(backgroundColor, cornerImages, true));
    blueBin->add(*layout);
    Gtk::Window::add(*blueBin);

    // register signal handlers
    this->add_events(Gdk::BUTTON_PRESS_MASK);
    this->signal_button_press_event().connect(sigc::mem_fun(*this,
                                                &WhiteWindow::onTitleClicked));
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
WhiteWindow :: ~WhiteWindow(void)                            throw ()
{
    on_remove(blueBin);
}


/*------------------------------------------------------------------------------
 *  Handle the size request event.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: on_size_request(Gtk::Requisition* requisition)       throw ()
{
    Gtk::Window::on_size_request(requisition);

    if (defaultWidth >= 0 && requisition->width < defaultWidth) {
        requisition->width = defaultWidth;
    }
    if (defaultHeight >= 0 && requisition->height < defaultHeight) {
        requisition->height = defaultHeight;
    }
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
    if (child == blueBin) {
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
    if (child) {
        if (child == blueBin && child->get_parent() == this) {
            Gtk::Window::on_remove(child);
        } else if (child == childContainer->get_child()) {
            childContainer->remove();
        }
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
WhiteWindow :: onMinimizeButtonClicked (void)               throw ()
{
    iconify();
}


/*------------------------------------------------------------------------------
 *  Event handler for the maximize button getting clicked.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: onMaximizeButtonClicked (void)               throw ()
{
    if (isMaximized) {
        unmaximize();
        isMaximized = false;
    } else {
        maximize();
        isMaximized = true;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the close button getting clicked.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: onCloseButtonClicked (void)                  throw ()
{
    hide();
}


/*------------------------------------------------------------------------------
 *  Set the title of the window.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: setTitle(const Glib::ustring &   title,
                        const Glib::ustring &   applicationTitle)
                                                            throw ()
{
    Gtk::Window::set_title(title + " - " + applicationTitle);
    if (titleLabel) {
        titleLabel->set_label(title.uppercase());
    }
}


/*------------------------------------------------------------------------------
 *  Set the default window size.
 *----------------------------------------------------------------------------*/
void
WhiteWindow :: set_default_size(int     width,
                                int     height)             throw ()
{
    defaultWidth  = width;
    defaultHeight = height;
}


/*------------------------------------------------------------------------------
 *  The event of the resize image being clicked
 *----------------------------------------------------------------------------*/
bool
WhiteWindow :: onResizeClicked(GdkEventButton     * event)  throw ()
{
    if (event->button == 1) {
        begin_resize_drag(Gdk::WINDOW_EDGE_SOUTH_EAST,
                          event->button,
                          (int) event->x_root,
                          (int) event->y_root,
                          event->time);
        return true;
        
    } else {
        return false;
    }
}

