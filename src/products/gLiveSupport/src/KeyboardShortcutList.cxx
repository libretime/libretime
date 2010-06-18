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

#include "KeyboardShortcutList.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
const std::string           KeyboardShortcutList::configElementName
                                                = "keyboardShortcutList";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a metadata type container element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
KeyboardShortcutList :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementName) {
        throw std::invalid_argument("bad coniguration element "
                                  + element.get_name());
    }
    
    xmlpp::Node::NodeList   nodes = element.get_children(
                            KeyboardShortcutContainer::getConfigElementName());
    xmlpp::Node::NodeList::const_iterator   it = nodes.begin();
    while (it != nodes.end()) {
        Ptr<KeyboardShortcutContainer>::Ref ksc(new KeyboardShortcutContainer);
        ksc->configure(*((const xmlpp::Element*) *it));
        containerList.push_back(ksc);
        ++it;
    }
}

/*------------------------------------------------------------------------------
 *  Find the action triggered by the given key in the given window.
 *----------------------------------------------------------------------------*/
KeyboardShortcut::Action
KeyboardShortcutList :: findAction(const Glib::ustring &    windowName,
                                   Gdk::ModifierType        modifiers,
                                   guint                    key) const
                                                                    throw ()
{
    for (iterator it = begin(); it != end(); ++it) {
        Ptr<const KeyboardShortcutContainer>::Ref     ksc = *it;
        if (*ksc->getWindowName() == windowName) {
            return ksc->findAction(modifiers, key);
        }
    }

    return KeyboardShortcut::noAction;
}

