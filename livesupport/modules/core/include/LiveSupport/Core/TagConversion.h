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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/Attic/TagConversion.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_TagConversion_h
#define LiveSupport_Core_TagConversion_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <string>
#include <libxml++/libxml++.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Core {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class wrapper of an id3v2-to-Dublin Core conversion table.
 *  
 *  For a description of these metadata tag standards, see 
 *  http://www.id3.org/id3v2.4.0-frames.txt
 *  and http://dublincore.org/documents/dces/.
 * 
 *  This object has to be configured with an XML configuration element
 *  called tagConversionTable. This may look like the following:
 *
 *  <pre><code>
 *  &lt;tagConversionTable&gt;
 *         &lt;tag&gt;
 *             &lt;id3&gt;Title&lt;/id3&gt;
 *             &lt;dc&gt;dc:title&lt;/dc&gt;
 *         &lt;/tag&gt;
 *         &lt;tag&gt;
 *             &lt;id3&gt;Artist&lt;/id3&gt;
 *             &lt;id3&gt;TPE1&lt;/id3&gt;
 *             &lt;dc&gt;dcterms:extent&lt;/dc&gt;
 *         &lt;/tag&gt;
 *             ...
 *  &lt;/tagConversionTable&gt;
 *  </code></pre>
 *
 *  Note that more than one id3 tag name can map to the same dc tag name.
 *
 *  The DTD for the above element is:
 *
 *  <pre><code>
 *  &lt;!ELEMENT tagConversionTable (tag*) &gt;
 *  &lt;!ATTLIST tag    (id3+, dc) &gt;
 *  &lt;!ATTLIST id3    (#CDATA) &gt;
 *  &lt;!ATTLIST dc     (#CDATA) &gt;
 *  </code></pre>
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.2 $
 */
class TagConversion
{
    private:
        /**
         *  The name of the configuration XML element used by this class.
         */
        static const std::string            configElementNameStr;

        /**
         *  The type for the conversion table.
         */
        typedef std::map<const std::string, const std::string>
                                            TableType;

        /**
         *  The conversion table, as read from the configuration file.
         */
        static Ptr<TableType>::Ref          table;

        /**
         *  The default constructor.
         */
        TagConversion(void)                        throw ()
        {
        }


    public:
        /**
         *  Return the name of the XML element this class expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                      throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Configure the class based on the XML element supplied.
         *  The supplied element is expected to be of the name
         *  returned by configElementName().
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuration information
         */
        static void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument);

        /**
         *  Check whether the class has been configured.
         *
         *  @return true or false
         */
        static bool
        isConfigured(void)                      throw ()
        {
            return (table.get() != 0);
        }

        /**
         *  Check whether a given id3v2 tag is listed in the table.
         *
         *  @return true or false
         *  @exception std::invalid_argument if the conversion table has not
         *             not been configured yet
         */
        static bool
        existsId3Tag(const std::string &id3Tag) throw (std::invalid_argument)
        {
            if (table) {
                return (table->find(id3Tag) != table->end());
            } else {
                throw std::invalid_argument("conversion table has "
                                            "not been configured");
            }
        }

        /**
         *  Convert an id3v2 tag to a Dublin Core tag (with namespace).
         *
         *  @return the converted tag
         *  @exception std::invalid_argument if the conversion table has not
         *             not been configured yet, or if the id3Tag name does
         *             not exist in the table
         */
        static const std::string &
        id3ToDublinCore(const std::string &id3Tag)
                                                throw (std::invalid_argument);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_TagConversion_h

