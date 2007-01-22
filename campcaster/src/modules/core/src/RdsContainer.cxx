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

#include "LiveSupport/Core/RdsContainer.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
const std::string       RdsContainer::configElementName = "rdsContainer";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create an RDS container object based on an XML element.
 *----------------------------------------------------------------------------*/
void
RdsContainer :: configure(const xmlpp::Element &    element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementName) {
        throw std::invalid_argument("bad coniguration element "
                                  + element.get_name());
    }

    xmlpp::Node::NodeList childNodes = element.get_children(
                                            RdsItem::getConfigElementName());
    xmlpp::Node::NodeList::const_iterator   it;

    rdsItemList.clear();
    for (it = childNodes.begin(); it != childNodes.end(); ++it) {
        const xmlpp::Element *      rdsItemElement 
                                    = dynamic_cast<const xmlpp::Element*> (*it);
                                    
        Ptr<RdsItem>::Ref           rdsItem(new RdsItem());
        rdsItem->configure(*rdsItemElement);
        
        rdsItemList.push_back(rdsItem);
    }
}


/*------------------------------------------------------------------------------
 *  Set the RDS options.
 *----------------------------------------------------------------------------*/
void
RdsContainer :: setRdsOptions(Ptr<const Glib::ustring>::Ref  key,
                              Ptr<const Glib::ustring>::Ref  value,
                              bool                           enabled)
                                                                    throw ()
{
    RdsItemListType::const_iterator     it;

    bool found = false;
    for(it = rdsItemList.begin(); it != rdsItemList.end(); ++it) {
        Ptr<RdsItem>::Ref               rdsItem = *it;
        if (*rdsItem->getKey() == *key) {
            found = true;
            rdsItem->setValue(value);
            rdsItem->setEnabled(enabled);
            break;
        }
    }
    
    if (!found) {
        Ptr<RdsItem>::Ref   rdsItem(new RdsItem(key, value, enabled));
        rdsItemList.push_back(rdsItem);
    }
    
    touched = true;
}


/*------------------------------------------------------------------------------
 *  Get the value of an RDS string.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
RdsContainer :: getRdsValue(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument)
{
    RdsItemListType::const_iterator     it;
    for(it = rdsItemList.begin(); it != rdsItemList.end(); ++it) {
        Ptr<RdsItem>::Ref               rdsItem = *it;
        if (*rdsItem->getKey() == *key) {
            return rdsItem->getValue();
        }
    }
    
    Glib::ustring   safeKey = key ? *key : "(null)";
    throw std::invalid_argument("RDS option " + safeKey + "not found.");
}


/*------------------------------------------------------------------------------
 *  Get the enabled/disabled state of an RDS option.
 *----------------------------------------------------------------------------*/
bool
RdsContainer :: getRdsEnabled(Ptr<const Glib::ustring>::Ref  key)
                                                throw (std::invalid_argument)
{
    RdsItemListType::const_iterator     it;
    for(it = rdsItemList.begin(); it != rdsItemList.end(); ++it) {
        Ptr<RdsItem>::Ref               rdsItem = *it;
        if (*rdsItem->getKey() == *key) {
            return rdsItem->getEnabled();
        }
    }
    
    Glib::ustring   safeKey = key ? *key : "(null)";
    throw std::invalid_argument("RDS option " + safeKey + "not found.");
}


/*------------------------------------------------------------------------------
 *  Convert the object to a string.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
RdsContainer :: toString(void)                                      throw ()
{
    Ptr<Glib::ustring>::Ref     rdsString(new Glib::ustring);
    
    RdsItemListType::const_iterator     it;
    for(it = rdsItemList.begin(); it != rdsItemList.end(); ++it) {
        Ptr<RdsItem>::Ref               rdsItem = *it;
        rdsString->append(*rdsItem->toString());
    }
    
    return rdsString;
}


/*------------------------------------------------------------------------------
 *  Convert the object to XML.
 *----------------------------------------------------------------------------*/
const xmlpp::Element *
RdsContainer :: toXmlElement(void)                                  throw ()
{
    if (!touched && xmlDocument) {
        return xmlDocument->get_root_node();
    }
    
    xmlDocument.reset(new xmlpp::Document());
    xmlpp::Element *    rootNode = xmlDocument->create_root_node(
                                                            configElementName);
    RdsItemListType::const_iterator     it;
    for(it = rdsItemList.begin(); it != rdsItemList.end(); ++it) {
        Ptr<RdsItem>::Ref               rdsItem = *it;
        rootNode->import_node(rdsItem->toXmlElement(), true);
    }
    
    touched = false;
    return rootNode;
}

