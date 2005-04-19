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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/SearchWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Notebook.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/EntryBin.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "SearchWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
SearchWindow :: SearchWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                              Ptr<ResourceBundle>::Ref    bundle)
                                                                throw ()
          : WhiteWindow(WidgetFactory::searchWindowTitleImage,
                        Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle)
{
    this->gLiveSupport = gLiveSupport;
    treeModel = Gtk::ListStore::create(modelColumns);

    Gtk::VBox *     searchView = Gtk::manage(new Gtk::VBox);
    Gtk::VBox *     advancedSearchView = constructAdvancedSearchView();
    Gtk::VBox *     browseView = Gtk::manage(new Gtk::VBox);

    Notebook *      views = Gtk::manage(new Notebook);    
    views->appendPage(*searchView,          *getResourceUstring("searchTab"));
    views->appendPage(*advancedSearchView,  *getResourceUstring(
                                                        "advancedSearchTab"));
    views->appendPage(*browseView,          *getResourceUstring("browseTab"));

    add(*views);    

    // show
    set_name("searchWindow");
//    set_default_size(300, 300);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    
//  showContents();
    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
SearchWindow :: ~SearchWindow (void)                            throw ()
{
}


/*------------------------------------------------------------------------------
 *  Construct the advanced search view.
 *----------------------------------------------------------------------------*/
Gtk::VBox*
SearchWindow :: constructAdvancedSearchView(void)               throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    // the three main components of the window    
    Gtk::Box *      searchOptionsBox = Gtk::manage(new Gtk::HBox);
    Gtk::Box *      searchButtonBox = Gtk::manage(new Gtk::HButtonBox(
                                                        Gtk::BUTTONBOX_END ));
    ZebraTreeView * searchResults = Gtk::manage(wf->createTreeView(treeModel));

    // make a new box, and pack the main components into it
    Gtk::VBox *     view = Gtk::manage(new Gtk::VBox);
    view->pack_start(*searchOptionsBox, Gtk::PACK_SHRINK, 5);
    view->pack_start(*searchButtonBox,  Gtk::PACK_SHRINK, 5);
    view->pack_start(*searchResults,    Gtk::PACK_SHRINK, 5);

    // set up the search options box
    Gtk::Label *    searchByLabel = Gtk::manage(new Gtk::Label(
                                    *getResourceUstring("searchByTextLabel") ));
    ComboBoxText *  metadataType = Gtk::manage(wf->createComboBoxText());
    metadataType->append_text("Title");
    metadataType->append_text("Creator");
    metadataType->append_text("Length");
    metadataType->set_active_text("Title");
    ComboBoxText *  operatorType = Gtk::manage(wf->createComboBoxText());
    operatorType->append_text("contains");
    operatorType->append_text("equals");
    operatorType->append_text(">=");
    operatorType->append_text("<");
    operatorType->set_active_text("contains");
    EntryBin *      entryBin = Gtk::manage(wf->createEntryBin());
    searchOptionsBox->pack_start(*searchByLabel, Gtk::PACK_SHRINK, 5);
    searchOptionsBox->pack_start(*metadataType, Gtk::PACK_EXPAND_WIDGET, 5);
    searchOptionsBox->pack_start(*operatorType, Gtk::PACK_EXPAND_WIDGET, 5);
    searchOptionsBox->pack_start(*entryBin,     Gtk::PACK_EXPAND_WIDGET, 5);
    
    // set up the search button box
    Button *        searchButton = Gtk::manage(wf->createButton(
                                    *getResourceUstring("searchButtonLabel") ));
    searchButtonBox->pack_start(*searchButton, Gtk::PACK_SHRINK, 5);
    
    return view;
}









    
