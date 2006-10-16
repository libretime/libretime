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
#ifndef LiveSupport_Widgets_Colors_h
#define LiveSupport_Widgets_Colors_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <map>

#include "gdkmm/color.h"
#include "gdkmm/colormap.h"


namespace LiveSupport {
namespace Widgets {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A helper class to hold all the standard colors used by the LiveSupport GUI.
 *
 *  The definitions of the colors can be found in doc/gui/styleguide.pdf;
 *  the last two colors were taken from doc/gui/designs/livemode.gif.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class Colors
{
    public:
        /**
         *  The names of the colors.
         */
        typedef enum  { White, Black, 
                        LightBlue, BrightBlue, Blue, DarkBlue, 
                        Gray, SlateGray, MediumBlueGray, DarkGray, 
                        Yellow, Orange, Red,
                        MasterPanelCenterBlue, LiveModeRowBlue,
                        WindowBackground = White }              ColorName;

    private:
        /**
         *  The vector holding the colors.
         */
        static std::map<ColorName, Gdk::Color>  colors;

        /**
         *  This loads the colors.
         */
        static void
        initialize(void)                                        throw ();

        /**
         *  Whether we have been initialized yet.
         */
        bool
        static initialized;

    public:
        /**
         *  Get a color by its name.
         */
        static const Gdk::Color&
        getColor(const ColorName&)                              throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_Colors_h

