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
    Version  : $Revision: 1.12 $
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

/**
 *  The name of the metadata child element.
 */
static const std::string    metadataElementName = "metadata";

/**
 *  The prefix of the extent (length) metadata element.
 */
static const std::string    extentElementPrefix = "dcterms";

/**
 *  The URI identifier for the "dcterms" prefix
 */
static const std::string    extentElementUri = "http://purl.org/dc/terms/";

/**
 *  The name of the extent (length) metadata element.
 */
static const std::string    extentElementName = "extent";

/**
 *  The prefix of the title metadata element.
 */
static const std::string    titleElementPrefix = "dc";

/**
 *  The URI identifier for the "dc" prefix
 */
static const std::string    titleElementUri ="http://purl.org/dc/elements/1.1/";

/**
 *  The name of the title metadata element.
 */
static const std::string    titleElementName = "title";

/**
 *  The URI identifier for the default XML namespace
 */
static const std::string    defaultPrefixUri ="http://www.streamonthefly.org/";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Test constructor without title.
 *----------------------------------------------------------------------------*/
AudioClip :: AudioClip(Ptr<UniqueId>::Ref       id,
                  Ptr<time_duration>::Ref       playlength,
                  Ptr<const std::string>::Ref   uri)
                                                           throw ()
{
    this->id         = id;
    this->title.reset(new Glib::ustring(""));
    this->playlength = playlength;
    this->uri        = uri;
    
    Ptr<const Glib::ustring>::Ref playlengthString(new const Glib::ustring(
                                        to_simple_string(*playlength) ));
    setMetadata(playlengthString, extentElementName, extentElementPrefix);
}

/*------------------------------------------------------------------------------
 *  Test constructor with title.
 *----------------------------------------------------------------------------*/
AudioClip :: AudioClip(Ptr<UniqueId>::Ref               id,
                       Ptr<const Glib::ustring>::Ref    title,
                       Ptr<time_duration>::Ref          playlength,
                       Ptr<const std::string>::Ref      uri)
                                                           throw ()
{
    this->id         = id;
    this->title      = title;
    this->playlength = playlength;
    this->uri        = uri;

    Ptr<const Glib::ustring>::Ref playlengthString(new const Glib::ustring(
                                        to_simple_string(*playlength) ));
    setMetadata(playlengthString, extentElementName, extentElementPrefix);

    setMetadata(title, titleElementName, titleElementPrefix);
}


/*------------------------------------------------------------------------------
 *  Set the value of the title field.
 *----------------------------------------------------------------------------*/
void
AudioClip :: setTitle(Ptr<const Glib::ustring>::Ref title)
                                                throw ()
{
    this->title = title;
    setMetadata(title, titleElementName, titleElementPrefix);
}


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
        std::stringstream           idStream(attribute->get_value());
        UniqueId::IdType            idValue;
        idStream >> idValue;
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

        xmlAudioClip.reset(new xmlpp::Document);
        xmlpp::Element*     root = xmlAudioClip->create_root_node("audioClip");
        root->set_attribute("id", std::string(*id));
        root->import_node(metadataElement, true);    // true = recursive

        const xmlpp::Node::NodeList dataFieldList
                                    = metadataElement->get_children();
        xmlpp::Node::NodeList::const_iterator listIt = dataFieldList.begin();

        while (listIt != dataFieldList.end()) {
            const xmlpp::Node*  dataNode = *listIt;
            std::string         prefix   = dataNode->get_namespace_prefix();
            std::string         name     = dataNode->get_name();
            const xmlpp::Element*
                                dataElement 
                              = dynamic_cast<const xmlpp::Element*> (dataNode);
            if (!dataElement) {
                ++listIt;
                continue;
            }

            if (!playlength && prefix  == extentElementPrefix
                            && name    == extentElementName
                            && dataElement->has_child_text()) {
                playlength.reset(new time_duration(duration_from_string(
                            dataElement->get_child_text()->get_content() )));
            }

            if (!title && prefix  == titleElementPrefix
                       && name    == titleElementName
                       && dataElement->has_child_text()) {
                Glib::ustring       value = dataElement->get_child_text()
                                                       ->get_content();
                Ptr<const Glib::ustring>::Ref ptrToValue(
                                                new const Glib::ustring(value));
                title = ptrToValue;
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
        eMsg += " or metadata element ";
        eMsg += extentElementPrefix + ":" + extentElementName;
        throw std::invalid_argument(eMsg);
    }
    
    Ptr<const Glib::ustring>::Ref playlengthString(new const Glib::ustring(
                                             to_simple_string(*playlength) ));
    setMetadata(playlengthString, extentElementName, extentElementPrefix);
}


/*------------------------------------------------------------------------------
 *  Return the value of a metadata field.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
AudioClip :: getMetadata(const string &key, const std::string &ns) const
                                                throw ()
{
    Ptr<Glib::ustring>::Ref value;

    if (! xmlAudioClip) {
        return value;
    }
    xmlpp::Element*         rootNode = xmlAudioClip->get_root_node();
    if (! rootNode) {
        return value;
    }
    xmlpp::Node::NodeList   rootList = rootNode->get_children("metadata");
    if (rootList.size() == 0) {
        return value;
    }
    
    xmlpp::Node*            metadata = rootList.front();
    xmlpp::Node::NodeList   nodeList = metadata->get_children(key);
    xmlpp::Node::NodeList::iterator it = nodeList.begin();
    
    while (it != nodeList.end()) {
        xmlpp::Node*        node = *it;
        if (node->get_namespace_prefix() == ns) {
            xmlpp::Element* element = dynamic_cast<xmlpp::Element*> (node);
            value.reset(new Glib::ustring(element->get_child_text()
                                                 ->get_content()));
            return value;
        }
        ++it;
    }

    return value;
}


/*------------------------------------------------------------------------------
 *  Set the value of a metadata field.
 *----------------------------------------------------------------------------*/
void
AudioClip :: setMetadata(Ptr<const Glib::ustring>::Ref value, 
                         const std::string &key,
                         const std::string &ns)
                                                throw ()
{
    if (ns == extentElementPrefix && key == extentElementName) {
        playlength.reset(new time_duration(
                                duration_from_string(*value) ));
    }
    
    if (ns == titleElementPrefix && key == titleElementName) {
        title = value;
    }

    if (! xmlAudioClip) {
        xmlAudioClip.reset(new xmlpp::Document);
    }
    xmlpp::Element*         rootNode = xmlAudioClip->get_root_node();
    if (! rootNode) {
        rootNode = xmlAudioClip->create_root_node("audioClip");
    }
    xmlpp::Node::NodeList   rootList = rootNode->get_children("metadata");
    xmlpp::Element*         metadata;
    if (rootList.size() > 0) {
        metadata = dynamic_cast<xmlpp::Element*> (rootList.front());
    }
    else {
        metadata = rootNode->add_child("metadata");
        metadata->set_namespace_declaration(defaultPrefixUri);
        metadata->set_namespace_declaration(titleElementUri, 
                                            titleElementPrefix);
        metadata->set_namespace_declaration(extentElementUri, 
                                            extentElementPrefix);
    }

    xmlpp::Node::NodeList   nodeList = metadata->get_children(key);
    xmlpp::Node::NodeList::iterator it = nodeList.begin();
    xmlpp::Element*         element;

    while (it != nodeList.end()) {
        xmlpp::Node*        node = *it;
        if (node->get_namespace_prefix() == ns) {
            element = dynamic_cast<xmlpp::Element*> (nodeList.front());
            break;
        }
        ++it;
    }
    
    if (it == nodeList.end()) {
        element = metadata->add_child(key);
        try {
            element->set_namespace(ns);
        }
        catch (xmlpp::exception &e) {
        // this namespace has not been declared; well OK, do nothing then
        }
    }
    
    element->set_child_text(*value);
}

