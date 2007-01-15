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
#ifndef KeyboardShortcut_h
#define KeyboardShortcut_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <gtkmm/accelkey.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class for representing a keyboard shortcut.
 *
 *  This object has to be configured with an XML configuration element
 *  called keyboardShortcut. This may look like the following:
 *
 *  <pre><code>
 *  &lt;keyboardShortcut    action  = "pauseAudio"
 *                          key     = "&lt;Alt&gt;&lt;Ctrl&gt;P" /&gt;
 *  </code></pre>
 *
 *  The possible action values are the members of the Action enumeration.
 *
 *  The possible key values are zero or more of the modifiers
 *  &lt;Shift&gt;, &lt;Control&gt; and &lt;Alt&gt;, followed by the 
 *  name of the key, e.g., the letters A-Z (or a-z; they are not 
 *  case-sensitive), the numbers 0-9, Space, Tab, etc.
 *  The key names are the ones defined in Gtk::AccelKey, used in the
 *  Gnome Keyboard Shortcuts applet, for example.
 *  (Note: Gtk::AccelKey is a wrapper for the gdk_keyval_name() and
 *  gdk_keyval_from_name() C functions in GDK.)
 *
 *  There must be exactly one each of the <code>action</code> 
 *  <code>key</code> attributes.
 *
 *  The DTD for the expected XML element looks like the following:
 *
 *  <pre><code>
 *  &lt;!ELEMENT keyboardShortcut   EMPTY &gt;
 *  &lt;!ATTLIST keyboardShortcut   action  CDATA   #REQUIRED &gt;
 *  &lt;!ATTLIST keyboardShortcut   key     CDATA   #REQUIRED &gt;
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
         *  A string representation of the action.
         */
        Ptr<Glib::ustring>::Ref     actionString;

        /**
         *  The key associated with this keyboard shortcut.
         */
        Gtk::AccelKey               shortcutKey;

        /**
         *  Convert an action name string to an enumeration value.
         *  If no matching enumeration value is found, noAction is returned.
         *
         *  @param actionName   a string containing the name of the action.
         */
        Action
        stringToAction(Ptr<const Glib::ustring>::Ref    actionName)
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
         *  Set the shortcut key.
         *
         *  @param  keyName the string representation of the shortcut key.
         */
        void
        setKey(const Glib::ustring &    keyName)
                                                throw (std::invalid_argument);

        /**
         *  Tell whether the given modifier-key pair is one of those
         *  associated with this object.
         *
         *  @return true if the modifier-key pair triggers this action,
         *          false if not.
         */
        bool
        isTriggeredBy(Gdk::ModifierType     modifiers,
                      guint                 key) const              throw ();
        
        /**
         *  Return a string corresponding to the action of this shortcut.
         *
         *  @return a string representing the action of this shortcut.
         */
        Ptr<const Glib::ustring>::Ref
        getActionString(void) const                                 throw ()
        {
            return actionString;
        }
        
       /**
        *   Return the first key associated with this shortcut.
        *
        *   @return a string representing the first modifier-key pair of
        *           this shortcut.
        */
        Ptr<const Glib::ustring>::Ref
        getKeyString(void) const                                     throw ()
        {
            Ptr<const Glib::ustring>::Ref   keyName(new Glib::ustring(
                                                shortcutKey.get_abbrev() ));
            return keyName;
        }

       /**
        *   Convert a modifiers-key code pair to a user-readable string.
        *
        *   @return a string representing the modifier-key pair.
        */
        static Ptr<Glib::ustring>::Ref
        modifiedKeyToString(Gdk::ModifierType   modifiers,
                            guint               key)                throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // KeyboardShortcut_h

