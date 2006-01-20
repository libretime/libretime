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

#include "OptionsContainer.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

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
        changed(false)
{
    optionsDocument.create_root_node_by_import(&optionsElement, true);
                                                        // true == recursive
}


/*------------------------------------------------------------------------------
 *  Set a string type option.
 *----------------------------------------------------------------------------*/
void
OptionsContainer :: setOptionItem(OptionItemString                  optionItem,
                                  Ptr<const Glib::ustring>::Ref     value)
                                                throw (std::invalid_argument)
{
    xmlpp::Node *           targetNode   = 0;
    bool                    isAttribute  = false; // text node or attr node
    
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
    }

    if (isAttribute) {
        xmlpp::Attribute *  attr = dynamic_cast<xmlpp::Attribute*>(targetNode);
        if (attr != 0) {
            attr->set_value(*value);
            changed = true;
            return;
        }
    } else {
        xmlpp::TextNode *   text = dynamic_cast<xmlpp::TextNode*>(targetNode);
        if (text != 0) {
            text->set_content(*value);
            changed = true;
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
    const xmlpp::Node *     targetNode = 0;
    bool                    isAttribute = false; // child text node or attr
    
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
    }

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
 *  Save the options to a file.
 *----------------------------------------------------------------------------*/
void
OptionsContainer :: writeToFile(void)                               throw ()
{
    if (configFileName) {
        std::ofstream   file(configFileName->c_str());
        if (file.good()) {
            optionsDocument.write_to_stream_formatted(file, "utf-8");
            changed = false;
        }
        file.close();
    }
}

