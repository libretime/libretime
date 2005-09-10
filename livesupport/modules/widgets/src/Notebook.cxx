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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/Notebook.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Notebook.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
Notebook :: Notebook(void)                                      throw ()
{
    Ptr<WidgetFactory>::Ref   wf = WidgetFactory::getInstance();

    layout     = Gtk::manage(new Gtk::Table());
    tabBox     = Gtk::manage(new Gtk::HBox());
    pageHolder = Gtk::manage(new Gtk::Alignment());

    layout->attach(*tabBox,     0, 1, 0, 1, Gtk::SHRINK, Gtk::SHRINK, 5, 5);
    layout->attach(*pageHolder, 0, 1, 1, 2, Gtk::SHRINK, Gtk::SHRINK, 5, 5);

    add(*layout);

    // show all
    show_all();

    activePage = 0;
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
Notebook :: ~Notebook(void)                            throw ()
{
    while (!pageList.empty()) {
        Page      * page = pageList.back();
        pageList.pop_back();
        delete page;
    }
}


/*------------------------------------------------------------------------------
 *  Handle the size request event.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_size_request(Gtk::Requisition* requisition)       throw ()
{
    *requisition = Gtk::Requisition();

    Gtk::Requisition    layoutRequisition = layout->size_request();
    requisition->width  = layoutRequisition.width;
    requisition->height = layoutRequisition.height;
}


/*------------------------------------------------------------------------------
 *  Handle the size allocate event.
 *  We will not be given heights or widths less than we have requested,
 *  though we might get more.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_size_allocate(Gtk::Allocation& allocation)        throw ()
{
    Gtk::Alignment::on_size_allocate(allocation);
}


/*------------------------------------------------------------------------------
 *  Execute a function on all the children.
 *----------------------------------------------------------------------------*/
void
Notebook :: forall_vfunc(gboolean    includeInternals,
                            GtkCallback callback,
                            gpointer    callbackData)               throw ()
{
    Gtk::Alignment::forall_vfunc(includeInternals, callback, callbackData);
}


/*------------------------------------------------------------------------------
 *  Handle the add child widget event.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_add(Gtk::Widget* child)                           throw ()
{
    Gtk::Alignment::on_add(child);
}


/*------------------------------------------------------------------------------
 *  Handle the remove child widget event.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_remove(Gtk::Widget* child)                        throw ()
{
    Gtk::Alignment::on_remove(child);
}


/*------------------------------------------------------------------------------
 *  Return what kind of widgets can be added to this container.
 *----------------------------------------------------------------------------*/
GtkType
Notebook :: child_type_vfunc() const                             throw ()
{
    return Gtk::Alignment::child_type_vfunc();
}


/*------------------------------------------------------------------------------
 *  Handle the map event.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_map()                                             throw ()
{
    Gtk::Alignment::on_map();
}


/*------------------------------------------------------------------------------
 *  Handle the unmap event.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_unmap()                                           throw ()
{
    Gtk::Alignment::on_unmap();
}


/*------------------------------------------------------------------------------
 *  Handle the realize event.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_realize()                                         throw ()
{
    Gtk::Alignment::on_realize();
}


/*------------------------------------------------------------------------------
 *  Handle the unrealize event.
 *----------------------------------------------------------------------------*/
void
Notebook :: on_unrealize()                                   throw ()
{
    Gtk::Alignment::on_unrealize();
}


/*------------------------------------------------------------------------------
 *  Handle the expose event.
 *----------------------------------------------------------------------------*/
bool
Notebook :: on_expose_event(GdkEventExpose* event)           throw ()
{
    return Gtk::Alignment::on_expose_event(event);
}


/*------------------------------------------------------------------------------
 *  Append a page to the notebook
 *----------------------------------------------------------------------------*/
void
Notebook :: appendPage(Gtk::Widget            & widget,
                       const Glib::ustring    & label)      throw ()
{
    Ptr<WidgetFactory>::Ref     wf     = WidgetFactory::getInstance();
    Button                    * button = wf->createButton(label,
                                                    WidgetFactory::tabButton);

    Page      * page = new Page(this, pageList.size(), &widget, button);
    pageList.push_back(page);

    pagesAdded();
}


/*------------------------------------------------------------------------------
 *  Prepare the visuals for the widget
 *----------------------------------------------------------------------------*/
void
Notebook :: pagesAdded(void)                            throw ()
{
    // clean already existing widgets
    tabBox->children().clear();

    // build up the widgets based on the current pages
    PageList::iterator          pagesIt  = pageList.begin();
    PageList::iterator          pagesEnd = pageList.end();

    while (pagesIt != pagesEnd) {
        Page        * page   = *pagesIt;

        tabBox->pack_start(*page->button);
        page->button->signal_clicked().connect(sigc::mem_fun(*page,
                                                &Notebook::Page::onTabClicked));
        pagesIt++;
    }

    // reset the active page to 0, and show it
    activatePage(0);
}


/*------------------------------------------------------------------------------
 *  Make a page active
 *----------------------------------------------------------------------------*/
void
Notebook :: activatePage(unsigned int   pageNo)         throw ()
{
    if (pageNo >= pageList.size()) {
        return;
    }

    pageList[activePage]->button->unselect();
    pageList[activePage]->container->hide();
    pageHolder->remove();
    activePage = pageNo;
    pageHolder->add(*(pageList[pageNo]->container));
    pageList[pageNo]->button->select();
    show_all();
}


