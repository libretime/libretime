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
    Version  : $Revision: 1.12 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/TestWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"
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
          : WhiteWindow("test window",
                        0xffffff,
                        WidgetFactory::getInstance()->getWhiteWindowCorners())
{
    Ptr<WidgetFactory>::Ref  widgetFactory = WidgetFactory::getInstance();

    // init the imageButton
    imageButton = Gtk::manage(
                    widgetFactory->createButton(WidgetFactory::deleteButton));

    // create a button
    button = Gtk::manage(widgetFactory->createButton("Hello, World!"));

    // create a combo box
    comboBoxText = Gtk::manage(widgetFactory->createComboBoxText());
    comboBoxText->append_text("item1");
    comboBoxText->append_text("long item2");
    comboBoxText->append_text("very very very long item3");
    comboBoxText->set_active_text("item2");

    // create a text entry, ant put it inside a blue bin
    entryBin = Gtk::manage(widgetFactory->createEntryBin());
    entry    = entryBin->getEntry();

    // create a notebook
    notebook = Gtk::manage(new Notebook());
    notebook->appendPage(*button, "first page");
    notebook->appendPage(*comboBoxText, "second page");
    notebook->appendPage(*entryBin, "third page");

    // create a blue container
    blueBin = Gtk::manage(widgetFactory->createDarkBlueBin());

    // create and set up the layout
    layout = Gtk::manage(new Gtk::Table());
    layout->attach(*imageButton,    0, 1, 0, 1);
    layout->attach(*notebook,       0, 1, 1, 2);
    blueBin->add(*layout);
    add(*blueBin);
    show_all();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
TestWindow :: ~TestWindow (void)                                    throw ()
{
}


