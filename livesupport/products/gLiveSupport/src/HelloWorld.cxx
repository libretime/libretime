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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/HelloWorld.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "HelloWorld.h"


using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The Hello, World! string
 */
static std::string      helloWorld("Hello, World!");


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
HelloWorld :: HelloWorld (void)                         throw ()
                : button(helloWorld)
{
    // Sets the border width of the window.
    set_border_width(10);

    // Register the signal handler for the button getting clicked.
    button.signal_clicked().connect(slot(*this, &HelloWorld::onButtonClicked));

    // This packs the button into the Window (a container).
    add(button);

    // The final step is to display this newly created widget...
    button.show();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
HelloWorld :: ~HelloWorld (void)                        throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for the button getting clicked.
 *----------------------------------------------------------------------------*/
void
HelloWorld :: onButtonClicked (void)                    throw ()
{
    std::cout << helloWorld << std::endl;
}

