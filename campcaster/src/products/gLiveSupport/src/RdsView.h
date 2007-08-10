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

#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "RdsEntry.h"
#include "GLiveSupport.h"

#include "GuiComponent.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The RDS view, a subclass of Gtk::VBox.
 *  This will be contained in another window, currently in the OptionsWindow.
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
 *  On construction, the entries are filled in using the OptionsContainer
 *  object found in the GLiveSupport object.  The OptionsContainer can be
 *  updated to the new contents of the entries using saveChanges(), and the
 *  entries can be re-initialized from the OptionsContainer using reset().
 *
 *  @author $Author$
 *  @version $Revision$
 */
class RdsView : public GuiComponent
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

        /**
         *  Fill in the entry from the OptionsContainer.
         *
         *  @param  entry   the RdsEntry to be filled in.
         */
        void
        fillEntry(Ptr<RdsEntry>::Ref        entry)                  throw ();


    protected:

        /**
         *  The entry field for the serial device.
         */
        Gtk::Entry *                                deviceEntry;


    public:

        /**
         *  Constructor.
         *
         *  @param  parent  the GuiObject which contains this one.
         */
        RdsView(GuiObject *         parent)
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

        /**
         *  Reset the widget to its saved state.
         */
        void
        reset(void)                                                 throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // RdsView_h

