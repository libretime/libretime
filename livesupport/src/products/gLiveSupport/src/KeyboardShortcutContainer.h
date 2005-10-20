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
#ifndef LiveSupport_GLiveSupport_KeyboardShortcutContainer_h
#define LiveSupport_GLiveSupport_KeyboardShortcutContainer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <map>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"

#include "KeyboardShortcut.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Container holding KeyboardShortcut objects.
 *
 *  This object has to be configured with an XML configuration element
 *  called keyboardShortcutContainer. This may look like the following:
 *
 *  <pre><code>
 *  &lt;keyboardShortcutContainer&gt;
 *      &lt;keyboardShortcut&gt; ... &lt;keyboardShortcut/&gt;
 *      &lt;keyboardShortcut&gt; ... &lt;keyboardShortcut/&gt;
 *      ...
 *      &lt;keyboardShortcut&gt; ... &lt;keyboardShortcut/&gt;
 *  &lt;/keyboardShortcutContainer&gt;
 *  </code></pre>
 *
 *  The DTD for the expected XML element is the following:
 *
 *  <pre><code>
 *  <!ELEMENT keyboardShortcutContainer (keyboardShortcut+) >
 *  </code></pre>
 *
 *  For a description of the keyboardShortcut XML element, see the documentation
 *  of the KeyboardShortcut class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see KeyboardShortcut
 */
class KeyboardShortcutContainer : public Configurable
{
    private:
        /**
         *  The name of the configuration XML element used by
         *  KeyboardShortcutContainer.
         */
        static const std::string    configElementNameStr;

        /**
         *  A vector type holding contant KeyboardShortcut references.
         */
        typedef std::vector<Ptr<const KeyboardShortcut>::Ref>
                                    ShortcutListType;

        /**
         *  The list of all KeyboardShortcut references.
         */
        ShortcutListType            shortcutList;


    public:
        /**
         *  Constructor.
         */
        KeyboardShortcutContainer()                                 throw ()
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~KeyboardShortcutContainer(void)                            throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                              throw ()
        {
            return configElementNameStr;
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
         *  Return the action triggered by the given key.
         *  Scans the keyboard shortcuts in order, and returns the first
         *  match.
         *
         *  @param  modifiers   the gdktypes code of the modifiers flag.
         *  @param  key         the gdkkeysyms code of the key pressed.
         *  @return the action; or noAction if none is found.
         */
        KeyboardShortcut::Action
        findAction(unsigned int modifiers, unsigned int key)        throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveSupport_GLiveSupport_KeyboardShortcutContainer_h

