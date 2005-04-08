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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/ZebraTreeView.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_ZebraTreeView_h
#define LiveSupport_Widgets_ZebraTreeView_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/treemodel.h>
#include <gtkmm/treeview.h>
#include <gtkmm/label.h>
#include <gtkmm/table.h>
#include <gtkmm/alignment.h>
#include <gtkmm/eventbox.h>
#include <gtkmm/image.h>
#include <gtkmm/window.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Widgets/CornerImages.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/BlueBin.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A list of items, in rows colored alternately grey and light blue.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.3 $
 */
class ZebraTreeView : public Gtk::TreeView
{
    private:
        /**
         *  Default constructor.
         */
        ZebraTreeView(void)                                     throw ()
        {
        }

    protected:

    public:
        /**
         *  Constructor.
         *
         *  @param treeModel the data the treeView will show.
         */
        ZebraTreeView(Glib::RefPtr<Gtk::TreeModel>   treeModel)
                                                                throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~ZebraTreeView(void)                                    throw ();

        /**
         *  Set the callback function for every column.
         */
        void 
        setCellDataFunction(const Column::SlotCellData&    callback)
                                                                throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ZebraTreeView_h

