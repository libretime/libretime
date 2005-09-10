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
#ifndef LiveSupport_Widgets_CornerImages_h
#define LiveSupport_Widgets_CornerImages_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "gdkmm/pixbuf.h"


namespace LiveSupport {
namespace Widgets {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A helper class to hold a set of corner images.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class CornerImages
{
    private:
        /**
         *  Load an image relative the path, and signal error if not found.
         *
         *  @param path the path to find the image for.
         *  @param imageName the name of the image, relative to path
         *  @return the loaded image
         *  @exception std::invalid_argument if the image was not found
         */
        Glib::RefPtr<Gdk::Pixbuf>
        loadImage(const std::string     path,
                  const std::string     imageName)
                                                throw (std::invalid_argument);


    public:
        /**
         *  The top left image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       topLeftImage;

        /**
         *  The left image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       leftImage;

        /**
         *  The top image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       topImage;

        /**
         *  The top right image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       topRightImage;

        /**
         *  The right image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       rightImage;

        /**
         *  The bottom left image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       bottomLeftImage;

        /**
         *  The bottom image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       bottomImage;

        /**
         *  The bottom right image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       bottomRightImage;

        /**
         *  The default constructor.
         */
        CornerImages(void)              throw ()
        {
        }

        /**
         *  Constructor with image references.
         *  If any of the images is not available, the result is undefined.
         *
         *  @param topLeftImage the top left image of the border
         *  @param leftImage the left image of the border
         *  @param topImage the top image of the border
         *  @param topRightImage the top right image of the border
         *  @param rightImage the right image of the border
         *  @param bottomLeftImage the bottom left image of the border
         *  @param bottomImage the bottom image of the border
         *  @param bottomRightImage the bottom right image of the border
         */
        CornerImages(Glib::RefPtr<Gdk::Pixbuf>   topLeftImage,
                     Glib::RefPtr<Gdk::Pixbuf>   leftImage,
                     Glib::RefPtr<Gdk::Pixbuf>   topImage,
                     Glib::RefPtr<Gdk::Pixbuf>   topRightImage,
                     Glib::RefPtr<Gdk::Pixbuf>   rightImage,
                     Glib::RefPtr<Gdk::Pixbuf>   bottomLeftImage,
                     Glib::RefPtr<Gdk::Pixbuf>   bottomImage,
                     Glib::RefPtr<Gdk::Pixbuf>   bottomRightImage)
                                                            throw ()
        {
            this->topLeftImage      = topLeftImage;
            this->leftImage         = leftImage;
            this->topImage          = topImage;
            this->topRightImage     = topRightImage;
            this->rightImage        = rightImage;
            this->bottomLeftImage   = bottomLeftImage;
            this->bottomImage       = bottomImage;
            this->bottomRightImage  = bottomRightImage;
        }

        /**
         *  Constructor based on a path, where all the images can be loaded
         *  from.
         *
         *  @param path the path where all the images can be loaded from.
         */
        CornerImages(const std::string      path)           throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~CornerImages(void)             throw ()
        {
        }


};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_CornerImages_h

