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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/widgets/src/MasterPanelBin.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


#include "LiveSupport/Widgets/MasterPanelBin.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
MasterPanelBin :: MasterPanelBin(
                        Colors::ColorName            backgroundColor,
                        Ptr<CornerImages>::Ref       cornerImages,
                        bool                         transparentBorder)
                                                                    throw ()
      : BlueBin(backgroundColor, cornerImages, transparentBorder)
{
    // generate the transparency mask bitmap for the bottom border
    Glib::RefPtr<Gdk::Pixmap>       pixmap;
    cornerImages->bottomImage->render_pixmap_and_mask(pixmap,
                                                      bottomBitmap,
                                                      100);
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
MasterPanelBin :: ~MasterPanelBin(void)                            throw ()
{
    on_remove(child);
}


/*------------------------------------------------------------------------------
 *  Handle the size request event.
 *----------------------------------------------------------------------------*/
void
MasterPanelBin :: on_size_request(Gtk::Requisition* requisition)       throw ()
{
    *requisition = Gtk::Requisition();

    int     width  = 0;
    int     height = 0;

    if (child) {
        Gtk::Requisition  childRequisition = child->size_request();
        width  = childRequisition.width;
        height = childRequisition.height;
    }

    requisition->width  = width;
    requisition->height = height
                        + cornerImages->bottomImage->get_height();
}


/*------------------------------------------------------------------------------
 *  Handle the size allocate event.
 *  We will not be given heights or widths less than we have requested,
 *  though we might get more.
 *----------------------------------------------------------------------------*/
void
MasterPanelBin :: on_size_allocate(Gtk::Allocation& allocation)        throw ()
{
    set_allocation(allocation);

    if (gdkWindow) {
        gdkWindow->move_resize( allocation.get_x(), 
                                allocation.get_y(), 
                                allocation.get_width(), 
                                allocation.get_height() );
    }

    if (child) {
        Gtk::Allocation     childAlloc;

        childAlloc.set_x(0);
        childAlloc.set_y(0);
        childAlloc.set_width(allocation.get_width());
        childAlloc.set_height(allocation.get_height()
                            - cornerImages->bottomImage->get_height());

        child->size_allocate(childAlloc);
    }

    Gtk::Bin::on_size_allocate(allocation);
}


/*------------------------------------------------------------------------------
 *  Handle the expose event.
 *----------------------------------------------------------------------------*/
bool
MasterPanelBin :: on_expose_event(GdkEventExpose* event)           throw ()
{
    if (event->count > 0) {
        return false;
    }  

    int width  = get_width();
    int height = get_height();

    if (transparentBorder) {
        unsigned int    bitmapSize = 1 + (width * height);

        char  * bitmapData = new char[bitmapSize];
        memset(bitmapData, 0xff, bitmapSize);

        Glib::RefPtr<Gdk::Bitmap>    mask = Gdk::Bitmap::create(bitmapData,
                                                                width,
                                                                height);
        delete[] bitmapData;

        Glib::RefPtr<Gdk::GC>       gc = Gdk::GC::create(mask);
        Glib::RefPtr<Gdk::Bitmap>   borderMask;

        mask->draw_drawable(gc, cornerBitmaps->topLeftBitmap, 0, 0, 0, 0);

        mask->draw_drawable(gc, cornerBitmaps->topRightBitmap, 0, 0,
                            width - cornerImages->topRightImage->get_width(),
                            0);

        mask->draw_drawable(gc, cornerBitmaps->bottomLeftBitmap, 0, 0,
                          0,
                          height - cornerImages->bottomLeftImage->get_height());

        mask->draw_drawable(gc, cornerBitmaps->bottomRightBitmap, 0, 0,
                         width - cornerImages->bottomRightImage->get_width(),
                         height - cornerImages->bottomRightImage->get_height());

        get_parent_window()->shape_combine_mask(mask, 0, 0);
    }

    if (gdkWindow) {
        gdkWindow->clear();

        // draw the bottom side as many times as necessary
        for (int x = 0; x < width;
                        x += cornerImages->bottomImage->get_width()) {
            renderImage(cornerImages->bottomImage,
                        x,
                        height - cornerImages->bottomImage->get_height());
        }
    }

    Gtk::Bin::on_expose_event(event);

    return false;
}

