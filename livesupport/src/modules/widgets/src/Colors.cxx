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
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "gtkmm/widget.h"

#include "LiveSupport/Widgets/Colors.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The vector holding the colors.
 */
std::map<Colors::ColorName, Gdk::Color> Colors :: colors;

/**
 *  Clear the "initialized" flag.
 */
bool                                    Colors :: initialized = false;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Load the colors.
 *----------------------------------------------------------------------------*/
void
Colors :: initialize(void)                                      throw ()
{
    Gdk::Color  whiteColor          ("#ffffff");
    Gdk::Color  blackColor          ("#000000");
    Gdk::Color  lightBlueColor      ("#cfdee7");
    Gdk::Color  brightBlueColor     ("#6fb0ff");
    Gdk::Color  blueColor           ("#9ebadb");
    Gdk::Color  darkBlueColor       ("#688597");
    Gdk::Color  grayColor           ("#eaeaea");
    Gdk::Color  slateGrayColor      ("#c7cdd3");
    Gdk::Color  mediumBlueGrayColor ("#97bacf");
    Gdk::Color  darkGrayColor       ("#5a5a5a");
    Gdk::Color  orangeColor         ("#ff4b00");
    Gdk::Color  masterPanelCenterBlueColor  ("#99cdff");
    Gdk::Color  liveModeRowBlueColor        ("#cde0f1");

    Glib::RefPtr<Gdk::Colormap> colormap = Gtk::Widget::get_default_colormap();
    colormap->alloc_color(whiteColor);
    colormap->alloc_color(blackColor);
    colormap->alloc_color(lightBlueColor);
    colormap->alloc_color(brightBlueColor);
    colormap->alloc_color(blueColor);
    colormap->alloc_color(darkBlueColor);
    colormap->alloc_color(grayColor);
    colormap->alloc_color(slateGrayColor);
    colormap->alloc_color(mediumBlueGrayColor);
    colormap->alloc_color(darkGrayColor);
    colormap->alloc_color(orangeColor);
    colormap->alloc_color(masterPanelCenterBlueColor);
    colormap->alloc_color(liveModeRowBlueColor);

    colors[White]           = whiteColor;
    colors[Black]           = blackColor;
    colors[LightBlue]       = lightBlueColor;
    colors[BrightBlue]      = brightBlueColor;
    colors[Blue]            = blueColor;
    colors[DarkBlue]        = darkBlueColor;
    colors[Gray]            = grayColor;
    colors[SlateGray]       = slateGrayColor;
    colors[MediumBlueGray]  = mediumBlueGrayColor;
    colors[DarkGray]        = darkGrayColor;
    colors[Orange]          = orangeColor;
    colors[MasterPanelCenterBlue]   = masterPanelCenterBlueColor;
    colors[LiveModeRowBlue]         = liveModeRowBlueColor;
    
    initialized = true;
}


/*------------------------------------------------------------------------------
 *  Get a color by its name.
 *----------------------------------------------------------------------------*/
const Gdk::Color&
Colors :: getColor(const ColorName&     name)                   throw ()
{
    if (!initialized) {
        initialize();
    }
    
    return colors[name];
}

