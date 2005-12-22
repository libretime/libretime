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

/**
 *  The name of the attribute of the action element.
 */
static const std::string    actionElementName   = "action";

/**
 *  The name of the attribute of the key element.
 */
static const std::string    keyElementName      = "key";

/*
 *  The modifier keys we check against.
 *  The following modifiers are omitted, hence ignored: 
 *  GDK_LOCK_MASK (caps lock),
 *  GDK_MOD2_MASK (don't know what; always on on my computer),
 *  GDK_MOD3_MASK (don't know what; always off on my computer),
 *  GDK_BUTTONX_MASK (mouse buttons, X = 1..5).
 */
static const unsigned int  modifiersChecked = GDK_SHIFT_MASK 
                                            | GDK_CONTROL_MASK
                                            | GDK_MOD1_MASK     // Alt
                                            | GDK_MOD4_MASK     // Windows key
                                            | GDK_MOD5_MASK;    // AltGr


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Add a shortcut key for this object.
 *----------------------------------------------------------------------------*/
void
KeyboardShortcut :: addKey(const Glib::ustring &    modifiedKeyName)
                                                throw (std::invalid_argument)
{
    Ptr<Glib::ustring>::Ref     inputString(new Glib::ustring(
                                                        modifiedKeyName ));

    Ptr<Glib::ustring>::Ref     keyName     = getToken(inputString);
    if (!keyName) {
        throw std::invalid_argument("");
    }
    unsigned int                key         = stringToKey(keyName);
    
    Ptr<Glib::ustring>::Ref     modifierName;
    unsigned int                modifiers   = 0;
    while ((modifierName = getToken(inputString))) {
        modifiers |= stringToModifier(modifierName);
    }
    
    addKey(modifiers, key);
}


/*------------------------------------------------------------------------------
 *  Add a shortcut key for this object.
 *----------------------------------------------------------------------------*/
void
KeyboardShortcut :: addKey(unsigned int     modifiers,
                           unsigned int     key)                    throw ()
{
    modifierList.push_back(modifiers);
    keyList.push_back(key);
}


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

    xmlpp::Node::NodeList     children;

    // set the action
    children = element.get_children(actionElementName);
    if (children.size() < 1) {
        throw std::invalid_argument("missing " 
                                    + actionElementName + " element");
    } else if (children.size() > 1) {
        throw std::invalid_argument("too many " 
                                    + actionElementName + " elements");
    }
    const xmlpp::Element*   actionElement = dynamic_cast<const xmlpp::Element*>(
                                                children.front());
    const Glib::ustring     actionString  = actionElement->get_child_text()
                                                         ->get_content();
    try {
        action = stringToAction(actionString);
    } catch (std::invalid_argument &e) {
        std::string eMsg = "Invalid action specification ";
        eMsg += actionString;
        eMsg += ".";
        throw std::invalid_argument(eMsg);
    }

    // set the keys
    children = element.get_children(keyElementName);
    if (children.size() < 1) {
        throw std::invalid_argument("missing " 
                                    + keyElementName + " element");
    }
    xmlpp::Node::NodeList::const_iterator   it;
    for (it = children.begin(); it != children.end(); ++it) {
        const xmlpp::Element*   keyElement = 
                                    dynamic_cast<const xmlpp::Element*>(*it);
        const Glib::ustring     keyString  = keyElement->get_child_text()
                                                       ->get_content();
        try {
            addKey(keyString);
        } catch (std::invalid_argument &e) {
            std::string eMsg = "Invalid key specification ";
            eMsg += keyString;
            eMsg += " for action ";
            eMsg += actionString;
            eMsg += ".";
            throw std::invalid_argument(eMsg);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Tell whether the given modifier-key pair triggers this action.
 *----------------------------------------------------------------------------*/
bool
KeyboardShortcut :: isTriggeredBy(unsigned int  modifiers,
                                  unsigned int  key) const          throw ()
{
    KeyListType::const_iterator modifierIt = modifierList.begin();
    KeyListType::const_iterator keyIt      = keyList.begin();
    
    while (keyIt != keyList.end()) {
        if (*modifierIt == (modifiers & modifiersChecked) && *keyIt == key) {
            return true;
        }
        ++modifierIt;
        ++keyIt;
    }
    
    return false;        
}


/*------------------------------------------------------------------------------
 *  Convert an action name string to an enumeration value.
 *----------------------------------------------------------------------------*/
KeyboardShortcut::Action
KeyboardShortcut :: stringToAction(const Glib::ustring &    actionName)
                                                throw (std::invalid_argument)
{
    if (actionName == "playAudio") {
        return playAudio;
    } else if (actionName == "pauseAudio") {
        return pauseAudio;
    } else if (actionName == "stopAudio") {
        return stopAudio;
    } else if (actionName == "nextTrack") {
        return nextTrack;
    } else if (actionName == "fadeOut") {
        return fadeOut;
    } else if (actionName == "moveItemUp") {
        return moveItemUp;
    } else if (actionName == "moveItemDown") {
        return moveItemDown;
    } else if (actionName == "removeItem") {
        return removeItem;
    } else {
        throw std::invalid_argument("");
    }
}


/*------------------------------------------------------------------------------
 *  Get the next token in the key description string.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
KeyboardShortcut :: getToken(Ptr<Glib::ustring>::Ref    inputString)
                                                                    throw ()
{
    Ptr<Glib::ustring>::Ref     token;
    
    if (!inputString || inputString->length() == 0) {
        return token;                       // initialized to a 0 pointer
    }

    unsigned int minusPosition = inputString->rfind('-');
    unsigned int lastPosition  = inputString->length() - 1;
    
    if (minusPosition == lastPosition) {
        if (minusPosition == 0) {
            token.reset(new Glib::ustring("-"));
            inputString->erase();
            return token;
        } else if (inputString->at(minusPosition - 1) == '-') {
            token.reset(new Glib::ustring("-"));
            inputString->erase(minusPosition - 1);
            return token;
        } else {
            return token;
        }
    } else if (minusPosition == Glib::ustring::npos) {
        token.reset(new Glib::ustring(*inputString));
        inputString->erase();
        return token;
    } else {
        token.reset(new Glib::ustring(*inputString, minusPosition + 1));
        inputString->erase(minusPosition);
        return token;
    }
}


/*------------------------------------------------------------------------------
 *  Convert a key name to a gtk+ gdkkeysyms value.
 *----------------------------------------------------------------------------*/
unsigned int
KeyboardShortcut :: stringToKey(Ptr<const Glib::ustring>::Ref   keyName)
                                                throw (std::invalid_argument)
{
    if (keyName->length() == 1) {
        char    c = keyName->at(0);
        if (c >= '0' && c <= '9') {
            return GDK_0 + (c - '0');
        } else if (c >= 'A' && c <= 'Z') {
            return GDK_A + (c - 'A');
        } else if (c >= 'a' && c <= 'z') {
            return GDK_a + (c - 'a');
        }
    } else if (*keyName == "Space") {
        return GDK_space;
    } else if (*keyName == "Esc") {
        return GDK_Escape;
    } else if (*keyName == "Escape") {
        return GDK_Escape;
    } else if (*keyName == "Tab") {
        return GDK_Tab;
    } else if (*keyName == "Backspace") {
        return GDK_BackSpace;
    } else if (*keyName == "Del") {
        return GDK_Delete;
    } else if (*keyName == "Delete") {
        return GDK_Delete;
    } else if (*keyName == "Home") {
        return GDK_Home;
    } else if (*keyName == "End") {
        return GDK_End;
    } else if (*keyName == "Up") {
        return GDK_Up;
    } else if (*keyName == "Down") {
        return GDK_Down;
    } else if (*keyName == "Left") {
        return GDK_Left;
    } else if (*keyName == "Right") {
        return GDK_Right;
    } else if (*keyName == "PgUp") {
        return GDK_Page_Up;
    } else if (*keyName == "PageUp") {
        return GDK_Page_Up;
    } else if (*keyName == "PgDn") {
        return GDK_Page_Down;
    } else if (*keyName == "PgDown") {
        return GDK_Page_Down;
    } else if (*keyName == "PageDown") {
        return GDK_Page_Down;
    } else if (*keyName == "F1") {
        return GDK_F1;
    } else if (*keyName == "F2") {
        return GDK_F2;
    } else if (*keyName == "F3") {
        return GDK_F3;
    } else if (*keyName == "F4") {
        return GDK_F4;
    } else if (*keyName == "F5") {
        return GDK_F5;
    } else if (*keyName == "F6") {
        return GDK_F6;
    } else if (*keyName == "F7") {
        return GDK_F7;
    } else if (*keyName == "F8") {
        return GDK_F8;
    } else if (*keyName == "F9") {
        return GDK_F9;
    } else if (*keyName == "F10") {
        return GDK_F10;
    } else if (*keyName == "F11") {
        return GDK_F11;
    } else if (*keyName == "F12") {
        return GDK_F12;
    }
    
    // if none of the above:
    throw std::invalid_argument("");
}


/*------------------------------------------------------------------------------
 *  Convert a mofifier name to a gtk+ gdktypes value.
 *----------------------------------------------------------------------------*/
unsigned int
KeyboardShortcut :: stringToModifier(Ptr<const Glib::ustring>::Ref modifierName)
                                                throw (std::invalid_argument)
{
    if (*modifierName == "Shift") {
        return GDK_SHIFT_MASK;
    } else if (*modifierName == "Ctrl") {
        return GDK_CONTROL_MASK;
    } else if (*modifierName == "Control") {
        return GDK_CONTROL_MASK;
    } else if (*modifierName == "Alt") {
        return GDK_MOD1_MASK;
    } else if (*modifierName == "AltGr") {
        return GDK_MOD5_MASK;
    } else if (*modifierName == "Win") {
        return GDK_MOD4_MASK;
    } else if (*modifierName == "Windows") {
        return GDK_MOD4_MASK;
    } else {
        throw std::invalid_argument("");
    }
}

