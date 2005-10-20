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
#ifndef LiveSupport_GLiveSupport_KeyboardShortcut_h
#define LiveSupport_GLiveSupport_KeyboardShortcut_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <vector>
#include <gdk/gdktypes.h>
#include <gdk/gdkkeysyms.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

// class KeyboardShortcutContainer; // TODO: remove or activate


/**
 *  A class for representing a keyboard shortcut.
 *
 *  This object has to be configured with an XML configuration element
 *  called keyboardShortcut. This may look like the following:
 *
 *  <pre><code>
 *  &lt;keyboardShortcut&gt;
 *      &lt;action&gt;pauseAudio&lt;/action&gt;
 *      &lt;key&gt;Ctrl-Alt-P&lt;/key&gt;
 *      &lt;key&gt;Space&lt;/key&gt;
 *      &lt;key&gt;F10&lt;/key&gt;
 *  &lt;/keyboardShortcut&gt;
 *  </code></pre>
 *
 *  The possible action values are the members of the Action enumeration.
 *
 *  The possible key values are the letters a-z, A-Z, the numbers 0-9,
 *  plus Space, Esc (or Escape), Tab, Backspace, Delete (Del), Home, End,
 *  Up, Down, Left, Right, PgUp (PageUp), PgDown (PageDown, PgDn),
 *  and the function keys F1 through F12.
 *
 *  The key names can be prefixed with zero or more of the modifiers
 *  Shift, Ctrl (or Control), Alt, AltGr, and Win (or Windows),
 *  separated by '-' characters.
 *
 *  There must be exactly one <code>action</code> attribute, and one or more
 *  <code>key</code> attributes.  (Zero keys is not an error, but pointless.)
 *
 *  The DTD for the expected XML element looks like the following:
 *
 *  <pre><code>
 *  &lt;!ELEMENT keyboardShortcut   (action, key+) &gt;
 *  &lt;!ELEMENT action             (CDATA) &gt;
 *  &lt;!ELEMENT key                (CDATA) &gt;
 *  </code></pre>
 *
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see KeyboardShortcutContainer
 */
class KeyboardShortcut : public Configurable
{
    friend class KeyboardShortcutContainer;

    public:
        /**
         *  An enumeration of all possible actions.
         */
        typedef enum { noAction,
                       playAudio,
                       pauseAudio,
                       stopAudio,
                       nextTrack,
                       fadeOut,
                       moveItemUp,
                       moveItemDown,
                       removeItem }    Action;


    private:
        /**
         *  The name of the configuration XML element used by KeyboardShortcut.
         */
        static const std::string    configElementName;

        /**
         *  The action associated with this keyboard shortcut.
         */
        Action                      action;

        /**
         *  The type for storing key and modifier values.
         */
        typedef std::vector<unsigned int>
                                    KeyListType;
        
        /**
         *  The list of modifiers to be used with the keys.
         *  For a list of the possible values, see 
         *  <code>/usr/include/gtk-2.0/gdk/gdktypes.h</code>.
         *
         *  This always has the same length as <code>keys</code>.
         */
        KeyListType                 modifierList;

        /**
         *  The list of keys associated with this keyboard shortcut.
         *  For a list of the possible values, see 
         *  <code>/usr/include/gtk-2.0/gdk/gdkkeysyms.h</code>.
         *
         *  This always has the same length as <code>modifiers</code>.
         */
        KeyListType                 keyList;

        /**
         *  Convert an action name string to an enumeration value.
         *  If no matching enumeration value is found, noAction is returned.
         *
         *  @param actionName   a string containing the name of the action.
         */
        Action
        stringToAction(const Glib::ustring &     actionName)
                                                throw(std::invalid_argument);
        
        /**
         *  Get the next token in the key description string.
         *  The tokens are read right-to-left, and the separator is '-'.
         *
         *  @param inputString  the string to be parsed; it will be
         *                      truncated on return.
         *  @return the token extracted; if the input string was empty,
         *          a null pointer is returned.
         */
        Ptr<Glib::ustring>::Ref
        getToken(Ptr<Glib::ustring>::Ref    inputString)            throw ();

        /**
         *  Convert a modifier name to a gtk+ gdktypes value.
         *
         *  @param  modifierName    a string containing the modifier's name.
         *  @return                 the numeric value of the modifier.
         *  @exception  std::invalid_argument if the string is not a valid
         *                                    modifier name.
         */
        unsigned int
        stringToModifier(Ptr<const Glib::ustring>::Ref  modifierName)
                                                throw(std::invalid_argument);

        /**
         *  Convert a key name to a gtk+ gdkkeysyms value.
         *
         *  @param  keyName     a string containing the key's name.
         *  @return             the numeric value of the key.
         *  @exception  std::invalid_argument if the string is not a valid
         *                                    key name.
         */
        unsigned int
        stringToKey(Ptr<const Glib::ustring>::Ref   keyName)
                                                throw(std::invalid_argument);
        
    protected:
        /**
         *  Default constructor.
         */
        KeyboardShortcut()                                          throw ()
            : action(noAction)
        {
        }

        /**
         *  Constructor.
         *
         *  @param  action  the action associated with this object.
         */
        KeyboardShortcut(Action     action)                         throw ()
            : action(action)
        {
        }

        /**
         *  Add a shortcut key for this object.  This will be one of the
         *  modifier-key pairs which trigger the action.
         *
         *  @param modifiedKeyName  a string containing a key name (with
         *                          some modifiers, optionally).
         *  @exception std::invalid_argument if the string is not a valid
         *                  key description.
         */
        void
        addKey(const Glib::ustring &    modifiedKeyName)
                                                throw (std::invalid_argument);

        /**
         *  Add a shortcut key for this action.  This will be one of the
         *  modifier-key pairs which trigger the action.
         *
         *  @param key        the gtk+ code of the key.
         *  @param modifiers  the gtk+ modifier bits.
         */
        void
        addKey(unsigned int     modifiers,
               unsigned int     key)                                throw ();

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                                  throw ()
        {
            return configElementName;
        }

        /**
         *  Configure the metadata object based on an XML configuration element.
         *
         *  @param elemen the XML configuration element.
         *  @exception std::invalid_argument of the supplied XML element
         *             contains bad configuration information
         */
        virtual void
        configure(const xmlpp::Element &element)
                                                throw (std::invalid_argument);


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~KeyboardShortcut(void)                                     throw ()
        {
        }

        /**
         *  Return the action.
         *
         *  @return the Action enumeration value associated with this object.
         */
        Action
        getAction(void) const                                       throw ()
        {
            return action;
        }

        /**
         *  Tell whether the given modifier-key pair is one of those
         *  associated with this object.
         *
         *  @return true if the modifier-key pair triggers this action,
         *          false if not.
         */
        bool
        isTriggeredBy(unsigned int  modifiers,
                      unsigned int  key) const                      throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveSupport_GLiveSupport_KeyboardShortcut_h

