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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/ButtonImages.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_ButtonImages_h
#define LiveSupport_Widgets_ButtonImages_h

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
 *  A helper class to hold a set of images related to buttons.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class ButtonImages
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
         *  The passive left image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   passiveImageLeft;

        /**
         *  The passive center image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   passiveImageCenter;

        /**
         *  The passive right image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   passiveImageRight;

        /**
         *  The rollover left image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   rollImageLeft;

        /**
         *  The rollover center image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   rollImageCenter;

        /**
         *  The rollover right image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   rollImageRight;

        /**
         *  The selected left image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   selectedImageLeft;

        /**
         *  The selected center image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   selectedImageCenter;

        /**
         *  The selected right image for the button.
         */
        Glib::RefPtr<Gdk::Pixbuf>   selectedImageRight;

        /**
         *  The default constructor.
         */
        ButtonImages(void)                  throw ()
        {
        }

        /**
         *  Constructor with image references.
         *  If any of the images is not available, the result is undefined.
         *
         *  @param passiveImageLeft the passive left image
         *  @param passiveImageCenter the passive center image
         *  @param passiveImageRight the passive right image
         *  @param rollImageLeft the left rollover image
         *  @param rollImageCenter the center rollover image
         *  @param rollImageRight the right rollover image
         *  @param selectedImageLeft the left rollover image
         *  @param selectedImageCenter the center rollover image
         *  @param selectedImageRight the right rollover image
         */
        ButtonImages(Glib::RefPtr<Gdk::Pixbuf>   passiveImageLeft,
                     Glib::RefPtr<Gdk::Pixbuf>   passiveImageCenter,
                     Glib::RefPtr<Gdk::Pixbuf>   passiveImageRight,
                     Glib::RefPtr<Gdk::Pixbuf>   rollImageLeft,
                     Glib::RefPtr<Gdk::Pixbuf>   rollImageCenter,
                     Glib::RefPtr<Gdk::Pixbuf>   rollImageRight,
                     Glib::RefPtr<Gdk::Pixbuf>   selectedImageLeft,
                     Glib::RefPtr<Gdk::Pixbuf>   selectedImageCenter,
                     Glib::RefPtr<Gdk::Pixbuf>   selectedImageRight)
                                                            throw ()
        {
            this->passiveImageLeft      = passiveImageLeft;
            this->passiveImageCenter    = passiveImageCenter;
            this->passiveImageRight     = passiveImageRight;
            this->rollImageLeft         = rollImageLeft;
            this->rollImageCenter       = rollImageCenter;
            this->rollImageRight        = rollImageRight;
            this->selectedImageLeft     = selectedImageLeft;
            this->selectedImageCenter   = selectedImageCenter;
            this->selectedImageRight    = selectedImageRight;
        }

        /**
         *  Constructor based on a path, where all the images can be loaded
         *  from.
         *
         *  @param path the path where all the images can be loaded from.
         */
        ButtonImages(const std::string      path)           throw ();

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~ButtonImages(void)             throw ()
        {
        }

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_ButtonImages_h

