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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.10 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/AudioClip.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>

#include "LiveSupport/Core/AudioClip.h"

using namespace boost::posix_time;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string AudioClip::configElementNameStr = "audioClip";

/**
 *  The name of the metadata child element.
 */
static const std::string    metadataElementName = "metadata";

/**
 *  The prefix of the extent (length) metadata element.
 */
static const std::string    extentElementPrefix = "dcterms";

/**
 *  The name of the extent (length) metadata element.
 */
static const std::string    extentElementName = "extent";

/**
 *  The name of the attribute to get the id of the audio clip.
 */
static const std::string    idAttrName = "id";

/**
 *  The name of the attribute to get the URI of the audio clip.
 */
static const std::string    uriAttrName = "uri";

/**
 *  The name of the attribute to get the playlength of the audio clip.
 */
static const std::string    playlengthAttrName = "playlength";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create an audio clip object based on an XML element.
 *----------------------------------------------------------------------------*/
void
AudioClip :: configure(const xmlpp::Element  & element)
                                               throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }
    
    const xmlpp::Attribute    * attribute;

    if (!id) {
        if (!(attribute = element.get_attribute(idAttrName))) {
            std::string eMsg = "missing attribute ";
            eMsg += idAttrName;
            throw std::invalid_argument(eMsg);
        }
        std::stringstream   strStr(attribute->get_value());
        UniqueId::IdType    idValue;
        strStr >> idValue;
        id.reset(new UniqueId(idValue));
    }

    if (!playlength
            && (attribute = element.get_attribute(playlengthAttrName))) {
        playlength.reset(new time_duration(
                                duration_from_string(attribute->get_value())));
    }

    if (!uri 
            && (attribute = element.get_attribute(uriAttrName))) {
        uri.reset(new std::string(attribute->get_value()));
    }

    xmlpp::Node::NodeList       childNodes 
                                = element.get_children(metadataElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it != childNodes.end()) {
        const xmlpp::Element    * metadataElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);

        xmlpp::Node::NodeList   dataFieldList
                                = metadataElement->get_children();
        xmlpp::Node::NodeList::iterator listIt = dataFieldList.begin();

        while (listIt != dataFieldList.end()) {
            const xmlpp::Node * dataNode = *listIt;
            if (!playlength 
                    && dataNode->get_namespace_prefix() == extentElementPrefix
                    && dataNode->get_name() == extentElementName) {
                const xmlpp::Element
                        * dataElement 
                        = dynamic_cast<const xmlpp::Element*> (dataNode);
                if (dataElement->has_child_text()) {
                    std::stringstream strStr(dataElement->get_child_text()
                                                        ->get_content());
                    unsigned long int seconds;
                    strStr >> seconds;
                    playlength.reset(new time_duration(0,0,seconds,0));
                }
            }
            ++listIt;
        }
        
        ++it;
        if (it != childNodes.end()) {
            std::string eMsg = "more than one ";
            eMsg += metadataElementName;
            eMsg += " XML element";
            throw std::invalid_argument(eMsg);
        }
    }

    if (!playlength) {
        std::string eMsg = "missing attribute ";
        eMsg += playlengthAttrName;
        throw std::invalid_argument(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Return the value of a metadata field.
 *----------------------------------------------------------------------------*/
Ptr<UnicodeString>::Ref
AudioClip :: getMetadata(const string &key) const
                                                throw ()
{
    metadataType::const_iterator  it = metadata.find(key);

    if (it != metadata.end()) {
        return it->second;
    }
    else {
        Ptr<UnicodeString>::Ref nullPointer;
        return nullPointer;
    }
}


/*------------------------------------------------------------------------------
 *  Set the value of a metadata field.
 *----------------------------------------------------------------------------*/
void
AudioClip :: setMetadata(const string &key, Ptr<UnicodeString>::Ref value)
                                                throw ()
{
    metadata[key] = value;
}


/*------------------------------------------------------------------------------
 *  Create an XML document from this audio clip.
 *----------------------------------------------------------------------------*/
Ptr<xmlpp::Document>::Ref
AudioClip :: toXml()
                                               throw ()
{
    Ptr<xmlpp::Document>::Ref   metadata(new xmlpp::Document);
    metadata->create_root_node("metadata");
    metadata->add_comment("some data will come here");
    return metadata;
}

