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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/include/LiveSupport/Authentication/Attic/SessionId.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Authentication_SessionId_h
#define LiveSupport_Authentication_SessionId_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

namespace LiveSupport {
namespace Authentication {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing session identifiers.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
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
         *  The type for the numeric value the session id is represented in.
         */
        typedef std::string     IdType;

        /**
         *  Constructor to create a SessionId with a specific value.
         *  TODO: remove this later, as this is for testing purposes only.
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
         *  Return the string value of this session ID.
         *
         *  @return the string value of this id.
         */
        const IdType
        getId(void) const                       throw ()
        {
            return id;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Authentication
} // namespace LiveSupport

#endif // LiveSupport_Authentication_SessionId_h

