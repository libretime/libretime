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
const std::string       WidgetFactory::configElementNameStr = "widgetFactory";

/*------------------------------------------------------------------------------
 *  The singleton instance of WidgetFactory
 *----------------------------------------------------------------------------*/
Ptr<WidgetFactory>::Ref WidgetFactory::singleton;

namespace {

/*------------------------------------------------------------------------------
 *  The name of the attribute to get the path for the widget images.
 *----------------------------------------------------------------------------*/
const std::string       pathAttrName = "path";

/*------------------------------------------------------------------------------
 *  The relative path for the standard button images.
 *----------------------------------------------------------------------------*/
const std::string       buttonPath = "button/";

/*------------------------------------------------------------------------------
 *  The name of the image for the audio clip icon.
 *----------------------------------------------------------------------------*/
const std::string       audioClipIconImageName = "icons/audioClipIcon.png";

/*------------------------------------------------------------------------------
 *  The name of the image for the playlist icon.
 *----------------------------------------------------------------------------*/
const std::string       playlistIconImageName = "icons/playlistIcon.png";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to WidgetFactory
 *----------------------------------------------------------------------------*/
Ptr<WidgetFactory>::Ref
WidgetFactory :: getInstance(void)                                  throw ()
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

    const xmlpp::Attribute    * attribute = 0;

    if (!(attribute = element.get_attribute(pathAttrName))) {
        throw std::invalid_argument("Missing path attribute");
    }
    path = attribute->get_value();

    imageTypePixbufs[WidgetConstants::audioClipIconImage]
                                    = loadImage(audioClipIconImageName);
    imageTypePixbufs[WidgetConstants::playlistIconImage]
                                    = loadImage(playlistIconImageName);
}


/*------------------------------------------------------------------------------
 *  Load an image
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
WidgetFactory :: loadImage(const std::string    imageName)
                                                throw (std::invalid_argument)
{
    Glib::RefPtr<Gdk::Pixbuf>   image;

    bool    success = true;
    try {
        image = Gdk::Pixbuf::create_from_file(path + imageName);
    } catch (Glib::FileError &e) {
        success = false;
    } catch (Gdk::PixbufError &e) {
        success = false;
    }
    
    if (!success || !image) {
        throw std::invalid_argument("Missing " + imageName);
    }

    return image;
}


/*------------------------------------------------------------------------------
 *  Return a Gdk::Pixbuf reference to a named image
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
WidgetFactory :: getPixbuf(WidgetConstants::ImageType  imageName)   throw ()
{
    return imageTypePixbufs[imageName];
}

