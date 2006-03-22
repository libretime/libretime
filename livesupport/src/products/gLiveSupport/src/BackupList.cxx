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
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/BackupList.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "BackupList.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The localization key for the 'working' status.
 *----------------------------------------------------------------------------*/
static const std::string   workingStatusKey = "workingStatus";

/*------------------------------------------------------------------------------
 *  The localization key for the 'success' status.
 *----------------------------------------------------------------------------*/
static const std::string   successStatusKey = "successStatus";

/*------------------------------------------------------------------------------
 *  The localization key for the 'fault' status.
 *----------------------------------------------------------------------------*/
static const std::string   faultStatusKey = "faultStatus";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
BackupList :: BackupList (Ptr<GLiveSupport>::Ref    gLiveSupport,
                          Ptr<ResourceBundle>::Ref  bundle)
                                                                    throw ()
          : LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();

    // create the tree view
    treeModel = Gtk::ListStore::create(modelColumns);
    treeView = Gtk::manage(widgetFactory->createTreeView(treeModel));
    treeView->set_enable_search(false);
}


/*------------------------------------------------------------------------------
 *  Add a new item to the list.
 *----------------------------------------------------------------------------*/
void
BackupList :: add(Ptr<Glib::ustring>::Ref     name,
                  Ptr<SearchCriteria>::Ref    criteria)
                                                throw (std::runtime_error)
{
}


/*------------------------------------------------------------------------------
 *  Remove the currently selected item from the list.
 *----------------------------------------------------------------------------*/
void
BackupList :: remove(void)                                          throw ()
{
}


/*------------------------------------------------------------------------------
 *  Get the URL of the currently selected item.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
BackupList :: getUrl(void)                      throw (std::invalid_argument)
{
    Ptr<Glib::ustring>::Ref     url(new Glib::ustring);
    return url;
}


/*------------------------------------------------------------------------------
 *  Query the storage server about the status of the pending backup.
 *----------------------------------------------------------------------------*/
void
BackupList :: update(void)                                          throw ()
{
}

