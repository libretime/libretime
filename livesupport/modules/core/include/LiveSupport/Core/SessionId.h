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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/SessionId.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_SessionId_h
#define LiveSupport_Core_SessionId_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing session identifiers.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.3 $
 */
class SessionId
{
    private:
        /**
         *  The value of the session ID.
         */
        std::string             id;

        /**
         *  Default constructor.
         */
        SessionId(void)                         throw ()
        {
        }


    public:
        /**
         *  The type for the value the session id is represented in.
         */
        typedef std::string     IdType;

        /**
         *  Constructor to create a SessionId with a specific value.
         *
         *  @param id the value of the created id object.
         */
        SessionId(const IdType  id)             throw ()
        {
            this->id = id;
        }

        /**
         *  Compare this is with an other one.
         *
         *  @param otherId the other unqiue id to compare to.
         *  @return true if this an otherId have the same ID value,
         *          false otherwise.
         */
        bool
        operator==(const SessionId & otherId) const
                                                throw ()
        {
            return this->id == otherId.id;
        }

        /**
         *  Compare this id with an other one.
         *
         *  @param otherId the other session id to compare to.
         *  @return true if this id is smaller than the other one,
         *          false otherwise.
         */
        bool
        operator<(const SessionId  & otherId) const
                                                throw ()
        {
            return this->id < otherId.id;
        }

        /**
         *  Return the (string) value of this session ID.
         *
         *  @return the value of this id.
         */
        const IdType
        getId(void) const                       throw ()
        {
            return id;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_SessionId_h

