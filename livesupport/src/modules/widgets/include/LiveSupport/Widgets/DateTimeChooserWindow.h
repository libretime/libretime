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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/widgets/include/LiveSupport/Widgets/DateTimeChooserWindow.h $

------------------------------------------------------------------------------*/
#ifndef DateTimeChooserWindow_h
#define DateTimeChooserWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/box.h>
#include <gtkmm/label.h>
#include <gtkmm/calendar.h>
#include <gtkmm/main.h>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/EntryBin.h"

#include "LiveSupport/Widgets/WhiteWindow.h"

namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
using namespace boost::posix_time;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A dialog window for choosing a date/time.
 *
 *  The constructor is called with a resource bundle.  The resource bundle
 *  is expected to contain keys named cancelButtonLabel, okButtonLabel,
 *  hourLabel and minuteLabel.
 *
 *  The return value of the run() method is a boost::posix_time::ptime value.
 *  The DateTimeChooserWindow object is not destroyed when it returns from 
 *  run(); it is the responsibility of the caller to delete it (or it can be
 *  reused a few times first).
 *
 *  @author $Author: fgerlits $
 *  @version $Revision$
 */
class DateTimeChooserWindow : public WhiteWindow,
                              public LocalizedObject
{
    private:
        /**
         *  The calendar where the date is chosen.
         */
        Gtk::Calendar *     calendar;
        
        /**
         *  The entry field for the hour.
         */
        EntryBin *          hourEntry;
        
        /**
         *  The entry field for the minute.
         */
        EntryBin *          minuteEntry;
        
        /**
         *  The return value; set to 0 if the user pressed Cancel.
         */
        Ptr<ptime>::Ref     chosenDateTime;


    protected:
        /**
         *  The event handler for the Cancel button clicked.
         */
        void
        onCancelButtonClicked(void)                                 throw ();

        /**
         *  The event handler for the OK button clicked.
         */
        void
        onOkButtonClicked(void)                                     throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param bundle   a resource bundle containing the localized
         *                  button labels
         */
        DateTimeChooserWindow(Ptr<ResourceBundle>::Ref   bundle)    throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~DateTimeChooserWindow(void)                                throw ()
        {
        }

        /**
         *  Run the window and return the date/time selected.
         *  The returned value may be a 0 pointer (if the user pressed Cancel),
         *  and it may be not_a_date_time, if the user's selection is invalid.
         */
        Ptr<const ptime>::Ref
        run(void)                                                   throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // DateTimeChooserWindow_h

