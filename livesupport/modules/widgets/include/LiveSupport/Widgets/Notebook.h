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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/Notebook.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_Notebook_h
#define LiveSupport_Widgets_Notebook_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <vector>

#include <gtkmm/table.h>
#include <gtkmm/buttonbox.h>
#include <gtkmm/alignment.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/CornerImages.h"
#include "LiveSupport/Widgets/ImageButton.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A container holding a range of children, showing one a time, in tabs.
 *
 *  After adding pages to a Notebook object, call the function pagesAdded().
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class Notebook : public Gtk::Alignment
{
    private:
        /**
         *  A container class, holding all that is needed to represent
         *  a page in the notepad.
         *
         *  @author  $Author: maroy $
         *  @version $Revision: 1.1 $
         */
        class Page
        {
            public:
                /**
                 *  The Notebook this page is contained in.
                 */
                Notebook      * notebook;

                /**
                 *  The index of the page.
                 */
                unsigned int    index;

                /**
                 *  The container for the widget.
                 */
                Gtk::Alignment    * container;

                /**
                 *  The contents of the page.
                 */
                Gtk::Widget   * widget;

                /**
                 *  The button of the page.
                 */
                Button        * button;

                /**
                 *  Signal handler for the tab button clicked.
                 */
                virtual void
                onTabClicked(void)                              throw ()
                {
                    notebook->activatePage(index);
                }

                /**
                 *  Constructor.
                 *
                 *  @param notebook the notebook this page is contained in.
                 *  @param index the index of the page.
                 *  @param widget the widget of the page.
                 *  @param button the button of the page.
                 */
                Page(Notebook         * notebook,
                     unsigned int       index,
                     Gtk::Widget      * widget,
                     Button           * button)                 throw ()
                {
                    this->notebook = notebook;
                    this->index    = index;
                    this->widget   = widget;
                    this->button   = button;

                    container = new Gtk::Alignment;
                    container->add(*widget);
                }

                /**
                 *  Destructor.
                 */
                virtual
                ~Page(void)                                     throw ()
                {
                    delete container;
                }
        };

        /**
         *  The list type, for the list of pages.
         */
        typedef std::vector<Page*>  PageList;

        /**
         *  The list of pages in the notebook.
         */
        PageList                        pageList;

        /**
         *  The layout of the window.
         */
        Gtk::Table                    * layout;

        /**
         *  The horizontal box holding the tabs.
         */
        Gtk::HBox                     * tabBox;

        /**
         *  The container for the displaying a page at a time.
         */
        Gtk::Alignment                * pageHolder;

        /**
         *  The index of the current active page.
         */
        unsigned int                    activePage;


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
         *  Call this function after finished adding pages to this object.
         *  This call will make the object prepare the visuals to display.
         */
        virtual void
        pagesAdded(void)                                    throw ();

        /**
         *  Make a specific page active.
         *
         *  @param pageNo the index of the page to make active.
         */
        virtual void
        activatePage(unsigned int   pageNo)                 throw ();


    public:
        /**
         *  Constructor.
         */
        Notebook()                                          throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~Notebook(void)                                     throw ();

        /**
         *  Append a page to the notebook.
         *
         *  @param widget the widget that is the page itself.
         *  @param label the label of the page.
         */
        void
        appendPage(Gtk::Widget            & widget,
                   const Glib::ustring    & label)          throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_Notebook_h

