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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/EntryBin.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


#include "LiveSupport/Widgets/EntryBin.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
EntryBin :: EntryBin(Colors::ColorName            backgroundColor,
                     Ptr<CornerImages>::Ref       cornerImages)
                                                                    throw ()
            : BlueBin(backgroundColor, cornerImages)
{
    entry = Gtk::manage(new Gtk::Entry());
    entry->set_has_frame(false);
 
    // TODO: this doesn't change the background color, for some reason :(
    entry->modify_bg(Gtk::STATE_NORMAL, getBgColor());

    Glib::RefPtr<Gtk::Style>     style = entry->get_style();
    style->set_base(Gtk::STATE_NORMAL, getBgColor());
    style->set_base(Gtk::STATE_ACTIVE, getBgColor());
    style->set_base(Gtk::STATE_PRELIGHT, getBgColor());
    style->set_base(Gtk::STATE_SELECTED, getBgColor());
    style->set_base(Gtk::STATE_INSENSITIVE, getBgColor());
    entry->set_style(style); 

    add(*entry);
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
EntryBin :: ~EntryBin(void)                            throw ()
{
}

