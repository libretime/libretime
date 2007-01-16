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
#ifndef LiveSupport_Core_XmlRpcMethodFaultException_h
#define LiveSupport_Core_XmlRpcMethodFaultException_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>

#include "LiveSupport/Core/XmlRpcException.h"

namespace LiveSupport {
namespace Core {


/* ================================================================ constants */

namespace {

/*------------------------------------------------------------------------------
 *  The default fault code, the value when no fault code is set.
 *----------------------------------------------------------------------------*/
const int           defaultFaultCode = -1;

}

/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Exception signaling an XML-RPC problem: the XML-RPC method returned a fault
 *  response.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class XmlRpcMethodFaultException : public XmlRpcException
{
    private:
        /**
         *  The XML-RPC faultCode of the exception.
         */
        int             faultCode;


    public:
        /**
         *  Constructor based on a string.
         *
         *  @param msg the message of the exception.
         */
        XmlRpcMethodFaultException(const std::string &msg)      throw ()
                    : XmlRpcException(msg),
                      faultCode(defaultFaultCode)
        {
        }

        /**
         *  Constructor based on a parent exception.
         *
         *  @param parent the parent exception to this one.
         */
        XmlRpcMethodFaultException(const std::exception  & parent)
                                                                    throw ()
                : XmlRpcException(parent),
                  faultCode(defaultFaultCode)
        {
        }

        /**
         *  Constructor based on a message and a parent exception.
         *
         *  @param msg the message of the exception.
         *  @param parent the parent exception.
         */
        XmlRpcMethodFaultException(const std::string    & msg,
                                   const std::exception & parent)
                                                                    throw ()
                : XmlRpcException(msg, parent),
                  faultCode(defaultFaultCode)
        {
        }

        /**
         *  Constructor based on a fault code, fault string pair.
         *
         *  @param  methodName  the name of the method throwing the exception.
         *  @param  faultCode   the code of the exception.
         *  @param  faultString the message of the exception.
         */
        XmlRpcMethodFaultException(const std::string &  methodName,
                                   int                  faultCode,
                                   const std::string &  faultString)
                                                                    throw ()
                : XmlRpcException(""),
                  faultCode(faultCode)
        {
            std::stringstream   msg;
            msg << "XML-RPC method '" 
                << methodName
                << "' returned error message:\n"
                << faultCode
                << " - "
                << faultString;
            setMessage(msg.str());
        }

        /**
         *  Virtual destructor.
         */
        ~XmlRpcMethodFaultException(void)                           throw ()
        {
        }

        /**
         *  Get the XML-RPC faultCode of the exception.
         *
         *  @return the fault code, if one is set; or -1 if not.
         */
        int
        getFaultCode(void) const                                    throw ()
        {
            return faultCode;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_XmlRpcMethodFaultException_h

