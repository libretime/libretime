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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/include/LiveSupport/Storage/Attic/StorageException.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Storage_StorageException_h
#define LiveSupport_Storage_StorageException_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

namespace LiveSupport {
namespace Storage {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Common parent of exception classes for this module.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class StorageException : public std::runtime_error
{
    public:
        StorageException(const std::string &msg) 
                                        : std::runtime_error(msg) {
        }
};

/**
  *  XML-RPC communication problem.
  */
class XmlRpcCommunicationException : public StorageException
{
    public:
        XmlRpcCommunicationException(const std::string &msg) 
                                        : StorageException(msg) {
        }
};
        
/**
  *  XML-RPC fault thrown by the method called.
  */
class XmlRpcMethodFaultException : public StorageException
{
    public:
        XmlRpcMethodFaultException(const std::string &msg) 
                                        : StorageException(msg) {
        }
};

/**
  *  Unexpected response from the XML-RPC method.
  */
class XmlRpcMethodResponseException : public StorageException
{
    public:
        XmlRpcMethodResponseException(const std::string &msg) 
                                        : StorageException(msg) {
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Storage
} // namespace LiveSupport

#endif // LiveSupport_Storage_StorageException_h

