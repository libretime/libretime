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
 

    This code is based upon the Leach working draft for UUIDs,
    http://www.opengroup.org/dce/info/draft-leach-uuids-guids-01.txt
    and the sample code therein.
    The original copyright message of the sample code is the following:

** Copyright (c) 1990- 1993, 1996 Open Software Foundation, Inc.
** Copyright (c) 1989 by Hewlett-Packard Company, Palo Alto, Ca. &
** Digital Equipment Corporation, Maynard, Mass.
** Copyright (c) 1998 Microsoft.
** To anyone who acknowledges that this file is provided "AS IS"
** without any express or implied warranty: permission to use, copy,
** modify, and distribute this file for any purpose is hereby
** granted without fee, provided that the above copyright notices and
** this notice appears in all source code copies, and that none of
** the names of Open Software Foundation, Inc., Hewlett-Packard
** Company, or Digital Equipment Corporation be used in advertising
** or publicity pertaining to distribution of the software without
** specific, written prior permission.  Neither Open Software
** Foundation, Inc., Hewlett-Packard Company, Microsoft, nor Digital Equipment
** Corporation makes any representations about the suitability of
** this software for any purpose.

 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_Uuid_h
#define LiveSupport_Core_Uuid_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_STDINT_H
#include <stdint.h>
#else
#error need stdint.h
#endif


#include <string>
#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing globally unique identifiers.
 *  This implementation is based on the Leach UUID/GUID draft:
 *  http://www.opengroup.org/dce/info/draft-leach-uuids-guids-01.txt
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see http://www.opengroup.org/dce/info/draft-leach-uuids-guids-01.txt
 */
class Uuid
{
    private:
        /**
         *  The raw time type
         */
        typedef uint64_t        UuidTime;

        /**
         *  Structure holding a 6-byte system node id.
         */
        typedef struct {
            char            nodeId[6];
        } UuidNode;

        /**
         *  The value of the id.
         */
        long long int   id;

        /**
         *  A string representation of the id, in hexadecimal notation.
         */
        std::string     idAsString;

        /**
         *  The low part of time.
         */
        uint32_t        timeLow;

        /**
         *  The middle part of time.
         */
        uint16_t        timeMid;

        /**
         *  The high part of time, with version included.
         */
        uint16_t        timeHiAndVersion;

        /**
         *  Clock sequence number high and reserved parts.
         */
        uint8_t         clockSeqHiAndReserved;

        /**
         *  Clock sequenc number, low part.
         */
        uint8_t         clockSeqLow;

        /**
         *  The 6 byte system node id.
         */
        uint8_t         node[6];

        /**
         *  Default constructor.
         */
        Uuid(void)                          throw ()
        {
        }

        /**
         *  Compare two UUIDs
         *
         *  @param id1 one id to compare
         *  @param id2 the other id to compare
         *  @return true if they are equal, false otherwise
         */
        static bool
        compare(const Uuid    & id1,
                const Uuid    & id2)                                throw ();

        /**
         *  Return the current time, but always return a different value,
         *  even if the system clock granularity is not fine enough.
         *
         *  @param timestamp an out parameter for the current time.
         */
        static void
        getCurrentTime(UuidTime   * timestamp)              throw ();

        /**
         *  Try to generate a truely random number (which is not possible,
         *  of course).
         *
         *  @return a random number
         */
        static uint16_t
        trueRandom(void)                                    throw ();

        /**
         *  Read the last saved state of the UUID generator from
         *  non-volatile storage.
         *
         *  @param clockSeq out parameter for the clock sequence
         *  @param timestamp out parameter for the timestamp
         *  @param node out parameter for the system node id
         */
        int
        readState(uint16_t    * clockSeq,
                  UuidTime    * timestamp,
                  UuidNode    * node)                       throw ();

        /**
         *  Write the last saved state of the UUID generator to
         *  non-volatile storage.
         *
         *  @param clockSeq the clock sequence
         *  @param timestamp the timestamp
         *  @param node the system node id
         */
        void
        writeState(uint16_t     clockSeq,
                   UuidTime     timestamp,
                   UuidNode     node)                       throw ();

        /**
         *  Generate a type 1 UUID.
         *  This function will fill out the object's internal attributes
         *  for it to become a proper UUID.
         *
         *  For UUID types, see
         *  http://www.webdav.org/specs/draft-leach-uuids-guids-01.txt
         *
         *  @param clockSeq the clock sequence
         *  @param timestamp the time stamp
         *  @param node the system node id
         */
        void
        format(uint16_t     clockSeq,
               UuidTime     timestamp,
               UuidNode     node)                           throw ();

        /**
         *  Get the system time in the UUID UTC base time,
         *  which is October 15, 1582.
         *
         *  @param uuidTime an out parameter for the time.
         */
        static void
        getSystemTime(UuidTime * uuidTime)                          throw ();

        /**
         *  Return the IEEE node id.
         *
         *  @param node an out parameter for the node id.
         */
        static void
        getIeeeNodeIdentifier(UuidNode    * node)                   throw ();

        /**
         *  Create a string representation of the UUID.
         *  Store the result in the idAsString attribute.
         */
        void
        representAsString(void)                                     throw ();


    public:
        /**
         *  Generate a globally unique id.  This is used for testing.
         *  In real life, unique IDs are generated by the storage server.
         */
        static Ptr<Uuid>::Ref
        generateId(void)                        throw ();

        /**
         *  Compare this is with an other one.
         *
         *  @param otherId the other unqiue id to compare to.
         *  @return true if this an otherId have the same ID value,
         *          false otherwise.
         */
        bool
        operator==(const Uuid & otherId) const              throw ()
        {
            return compare(*this, otherId);
        }

        /**
         *  Compare this is with an other one.
         *
         *  @param otherId the other unqiue id to compare to.
         *  @return true if this an otherId do not have the same ID value,
         *          false otherwise.
         */
        bool
        operator!=(const Uuid & otherId) const              throw ()
        {
            return !compare(*this, otherId);
        }

        /**
         *  Return the string value of this globally unique id.
         *
         *  @return the string value of this id.
         */
        operator std::string() const            throw ()
        {
            return idAsString;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */

inline std::ostream &
operator<< (std::ostream      & os,
            const Uuid        & id)
{
    os << (std::string) id;

    return os;
}


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Uuid_h

