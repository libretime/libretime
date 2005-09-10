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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/ButtonImages.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/ButtonImages.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the passive left image.
 */
static const std::string    passiveLeftName = "left.png";

/**
 *  The name of the passive center image.
 */
static const std::string    passiveCenterName = "center.png";

/**
 *  The name of the passive right image.
 */
static const std::string    passiveRightName = "right.png";

/**
 *  The name of the rollover left image
 */
static const std::string    rollLeftName = "leftRoll.png";

/**
 *  The name of the rollover center image
 */
static const std::string    rollCenterName = "centerRoll.png";

/**
 *  The name of the rollover right image
 */
static const std::string    rollRightName = "rightRoll.png";

/**
 *  The name of the selected left image
 */
static const std::string    selectedLeftName = "leftSel.png";

/**
 *  The name of the selected center image
 */
static const std::string    selectedCenterName = "centerSel.png";

/**
 *  The name of the selected right image
 */
static const std::string    selectedRightName = "rightSel.png";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor, based on relative path
 *----------------------------------------------------------------------------*/
ButtonImages :: ButtonImages(const std::string      path)
                                                                throw ()
{
    try {
        passiveImageLeft    = loadImage(path, passiveLeftName);
        passiveImageCenter  = loadImage(path, passiveCenterName);
        passiveImageRight   = loadImage(path, passiveRightName);
        rollImageLeft       = loadImage(path, rollLeftName);
        rollImageCenter     = loadImage(path, rollCenterName);
        rollImageRight      = loadImage(path, rollRightName);
        selectedImageLeft   = loadImage(path, selectedLeftName);
        selectedImageCenter = loadImage(path, selectedCenterName);
        selectedImageRight  = loadImage(path, selectedRightName);
    } catch (std::invalid_argument &e) {
        // just ignore, it's not polite to through exceptions from constructors
    }
}


/*------------------------------------------------------------------------------
 *  Load an image
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
ButtonImages :: loadImage(const std::string     path,
                          const std::string     imageName)
                                                throw (std::invalid_argument)
{
    Glib::RefPtr<Gdk::Pixbuf>   image;

    if (!(image = Gdk::Pixbuf::create_from_file(path + imageName))) {
        throw std::invalid_argument("Missing " + image);
    }

    return image;
}

