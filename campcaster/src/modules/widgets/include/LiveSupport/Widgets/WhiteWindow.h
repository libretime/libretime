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
#ifndef LiveSupport_Widgets_WhiteWindow_h
#define LiveSupport_Widgets_WhiteWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/enable_shared_from_this.hpp>
#include <gtkmm/label.h>
#include <gtkmm/table.h>
#include <gtkmm/alignment.h>
#include <gtkmm/eventbox.h>
#include <gtkmm/image.h>
#include <gtkmm/window.h>
#include <gtkmm/buttonbox.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/WidgetConstants.h"
#include "LiveSupport/Widgets/CornerImages.h"
#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/BlueBin.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A container holding exactly one child, having a light blue border to it.
 *
 *  To create a white window, subclass your window class from WhiteWindow,
 *  and supply appropriate parameters to the WhiteWindow constructor,
 *  upon construction.
 *
 *  For example:
 *  <code><pre>
 *  class MyWindow : public WhiteWindow
 *  {
 *      MyWindow(void);
 *      ...
 *  };
 *
 *  MyWindow::MyWindow(void)
 *       : WhiteWindow("window title",
 *                     Colors::White,
 *                     WidgetFactory::getInstance()->getWhiteWindowCorners())
 *  {
 *      ...
 *  }
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see WidgetFactory
 *  @see WidgetFactory#getWhiteWindowCorners
 */
class WhiteWindow : public Gtk::Window,
                    public boost::enable_shared_from_this<WhiteWindow>
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
         *  The left alignment contaner for the title.
         */
        Gtk::Alignment                * titleAlignment;

        /**
         *  The title of the window (if it's of text type, otherwise 0).
         */
        Gtk::Label                    * titleLabel;

        /**
         *  The right alignment contaner for the minimize, maximize and
         *  close buttons.
         */
        Gtk::Alignment                * cornerButtonAlignment;

        /**
         *  True if the window has been minimized.
         */
        bool                            isMaximized;

        /**
         *  The close button.
         */
        ImageButton                   * minimizeButton;

        /**
         *  The close button.
         */
        ImageButton                   * maximizeButton;

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
         *  @param properties   some WindowProperties flags
         */
        void
        constructWindow(Colors::ColorName           backgroundColor,
                        Ptr<CornerImages>::Ref      cornerImages,
                        int                         properties)
                                                            throw ();

        /**
         *  The event handler for the title being clicked on.
         *
         *  @param event the button click event.
         *  @return true if the the event was handled, false otherwise.
         */
        virtual bool
        onTitleClicked(GdkEventButton     * event)          throw ();

        /**
         *  Signal handler for the minimize button clicked.
         */
        virtual void
        onMinimizeButtonClicked(void)                       throw ();

        /**
         *  Signal handler for the maximize button clicked.
         */
        virtual void
        onMaximizeButtonClicked(void)                       throw ();

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
        virtual bool
        onResizeClicked(GdkEventButton     * event)         throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param title the title of the window.
         *  @param applicationTitle the name of the application.
         *  @param backgroundColor the background color.
         *  @param cornerImages the corner images.
         *  @param properties   some WindowProperties flags
         */
        WhiteWindow(Colors::ColorName           backgroundColor,
                    Ptr<CornerImages>::Ref      cornerImages,
                    int                         properties = 0)
                                                            throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~WhiteWindow(void)                                  throw ();

        /**
         *  Set the title of the window.
         *
         *  This sets the title shown in the upper left corner of the window
         *  to "TITLE", and sets the window manager title shown in the task
         *  bar to "title - applicationTitle".
         *
         *  @param title                the title of the window.
         *  @param applicationTitle     the name of the application.
         */
        void
        setTitle(const Glib::ustring &     title,
                 const Glib::ustring &     applicationTitle)
                                                            throw ();

        /**
         *  Set the default size of the window.
         *
         *  @param width the default width of the window.
         *  @param height the default height of the window.
         */
        void
        set_default_size(int    width,
                         int    height)                     throw ();

        /**
         *  Properties the WhiteWindow can have.  This is passed as the
         *  properties parameter to the constructors.
         */
        typedef enum  { hasNoTitle      = 1,
                        isNotResizable  = 2,
                        isModal         = 4 }       WindowProperties;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_WhiteWindow_h

