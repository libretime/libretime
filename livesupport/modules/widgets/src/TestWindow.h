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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/TestWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef TestWindow_h
#define TestWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/window.h>
#include <gtkmm/table.h>

#include "LiveSupport/Core/Ptr.h"

#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/BlueBin.h"
#include "LiveSupport/Widgets/WhiteWindow.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, enabling interactive testing of UI components.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.4 $
 */
class TestWindow : public WhiteWindow
{
    protected:
        /**
         *  The layout used in the window.
         */
        Ptr<Gtk::Table>::Ref        layout;

        /**
         *  An image button.
         */
        Ptr<ImageButton>::Ref       imageButton;

        /**
         *  A button.
         */
        Ptr<Button>::Ref            button;

        /**
         *  A blue container.
         */
        Ptr<BlueBin>::Ref           blueBin;


    public:
        /**
         *  Constructor.
         */
        TestWindow(void)                                    throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~TestWindow(void)                                   throw ();

};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // TestWindow_h

