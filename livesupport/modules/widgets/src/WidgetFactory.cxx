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
    Version  : $Revision: 1.4 $
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
static const std::string    buttonPassiveLeftName = "button/left.png";

/**
 *  The name of the center passive image for the button.
 */
static const std::string    buttonPassiveCenterName = "button/center.png";

/**
 *  The name of the right passive image for the button.
 */
static const std::string    buttonPassiveRightName = "button/right.png";

/**
 *  The name of the left rollover image for the button.
 */
static const std::string    buttonRollLeftName = "button/leftRoll.png";

/**
 *  The name of the center rollover image for the button.
 */
static const std::string    buttonRollCenterName = "button/centerRoll.png";

/**
 *  The name of the right rollover image for the button.
 */
static const std::string    buttonRollRightName = "button/rightRoll.png";

/**
 *  The relative path for the blue bin images.
 */
static const std::string    blueBinPath = "blueBin/";

/**
 *  The relative path for the dark blue bin images.
 */
static const std::string    darkBlueBinPath = "darkBlueBin/";

/**
 *  The relative path for the white window images.
 */
static const std::string    whiteWindowPath = "whiteWindow/";

/**
 *  The name of the passive image for the delete button.
 */
static const std::string    deleteButtonPassiveName = "imageButton/delete.png";

/**
 *  The name of the rollover image for the delete button.
 */
static const std::string    deleteButtonRollName = "imageButton/deleteRoll.png";


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

    // load the images for the bins
    blueBinImages.reset(new CornerImages(path + blueBinPath));
    darkBlueBinImages.reset(new CornerImages(path + darkBlueBinPath));

    // load the white window corner images
    whiteWindowImages.reset(new CornerImages(path + whiteWindowPath));
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
    Ptr<BlueBin>::Ref   blueBin(new BlueBin(0xcfdee7, blueBinImages));

    return blueBin;
}


/*------------------------------------------------------------------------------
 *  Create a dark blue bin
 *----------------------------------------------------------------------------*/
Ptr<BlueBin>::Ref
WidgetFactory :: createDarkBlueBin(void)                        throw ()
{
    Ptr<BlueBin>::Ref   blueBin(new BlueBin(0x99cdff, darkBlueBinImages));

    return blueBin;
}


/*------------------------------------------------------------------------------
 *  Create a stock button
 *----------------------------------------------------------------------------*/
Ptr<ImageButton>::Ref
WidgetFactory :: createButton(ButtonType    type)               throw ()
{
    Glib::RefPtr<Gdk::Pixbuf>   passiveImage;
    Glib::RefPtr<Gdk::Pixbuf>   rollImage;

    switch (type) {
        case deleteButton:
        default:
            passiveImage = loadImage(deleteButtonPassiveName);
            rollImage    = loadImage(deleteButtonRollName);
    }

    Ptr<ImageButton>::Ref   button(new ImageButton(passiveImage, rollImage));

    return button;
}

