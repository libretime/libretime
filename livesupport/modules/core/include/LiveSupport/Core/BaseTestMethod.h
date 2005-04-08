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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/BaseTestMethod.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_BaseTestMethod_h
#define LiveSupport_Core_BaseTestMethod_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include <libxml++/libxml++.h>
#include <cppunit/extensions/HelperMacros.h>


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A base class for the test methods.
 *  Subclass this class for the methods that use configuration files.
 *  This class gives helpers to access the configuration files
 *  from various locations (~/.livesupport, ./etc)
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class BaseTestMethod : public CPPUNIT_NS::TestFixture
{
    private:
        /**
         *  Get the current working directory.
         *
         *  @return the current working directory.
         */
        static std::string
        getCwd(void)                                    throw ();

    public:
        /**
         *  Return the full path for a configuration file.
         *
         *  @param configFileName the name of the configuration file.
         *  @return the full path of the configuration file, found in the
         *          appropriate directory.
         *   @exception std::invalid_argument if the specified config file
         *              does not exist.
         */
        static std::string
        getConfigFile(const std::string   configFileName)
                                            throw (std::invalid_argument);

        /**
         *  Helper function to return an XML Document object based on
         *  a config file name.
         *  First, the proper location of the config file is found.
         *
         *  @param parser the XML DOM parser to use for parsing.
         *  @param configFileName the name of the configuration file.
         *  @return an XML document, containing the contents of the 
         *          config file
         *  @exception std::invalid_argument if the configuration file
         *             could not be found
         *  @exception std::exception on parsing errors.
         */
        static const xmlpp::Document *
        getConfigDocument(xmlpp::DomParser    & parser,
                          const std::string     configFileName)
                                                throw (std::invalid_argument,
                                                       std::exception);
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_BaseTestMethod_h

