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
#ifndef KeyboardShortcutList_h
#define KeyboardShortcutList_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <map>
#include <iostream>     // TODO: REMOVE ME

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"

#include "KeyboardShortcutContainer.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A list of KeyboardShortcutContainer objects.
 *
 *  This object has to be configured with an XML configuration element
 *  called keyboardShortcutList.
 *  
 *  The DTD for the expected XML element is the following:
 *  <pre><code>
 *  <!ELEMENT keyboardShortcutList (keyboardShortcutContainer*) >
 *  </code></pre>
 *
 *  For a description of the keyboardShortcutContainer XML element,
 *  see the documentation of the KeyboardShortcutContainer class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see KeyboardShortcut
 */
class KeyboardShortcutList : public Configurable
{
    private:
        /**
         *  The name of the configuration XML element used by
         *  KeyboardShortcutList.
         */
        static const std::string    configElementName;

        /**
         *  The type for storing the keyboard shortcut containers.
         */
        typedef std::vector<Ptr<KeyboardShortcutContainer>::Ref> 
                                    ContainerListType;

        /**
         *  The list of keyboard shortcut containers for the various windows.
         */
        ContainerListType           containerList;


    public:
        /**
         *  Constructor.
         */
        KeyboardShortcutList()                                      throw ()
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~KeyboardShortcutList(void)                                 throw ()
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

        /**
         *  Find the action triggered by the given key in the given window.
         *
         *  @param  windowName  a string identifying the window (not localized).
         *  @param  modifiers   the gdktypes code for the Shift, Ctrl etc.
         *                          modifier keys which are pressed.
         *  @param  key         the gdkkeysyms code for the key pressed.
         *  @return the associated action; or noAction, if none is found.
         */
        KeyboardShortcut::Action
        findAction(const Glib::ustring &    windowName,
                   Gdk::ModifierType        modifiers,
                   guint                    key) const              throw ();
        
        /**
         *  The iterator for cycling through the keyboard shortcut containers.
         *  Dereference an iterator to get a 
         *  Ptr<KeyboardShortcutContainer>::Ref.
         */
        typedef ContainerListType::const_iterator   iterator;
        
        /**
         *  The first item in the list.
         */
        iterator
        begin(void) const                                           throw ()
        {
            return containerList.begin();
        }
        
        /**
         *  One after the last item in the list.
         */
        iterator
        end(void) const                                             throw ()
        {
            return containerList.end();
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // KeyboardShortcutList_h

