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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/BrowseEntry.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "BrowseEntry.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
BrowseEntry :: BrowseEntry(
        Ptr<LiveSupport::GLiveSupport::GLiveSupport>::Ref   gLiveSupport,
        Ptr<ResourceBundle>::Ref                            bundle)
                                                        throw ()
          : LocalizedObject(bundle)
{
    browseItemOne   = Gtk::manage(new BrowseItem(gLiveSupport, 
                                                 bundle,
                                                 4 /* Genre */));
    browseItemTwo   = Gtk::manage(new BrowseItem(gLiveSupport, 
                                                 bundle,
                                                 1 /* Creator */));
    browseItemThree = Gtk::manage(new BrowseItem(gLiveSupport, 
                                                 bundle,
                                                 2 /* Album */));
    // TODO: change hard-coded indices to stuff read from config

    browseItemOne->signalSelectionChanged().connect(
        sigc::bind<BrowseItem*>(
            sigc::mem_fun(*browseItemTwo, &BrowseItem::onParentChangedShow),
            browseItemOne ));
    browseItemTwo->signalSelectionChanged().connect(
        sigc::bind<BrowseItem*>(
            sigc::mem_fun(*browseItemThree, &BrowseItem::onParentChangedShow),
            browseItemTwo ));
                                 
    pack_start(*browseItemOne,   Gtk::PACK_EXPAND_WIDGET, 5);
    pack_start(*browseItemTwo,   Gtk::PACK_EXPAND_WIDGET, 5);
    pack_start(*browseItemThree, Gtk::PACK_EXPAND_WIDGET, 5);
}

