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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/MetadataTypeContainer.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/MetadataTypeContainer.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string MetadataTypeContainer::configElementNameStr
                                                = "metadataTypeContainer";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a metadata type container element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
MetadataTypeContainer :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        throw std::invalid_argument("bad coniguration element "
                                  + element.get_name());
    }

    // get the metadataType child elements, and process them
    xmlpp::Node::NodeList   childNodes  = element.get_children(
                                        MetadataType::getConfigElementName());
    xmlpp::Node::NodeList::iterator it  = childNodes.begin();
    xmlpp::Node::NodeList::iterator end = childNodes.end();

    while (it != end) {
        const xmlpp::Element    * metadataTypeElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
        Ptr<MetadataType>::Ref    metadataType(
                                        new MetadataType(shared_from_this()));

        metadataType->configure(*metadataTypeElement);

        if (dcNameMap.find(*metadataType->getDcName()) != dcNameMap.end()) {
            throw std::invalid_argument("trying to insert duplicate metadata "
                                        "type: " + *metadataType->getDcName());
        }
        if (metadataType->getId3Tag().get()) {
            if (id3TagMap.find(*metadataType->getId3Tag()) != id3TagMap.end()) {
                throw std::invalid_argument("trying to insert duplicate "
                                            "metadata by ID3v2 tag: "
                                          + *metadataType->getId3Tag());
            }
        }

        dcNameMap[*metadataType->getDcName()] = metadataType;
        if (metadataType->getId3Tag().get()) {
            id3TagMap[*metadataType->getId3Tag()] = metadataType;
        }
        vector.push_back(metadataType);

        ++it;
    }
}


/*------------------------------------------------------------------------------
 *  Tell if a metadata type object by the Dublin Core name exists
 *----------------------------------------------------------------------------*/
bool
MetadataTypeContainer :: existsByDcName(const Glib::ustring    dcName) const
                                                                throw ()
{
    return dcNameMap.find(dcName) != dcNameMap.end();
}


/*------------------------------------------------------------------------------
 *  Look for a metadata type object by the Dublin Core name
 *----------------------------------------------------------------------------*/
Ptr<const MetadataType>::Ref
MetadataTypeContainer :: getByDcName(const Glib::ustring    dcName)
                                                throw (std::invalid_argument)
{
    if (!existsByDcName(dcName)) {
        throw std::invalid_argument("no metadata type by the DC name " +dcName);
    }

    return dcNameMap[dcName];
}


/*------------------------------------------------------------------------------
 *  Tell if a metadata type object by the ID3v2 tag name exists.
 *----------------------------------------------------------------------------*/
bool
MetadataTypeContainer :: existsById3Tag(const Glib::ustring    id3Tag) const
                                                                throw ()
{
    return id3TagMap.find(id3Tag) != id3TagMap.end();
}


/*------------------------------------------------------------------------------
 *  Look for a metadata type object by the ID3v2 tag name.
 *----------------------------------------------------------------------------*/
Ptr<const MetadataType>::Ref
MetadataTypeContainer :: getById3Tag(const Glib::ustring    id3Tag)
                                                throw (std::invalid_argument)
{
    if (!existsById3Tag(id3Tag)) {
        throw std::invalid_argument("no metadata type by the ID3v2 Tag "
                                  + id3Tag);
    }

    return id3TagMap[id3Tag];
}

