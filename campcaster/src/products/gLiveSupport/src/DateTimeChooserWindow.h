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
#ifndef DateTimeChooserWindow_h
#define DateTimeChooserWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/gregorian/gregorian.hpp>
#include <boost/date_time/posix_time/posix_time.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "GLiveSupport.h"

#include "GuiWindow.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A dialog window for choosing a date/time.
 *
 *  The return value of the run() method is a boost::posix_time::ptime value.
 *  The DateTimeChooserWindow object is not destroyed when it returns from 
 *  run(); it is the responsibility of the caller to delete it (or it can be
 *  reused a few times first).
 *
 *  @author $Author$
 *  @version $Revision$
 */
class DateTimeChooserWindow : public GuiWindow
{
    private:

        /**
         *  The calendar where the date is chosen.
         */
        Gtk::Calendar *                     calendar;
        
        /**
         *  The entry field for hours.
         */
        Gtk::SpinButton *                   hourEntry;

        /**
         *  The entry field for minutes.
         */
        Gtk::SpinButton *                   minuteEntry;
        
        /**
         *  The OK button
         */
        Gtk::Button *                       okButton;
        
        /**
         *  The return value; set to 0 if the user closed the window.
         */
        Ptr<boost::posix_time::ptime>::Ref  chosenDateTime;


    protected:

        /**
         *  The event handler for the OK button clicked.
         */
        virtual void
        onOkButtonClicked(void)                                     throw ();


    public:

        /**
         *  Constructor.
         */
        DateTimeChooserWindow(void)
                                                                    throw ();

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
        Ptr<const boost::posix_time::ptime>::Ref
        run(void)                                                   throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // DateTimeChooserWindow_h

