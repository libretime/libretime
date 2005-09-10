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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/XmlRpcException.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_XmlRpcException_h
#define LiveSupport_Core_XmlRpcException_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace Core {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Common parent of exception classes for XML-RPC related problems.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class XmlRpcException : public std::exception
{
    private:
        /**
         *  The message of the exception.
         */
        Ptr<std::string>::Ref       message;

        /**
         *  A possible embedded exception.
         */
        const std::exception      & parent;

    public:
        /**
         *  Constructor based on a string.
         *
         *  @param msg the message of the exception.
         */
        XmlRpcException(const std::string &msg)             throw ()
                    : parent(*this)
        {
            message.reset(new std::string(msg));
        }

        /**
         *  Constructor based on a parent exception.
         *
         *  @param parent the parent exception to this one.
         */
        XmlRpcException(const std::exception  & parent)     throw ()
                : parent(parent)
        {
            message.reset(new std::string(parent.what()));
        }

        /**
         *  Constructor based on a message ant a parent exception.
         *
         *  @param msg the message of the exception.
         *  @param parent the parent exception.
         */
        XmlRpcException(const std::string    & msg,
                        const std::exception & parent)      throw ();

        /**
         *  Virtual destructor.
         */
        ~XmlRpcException(void)                              throw ()
        {
        }

        /**
         *  Get the message of the exception.
         *
         *  @return the message of the exception.
         */
        virtual const char *
        what(void) const                                    throw ()
        {
            return message->c_str();
        }

        /**
         *  Get the parent exception.
         *
         *  @return the parent exception, which may be null.
         */
        virtual const std::exception *
        getParent(void) const                               throw ()
        {
            return &parent == this ? 0 : &parent;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_XmlRpcException_h

