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
#ifndef TaskbarIcons_h
#define TaskbarIcons_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <vector>
#include <gdkmm/pixbuf.h>
#include <glibmm/listhandle.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Container holding taskbar icon images.
 *
 *  This object has to be configured with an XML configuration element
 *  called taskbarIcons. This may look like the following:
 *
 *  <pre><code>
 *  &lt;taskbarIcons&gt;
 *      &lt;icon path="..." /&gt;
 *      &lt;icon path="..." /&gt;
 *      ...
 *      &lt;icon path="..." /&gt;
 *  &lt;/taskbarIcons&gt;
 *  </code></pre>
 *
 *  The DTD for the expected XML element is the following:
 *
 *  <pre><code>
 *  <!ELEMENT taskbarIcons  (icon*)                   >
 *  <!ELEMENT icon          EMPTY                     >
 *  <!ATTLIST icon          path    CDATA   #REQUIRED >
 *  </code></pre>
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see TaskbarIcon
 */
class TaskbarIcons : public Configurable
{
    private:
        /**
         *  The name of the configuration XML element used by TaskbarIcons.
         */
        static const std::string    configElementName;

        /**
         *  A vector type holding the taskbar icon images.
         */
        typedef std::vector<Glib::RefPtr<Gdk::Pixbuf> >
                                    PixbufListType;

        /**
         *  The list of all taskbar icon images.
         */
        PixbufListType              taskbarIconList;


    public:
        /**
         *  Constructor.
         */
        TaskbarIcons()                                              throw ()
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~TaskbarIcons(void)                                         throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                                  throw ()
        {
            return configElementName;
        }

        /**
         *  Configure the metadata object based on an XML configuration element.
         *
         *  @param elemen the XML configuration element.
         *  @exception std::invalid_argument of the supplied XML element
         *             contains bad configuration information
         */
        virtual void
        configure(const xmlpp::Element &element)
                                                throw (std::invalid_argument);

        /**
         *  Return a Glib::ListHandle to the list of taskbar icon images.
         */
        const Glib::ListHandle<Glib::RefPtr<Gdk::Pixbuf> >
        getIconList(void) const                                     throw ()
        {
            return taskbarIconList;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // TaskbarIcons_h

