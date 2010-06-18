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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <fstream>
#include <sstream>

#include "LiveSupport/Core/OptionsContainer.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
OptionsContainer :: OptionsContainer(
                            const xmlpp::Element &          optionsElement,
                            Ptr<const Glib::ustring>::Ref   configFileName)
                                                                    throw ()
      : configFileName(configFileName),
        touched(false)
{
    optionsDocument.create_root_node_by_import(&optionsElement, true);
                                                        // true == recursive

    xmlpp::Node::NodeList   nodes = optionsElement.get_children(
                                        RdsContainer::getConfigElementName());
    if (nodes.size() > 0) {
        rdsContainer.reset(new RdsContainer());
        rdsContainer->configure(
                        *dynamic_cast<const xmlpp::Element*>(nodes.front()));
    }
}


/*------------------------------------------------------------------------------
 *  Set a string type option.
 *----------------------------------------------------------------------------*/
void
OptionsContainer :: setOptionItem(OptionItemString                  optionItem,
                                  Ptr<const Glib::ustring>::Ref     value)
                                                throw (std::invalid_argument)
{
    bool              isAttribute  = false; // text node or attr node
    xmlpp::Node *     targetNode = selectNode(optionItem, isAttribute);

    if (!targetNode) {
        targetNode = createNode(optionItem);
    }
    
    if (isAttribute) {
        xmlpp::Attribute *  attr = dynamic_cast<xmlpp::Attribute*>(targetNode);
        if (attr != 0) {
            attr->set_value(*value);
            touched = true;
            return;
        }
    } else {
        xmlpp::TextNode *   text = dynamic_cast<xmlpp::TextNode*>(targetNode);
        if (text != 0) {
            text->set_content(*value);
            touched = true;
            return;
        }
    }
    
    throw std::invalid_argument("option item not found");
}


/*------------------------------------------------------------------------------
 *  Get a string type option.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
OptionsContainer :: getOptionItem(OptionItemString  optionItem)
                                                throw (std::invalid_argument)
{
    bool                    isAttribute = false; // text node or attr node
    const xmlpp::Node *     targetNode = selectNode(optionItem, isAttribute);
    
    if (isAttribute) {
        const xmlpp::Attribute *
                    attr = dynamic_cast<const xmlpp::Attribute*>(targetNode);
        if (attr != 0) {
            Ptr<Glib::ustring>::Ref value(new Glib::ustring(
                                                        attr->get_value() ));
            return value;
        }
    } else {
        const xmlpp::TextNode *
                    text = dynamic_cast<const xmlpp::TextNode*>(targetNode);
        if (text != 0) {
            Ptr<Glib::ustring>::Ref value(new Glib::ustring(
                                                        text->get_content() ));
            return value;
        }
    }
    
    throw std::invalid_argument("option item not found");
}


/*------------------------------------------------------------------------------
 *  Set a keyboard shortcut type option.
 *----------------------------------------------------------------------------*/
void
OptionsContainer :: setKeyboardShortcutItem(
                                int                             containerNo,
                                int                             shortcutNo,
                                Ptr<const Glib::ustring>::Ref   value)
                                                throw (std::invalid_argument)
{
    xmlpp::Node *     targetNode = selectKeyboardShortcutNode(
                                                    containerNo, shortcutNo);

    xmlpp::Attribute *  attr = dynamic_cast<xmlpp::Attribute*>(targetNode);
    if (attr != 0) {
        attr->set_value(*value);
        touched = true;
        return;

    } else {
        throw std::invalid_argument("keyboard shortcut not found");
    }
}


/*------------------------------------------------------------------------------
 *  Set the value of an RDS string.
 *----------------------------------------------------------------------------*/
void
OptionsContainer :: setRdsOptions(Ptr<const Glib::ustring>::Ref  key,
                                  Ptr<const Glib::ustring>::Ref  value,
                                  bool                           enabled)
                                                                    throw ()
{
    if (!rdsContainer) {
        rdsContainer.reset(new RdsContainer());
    }
    
    rdsContainer->setRdsOptions(key, value, enabled);
}


/*------------------------------------------------------------------------------
 *  Get the value of an RDS string.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
OptionsContainer :: getRdsValue(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument)
{
    if (rdsContainer) {
        return rdsContainer->getRdsValue(key);
    } else {
        throw std::invalid_argument("no RDS container found");
    }
}


/*------------------------------------------------------------------------------
 *  Get the enabled/disabled state of an RDS option.
 *----------------------------------------------------------------------------*/
bool
OptionsContainer :: getRdsEnabled(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument)
{
    if (rdsContainer) {
        return rdsContainer->getRdsEnabled(key);
    } else {
        throw std::invalid_argument("no RDS container found");
    }
}


/*------------------------------------------------------------------------------
 *  Find the node corresponding to an OptionItemString value.
 *----------------------------------------------------------------------------*/
xmlpp::Node *
OptionsContainer :: selectNode(OptionItemString     optionItem,
                               bool &               isAttribute)
                                                throw (std::invalid_argument)
{
    xmlpp::Node *   targetNode = 0;

    switch (optionItem) {
        case outputPlayerDeviceName :
            targetNode  = getNode("outputPlayer/audioPlayer/gstreamerPlayer/"
                                  "@audioDevice");
            isAttribute = true;
            break;
        
        case cuePlayerDeviceName :
            targetNode  = getNode("cuePlayer/audioPlayer/gstreamerPlayer/"
                                  "@audioDevice");
            isAttribute = true;
            break;
        
        case authenticationServer :
            targetNode  = getNode("authenticationClientFactory/"
                                  "webAuthentication/location/@server");
            isAttribute = true;
            break;
        
        case authenticationPort :
            targetNode  = getNode("authenticationClientFactory/"
                                  "webAuthentication/location/@port");
            isAttribute = true;
            break;
        
        case authenticationPath :
            targetNode  = getNode("authenticationClientFactory/"
                                  "webAuthentication/location/@path");
            isAttribute = true;
            break;
        
        case storageServer :
            targetNode  = getNode("storageClientFactory/"
                                  "webStorage/location/@server");
            isAttribute = true;
            break;
        
        case storagePort :
            targetNode  = getNode("storageClientFactory/"
                                  "webStorage/location/@port");
            isAttribute = true;
            break;
        
        case storagePath :
            targetNode  = getNode("storageClientFactory/"
                                  "webStorage/location/@path");
            isAttribute = true;
            break;
        
        case schedulerServer :
            targetNode  = getNode("schedulerClientFactory/"
                                  "schedulerDaemonXmlRpcClient/@xmlRpcHost");
            isAttribute = true;
            break;
        
        case schedulerPort :
            targetNode  = getNode("schedulerClientFactory/"
                                  "schedulerDaemonXmlRpcClient/@xmlRpcPort");
            isAttribute = true;
            break;
        
        case schedulerPath :
            targetNode  = getNode("schedulerClientFactory/"
                                  "schedulerDaemonXmlRpcClient/@xmlRpcUri");
            isAttribute = true;
            break;
        
        case serialDeviceName :
            targetNode  = getNode("serialPort/@path");
            isAttribute = true;
            break;
    }
    
    return targetNode;
}


/*------------------------------------------------------------------------------
 *  Find the node corresponding to a keyboard shortcut.
 *----------------------------------------------------------------------------*/
xmlpp::Node *
OptionsContainer :: selectKeyboardShortcutNode(int      containerNo,
                                               int      shortcutNo)
                                                throw (std::invalid_argument)
{
    std::stringstream   xPathStream;
    xPathStream << "keyboardShortcutList/keyboardShortcutContainer["
                << containerNo
                << "]/keyboardShortcut["
                << shortcutNo
                << "]/@key";
    return getNode(xPathStream.str());
}


/*------------------------------------------------------------------------------
 *  Return the first node matching an XPath string.
 *----------------------------------------------------------------------------*/
xmlpp::Node *
OptionsContainer :: getNode(const Glib::ustring &   xPath)
                                                throw (std::invalid_argument)
{
    xmlpp::Element *    rootNode = optionsDocument.get_root_node();
    xmlpp::NodeSet      nodes;

    try {
        nodes = rootNode->find(xPath);
        
    } catch (xmlpp::exception &e) {
        throw std::invalid_argument(e.what());
    }
    
    std::vector<xmlpp::Node*>::iterator     it = nodes.begin();
    if (it != nodes.end()) {
        return *it;
    } else {
        return 0;
    }
}


/*------------------------------------------------------------------------------
 *  Create the node corresponding to an OptionItemString value.
 *----------------------------------------------------------------------------*/
xmlpp::Node *
OptionsContainer :: createNode(OptionItemString     optionItem)     throw ()
{
    xmlpp::Element *    rootNode = optionsDocument.get_root_node();
    xmlpp::Element *    element = 0;
    xmlpp::Attribute *  attribute = 0;
    
    // only supports the serialDeviceName option item, for now
    switch (optionItem) {
        case serialDeviceName :
            element = dynamic_cast<xmlpp::Element*>(
                                                getNode("serialPort"));
            if (!element) {
                element = rootNode->add_child("serialPort");
            }
            attribute = dynamic_cast<xmlpp::Attribute*>(
                                                getNode("serialPort/@path"));
            if (!attribute) {
                attribute = element->set_attribute("path", "");
            }
            return attribute;
        
        default:
            return 0;
    }
}


/*------------------------------------------------------------------------------
 *  Save the options to a file.
 *----------------------------------------------------------------------------*/
void
OptionsContainer :: writeToFile(void)                               throw ()
{
    if (configFileName) {
        if (rdsContainer && rdsContainer->isTouched()) {
            xmlpp::Element *        rootNode = optionsDocument.get_root_node();
            xmlpp::Node::NodeList   nodes    = rootNode->get_children(
                                        RdsContainer::getConfigElementName());
            if (nodes.size() > 0) {
                rootNode->remove_child(nodes.front());
            }
            rootNode->import_node(rdsContainer->toXmlElement(), true);
        }

        std::ofstream   file(configFileName->c_str());
        if (file.good()) {
            optionsDocument.write_to_stream_formatted(file, "utf-8");
            touched = false;
        }
        file.close();
    }
}

