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

#include "KeyboardShortcut.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string KeyboardShortcut::configElementName = "keyboardShortcut";

namespace {

/**
 *  The name of the attribute of the action element.
 */
const std::string           actionAttributeName   = "action";

/**
 *  The name of the attribute of the key element.
 */
const std::string           keyAttributeName      = "key";

/*
 *  The modifier keys we check against.
 *  The following modifiers are omitted, hence ignored: 
 *  Gdk::LOCK_MASK (caps lock),
 *  Gdk::MOD2_MASK (don't know what; always on on my computer),
 *  Gdk::MOD3_MASK (don't know what; always off on my computer),
 *  Gdk::BUTTONX_MASK (mouse buttons, X = 1..5).
 */
const Gdk::ModifierType     modifiersChecked = Gdk::SHIFT_MASK 
                                             | Gdk::CONTROL_MASK
                                             | Gdk::MOD1_MASK     // Alt
                                             | Gdk::MOD4_MASK     // Windows key
                                             | Gdk::MOD5_MASK;    // AltGr

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a keyboard shortcut element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
KeyboardShortcut :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementName) {
        throw std::invalid_argument("bad coniguration element "
                                    + element.get_name());
    }

    // set the action
    xmlpp::Attribute *      actionAttribute = element.get_attribute(
                                                        actionAttributeName);
    if (actionAttribute) {
        actionString.reset(new Glib::ustring(actionAttribute->get_value()));
        try {
            action = stringToAction(actionString);
        } catch (std::invalid_argument &e) {
            std::string eMsg = "Invalid action specification ";
            eMsg += *actionString;
            eMsg += ".";
            throw std::invalid_argument(eMsg);
        }
    } else {
        throw std::invalid_argument("missing " 
                                    + actionAttributeName + " attribute");
    }

    // set the key
    xmlpp::Attribute *      keyAttribute = element.get_attribute(
                                                        keyAttributeName);
    if (keyAttribute) {
        setKey(keyAttribute->get_value());
    } else {
        throw std::invalid_argument("missing " 
                                    + keyAttributeName + " attribute");
    }
}


/*------------------------------------------------------------------------------
 *  Set the shortcut key.
 *----------------------------------------------------------------------------*/
void
KeyboardShortcut :: setKey(const Glib::ustring &    keyName)
                                                throw (std::invalid_argument)
{
    shortcutKey = Gtk::AccelKey(keyName);
    if (shortcutKey.get_key() == 0) {
        throw std::invalid_argument("invalid shortcut key name");
    }
}


/*------------------------------------------------------------------------------
 *  Tell whether the given modifier-key pair triggers this action.
 *----------------------------------------------------------------------------*/
bool
KeyboardShortcut :: isTriggeredBy(Gdk::ModifierType     modifiers,
                                  guint                 key) const
                                                                    throw ()
{
    Gdk::ModifierType   myModifiers = modifiers & modifiersChecked;
    
    if (shortcutKey.get_mod() == myModifiers 
                                    && shortcutKey.get_key() == key) {
        return true;
    } else {
        return false;
    }
}


/*------------------------------------------------------------------------------
 *  Convert an action name string to an enumeration value.
 *----------------------------------------------------------------------------*/
KeyboardShortcut::Action
KeyboardShortcut :: stringToAction(Ptr<const Glib::ustring>::Ref    actionName)
                                                throw (std::invalid_argument)
{
    if (*actionName == "playAudio") {
        return playAudio;
    } else if (*actionName == "pauseAudio") {
        return pauseAudio;
    } else if (*actionName == "stopAudio") {
        return stopAudio;
    } else if (*actionName == "nextTrack") {
        return nextTrack;
    } else if (*actionName == "fadeOut") {
        return fadeOut;
    } else if (*actionName == "moveItemUp") {
        return moveItemUp;
    } else if (*actionName == "moveItemDown") {
        return moveItemDown;
    } else if (*actionName == "removeItem") {
        return removeItem;
    } else {
        throw std::invalid_argument("");
    }
}


/*------------------------------------------------------------------------------
 *  Convert a modifiers-key code pair to a user-readable string.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
KeyboardShortcut :: modifiedKeyToString(Gdk::ModifierType   modifiers,
                                        guint               key)
                                                                    throw ()
{
    Gtk::AccelKey            accelKey(key, modifiers & modifiersChecked);
    Ptr<Glib::ustring>::Ref  keyName(new Glib::ustring(accelKey.get_abbrev()));
    return keyName;
}

