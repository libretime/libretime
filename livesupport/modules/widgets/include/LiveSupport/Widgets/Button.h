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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/Button.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_Button_h
#define LiveSupport_Widgets_Button_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/button.h>
#include <gtkmm/label.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A button holding a text.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class Button : public Gtk::Button
{
    private:
        /**
         *  The possible states of the button.
         */
        typedef enum { passiveState, rollState, selectedState } State;

        /**
         *  The Gdk::Window object, used to draw inside this button.
         */
        Glib::RefPtr<Gdk::Window>       gdkWindow;

        /**
         *   The Graphics Context, used to draw.
         */
        Glib::RefPtr<Gdk::GC>           gc;

        /**
         *  The text displayed inside the button.
         */
        Ptr<Gtk::Label>::Ref            label;

        /**
         *  The state of the button.
         */
        State                           state;

        /**
         *  The left image for the passive state of the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>       passiveImageLeft;

        /**
         *  The center image for the passive state of the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>       passiveImageCenter;

        /**
         *  The right image for the passive state of the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>       passiveImageRight;

        /**
         *  The left image of the button, when the mouse hovers above it.
         */
        Glib::RefPtr<Gdk::Pixbuf>       rollImageLeft;

        /**
         *  The center image of the button, when the mouse hovers above it.
         */
        Glib::RefPtr<Gdk::Pixbuf>       rollImageCenter;

        /**
         *  The right image of the button, when the mouse hovers above it.
         */
        Glib::RefPtr<Gdk::Pixbuf>       rollImageRight;

        /**
         *  Default constructor.
         */
        Button(void)                                   throw ()
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

        /**
         *  Handle the event when the mouse enters the button area.
         */
        virtual void
        on_enter(void)                                      throw ();

        /**
         *  Handle the event when the mouse leaves the button area.
         */
        virtual void
        on_leave(void)                                      throw ();


    public:
        /**
         *  Constructor, with only one state.
         *
         *  @param label the text to display in the button
         *  @param leftImage the left image for the button
         *  @param centerImage the center image for the button
         *  @param rightImage the right image for the button
         */
        Button(const Glib::ustring       & label,
               Glib::RefPtr<Gdk::Pixbuf>   leftImage,
               Glib::RefPtr<Gdk::Pixbuf>   centerImage,
               Glib::RefPtr<Gdk::Pixbuf>   rightImage)          throw ();

        /**
         *  Constructor, with a rollover state.
         *  Passive and rollover images are expected to be of the same size.
         *
         *  @param label the text to display in the button
         *  @param passiveImageLeft the left image for the button, passive
         *  @param passiveImageCenter the center image for the button, passive
         *  @param passiveImageRight the right image for the button, passive
         *  @param rollImageLeft the left image for the button, onmouseover
         *  @param rollImageCenter the center image for the button, onmouseover
         *  @param rollImageRight the right image for the button, onmouseover
         */
        Button(const Glib::ustring       & label,
               Glib::RefPtr<Gdk::Pixbuf>   passiveImageLeft,
               Glib::RefPtr<Gdk::Pixbuf>   passiveImageCenter,
               Glib::RefPtr<Gdk::Pixbuf>   passiveImageRight,
               Glib::RefPtr<Gdk::Pixbuf>   rollImageLeft,
               Glib::RefPtr<Gdk::Pixbuf>   rollImageCenter,
               Glib::RefPtr<Gdk::Pixbuf>   rollImageRight)      throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~Button(void)                                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_Button_h

