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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/ComboBoxText.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/ComboBoxText.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ComboBoxText :: ComboBoxText(Glib::RefPtr<Gdk::Pixbuf>      leftImage,
                             Glib::RefPtr<Gdk::Pixbuf>      centerImage,
                             Glib::RefPtr<Gdk::Pixbuf>      rightImage)
                                                                    throw ()
{
    set_flags(Gtk::NO_WINDOW);

    this->leftImage   = leftImage;
    this->centerImage = centerImage;
    this->rightImage  = rightImage;

    label = Gtk::manage(new Gtk::Label(""));
    label->set_parent(*this);

    // specify a white background
    Gdk::Color      bgColor = Colors::getColor(Colors::White);

    menu.reset(new Gtk::Menu());
    menu->modify_bg(Gtk::STATE_NORMAL, bgColor);

    // register the event handler for the mouse click
    add_events(Gdk::BUTTON_PRESS_MASK);
    signal_button_press_event().connect(sigc::mem_fun(*this,
                                                &ComboBoxText::onBoxClicked));
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
ComboBoxText :: ~ComboBoxText(void)                            throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the size request event.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: on_size_request(Gtk::Requisition* requisition)       throw ()
{
    *requisition = Gtk::Requisition();

    // get the required size from the label
    Gtk::Requisition    childRequisition = label->size_request();;

    // iterate through the menu elements, and get the biggest size
    Gtk::Menu::MenuList           & list = menu->items();
    Gtk::Menu::MenuList::iterator   it   = list.begin();
    Gtk::Menu::MenuList::iterator   end  = list.end();
    while (it != end) {
        Gtk::MenuItem     & item            = *it;
        Gtk::Requisition    itemRequisition = item.size_request();
        if (childRequisition.width < itemRequisition.width) {
            childRequisition.width = itemRequisition.width;
        }
        if (childRequisition.height < itemRequisition.height) {
            childRequisition.height = itemRequisition.height;
        }

        ++it;
    }

    requisition->width  = leftImage->get_width()
                        + childRequisition.width
                        + rightImage->get_width();
    requisition->height = centerImage->get_height();
}


/*------------------------------------------------------------------------------
 *  Handle the size allocate event.
 *  We will not be given heights or widths less than we have requested,
 *  though we might get more.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: on_size_allocate(Gtk::Allocation& allocation)        throw ()
{
    allocation.set_height(centerImage->get_height());
    set_allocation(allocation);

    if (gdkWindow) {
        gdkWindow->move_resize( allocation.get_x(), 
                                allocation.get_y(), 
                                allocation.get_width(), 
                                allocation.get_height() );
    }

    Gtk::Allocation     labelAlloc;

    labelX = leftImage->get_width();
    // put it 1 pixel lower, so that it looks good
    labelY = 1 + ((allocation.get_height() - centerImage->get_height()) / 2);

    labelAlloc.set_x(labelX);
    labelAlloc.set_y(labelY);
    labelAlloc.set_width(allocation.get_width()
                       - leftImage->get_width()
                       - rightImage->get_width());
    labelAlloc.set_height(centerImage->get_height());

    label->size_allocate(labelAlloc);

    Gtk::ComboBoxText::on_size_allocate(allocation);
}


/*------------------------------------------------------------------------------
 *  Execute a function on all the children.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: forall_vfunc(gboolean    includeInternals,
                       GtkCallback callback,
                       gpointer    callbackData)               throw ()
{
    callback((GtkWidget*) label->gobj(), callbackData);
}


/*------------------------------------------------------------------------------
 *  Handle the add child widget event.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: on_add(Gtk::Widget* child)                           throw ()
{
}


/*------------------------------------------------------------------------------
 *  Handle the remove child widget event.
 *  As this widget has no children, don't do anything.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: on_remove(Gtk::Widget* child)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Return what kind of widgets can be added to this container.
 *  As this widget has no children, return G_TYPE_NONE always.
 *----------------------------------------------------------------------------*/
GtkType
ComboBoxText :: child_type_vfunc() const                             throw ()
{
    return G_TYPE_NONE;
}


/*------------------------------------------------------------------------------
 *  Handle the map event.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: on_map()                                             throw ()
{
    Gtk::ComboBoxText::on_map();
}


/*------------------------------------------------------------------------------
 *  Handle the unmap event.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: on_unmap()                                           throw ()
{
    Gtk::ComboBoxText::on_unmap();
}


/*------------------------------------------------------------------------------
 *  Handle the realize event.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: on_realize()                                         throw ()
{
    // trick to make GTK-- allocate a window for the later get_window() call
    set_flags(Gtk::NO_WINDOW);
    Gtk::ComboBoxText::on_realize();

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
ComboBoxText :: on_unrealize()                                   throw ()
{
    gdkWindow.clear();
    gc.clear();

    Gtk::ComboBoxText::on_unrealize();
}


/*------------------------------------------------------------------------------
 *  Handle the expose event.
 *----------------------------------------------------------------------------*/
bool
ComboBoxText :: on_expose_event(GdkEventExpose* event)           throw ()
{
    if (event->count > 0) {
        return false;
    }  

    if (gdkWindow) {
        gdkWindow->clear();

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

        // draw the label itself
        gdkWindow->draw_layout(gc, labelX, labelY, label->get_layout());
    }

    Gtk::ComboBoxText::on_expose_event(event);

    return false;
}


/*------------------------------------------------------------------------------
 *  Return the menu position.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: onMenuPosition(int    & x,
                               int    & y,
                               bool   & pushIn)             throw ()
{
    int     windowX;
    int     windowY;

    gdkWindow->get_origin(windowX, windowY);

    x      = windowX + labelX;
    y      = windowY + labelY;
    pushIn = false;
}


/*------------------------------------------------------------------------------
 *  Return the menu position.
 *----------------------------------------------------------------------------*/
bool
ComboBoxText :: onBoxClicked(GdkEventButton   * event)          throw ()
{
    if (event->button == 1) {
        // display the menu
        menu->popup(sigc::mem_fun(*this, &ComboBoxText::onMenuPosition),
                    0, 0);
    }

    return false;
}


/*------------------------------------------------------------------------------
 *  Event handler for the menu item selected.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: onMenuItemSelected(void)                    throw ()
{
    Gtk::MenuItem  * item     = menu->get_active();
    Gtk::Label     * selected = (Gtk::Label*) item->get_child();
    set_active_text(selected->get_text());
    signalSelectionChanged().emit();
}


/*------------------------------------------------------------------------------
 *  Append a new text entry to the combo box menu.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: append_text(const Glib::ustring &text)              throw ()
{
    Gtk::Menu::MenuList& list = menu->items();

    list.push_back(Gtk::Menu_Helpers::MenuElem(text,
                                sigc::mem_fun(*this,
                                          &ComboBoxText::onMenuItemSelected)));
}


/*------------------------------------------------------------------------------
 *  Return the active text.
 *----------------------------------------------------------------------------*/
Glib::ustring
ComboBoxText :: get_active_text(void) const                         throw ()
{
    // TODO: this may actually be bogus data
    return label->get_text();
}


/*------------------------------------------------------------------------------
 *  Insert a new text entry at a given position.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: insert_text(int                     position,
                            const Glib::ustring   & text)           throw ()
{
    // TODO: this probably doesn't work, the menu->insert() function seems
    //       to be broken
    Gtk::MenuItem    item(text);
    menu->insert(item, position);
}


/*------------------------------------------------------------------------------
 *  Set the active text.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: set_active_text(const Glib::ustring   & text)       throw ()
{
    // TODO: the activate function probably doesn't work, it seems to be broken
    Gtk::MenuItem    item(text);
    menu->activate_item(item);

    label->set_text(text);
}


/*------------------------------------------------------------------------------
 *  Set the first item in the combo box to be the active text.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: set_active(int  index)                              throw ()
{
    menu->set_active(index);
    onMenuItemSelected();
}


/*------------------------------------------------------------------------------
 *  Add a new entry, together with an (invisible) key.
 *----------------------------------------------------------------------------*/
void
ComboBoxText :: appendPair(Ptr<const Glib::ustring>::Ref  text,
                           Ptr<const Glib::ustring>::Ref  key)       throw ()
{
    append_text(*text);
    keyMap[*text] = key;
}


/*------------------------------------------------------------------------------
 *  Get the key corresponding to the selected item.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
ComboBoxText :: getActiveKey(void)                  throw (std::logic_error)
{
    KeyMapType::const_iterator  it = keyMap.find(get_active_text());
    if (it != keyMap.end()) {
        return it->second;
    } else {
        throw std::logic_error("no active key found in OperatorComboBoxText");
    }
}


/*------------------------------------------------------------------------------
 *  Accessor for the selectionChanged signal.
 *----------------------------------------------------------------------------*/
sigc::signal<void>
ComboBoxText :: signalSelectionChanged(void)                        throw ()
{
    return signalSelectionChangedObject;
}

