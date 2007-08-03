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
 
 
    Author   : $Author $
    Version  : $Revision $
    Location : $URL $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_OptionsContainer_h
#define LiveSupport_Core_OptionsContainer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <glibmm/ustring.h>
#include <libxml++/libxml++.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/RdsContainer.h"


namespace LiveSupport {
namespace Core {
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A container for the options in gLiveSupport.xml.
 *
 *  It supports a number of named string options (see OptionItemString),
 *  plus two special kinds of options: keyboard shortcuts, and RDS strings.
 *
 *  @author  $Author $
 *  @version $Revision $
 */
class OptionsContainer
{
    public:
        /**
         *  The list of string options one can set.
         *  
         *  These are options of type Glib::ustring; any string is accepted
         *  as value, no range checking is done.
         */
        typedef enum { outputPlayerDeviceName,
                       cuePlayerDeviceName,
                       authenticationServer,
                       authenticationPort,
                       authenticationPath,
                       storageServer,
                       storagePort,
                       storagePath,
                       schedulerServer,
                       schedulerPort,
                       schedulerPath,
                       serialDeviceName }   OptionItemString;
        

    private:
        /**
         *  The XML document containing the options.
         */
        xmlpp::Document                 optionsDocument;
        
        /**
         *  The file name (including path) used by writeToFile().
         */
        Ptr<const Glib::ustring>::Ref   configFileName;
        
        /**
         *  Remember if we have been touched.
         */
        bool                            touched;
        
        /**
         *  Container for the RDS settings.
         */
        Ptr<RdsContainer>::Ref          rdsContainer;
        
        /**
         *  Default constructor.
         */
        OptionsContainer(void)                                      throw ()
        {
        }

        /**
         *  Find the node corresponding to an OptionItemString value.
         *
         *  If there is no matching node, it returns a 0 pointer.
         *
         *  @param  optionItem      the name of the item to find the node for
         *  @param  isAttribute     return parameter; is set to true if the
         *                              node is an attribute, false if it's
         *                              a CDATA text
         *  @return a pointer to the node found, or 0
         *  @exception  std::invalid_argument   thrown by getNode() [should
         *                                      never happen]
         */
        xmlpp::Node *
        selectNode(OptionItemString     optionItem,
                   bool &               isAttribute)
                                                throw (std::invalid_argument);
        
        /**
         *  Find the node corresponding to a keyboard shortcut.
         *
         *  If there is no matching node, it returns a 0 pointer.
         *
         *  @param  containerNo     the number of the KeyboardShortcutContainer
         *                              (starting with 1, as per XPath)
         *  @param  shortcutNo      the number of the KeyboardShortcut within
         *                              this container (also starting with 1)
         *  @return a pointer to the node found, or 0
         *  @exception  std::invalid_argument   thrown by getNode() [should
         *                                      never happen]
         */
        xmlpp::Node *
        selectKeyboardShortcutNode(int  containerNo,
                                   int  shortcutNo)
                                                throw (std::invalid_argument);
        
        /**
         *  Return the first node matching an XPath string.
         *
         *  If there is no matching node, it returns a 0 pointer.
         *
         *  @param  xPath   the XPath of the node (from the root node)
         *  @return a pointer to the node found, or 0
         *  @exception  std::invalid_argument   if the XPath is not well formed
         */
        xmlpp::Node *
        getNode(const Glib::ustring &   xPath)
                                                 throw (std::invalid_argument);
        
        /**
         *  Create a node corresponding to an option item.
         *
         *  So far, this is only implemented for serialDeviceName;
         *  for all other option items, it returns a 0 pointer.
         *  The XML element or attribute is created with a value of "".
         *
         *  TODO: implement this properly; ideally, the paths would be read
         *  from the DTD of the default config file, and added to the current
         *  config file as needed.
         *
         *  @param  optionItem  the option item to be created.
         *  @return a pointer to the node created, or 0.
         */
        xmlpp::Node *
        createNode(OptionItemString     optionItem)                  throw ();
        
        
    public:
        /**
         *  Constructor with XML element parameter.
         *
         *  @param optionsElement   the XML element containing the options
         *  @param configFileName   the name (with path) of the configuration
         *                              file used by writeToFile()
         *  @see writeToFile()
         */
        OptionsContainer(const xmlpp::Element &         optionsElement,
                         Ptr<const Glib::ustring>::Ref  configFileName)
                                                                    throw ();
        
        /**
         *  Report if the object has been touched.
         *
         *  It returns true if there has been any calls to setOptionItem()
         *  since its construction or the last call to writeToFile().
         *
         *  @return whether the options have been touched
         */
        bool
        isTouched(void)                                             throw ()
        {
            return touched || (rdsContainer && rdsContainer->isTouched());
        }

        /**
         *  Set a string type option.
         *
         *  @param      optionItem              which option to set
         *  @param      value                   the new value of the option
         *  @exception  std::invalid_argument   if the option name is not found
         */
        void
        setOptionItem(OptionItemString                  optionItem,
                      Ptr<const Glib::ustring>::Ref     value)
                                                 throw (std::invalid_argument);
        
        /**
         *  Get a string type option.
         *
         *  @return     the value of the option
         *  @exception  std::invalid_argument   if the option name is not found
         */
        Ptr<Glib::ustring>::Ref
        getOptionItem(OptionItemString      optionItem)
                                                 throw (std::invalid_argument);

        /**
         *  Set a keyboard shortcut type option.
         *
         *  @param      containerNo     which container to modify
         *  @param      shortcutNo      which shortcut to modify within this
         *                                  container
         *  @param      value           the name of the new shortcut key
         *  @exception  std::invalid_argument   if the shortcut is not found
         */
        void
        setKeyboardShortcutItem(int                             containerNo,
                                int                             shortcutNo,
                                Ptr<const Glib::ustring>::Ref   value)
                                                 throw (std::invalid_argument);
        
        /**
         *  Set the value of the RDS options.
         *  The key can be any of the RDS data codes, like PS, PI, PTY, RT,
         *  etc.  If there is already a value set for this code, it gets
         *  overwritten, otherwise a new key-value pair is added.
         *
         *  @param      key      which setting to modify
         *  @param      value    the new value of the RDS setting
         *  @param      enabled  the new enabled/disabled state of the 
         *                       RDS setting
         */
        void
        setRdsOptions(Ptr<const Glib::ustring>::Ref     key,
                      Ptr<const Glib::ustring>::Ref     value,
                      bool                              enabled)    throw ();
        
        /**
         *  Get the value of an RDS string.
         *  The key can be any of the RDS data codes, like PS, PI, PTY, RT,
         *  etc.
         *
         *  @param      key     which setting to modify
         *  @return     the value of the RDS setting
         *  @exception  std::invalid_argument   if there is no such RDS option.
         */
        Ptr<const Glib::ustring>::Ref
        getRdsValue(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument);
        
        /**
         *  Get the enabled/disabled state of an RDS option.
         *
         *  @param      key     which setting to modify
         *  @return     true if the RDS option is enabled, false otherwise.
         *  @exception  std::invalid_argument   if there is no such RDS option.
         */
        bool
        getRdsEnabled(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument);
        
        /**
         *  Get a string containing all the RDS values.
         *  This string can be sent to the RDS encoder.
         *
         *  @return     a string which can be sent to the RDS encoder;
         *              a 0 pointer if no RDS options have been defined.
         */
        Ptr<Glib::ustring>::Ref
        getCompleteRdsString(void)                                  throw ()
        {
            return rdsContainer ? rdsContainer->toString()
                                : Ptr<Glib::ustring>::Ref();
        }

        /**
         *  Save the options to a file.
         *
         *  This writes the options in XML format to the file specified in the
         *  constructor.  The directory must already exist (it's OK if the file
         *  does not), otherwise nothing is written.
         */
        void
        writeToFile(void)                                           throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_OptionsContainer_h

