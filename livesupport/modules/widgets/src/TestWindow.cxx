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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/TestWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "TestWindow.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
TestWindow :: TestWindow (void)
                                                                    throw ()
{
    // init the imageButton
    Glib::RefPtr<Gdk::Pixbuf>   passiveImage;
    Glib::RefPtr<Gdk::Pixbuf>   rollImage;

    passiveImage = Gdk::Pixbuf::create_from_file("var/delete.png");
    rollImage    = Gdk::Pixbuf::create_from_file("var/delete_roll.png");

    imageButton.reset(new ImageButton(passiveImage, rollImage));

    // init the button
    Glib::RefPtr<Gdk::Pixbuf>   passiveImageLeft;
    Glib::RefPtr<Gdk::Pixbuf>   passiveImageCenter;
    Glib::RefPtr<Gdk::Pixbuf>   passiveImageRight;
    Glib::RefPtr<Gdk::Pixbuf>   rollImageLeft;
    Glib::RefPtr<Gdk::Pixbuf>   rollImageCenter;
    Glib::RefPtr<Gdk::Pixbuf>   rollImageRight;

    passiveImageLeft   = Gdk::Pixbuf::create_from_file("var/button_left.png");
    passiveImageCenter = Gdk::Pixbuf::create_from_file("var/button_centre.png");
    passiveImageRight  = Gdk::Pixbuf::create_from_file("var/button_right.png");
    rollImageLeft   = Gdk::Pixbuf::create_from_file("var/button_left_roll.png");
    rollImageCenter = Gdk::Pixbuf::create_from_file(
                                                  "var/button_centre_roll.png");
    rollImageRight  = Gdk::Pixbuf::create_from_file(
                                                  "var/button_right_roll.png");

    button.reset(new Button("Hello, World!",
                            passiveImageLeft,
                            passiveImageCenter,
                            passiveImageRight,
                            rollImageLeft,
                            rollImageCenter,
                            rollImageRight));

    // create and set up the layout
    layout.reset(new Gtk::Table());
    layout->attach(*imageButton,    0, 1, 0, 1);
    layout->attach(*button,         0, 1, 1, 2);
    
    add(*layout);
    show_all();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
TestWindow :: ~TestWindow (void)                                    throw ()
{
}


