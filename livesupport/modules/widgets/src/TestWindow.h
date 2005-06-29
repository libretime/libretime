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
    Version  : $Revision: 1.11 $
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

#include <gtkmm/entry.h>
#include <gtkmm/window.h>
#include <gtkmm/table.h>

#include "LiveSupport/Core/Ptr.h"

#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/BlueBin.h"
#include "LiveSupport/Widgets/EntryBin.h"
#include "LiveSupport/Widgets/Notebook.h"
#include "LiveSupport/Widgets/WhiteWindow.h"
#include "LiveSupport/Widgets/DialogWindow.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, enabling interactive testing of UI components.
 *
 *  @author $Author: fgerlits $
 *  @version $Revision: 1.11 $
 */
class TestWindow : public WhiteWindow
{
    private:
        /**
         *  Change the image from "play" to "stop" on the button when pressed.
         */
        void
        onPlayButtonPressed(void)                           throw ();
    
        /**
         *  Change the image from "stop" to "play" on the button when pressed.
         */
        void
        onStopButtonPressed(void)                           throw ();
    
        /**
         *  The "are you sure?" dialog window.
         */
        DialogWindow              * dialogWindow;
    

    protected:
        /**
         *  The layout used in the window.
         */
        Gtk::Table                * layout;

        /**
         *  A notebook, to tab through pages.
         */
        Notebook                  * notebook;

        /**
         *  An image button with transparent background.
         */
        ImageButton               * hugeImageButton;

        /**
         *  A clickable image button showing a "play" icon.
         */
        ImageButton               * cuePlayImageButton;

        /**
         *  A clickable image button showing a "stop" icon.
         */
        ImageButton               * cueStopImageButton;

        /**
         *  A button.
         */
        Button                    * button;

        /**
         *  A combo box.
         */
        ComboBoxText              * comboBoxText;

        /**
         *  A text entry.
         */
        Gtk::Entry                * entry;

        /**
         *  A container holding a text entry.
         */
        EntryBin                  * entryBin;

        /**
         *  A blue container.
         */
        BlueBin                   * blueBin;

        /**
         *  Event handler for the button being clicked.
         */
        virtual void
        onButtonClicked(void)                               throw ();


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

