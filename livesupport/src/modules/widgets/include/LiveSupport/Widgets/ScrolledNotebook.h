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
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_ScrolledNotebook_h
#define LiveSupport_Widgets_ScrolledNotebook_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/Notebook.h"


namespace LiveSupport {
namespace Widgets {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A Widgets::Notebook subclass, which puts pages inside 
 *  a Widgets::ScrolledWindow before appending them.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class ScrolledNotebook : public Notebook
{
    public:
        /**
         *  Constructor.
         */
        ScrolledNotebook()                                          throw ()
            : Notebook()
        {
        }

        /**
         *  A virtual destructor.
         */
        virtual
        ~ScrolledNotebook(void)                                     throw ()
        {
        }

        /**
         *  Append a page to the notebook.
         *
         *  @param widget the widget that is the page itself.
         *  @param label the label of the page.
         */
        virtual void
        appendPage(Gtk::Widget            & widget,
                   const Glib::ustring    & label)                  throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ScrolledNotebook_h

