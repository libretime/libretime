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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/UniqueId.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_UniqueId_h
#define LiveSupport_Core_UniqueId_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing globally unique identifiers.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class UniqueId
{
    private:
        /**
         *  The value of the id.
         */
        unsigned long int       id;

        /**
         *  Default constructor.
         */
        UniqueId(void)                          throw ()
        {
        }


    public:
        /**
         *  The type for the numeric value the unique id is represented in.
         */
        typedef unsigned long int   IdType;

        /**
         *  Constructor to create a UniqueId with a specific value.
         *  TODO: remove this later, as this is for testing purposes only.
         *
         *  @param id the value of the created id object.
         */
        UniqueId(const IdType    id)            throw ()
        {
            this->id = id;
        }

        /**
         *  Compare this id with an other one.
         *
         *  @param otherId the other unique id to compare to.
         *  @return true if this id is smaller than the other one,
         *          false otherwise.
         */
        bool
        operator<(const UniqueId  & otherId) const          throw ()
        {
            return this->id < otherId.id;
        }

        /**
         *  Generate a globally unique id.
         */
        static Ptr<UniqueId>::Ref
        generateId(void)                        throw ();

        /**
         *  Return the numeric value of this globally unique id.
         *
         *  @return the numeric value of this id.
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

#endif // LiveSupport_Core_UniqueId_h

