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

#include "LiveSupport/Core/MetadataTypeContainer.h"
#include "LiveSupport/Core/MetadataType.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string MetadataType::configElementNameStr = "metadataType";

/**
 *  The name of the attribute of the DC name in the metadataType element.
 */
static const std::string    dcNameAttrName = "dcName";

/**
 *  The name of the attribute of the ID3v2 tag in the metadataType element
 */
static const std::string    id3TagAttrName = "id3Tag";

/**
 *  The name of the attribute of the localization key in the
 *  metadataType element.
 */
static const std::string    localizationKeyAttrName = "localizationKey";

/**
 *  The name of the attribute of the tab name in the metadataType element
 */
static const std::string    tabAttrName = "tab";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
MetadataType :: MetadataType(Ptr<MetadataTypeContainer>::Ref    container)
                                                                    throw ()
        : container(container),
          tab(noTab)
{
}


/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
MetadataType :: MetadataType(Ptr<MetadataTypeContainer>::Ref    container,
                             Glib::ustring                      dcName,
                             Glib::ustring                      id3Tag,
                             Glib::ustring                      localizationKey,
                             TabType                            tab)
                                                                    throw ()
        : container(container),
          tab(tab)
{
    this->dcName.reset(new Glib::ustring(dcName));
    this->id3Tag.reset(new Glib::ustring(id3Tag));
    this->localizationKey.reset(new Glib::ustring(localizationKey));
}


/*------------------------------------------------------------------------------
 *  Create a metadata type element object based on an XML element.
 *----------------------------------------------------------------------------*/
void
MetadataType :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        throw std::invalid_argument("bad configuration element "
                                  + element.get_name());
    }

    const xmlpp::Attribute*     attribute;

    // set the DC name
    if (!(attribute = element.get_attribute(dcNameAttrName))) {
        throw std::invalid_argument("missing attribute " + dcNameAttrName);
    }
    dcName.reset(new Glib::ustring(attribute->get_value()));

    // get the ID3v2 tag name, optional
    if ((attribute = element.get_attribute(id3TagAttrName))) {
        id3Tag.reset(new Glib::ustring(attribute->get_value()));
    }

    // get the localization key
    if (!(attribute = element.get_attribute(localizationKeyAttrName))) {
        throw std::invalid_argument("missing attribute "
                                  + localizationKeyAttrName);
    }
    localizationKey.reset(new Glib::ustring(attribute->get_value()));

    // get the tab, optional
    tab = noTab;
    if ((attribute = element.get_attribute(tabAttrName))) {
        Glib::ustring   tabString = attribute->get_value();
        if (tabString == "main") {
            tab = mainTab;
        } else if (tabString == "music") {
            tab = musicTab;
        } else if (tabString == "voice") {
            tab = voiceTab;
        }
    }
    
    // get the constraint, optional
    xmlpp::Node::NodeList   childNodes = element.get_children(
                                    MetadataConstraint::getConfigElementName());
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it != childNodes.end()) {
        const xmlpp::Element *  constraintElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
        if (constraintElement) {
            constraint.reset(new MetadataConstraint());
            constraint->configure(*constraintElement);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Return the localized name for this metadata type.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
MetadataType :: getLocalizedName(void) const
                                                throw (std::invalid_argument)
{
    return container->getResourceUstring(*localizationKey);
}


/*------------------------------------------------------------------------------
 *  Check that the given value satisfies the constraint.
 *----------------------------------------------------------------------------*/
bool
MetadataType :: check(Ptr<const Glib::ustring>::Ref     value) const
                                                                    throw ()
{
    if (!value) {
        return false;
    }
    
    if (constraint) {
        try {
            return constraint->check(value);
        } catch (std::logic_error &e) {
            return false;
        }
    } else {
        return true;
    }
}

