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
#ifndef RdsView_h
#define RdsView_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/box.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ScrolledWindow.h"
#include "RdsEntry.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The RDS view, a subclass of Gtk::VBox.
 *  This will be contained in another window, most likely
 *  as the contents of a notebook tab.
 *
 *  The layout of the view is roughly the following:
 *  <pre><code>
 *  +--- RDS view -----------------------------------+
 *  |                    ___________                 |
 *  | [x] Station name: |___________|                |
 *  |                  ________                      |
 *  | [x] Station ID: |________|                     |
 *  |                  ___________________________   |
 *  | [ ] Clip info:  |___________________________|  |
 *  |                                                |
 *  +------------------------------------------------+
 *  </code></pre>
 *  where each item has a checkbox [x] with which one can enable or disable it.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class RdsView : public Gtk::VBox,
                public LocalizedObject
{
    private:
        /**
         *  The type for the list of entry widgets.
         */
        typedef std::vector<Ptr<RdsEntry>::Ref>     RdsEntryListType;
        
        /**
         *  The list of the entry widgets.
         */
        RdsEntryListType                            rdsEntryList;


    protected:
        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref                      gLiveSupport;


    public:
        /**
         *  Constructor.
         *
         *  @param  gLiveSupport    the gLiveSupport object, containing
         *                          all the vital info.
         *  @param  bundle          the resource bundle holding the localized
         *                          resources for this window.
         */
        RdsView(Ptr<GLiveSupport>::Ref     gLiveSupport,
                Ptr<ResourceBundle>::Ref   bundle)
                                                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~RdsView(void)                                              throw ()
        {
        }

        /**
         *  Save the changes made by the user.
         *
         *  @return true if any changes were saved; false otherwise.
         */
        bool
        saveChanges(void)                                           throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // RdsView_h

