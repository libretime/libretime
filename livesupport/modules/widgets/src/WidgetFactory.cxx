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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/WidgetFactory.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/WidgetFactory.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string WidgetFactory::configElementNameStr = "widgetFactory";

/*------------------------------------------------------------------------------
 *  The singleton instance of WidgetFactory
 *----------------------------------------------------------------------------*/
Ptr<WidgetFactory>::Ref WidgetFactory::singleton;

/**
 *  The name of the attribute to get the path for the widget images.
 */
static const std::string    pathAttrName = "path";

/**
 *  The name of the left passive image for the button.
 */
static const std::string    buttonPassiveLeftName = "button_left.png";

/**
 *  The name of the center passive image for the button.
 */
static const std::string    buttonPassiveCenterName = "button_centre.png";

/**
 *  The name of the right passive image for the button.
 */
static const std::string    buttonPassiveRightName = "button_right.png";

/**
 *  The name of the left rollover image for the button.
 */
static const std::string    buttonRollLeftName = "button_left_roll.png";

/**
 *  The name of the center rollover image for the button.
 */
static const std::string    buttonRollCenterName = "button_centre_roll.png";

/**
 *  The name of the right rollover image for the button.
 */
static const std::string    buttonRollRightName = "button_right_roll.png";

/**
 *  The name of the top left image for BlueBin.
 */
static const std::string    blueBinTopLeftName = "corner_topleft.png";

/**
 *  The name of the left image for BlueBin.
 */
static const std::string    blueBinLeftName = "corner_leftside.png";

/**
 *  The name of the top image for BlueBin.
 */
static const std::string    blueBinTopName = "corner_topcentre.png";

/**
 *  The name of the top right image for BlueBin.
 */
static const std::string    blueBinTopRightName = "corner_topright.png";

/**
 *  The name of the right image for BlueBin.
 */
static const std::string    blueBinRightName = "corner_rightside.png";

/**
 *  The name of the bottom left image for BlueBin.
 */
static const std::string    blueBinBottomLeftName = "corner_botleft.png";

/**
 *  The name of the bottom image for BlueBin.
 */
static const std::string    blueBinBottomName = "corner_botcentre.png";

/**
 *  The name of the bottom right image for BlueBin.
 */
static const std::string    blueBinBottomRightName = "corner_botright.png";

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to WidgetFactory
 *----------------------------------------------------------------------------*/
Ptr<WidgetFactory>::Ref
WidgetFactory :: getInstance(void)                   throw ()
{
    if (!singleton.get()) {
        singleton.reset(new WidgetFactory());
    }

    return singleton;
}


/*------------------------------------------------------------------------------
 *  Configure the widget factory.
 *----------------------------------------------------------------------------*/
void
WidgetFactory :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(pathAttrName))) {
        throw std::invalid_argument("Missing path attribute");
    }
    path = attribute->get_value();

    // load the button images, and check if all exist
    buttonPassiveImageLeft   = loadImage(buttonPassiveLeftName);
    buttonPassiveImageCenter = loadImage(buttonPassiveCenterName);
    buttonPassiveImageRight  = loadImage(buttonPassiveRightName);
    buttonRollImageLeft      = loadImage(buttonRollLeftName);
    buttonRollImageCenter    = loadImage(buttonRollCenterName);
    buttonRollImageRight     = loadImage(buttonRollRightName);

    // load the blue bin images.
    blueBinTopLeftImage     = loadImage(blueBinTopLeftName);
    blueBinLeftImage        = loadImage(blueBinLeftName);
    blueBinTopImage         = loadImage(blueBinTopName);
    blueBinTopRightImage    = loadImage(blueBinTopRightName);
    blueBinRightImage       = loadImage(blueBinRightName);
    blueBinBottomLeftImage  = loadImage(blueBinBottomLeftName);
    blueBinBottomImage      = loadImage(blueBinBottomName);
    blueBinBottomRightImage = loadImage(blueBinBottomRightName);

}


/*------------------------------------------------------------------------------
 *  Load an image
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
WidgetFactory :: loadImage(const std::string    imageName)
                                                throw (std::invalid_argument)
{
    Glib::RefPtr<Gdk::Pixbuf>   image;

    if (!(image = Gdk::Pixbuf::create_from_file(path + imageName))) {
        throw std::invalid_argument("Missing " + image);
    }

    return image;
}


/*------------------------------------------------------------------------------
 *  Create a button
 *----------------------------------------------------------------------------*/
Ptr<Button>::Ref
WidgetFactory :: createButton(const Glib::ustring & label)      throw ()
{
    Ptr<Button>::Ref    button(new Button(label,
                                          buttonPassiveImageLeft,
                                          buttonPassiveImageCenter,
                                          buttonPassiveImageRight,
                                          buttonRollImageLeft,
                                          buttonRollImageCenter,
                                          buttonRollImageRight));

    return button;
}


/*------------------------------------------------------------------------------
 *  Create a blue bin
 *----------------------------------------------------------------------------*/
Ptr<BlueBin>::Ref
WidgetFactory :: createBlueBin(void)                            throw ()
{
    Ptr<BlueBin>::Ref   blueBin(new BlueBin(blueBinTopLeftImage,
                                            blueBinLeftImage,
                                            blueBinTopImage,
                                            blueBinTopRightImage,
                                            blueBinRightImage,
                                            blueBinBottomLeftImage,
                                            blueBinBottomImage,
                                            blueBinBottomRightImage));

    return blueBin;
}


