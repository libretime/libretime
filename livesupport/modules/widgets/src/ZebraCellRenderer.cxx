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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/ZebraCellRenderer.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/ZebraCellRenderer.h"


using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ZebraCellRenderer::ZebraCellRenderer()                          throw ()
:
  Glib::ObjectBase      (typeid(ZebraCellRenderer)),
  Gtk::CellRendererText ()
{
//    std::cerr << "### constructor\n";
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
ZebraCellRenderer::~ZebraCellRenderer()                         throw ()
{
//    std::cerr << "### destructor\n";
}


/*------------------------------------------------------------------------------
 *  Calculate the size of the cell.
 *----------------------------------------------------------------------------*/
void 
ZebraCellRenderer::get_size_vfunc(Gtk::Widget& widget,
                                  const Gdk::Rectangle* cell_area,
                                  int* x_offset, int* y_offset,
                                  int* width,    int* height) const
                                                                throw ()
{
/*
    std::cerr << "### get_size_vfunc():"
        << (cell_area ? cell_area->get_x() : -1) << ", "
        << (cell_area ? cell_area->get_y() : -1) << ";  "
        << (cell_area ? cell_area->get_width() : -1) << ", "
        << (cell_area ? cell_area->get_height() : -1) << ";  "
        << (x_offset ? *x_offset : -1) << ", "
        << (y_offset ? *y_offset : -1) << " --- "
        << (width ? *width : -1) << ", "
        << (height ? *height : -1) << "\n";
*/
        // call the parent method
    Gtk::CellRendererText::get_size_vfunc(widget, cell_area,
                                          x_offset, y_offset,
                                          width, height);
/*
    std::cerr << "... done: "
        << (cell_area ? cell_area->get_x() : -1) << ", "
        << (cell_area ? cell_area->get_y() : -1) << ";  "
        << (cell_area ? cell_area->get_width() : -1) << ", "
        << (cell_area ? cell_area->get_height() : -1) << ";  "
        << (x_offset ? *x_offset : -1) << ", "
        << (y_offset ? *y_offset : -1) << " --- "
        << (width ? *width : -1) << ", "
        << (height ? *height : -1) << "\n";
*/
/*
  enum { TOGGLE_WIDTH = 12 };

  const int calc_width  = property_xpad() * 2 + TOGGLE_WIDTH;
  const int calc_height = property_ypad() * 2 + TOGGLE_WIDTH;

  if(width)
    *width = calc_width;

  if(height)
    *height = calc_height;

  if(cell_area)
  {
    if(x_offset)
    {
      *x_offset = int(property_xalign() * (cell_area->get_width() - calc_width));
      *x_offset = std::max(0, *x_offset);
    }

    if(y_offset)
    {
      *y_offset = int(property_yalign() * (cell_area->get_height() - calc_height));
      *y_offset = std::max(0, *y_offset);
    }
  }
*/
}


/*------------------------------------------------------------------------------
 *  Draw the cell.
 *----------------------------------------------------------------------------*/
void
ZebraCellRenderer::render_vfunc(const Glib::RefPtr<Gdk::Drawable>& window,
                                Gtk::Widget&            widget,
                                const Gdk::Rectangle&   background_area,
                                const Gdk::Rectangle&   cell_area,
                                const Gdk::Rectangle&   expose_area,
                                Gtk::CellRendererState  flags)
                                                                throw ()
{
/*
    std::cerr << "### render_vfunc(): "
        << widget.get_name() << " --- "
        << background_area.get_x() << ", "
        << background_area.get_y() << ";  "
        << background_area.get_width() << ", "
        << background_area.get_height() << " -- "
        << cell_area.get_x() << ", "
        << cell_area.get_y() << ";  "
        << cell_area.get_width() << ", "
        << cell_area.get_height() << ";  "
        << expose_area.get_x() << " -- "
        << expose_area.get_y() << ";  "
        << expose_area.get_width() << ", "
        << expose_area.get_height() << " --  "
        << flags << "\n";
*/
    // call the parent function
    Gtk::CellRendererText::render_vfunc(window, widget, background_area,
                                        cell_area, expose_area, flags);
/*
  const unsigned int cell_xpad = property_xpad();
  const unsigned int cell_ypad = property_ypad();

  int x_offset = 0, y_offset = 0, width = 0, height = 0;
  get_size(widget, cell_area, x_offset, y_offset, width, height);

  width  -= cell_xpad * 2;
  height -= cell_ypad * 2;

  if(width <= 0 || height <= 0)
    return;

  Gtk::StateType state = Gtk::STATE_INSENSITIVE;

  if(property_activatable_)
    state = Gtk::STATE_NORMAL;

  if((flags & Gtk::CELL_RENDERER_SELECTED) != 0)
    state = (widget.has_focus()) ? Gtk::STATE_SELECTED : Gtk::STATE_ACTIVE;

  const Gtk::ShadowType shadow = (property_active_) ? Gtk::SHADOW_IN : Gtk::SHADOW_OUT;

  //Cast the drawable to a Window. TODO: Maybe paint_option() should take a Drawable? murrayc.
  Glib::RefPtr<Gdk::Window> window_casted = Glib::RefPtr<Gdk::Window>::cast_dynamic<>(window);
  if(window_casted)
  {
    if(property_radio_)
    {
      widget.get_style()->paint_option(
          window_casted, state, shadow,
          cell_area, widget, "cellradio",
          cell_area.get_x() + x_offset + cell_xpad,
          cell_area.get_y() + y_offset + cell_ypad,
          width - 1, height - 1);
    }
    else
    {
      widget.get_style()->paint_check(
          window_casted, state, shadow,
          cell_area, widget, "cellcheck",
          cell_area.get_x() + x_offset + cell_xpad,
          cell_area.get_y() + y_offset + cell_ypad,
          width - 1, height - 1);
    }
  }
*/
}


/*------------------------------------------------------------------------------
 *  The user clicked on the cell.
 *----------------------------------------------------------------------------*/
bool ZebraCellRenderer::activate_vfunc(GdkEvent*              event,
                                       Gtk::Widget&           widget,
                                       const Glib::ustring&   path,
                                       const Gdk::Rectangle&  background_area,
                                       const Gdk::Rectangle&  cell_area,
                                       Gtk::CellRendererState flags)
                                                                throw ()
{
/*
    std::cerr << "### activate_vfunc(): "
        << widget.get_name() << ", "
        << path << ", "
//        << background_area << ", "
//        << cell_area << ", "
        << flags << "\n";
    // call the parent function
    Gtk::CellRendererText::activate_vfunc(event, widget, path, 
                                          background_area, cell_area, flags);
    std::cerr << "... done.\n";
*/
/*
  if(property_activatable_)
  {
    signal_toggled_(path);
    return true;
  }

  return false;
*/
}
