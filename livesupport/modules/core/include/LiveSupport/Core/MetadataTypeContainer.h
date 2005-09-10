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
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_MetadataTypeContainer_h
#define LiveSupport_Core_MetadataTypeContainer_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <map>

#include <boost/enable_shared_from_this.hpp>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Core/MetadataType.h"


namespace LiveSupport {
namespace Core {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Container holding MetadataType objects.
 *
 *  This object has to be configured with an XML configuration element
 *  called metadataTypeContainer. This may look like the following:
 *
 *  <pre><code>
 *  &lt;metadataTypeContainer&gt;
 *      &lt;metadataType ... /&gt;
 *      &lt;metadataType ... /&gt;
 *      ...
 *      &lt;metadataType ... /&gt;
 *  &lt;/metadataTypeContainer&gt;
 *  </code></pre>
 *
 *  The DTD for the expected XML element is the following:
 *
 *  <pre><code>
 *  <!ELEMENT metadataTypeContainer (metadataType+) >
 *  </code></pre>
 *
 *  For a description of the metadataType XML element, see the documentation
 *  for the MetadataType class.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see MetadataType
 */
class MetadataTypeContainer : public Configurable,
                              public LocalizedObject,
                   public boost::enable_shared_from_this<MetadataTypeContainer>
{
    public:
        /**
         *  A vector type holding contant MetadataType references.
         */
        typedef std::vector<Ptr<const MetadataType>::Ref>       Vector;


    private:
        /**
         *  Map type for storing MetadataType objects by Glib::ustrings.
         */
        typedef std::map<Glib::ustring, Ptr<const MetadataType>::Ref>  NameMap;

        /**
         *  The name of the configuration XML element used by
         *  MetadataTypeContainer.
         */
        static const std::string    configElementNameStr;

        /**
         *  A vector holding all MetadataType references.
         */
        Vector                      vector;

        /**
         *  Map of MetadaType objects, stored by DC name.
         */
        NameMap                     dcNameMap;

        /**
         *  Map of MetadaType objects, stored by ID3v2 tags.
         */
        NameMap                     id3TagMap;


    public:
        /**
         *  Constructor.
         *
         *  @param bundle the resource bundle holding the localized resources.
         */
        MetadataTypeContainer(Ptr<ResourceBundle>::Ref  bundle)
                                                                throw ()
            : LocalizedObject(bundle)
        {
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~MetadataTypeContainer(void)                            throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                              throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Configure the metadata object based on an XML configuration element.
         *
         *  @param elemen the XML configuration element.
         *  @exception std::invalid_argument of the supplied XML element
         *             contains bad configuration information
         */
        virtual void
        configure(const xmlpp::Element &element)
                                                throw (std::invalid_argument);

        /**
         *  Return an interator holding all MetadataType objects.
         *  The iterator holds objects of type Ptr<const MetadataType>::Ref.
         *
         *  @return an iterator holding the available metadata types.
         */
        Vector::const_iterator
        begin(void) const                                           throw ()
        {
            return vector.begin();
        }

        /**
         *  Return an interator pointing to the end of MetadataType objects.
         *  The iterator holds objects of type Ptr<const MetadataType>::Ref.
         *
         *  @return an iterator pointing to the end of metadata types.
         */
        Vector::const_iterator
        end(void) const                                             throw ()
        {
            return vector.end();
        }

        /**
         *  Tells if a MetadataType object exists with the specified
         *  Dublic Core name.
         *
         *  @param dcName the DC name of the metadata type.
         *  @return true if a metadata type exists with the specified name,
         *          false otherwise
         */
        bool
        existsByDcName(const Glib::ustring     dcName) const        throw ();

        /**
         *  Return a MetadataType object, by Dublic Core name.
         *
         *  @param dcName the DC name of the metadata type.
         *  @return a MetadataType object with the supplied DC name
         *  @exception std::invalid_argument if no metadata type exists
         *             with the suplied name.
         */
        Ptr<const MetadataType>::Ref
        getByDcName(const Glib::ustring     dcName)
                                                throw (std::invalid_argument);

        /**
         *  Tells if a MetadataType object exists with the specified
         *  ID3v2 tag.
         *
         *  @param id3Tag the ID3v2 tag of the metadata type.
         *  @return true if a metadata type exists with the specified tag name,
         *          false otherwise
         */
        bool
        existsById3Tag(const Glib::ustring     id3Tag) const        throw ();

        /**
         *  Return a MetadataType object, by ID3v2 tag.
         *
         *  @param id3Tag the ID3v2 tag of the metadata type.
         *  @return a MetadataType object with the supplied ID3v2 tag name.
         *  @exception std::invalid_argument if no metadata type exists
         *             with the suplied tag name.
         */
        Ptr<const MetadataType>::Ref
        getById3Tag(const Glib::ustring     id3Tag)
                                                throw (std::invalid_argument);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_MetadataTypeContainer_h

