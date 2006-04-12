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
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/products/gLiveSupport/src/ExportPlaylistWindow.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/RadioButtons.h"

#include "ExportPlaylistWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the window, used by the keyboard shortcuts (or by the .gtkrc).
 *----------------------------------------------------------------------------*/
const Glib::ustring     windowName = "exportPlaylistWindow";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
ExportPlaylistWindow :: ExportPlaylistWindow (
                        Ptr<GLiveSupport>::Ref      gLiveSupport,
                        Ptr<ResourceBundle>::Ref    bundle,
                        Ptr<Playlist>::Ref          playlist)
                                                                    throw ()
          : GuiWindow(gLiveSupport,
                      bundle, 
                      "")
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    
    Gtk::Label *    playlistTitleLabel;
    Gtk::Label *    formatLabel;
    Button *        cancelButton;
    Button *        saveButton;
    try {
        set_title(*getResourceUstring("windowTitle"));
        playlistTitleLabel  = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("playlistTitleLabel")));
        formatLabel         = Gtk::manage(new Gtk::Label(
                                *getResourceUstring("formatLabel")));
        cancelButton        = Gtk::manage(wf->createButton(
                                *getResourceUstring("cancelButtonLabel")));
        saveButton          = Gtk::manage(wf->createButton(
                                *getResourceUstring("saveButtonLabel")));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    Gtk::Box *      playlistTitleBox = Gtk::manage(new Gtk::HBox);
    Gtk::Label *    playlistTitle    = Gtk::manage(new Gtk::Label(
                                                    *playlist->getTitle() ));
    playlistTitleBox->pack_start(*playlistTitleLabel, Gtk::PACK_SHRINK, 5);
    playlistTitleBox->pack_start(*playlistTitle,      Gtk::PACK_SHRINK, 5);
    
    RadioButtons *  formatButtons   = Gtk::manage(new RadioButtons);
    try {
        formatButtons->add(getResourceUstring("internalFormatName"));
        formatButtons->add(getResourceUstring("smilFormatName"));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    Gtk::Box *      formatBox = Gtk::manage(new Gtk::HBox);
    formatBox->pack_start(*formatLabel,   Gtk::PACK_SHRINK, 5);
    formatBox->pack_start(*formatButtons, Gtk::PACK_SHRINK, 5);
    
    Gtk::Box *      buttonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END, 5));
    buttonBox->pack_start(*cancelButton);
    buttonBox->pack_start(*saveButton);
    
    Gtk::Box *      extraSpace = Gtk::manage(new Gtk::HBox);
    Gtk::Label *    statusBar  = Gtk::manage(new Gtk::Label(""));
    
    Gtk::Box *      layout = Gtk::manage(new Gtk::VBox);
    layout->pack_start(*extraSpace,       Gtk::PACK_SHRINK, 5);
    layout->pack_start(*playlistTitleBox, Gtk::PACK_SHRINK, 5);
    layout->pack_start(*formatBox,        Gtk::PACK_SHRINK, 0);
    layout->pack_start(*statusBar,        Gtk::PACK_SHRINK, 10);
    layout->pack_start(*buttonBox,        Gtk::PACK_SHRINK, 0);

    add(*layout);
    
    set_name(windowName);
    show_all();
}

