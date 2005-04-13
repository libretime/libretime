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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/CornerBitmaps.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_CornerBitmaps_h
#define LiveSupport_Widgets_CornerBitmaps_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "gdkmm/bitmap.h"


namespace LiveSupport {
namespace Widgets {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A helper class to hold a set of corner bitmaps.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class CornerBitmaps
{
    public:
        /**
         *  The top left image.
         */
        Glib::RefPtr<Gdk::Bitmap>       topLeftBitmap;

        /**
         *  The top right image.
         */
        Glib::RefPtr<Gdk::Bitmap>       topRightBitmap;

        /**
         *  The bottom left image.
         */
        Glib::RefPtr<Gdk::Bitmap>       bottomLeftBitmap;

        /**
         *  The bottom right image.
         */
        Glib::RefPtr<Gdk::Bitmap>       bottomRightBitmap;

        /**
         *  The default constructor.
         */
        CornerBitmaps(void)             throw ()
        {
        }

        /**
         *  Constructor with image references.
         *  If any of the images is not available, the result is undefined.
         *
         *  @param topLeftBitmap the top left bitmap of the border
         *  @param topRightBitmap the top right bitmap of the border
         *  @param bottomLeftBitmap the bottom left bitmap of the border
         *  @param bottomRightBitmap the bottom right bitmap of the border
         */
        CornerBitmaps(Glib::RefPtr<Gdk::Bitmap>   topLeftBitmap,
                      Glib::RefPtr<Gdk::Bitmap>   topRightBitmap,
                      Glib::RefPtr<Gdk::Bitmap>   bottomLeftBitmap,
                      Glib::RefPtr<Gdk::Bitmap>   bottomRightBitmap)
                                                            throw ()
        {
            this->topLeftBitmap     = topLeftBitmap;
            this->topRightBitmap    = topRightBitmap;
            this->bottomLeftBitmap  = bottomLeftBitmap;
            this->bottomRightBitmap = bottomRightBitmap;
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~CornerBitmaps(void)            throw ()
        {
        }


};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_CornerBitmaps_h

