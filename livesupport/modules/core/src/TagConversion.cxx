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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/Attic/TagConversion.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TagConversion.h"


using namespace xmlpp;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string   TagConversion::configElementNameStr = "tagConversionTable";

/*------------------------------------------------------------------------------
 *  Initialize the table to a null pointer at program start
 *----------------------------------------------------------------------------*/
Ptr<TagConversion::TableType>::Ref
                    TagConversion::table = Ptr<TagConversion::TableType>::Ref();


/* ================================================  local constants & macros */

/**
 *  The name of the tag child element.
 */
static const std::string    tagElementName = "tag";

/**
 *  The name of the id3v2 tag grandchild element.
 */
static const std::string    id3TagElementName = "id3";

/**
 *  The name of the dublin core tag grandchild element.
 */
static const std::string    dcTagElementName = "dc";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the class based on the XML element supplied.
 *----------------------------------------------------------------------------*/
void
TagConversion :: configure(const xmlpp::Element  & element)
                                               throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }
    
    table.reset(new TableType);         // discard old table, if any
    
    Node::NodeList
                listOfTags  = element.get_children(tagElementName);
    Node::NodeList::iterator
                listIt      = listOfTags.begin();

    while (listIt != listOfTags.end()) {
        Node::NodeList  id3Tags = (*listIt)->get_children(id3TagElementName);
        Node::NodeList  dcTags  = (*listIt)->get_children(dcTagElementName);
        
        if (id3Tags.size() != 1 || dcTags.size() != 1) {
            std::string eMsg = "bad <";
            eMsg += tagElementName;
            eMsg += "> element found";
            throw std::invalid_argument(eMsg);
        }

        Element*    id3Element  = dynamic_cast<Element*> (id3Tags.front());
        Element*    dcElement   = dynamic_cast<Element*> (dcTags.front());

        table->insert(std::make_pair(
                                id3Element->get_child_text()->get_content(),
                                dcElement ->get_child_text()->get_content() ));
        ++listIt;
    }
}


/*------------------------------------------------------------------------------
 *  Convert an id3v2 tag to a Dublin Core tag (with namespace).
 *----------------------------------------------------------------------------*/
const std::string &
TagConversion :: id3ToDublinCore(const std::string &id3Tag)
                                               throw (std::invalid_argument)
{
    if (!table) {
        throw std::invalid_argument("conversion table has not been configured");
    }

    TableType::const_iterator   it = table->find(id3Tag);
    if (it != table->end()) {
        return (*table)[id3Tag];
    } else {
        throw std::invalid_argument("unknown id3 tag name");
    }
}

