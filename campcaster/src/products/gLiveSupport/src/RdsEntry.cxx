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

#include "RdsEntry.h"


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
RdsEntry :: RdsEntry(Ptr<ResourceBundle>::Ref       bundle,
                     const Glib::ustring &          type,
                     int                            width)
                                                                    throw ()
          : LocalizedObject(bundle)
{
    this->type.reset(new const Glib::ustring(type));
    
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();

    checkBox = Gtk::manage(new Gtk::CheckButton());
    
    Gtk::Label *    label;
    Glib::ustring   labelKey = type + "rdsLabel";
    try {
        label = Gtk::manage(new Gtk::Label(*getResourceUstring(labelKey)));

    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    entryBin = Gtk::manage(wf->createEntryBin());
//    entryBin->? // set the size somehow
    
    pack_start(*checkBox,   Gtk::PACK_SHRINK, 5);
    pack_start(*label,      Gtk::PACK_SHRINK, 5);
    pack_start(*entryBin,   Gtk::PACK_EXPAND_WIDGET, 5);
}


/*------------------------------------------------------------------------------
 *  Set the state of the widget.
 *----------------------------------------------------------------------------*/
void
RdsEntry :: setOptions(bool                           enabled,
                       Ptr<const Glib::ustring>::Ref  value)        throw ()
{
    checkBox->set_active(enabled);
    entryBin->set_text(*value);
    
    checkBoxSaved = enabled;
    entryBinSaved = value;
}


/*------------------------------------------------------------------------------
 *  Save the changes made by the user.
 *----------------------------------------------------------------------------*/
bool
RdsEntry :: saveChanges(Ptr<GLiveSupport>::Ref      gLiveSupport)   throw ()
{
    bool            checkBoxNow = checkBox->get_active();
    Ptr<const Glib::ustring>::Ref
                    entryBinNow(new const Glib::ustring(entryBin->get_text()));
    
    if (!entryBinSaved || checkBoxNow != checkBoxSaved
                       || *entryBinNow != *entryBinSaved) {
        Ptr<OptionsContainer>::Ref      optionsContainer =
                                        gLiveSupport->getOptionsContainer();
        optionsContainer->setRdsOptions(type, entryBinNow, checkBoxNow);
        checkBoxSaved = checkBoxNow;
        entryBinSaved = entryBinNow;
        return true;
    } else {
        return false;
    }
}


/*------------------------------------------------------------------------------
 *  Clear the entries of the widget.
 *----------------------------------------------------------------------------*/
void
RdsEntry :: reset(void)                                             throw ()
{
    Ptr<const Glib::ustring>::Ref   empty(new const Glib::ustring(""));
    setOptions(false, empty);
}

