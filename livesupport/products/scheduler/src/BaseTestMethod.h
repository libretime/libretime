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
#ifndef BaseTestMethod_h
#define BaseTestMethod_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cppunit/extensions/HelperMacros.h>

#include "LiveSupport/Core/BaseTestMethod.h"


namespace LiveSupport {
namespace Scheduler {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A base class for the test methods.
 *  Subclass this class for the methods that connect to an XML-RPC source.
 *  Make sure to call BaseTestMethod::configure() before running the
 *  test cases.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class BaseTestMethod : public LiveSupport::Core::BaseTestMethod
{
    private:
        /**
         *  The XML-RPC host name to connect to.
         */
        static std::string         xmlRpcHost;

        /**
         *  The XML-RPC port to connect to.
         */
        static unsigned int        xmlRpcPort;

        /**
         *  A flag to indicate if configuration has already been done.
         */
        static bool                 configured;


    public:

        /**
         *  Function to read configuration information, and fill out
         *  relevant attributes, such as the XML-RPC port and host.
         *
         *  @param configFileName the name of the configuration file to read.
         *  @exception std::exception in case of errors reading the
         *             configuration file
         */
        static void
        configure(std::string   configFileName)
                                                    throw (std::exception);

        /**
         *  Return the XML-RPC port to connect to.
         *
         *  @return the XML-RPC port to connect to.
         */
        static unsigned int
        getXmlRpcPort(void)                             throw ()
        {
            return xmlRpcPort;
        }

        /**
         *  Return the XML-RPC host to connect to.
         *
         *  @return the XML-RPC host to connect to.
         */
        static std::string
        getXmlRpcHost(void)                             throw ()
        {
            return xmlRpcHost;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // BaseTestMethod_h

