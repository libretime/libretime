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

#include "KeyboardShortcutContainer.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
const std::string           KeyboardShortcutContainer::configElementName
                                                = "keyboardShortcutContainer";
namespace {

/**
 *  The name of the window name sub-element.
 */
const std::string           windowNameAttributeName = "windowName";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a metadata type container element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
KeyboardShortcutContainer :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementName) {
        throw std::invalid_argument("bad coniguration element "
                                  + element.get_name());
    }

    xmlpp::Node::NodeList childNodes = element.get_children(
                                    KeyboardShortcut::getConfigElementName());
    xmlpp::Node::NodeList::const_iterator it = childNodes.begin();

    while (it != childNodes.end()) {
        const xmlpp::Element *      keyboardShortcutElement 
                                    = dynamic_cast<const xmlpp::Element*> (*it);
                                    
        Ptr<KeyboardShortcut>::Ref  keyboardShortcut(new KeyboardShortcut);
        keyboardShortcut->configure(*keyboardShortcutElement);
        
        shortcutList.push_back(keyboardShortcut);
        ++it;
    }
    
    xmlpp::Attribute *      windowNameAttr = element.get_attribute(
                                                    windowNameAttributeName);
    if (windowNameAttr) {
        windowName.reset(new const Glib::ustring(windowNameAttr->get_value()));
    } else {
        throw std::invalid_argument("missing windowName attribute");
    }
}

/*------------------------------------------------------------------------------
 *  Return the action triggered by the given key.
 *----------------------------------------------------------------------------*/
KeyboardShortcut::Action
KeyboardShortcutContainer :: findAction(Gdk::ModifierType   modifiers, 
                                        guint               key) const
                                                                    throw ()
{
    ShortcutListType::const_iterator    it = shortcutList.begin();
    
    while (it != shortcutList.end()) {
        Ptr<const KeyboardShortcut>::Ref    shortcut = *it;
        if (shortcut->isTriggeredBy(modifiers, key)) {
            return shortcut->getAction();
        }
        ++it;
    }
    
    return KeyboardShortcut::noAction;
}

