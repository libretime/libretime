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
#ifndef TestWindow_h
#define TestWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm.h>
#include <libglademm.h>

#include "LiveSupport/Core/Ptr.h"

#include "LiveSupport/Widgets/ComboBoxText.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, enabling interactive testing of UI components.
 *
 *  @author $Author$
 *  @version $Revision$
 */
class TestWindow : public LocalizedObject
{
    private:

        /**
         *  Change the image from "play" to "stop" on the button when pressed.
         */
        void
        onPlayButtonClicked(void)                           throw ();
    
        /**
         *  Change the image from "stop" to "play" on the button when pressed.
         */
        void
        onStopButtonClicked(void)                           throw ();
    
        /**
         *  The "are you sure?" dialog window.
         */
        Ptr<Gtk::Dialog>::Ref       dialogWindow;
    

    protected:

        /**
         *  A large button.
         */
        Gtk::Button *               largeButton;

        /**
         *  A button showing a "play" icon.
         */
        Gtk::Button *               cuePlayButton;

        /**
         *  A button showing a "stop" icon.
         */
        Gtk::Button *               cueStopButton;

        /**
         *  A button.
         */
        Gtk::Button *               button;

        /**
         *  A button which sometimes gets disabled.
         */
        Gtk::Button *               disableTestButton;

        /**
         *  A combo box.
         */
        ComboBoxText *              comboBoxText;

        /**
         *  Event handler for the large button being clicked.
         */
        virtual void
        onButtonClicked(void)                               throw ();

        /**
         *  Event handler for the close button being clicked
         *  (overrides WhiteWindow::onCloseButtonClicked()).
         */
        virtual void
        onCloseButtonClicked(void)                          throw ();


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

