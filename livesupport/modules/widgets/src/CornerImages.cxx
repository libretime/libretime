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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/CornerImages.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/CornerImages.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the top left image.
 */
static const std::string    topLeftName = "topLeft.png";

/**
 *  The name of the left image.
 */
static const std::string    leftName = "left.png";

/**
 *  The name of the top image.
 */
static const std::string    topName = "top.png";

/**
 *  The name of the top right image.
 */
static const std::string    topRightName = "topRight.png";

/**
 *  The name of the right image.
 */
static const std::string    rightName = "right.png";

/**
 *  The name of the bottom left image.
 */
static const std::string    bottomLeftName = "bottomLeft.png";

/**
 *  The name of the bottom image.
 */
static const std::string    bottomName = "bottom.png";

/**
 *  The name of the bottom right image.
 */
static const std::string    bottomRightName = "bottomRight.png";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor, based on relative path
 *----------------------------------------------------------------------------*/
CornerImages :: CornerImages(const std::string      path)
                                                                throw ()
{
    try {
        topLeftImage      = loadImage(path, topLeftName);
        leftImage         = loadImage(path, leftName);
        topImage          = loadImage(path, topName);
        topRightImage     = loadImage(path, topRightName);
        rightImage        = loadImage(path, rightName);
        bottomLeftImage   = loadImage(path, bottomLeftName);
        bottomImage       = loadImage(path, bottomName);
        bottomRightImage  = loadImage(path, bottomRightName);
    } catch (std::invalid_argument &e) {
        // just ignore, it's not polite to through exceptions from constructors
    }
}


/*------------------------------------------------------------------------------
 *  Load an image
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
CornerImages :: loadImage(const std::string     path,
                          const std::string     imageName)
                                                throw (std::invalid_argument)
{
    Glib::RefPtr<Gdk::Pixbuf>   image;

    if (!(image = Gdk::Pixbuf::create_from_file(path + imageName))) {
        throw std::invalid_argument("Missing " + image);
    }

    return image;
}

