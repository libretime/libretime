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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/ComboBoxText.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_ComboBoxText_h
#define LiveSupport_Widgets_ComboBoxText_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/label.h>
#include <gtkmm/menu.h>
#include <gtkmm/comboboxtext.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A combo box holding text entries.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class ComboBoxText : public Gtk::ComboBoxText
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
         *  The text displayed inside the button.
         */
        Ptr<Gtk::Label>::Ref            label;

        /**
         *  The X coordinate of the label.
         */
        int                             labelX;

        /**
         *  The Y coordinate of the label.
         */
        int                             labelY;

        /**
         *  The drop-down menu for the combo box.
         */
        Ptr<Gtk::Menu>::Ref             menu;

        /**
         *  The left image of the widget.
         */
        Glib::RefPtr<Gdk::Pixbuf>       leftImage;

        /**
         *  The image behind the text display.
         */
        Glib::RefPtr<Gdk::Pixbuf>       centerImage;

        /**
         *  The right image for the widget.
         */
        Glib::RefPtr<Gdk::Pixbuf>       rightImage;

        /**
         *  Default constructor.
         */
        ComboBoxText(void)                                   throw ()
        {
        }

        /**
         *  Return the popup menu position.
         *
         *  @param x the X coordinate for the menu.
         *  @param y the Y coordinate for the menu.
         *  @param pushIn don't know what this does.
         */
        void
        onMenuPosition(int    & x,
                       int    & y,
                       bool   & pushIn)                     throw ();

        /**
         *  Event handler for the combo box being clicked.
         *
         *  @param event the button click event.
         *  @return true if the the event was handled, false otherwise.
         */
        bool
        onBoxClicked(GdkEventButton     * event)            throw ();

        /**
         *  Event handler for the menu item selected.
         */
        void
        onMenuItemSelected(void)                            throw ();


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
         *  Constructor.
         *
         *  @param leftImage the left image of the widget.
         *  @param centerImage the image under the text display.
         *  @param rightImage the right image for the widget.
         */
        ComboBoxText(Glib::RefPtr<Gdk::Pixbuf>   leftImage,
                     Glib::RefPtr<Gdk::Pixbuf>   centerImage,
                     Glib::RefPtr<Gdk::Pixbuf>   rightImage)        throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~ComboBoxText(void)                                  throw ();

        /**
         *  Append a new text entry to the combo box menu.
         *
         *  @param text the text entry to append.
         */
        void
        append_text(const Glib::ustring &text)              throw ();

        /**
         *  Return the active text.
         *
         *  @return the active text of the combo box.
         */
        Glib::ustring
        get_active_text(void) const                         throw ();

        /**
         *  Insert a new text entry at a given position.
         *
         *  @param position the position where to insert the text.
         *  @param text the text to insert.
         */
        void
        insert_text(int                     position,
                    const Glib::ustring   & text)           throw ();

        /**
         *  Set the active text.
         *
         *  @param text the text to select as active.
         */
        void
        set_active_text(const Glib::ustring   & text)       throw ();
        
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ComboBoxText_h

