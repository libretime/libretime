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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/EntryBin.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_EntryBin_h
#define LiveSupport_Widgets_EntryBin_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/entry.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/CornerImages.h"
#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/BlueBin.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A container, holding a Gtk::Entry as its only child.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.5 $
 */
class EntryBin : public BlueBin
{
    private:
        /**
         *  The text entry for this container.
         */
        Gtk::Entry                * entry;


    public:
        /**
         *  Constructor, with only one state.
         *
         *  @param backgroundColor the RGB value for the background color.
         *  @param cornerImages the corner images.
         */
        EntryBin(Colors::ColorName           backgroundColor,
                 Ptr<CornerImages>::Ref      cornerImages)
                                                            throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~EntryBin(void)                                     throw ();

        /**
         *  Return the entry held in this container.
         *
         *  @return the entry held in this container.
         */
        Gtk::Entry *
        getEntry(void)                                      throw ()
        {
            return entry;
        }

        /**
         *  Return the text of the entry.
         *
         *  @return the get_text() string of the Gtk::Entry.
         */
        Glib::ustring
        get_text(void)                                      throw ()
        {
            return getEntry()->get_text();
        }

        /**
         *  The signal proxy for pressing enter in the entry field.
         *
         *  @return the signal_activate() proxy of the Gtk::Entry.
         */
        Glib::SignalProxy0<void>
        signal_activate(void)                               throw ()
        {
            return getEntry()->signal_activate();
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_EntryBin_h

