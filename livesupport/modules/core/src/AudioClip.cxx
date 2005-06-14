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
    Version  : $Revision: 1.27 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/AudioClip.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>
#include <typeinfo>
#include <fileref.h>    // for TagLib
#include <mpegfile.h>   // for TagLib
#include <id3v1tag.h>   // for TagLib
#include <id3v2tag.h>   // for TagLib

#include "LiveSupport/Core/AudioClip.h"

using namespace boost::posix_time;

using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
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
 *  The name of the attribute to get the title of the audio clip.
 */
static const std::string    titleAttrName = "title";

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
 *  The prefix of the title metadata element.
 */
static const std::string    titleElementPrefix = "dc";

/**
 *  The name of the title metadata element.
 */
static const std::string    titleElementName = "title";

/**
 *  The prefix for the Live Support extension elements.
 */
static const std::string    liveSupportNamespacePrefix = "ls";

/**
 *  The prefix for the "xml:" prefix elements.
 */
static const std::string    xmlNamespacePrefix = "xml";

/**
 *  The URI identifier for the default namespace
 */
static const std::string    defaultNamespaceUri 
                            = "http://mdlf.org/livesupport/elements/1.0/";

/**
 *  The URI identifier for the "ls" prefix.
 */
static const std::string    liveSupportNamespaceUri 
                            = "http://mdlf.org/livesupport/elements/1.0/";

/**
 *  The URI identifier for the "dc" prefix
 */
static const std::string    dcNamespaceUri 
                            = "http://purl.org/dc/elements/1.1/";

/**
 *  The URI identifier for the "dcterms" prefix
 */
static const std::string    dctermsNamespaceUri 
                            = "http://purl.org/dc/terms/";

/**
 *  The URI identifier for the "xml" prefix
 */
static const std::string    xmlNamespaceUri 
                            = "http://www.w3.org/XML/1998/namespace";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Copy constructor.
 *----------------------------------------------------------------------------*/
AudioClip :: AudioClip(const AudioClip & otherAudioClip)   throw ()
                        : Playable(AudioClipType)
{
    this->id            = otherAudioClip.id;
    this->title         = otherAudioClip.title;
    this->playlength    = otherAudioClip.playlength;
    this->uri           = otherAudioClip.uri;
    this->token         = otherAudioClip.token;

    if (otherAudioClip.xmlAudioClip) {
        xmlAudioClip.reset(new xmlpp::Document);
        xmlAudioClip->create_root_node_by_import(
                        otherAudioClip.xmlAudioClip->get_root_node(),
                        true);     // true == recursive
    }
}


/*------------------------------------------------------------------------------
 *  Test constructor without title.
 *----------------------------------------------------------------------------*/
AudioClip :: AudioClip(Ptr<UniqueId>::Ref       id,
                  Ptr<time_duration>::Ref       playlength,
                  Ptr<const std::string>::Ref   uri)
                                                           throw ()
                        : Playable(AudioClipType)
{
    this->id         = id;
    this->title.reset(new Glib::ustring(""));
    this->playlength = playlength;
    this->uri        = uri;

    setMetadata(title, titleElementName, titleElementPrefix);
    
    Ptr<const Glib::ustring>::Ref playlengthString(new const Glib::ustring(
                                        toFixedString(playlength) ));
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
                        : Playable(AudioClipType)
{
    this->id         = id;
    this->title      = title;
    this->playlength = playlength;
    this->uri        = uri;

    setMetadata(title, titleElementName, titleElementPrefix);

    Ptr<const Glib::ustring>::Ref playlengthString(new const Glib::ustring(
                                        toFixedString(playlength) ));
    setMetadata(playlengthString, extentElementName, extentElementPrefix);
}


/*------------------------------------------------------------------------------
 *  Constructor without ID.
 *----------------------------------------------------------------------------*/AudioClip :: AudioClip(Ptr<const Glib::ustring>::Ref    title,
                       Ptr<time_duration>::Ref          playlength,
                       Ptr<const std::string>::Ref      uri)
                                                           throw ()
                        : Playable(AudioClipType)
{
    this->title      = title;
    this->playlength = playlength;
    this->uri        = uri;

    setMetadata(title, titleElementName, titleElementPrefix);
 
    Ptr<const Glib::ustring>::Ref playlengthString(new const Glib::ustring(
                                        toFixedString(playlength) ));
    setMetadata(playlengthString, extentElementName, extentElementPrefix);
}
 
 
/*------------------------------------------------------------------------------
 *  Convert to an XmlRpcValue.
 *----------------------------------------------------------------------------*/
AudioClip :: operator XmlRpc::XmlRpcValue() const
                                                throw()
{
    XmlRpc::XmlRpcValue     xmlRpcValue;
    xmlRpcValue[configElementNameStr] = std::string(*getXmlDocumentString());
    
    return xmlRpcValue;
}


/*------------------------------------------------------------------------------
 *  Construct from an XmlRpcValue.
 *----------------------------------------------------------------------------*/
AudioClip :: AudioClip(XmlRpc::XmlRpcValue &  xmlRpcValue)
                                                throw (std::invalid_argument)
                        : Playable(AudioClipType)
{
    if (!xmlRpcValue.hasMember(configElementNameStr)) {
        throw std::invalid_argument("no audio clip data found in XmlRpcValue");
    }
    
    xmlpp::DomParser    parser;
    try {
        parser.parse_memory(std::string(xmlRpcValue[configElementNameStr]));
    } catch (xmlpp::exception &e) {
        throw std::invalid_argument("error parsing XML document");
    }
    
    configure(*parser.get_document()->get_root_node());     // may throw
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
        id.reset(new UniqueId(attribute->get_value()));
    }

    if (!playlength
            && (attribute = element.get_attribute(playlengthAttrName))) {
        playlength.reset(new time_duration(duration_from_string(
                                                     attribute->get_value() )));
        Ptr<const Glib::ustring>::Ref playlengthString(new const Glib::ustring(
                                                     attribute->get_value() ));
        setMetadata(playlengthString, extentElementName, extentElementPrefix);
    }

    if (!title
            && (attribute = element.get_attribute(titleAttrName))) {
        title.reset(new const Glib::ustring(attribute->get_value()));
        setMetadata(title, titleElementName, titleElementPrefix);
    }

    if (!uri 
            && (attribute = element.get_attribute(uriAttrName))) {
        uri.reset(new const std::string(attribute->get_value()));
    }

    xmlpp::Node::NodeList       childNodes 
                                = element.get_children(metadataElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it != childNodes.end()) {
        const xmlpp::Element    * metadataElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);

        xmlAudioClip.reset(new xmlpp::Document);
        xmlpp::Element*     root = xmlAudioClip->create_root_node(
                                                        configElementNameStr);
        root->set_attribute(idAttrName, std::string(*id));
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
                            && name    == extentElementName) {
                if (dataElement->has_child_text()) {
                    playlength.reset(new time_duration(duration_from_string(
                            dataElement->get_child_text()->get_content() )));
                } else {              // or just leave blank?  bad either way
                    playlength.reset(new time_duration(0,0,0,0));
                }
            }

            if (!title && prefix  == titleElementPrefix
                       && name    == titleElementName) {
                Glib::ustring       value;
                if (dataElement->has_child_text()) {
                    value = dataElement->get_child_text()->get_content();
                } else {
                    value = "";
                }
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
    
    if (!title) {
        title.reset(new const Glib::ustring(""));
    }
}


/*------------------------------------------------------------------------------
 *  Return the value of a metadata field.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
AudioClip :: getMetadata(const string &key) const
                                                throw ()
{
    std::string name, prefix;
    separateNameAndNameSpace(key, name, prefix);

    Ptr<Glib::ustring>::Ref value;

    if (! xmlAudioClip) {
        return value;
    }
    xmlpp::Element*         rootNode = xmlAudioClip->get_root_node();
    if (! rootNode) {
        return value;
    }
    xmlpp::Node::NodeList   rootList = rootNode->get_children(
                                                        metadataElementName);
    if (rootList.size() == 0) {
        return value;
    }

    xmlpp::Node*            metadata = rootList.front();
    xmlpp::Node::NodeList   nodeList = metadata->get_children(name);
    xmlpp::Node::NodeList::iterator it = nodeList.begin();
    
    while (it != nodeList.end()) {
        xmlpp::Node*        node = *it;
        if (node->get_namespace_prefix() == prefix) {
            xmlpp::Element*   element  = dynamic_cast<xmlpp::Element*> (node);
            xmlpp::TextNode*  textNode = element->get_child_text();
            if (textNode) {
                value.reset(new Glib::ustring(textNode->get_content()));
            } else {
                value.reset(new Glib::ustring(""));
            }
            return value;
        }
        ++it;
    }

    return value;
}


/*------------------------------------------------------------------------------
 *  Set the value of a metadata field (public).
 *----------------------------------------------------------------------------*/
void
AudioClip :: setMetadata(Ptr<const Glib::ustring>::Ref value, 
                         const std::string &key)
                                                throw ()
{
    std::string name, prefix;
    separateNameAndNameSpace(key, name, prefix);
    setMetadata(value, name, prefix);
}


/*------------------------------------------------------------------------------
 *  Set the value of a metadata field (private).
 *----------------------------------------------------------------------------*/
void
AudioClip :: setMetadata(Ptr<const Glib::ustring>::Ref value, 
                         const std::string &name, const std::string &prefix)
                                                throw ()
{
    if (prefix == extentElementPrefix && name == extentElementName) {
        playlength.reset(new time_duration(
                                duration_from_string(*value) ));
    }
    
    if (prefix == titleElementPrefix && name == titleElementName) {
        title = value;
    }

    // create a new xmlpp::Document for the metadata if necessary
    if (! xmlAudioClip) {
        xmlAudioClip.reset(new xmlpp::Document);
    }
    xmlpp::Element*         rootNode = xmlAudioClip->get_root_node();
    if (! rootNode) {
        rootNode = xmlAudioClip->create_root_node(configElementNameStr);
        if (id) {
            rootNode->set_attribute(idAttrName, std::string(*id));
        }
    }
    xmlpp::Node::NodeList   rootList = rootNode->get_children(
                                                        metadataElementName);
    xmlpp::Element*         metadata;
    if (rootList.size() > 0) {
        metadata = dynamic_cast<xmlpp::Element*> (rootList.front());
    } else {
        metadata = rootNode->add_child(metadataElementName);
        metadata->set_namespace_declaration(defaultNamespaceUri);
        metadata->set_namespace_declaration(liveSupportNamespaceUri, 
                                            liveSupportNamespacePrefix);
        metadata->set_namespace_declaration(dcNamespaceUri, 
                                            titleElementPrefix);
        metadata->set_namespace_declaration(dctermsNamespaceUri, 
                                            extentElementPrefix);
        metadata->set_namespace_declaration(xmlNamespaceUri, 
                                            xmlNamespacePrefix);
    }

    // find the element to be modified
    xmlpp::Node::NodeList   nodeList    = metadata->get_children(name);
    xmlpp::Node::NodeList::iterator it  = nodeList.begin();
    xmlpp::Element*         element     = 0;

    while (it != nodeList.end()) {
        xmlpp::Node*        node = *it;
        if (node->get_namespace_prefix() == prefix) {
            element = dynamic_cast<xmlpp::Element*> (nodeList.front());
            break;
        }
        ++it;
    }
    
    // or add it if it did not exist before
    if (it == nodeList.end()) {
        element = metadata->add_child(name);
        try {
            element->set_namespace(prefix);
        }
        catch (xmlpp::exception &e) {
        // this namespace has not been declared; well OK, do nothing then
        }
    }
    
    element->set_child_text(*value);
}


/*------------------------------------------------------------------------------
 *  Return a string containing the essential fields of this object, in XML.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
AudioClip :: getXmlElementString(void) const    throw ()
{
    Ptr<Glib::ustring>::Ref     xmlString(new Glib::ustring);
    
    xmlString->append("<");
    xmlString->append(configElementNameStr + " ");
    xmlString->append(idAttrName + "=\"" 
                                 + std::string(*id) 
                                 + "\" ");
    xmlString->append(playlengthAttrName + "=\"" 
                                         + toFixedString(playlength)
                                         + "\" ");
    xmlString->append(Glib::ustring(titleAttrName) + "=\"" 
                                                   + *title
                                                   + "\"/>");
    return xmlString;
}


/*------------------------------------------------------------------------------
 *  Return a string containing an XML representation of this audio clip.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
AudioClip :: getXmlDocumentString() const       throw ()
{
    Ptr<xmlpp::Document>::Ref   localDocument;

    if (xmlAudioClip) {
        localDocument = xmlAudioClip;
    } else {
        localDocument.reset(new xmlpp::Document());
        xmlpp::Element* rootNode = localDocument->create_root_node(
                                                        configElementNameStr);
        if (id) {
            rootNode->set_attribute(idAttrName, std::string(*id));
        }
        
        xmlpp::Element*     metadata = rootNode->add_child(metadataElementName);
        metadata->set_namespace_declaration(defaultNamespaceUri);
        metadata->set_namespace_declaration(liveSupportNamespaceUri, 
                                            liveSupportNamespacePrefix);
        metadata->set_namespace_declaration(dcNamespaceUri, 
                                            titleElementPrefix);
        metadata->set_namespace_declaration(dctermsNamespaceUri, 
                                            extentElementPrefix);
        metadata->set_namespace_declaration(xmlNamespaceUri, 
                                            xmlNamespacePrefix);
    }
    
    Ptr<Glib::ustring>::Ref     metadataString(new Glib::ustring(
                                            localDocument->write_to_string() ));
    return metadataString;
}


/*------------------------------------------------------------------------------
 *  Read the metadata contained in the id3v2 tag of the binary sound file.
 *----------------------------------------------------------------------------*/
void
AudioClip :: readTag(Ptr<MetadataTypeContainer>::Ref  metadataTypes)
                                                throw (std::invalid_argument)
{
    if (!getUri()) {
        throw std::invalid_argument("audio clip has no uri field");
    }
    
    if (!TagLib::File::isReadable(getUri()->c_str())) {
        throw std::invalid_argument("binary sound file not found");
    }
    
    TagLib::MPEG::File      mpegFile(getUri()->c_str());
    TagLib::ID3v2::Tag*     id3v2Tag = mpegFile.ID3v2Tag();
    if (id3v2Tag) {
        Ptr<const MetadataType>::Ref    metadata;
        Ptr<const Glib::ustring>::Ref   value;

        TagLib::ID3v2::FrameListMap     frameListMap = id3v2Tag->frameListMap();
        TagLib::ID3v2::FrameListMap::ConstIterator it;

        for (it = frameListMap.begin(); it != frameListMap.end(); ++it) {
            std::string     keyString(it->first.data(), 4);
            try {
                metadata = metadataTypes->getById3Tag(keyString);
                TagLib::ID3v2::FrameList frameList = it->second;
                if (!frameList.isEmpty()) {
                    value.reset(new const Glib::ustring(
                                frameList.front()->toString().to8Bit(true)));
                    setMetadata(value, *metadata->getDcName());
                }
            } catch (std::invalid_argument &e) {
                // id3v2 tag name not found in MetadataTypeContainer
                // TODO: print warning?
            }
        }
        return;
    }

    TagLib::FileRef         genericFileRef(getUri()->c_str());
    TagLib::Tag*            tag = genericFileRef.tag();
    if (tag) {
        TagLib::String                  stringValue;
        TagLib::uint                    intValue;
        Ptr<const Glib::ustring>::Ref   value;

        stringValue = tag->artist();
        if (!stringValue.isNull()) {
            value.reset(new const Glib::ustring(stringValue.to8Bit(true)));
            setMetadata(value, "dc:creator");
        }
        
        stringValue = tag->title();
        if (!stringValue.isNull()) {
            value.reset(new const Glib::ustring(stringValue.to8Bit(true)));
            setMetadata(value, "dc:title");
        }
        
        stringValue = tag->album();
        if (!stringValue.isNull()) {
            value.reset(new const Glib::ustring(stringValue.to8Bit(true)));
            setMetadata(value, "dc:source");
        }
        
        stringValue = tag->comment();
        if (!stringValue.isNull()) {
            value.reset(new const Glib::ustring(stringValue.to8Bit(true)));
            setMetadata(value, "dc:description");
        }

        stringValue = tag->genre();
        if (!stringValue.isNull()) {
            value.reset(new const Glib::ustring(stringValue.to8Bit(true)));
            setMetadata(value, "dc:type");
        }

        intValue = tag->year();
        if (intValue != 0) {
            std::stringstream   yearString;
            yearString << intValue;
            value.reset(new const Glib::ustring(yearString.str()));
            setMetadata(value, "ls:year");
        }
        
        intValue = tag->track();
        if (intValue != 0) {
            std::stringstream   trackString;
            trackString << intValue;
            value.reset(new const Glib::ustring(trackString.str()));
            setMetadata(value, "ls:track_num");
        }
    }
}


/*------------------------------------------------------------------------------
 *  Separate a key into the metadata name and its namespace
 *----------------------------------------------------------------------------*/
void
LiveSupport::Core :: separateNameAndNameSpace(const std::string & key,
                                                 std::string &       name,
                                                 std::string &       prefix)
                                                            throw ()
{
    unsigned int    colonPosition = key.find(':');

    if (colonPosition != std::string::npos) {               // there is a colon
        prefix   = key.substr(0, colonPosition);
        name     = key.substr(colonPosition+1);
    } else {                                                // no colon found
        prefix   = "";
        name     = key;
    }
}

