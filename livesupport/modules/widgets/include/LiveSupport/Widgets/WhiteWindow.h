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
    Version  : $Revision: 1.10 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/WhiteWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_WhiteWindow_h
#define LiveSupport_Widgets_WhiteWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/label.h>
#include <gtkmm/table.h>
#include <gtkmm/alignment.h>
#include <gtkmm/eventbox.h>
#include <gtkmm/image.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/CornerImages.h"
#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/BlueBin.h"
#include "LiveSupport/Widgets/WidgetFactory.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A container holding exactly one child, habing a light blue border to it.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.10 $
 */
class WhiteWindow : public Gtk::Window
{
    private:
        /**
         *  The default width of the window, of -1, automatic.
         */
        int                             defaultWidth;

        /**
         *  The default height of the window, if -1, automatic
         */
        int                             defaultHeight;

        /**
         *  The rounded container for the window.
         */
        BlueBin                       * blueBin;

        /**
         *  The layout of the window.
         */
        Gtk::Table                    * layout;

        /**
         *  The event box for the title, enabling capturing mouse events.
         */
        Gtk::EventBox                 * titleEventBox;

        /**
         *  The left alignment contaner for the title.
         */
        Gtk::Alignment                * titleAlignment;

        /**
         *  The title of the window (if it's of text type, otherwise 0).
         */
        Gtk::Label                    * titleLabel;

        /**
         *  The right alignment contaner for the close button.
         */
        Gtk::Alignment                * closeButtonAlignment;

        /**
         *  The close button.
         */
        ImageButton                   * closeButton;

        /**
         *  The right alignment contaner for the resize image.
         */
        Gtk::Alignment                * resizeAlignment;

        /**
         *  The event box container for the resize image.
         */
        Gtk::EventBox                 * resizeEventBox;

        /**
         *  The resize image.
         */
        Gtk::Image                    * resizeImage;

        /**
         *  Just a container for the main content of the window.
         */
        Gtk::Alignment                * childContainer;

        /**
         *  The event handler for the title being clicked on.
         *
         *  @param event the button click event.
         *  @return true if the the event was handled, false otherwise.
         */
        bool
        onTitleClicked(GdkEventButton     * event)          throw ();

        /**
         *  Signal handler for the close button clicked.
         */
        virtual void
        onCloseButtonClicked(void)                          throw ();

        /**
         *  The event handler for the resize being clicked on.
         *
         *  @param event the button click event.
         *  @return true if the the event was handled, false otherwise.
         */
        bool
        onResizeClicked(GdkEventButton     * event)         throw ();

        /**
         *  Default constructor.
         */
        WhiteWindow(void)                                   throw ()
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
         *  The common part of both constructors.
         *
         *  @param backgroundColor the background color.
         *  @param cornerImages the corner images.
         *  @param resizable true if the user can resize the window.
         */
        void
        constructWindow(Colors::ColorName           backgroundColor,
                        Ptr<CornerImages>::Ref      cornerImages,
                        bool                        resizable = true)
                                                            throw ();


    public:
        /**
         *  Constructor for windows with image titles.
         *
         *  @param title the title of the window.
         *  @param backgroundColor the background color.
         *  @param cornerImages the corner images.
         *  @param resizable true if the user can resize the window.
         */
        WhiteWindow(WidgetFactory::ImageType    title,
                    Colors::ColorName           backgroundColor,
                    Ptr<CornerImages>::Ref      cornerImages,
                    bool                        resizable = true)
                                                            throw ();

        /**
         *  Constructor for windows with text titles.
         *
         *  @param title the title of the window.
         *  @param backgroundColor the background color.
         *  @param cornerImages the corner images.
         *  @param resizable true if the user can resize the window.
         */
        WhiteWindow(Glib::ustring               title,
                    Colors::ColorName           backgroundColor,
                    Ptr<CornerImages>::Ref      cornerImages,
                    bool                        resizable = true)
                                                            throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~WhiteWindow(void)                                  throw ();

        /**
         *  Set the title of the window.
         *
         *  @param title the title of the window.
         */
        void
        set_title(const Glib::ustring & title)              throw ();

        /**
         *  Get the title of the window.
         *
         *  @return the title of the window.
         */
        Glib::ustring
        get_title(void) const                               throw ();

        /**
         *  Set the default size of the window.
         *
         *  @param width the default width of the window.
         *  @param height the default height of the window.
         */
        void
        set_default_size(int    width,
                         int    height)                     throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_WhiteWindow_h

