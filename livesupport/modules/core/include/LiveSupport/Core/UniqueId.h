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
    Version  : $Revision: 1.8 $
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

#include <string>
#include <sstream>
#include <iomanip>
#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing globally unique identifiers.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.8 $
 */
class UniqueId
{
    private:
        /**
         *  The value of the id.
         */
        long long int   id;

        /**
         *  A string representation of the id, in hexadecimal notation.
         */
        std::string     idAsString;

        /**
         *  Default constructor.
         */
        UniqueId(void)                          throw ()
        {
        }


    public:
        /**
         *  The type for the numeric value the unique id is represented in.
         *  This is set to 'long long int', i.e., 32-bit signed integers.
         */
        typedef long long int   IdType;

        /**
         *  Constructor to create a UniqueId with a specific integer value.
         *  The argument is expected to be between 0 and 2^31-1 (inclusive).
         *
         *  @param id the numeric value of the created id object.
         */
        UniqueId(const IdType   id)             throw ()
        {
            this->id = id;
            std::stringstream idWriter;
            idWriter << std::hex << std::setw(16) << std::setfill('0') << id;
            this->idAsString = idWriter.str();
        }

        /**
         *  Constructor to create a UniqueId with a specific string value.
         *  If the argument is not a valid hexadecimal number between 0 and
         *  2^31-1 (inclusive), the integer value of the UniqueId will be 
         *  bogus.
         *
         *  @param idAsString the string value of the created id object.
         */
        UniqueId(const std::string    idAsString)           throw ()
        {
            this->idAsString = idAsString;
            // TODO: add error checking
            std::stringstream idReader(idAsString);
            idReader >> std::hex >> this->id;
        }

        /**
         *  Create a UniqueId from a numeric value, which is a string
         *  representation of the id, in base 10.
         *  If the argument is not a valid decimal number between 0 and
         *  2^31-1 (inclusive), the value of the UniqueId will be 
         *  bogus.
         *
         *  @param strValue the id in base 10, in string from.
         *  @return a new UniqueId with the specified ID value.
         */
        static Ptr<UniqueId>::Ref
        fromDecimalString(const std::string     idStr)          throw ()
        {
            IdType      id;
            // TODO: error checking
            std::stringstream    idReader(idStr);
            idReader >> id;

            Ptr<UniqueId>::Ref  uid(new UniqueId(id));
            return uid;
        }

        /**
         *  Return the UniqueId as a string in base 10.
         *
         *  @return a new string containing the value of the UniqueId.
         */
        Ptr<std::string>::Ref
        toDecimalString(void)                                   throw ()
        {
            std::stringstream idWriter;
            idWriter << std::dec << id;
            Ptr<std::string>::Ref   idString(new std::string(
                                                        idWriter.str() ));
            return idString;
        }

        /**
         *  Compare this is with an other one.
         *
         *  @param otherId the other unqiue id to compare to.
         *  @return true if this an otherId have the same ID value,
         *          false otherwise.
         */
        bool
        operator==(const UniqueId & otherId) const          throw ()
        {
            return this->id == otherId.id;
        }

        /**
         *  Compare this is with an other one.
         *
         *  @param otherId the other unqiue id to compare to.
         *  @return true if this an otherId do not have the same ID value,
         *          false otherwise.
         */
        bool
        operator!=(const UniqueId & otherId) const          throw ()
        {
            return this->id != otherId.id;
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
         *  Generate a globally unique id.  This is used for testing.
         *  In real life, unique IDs are generated by the storage server.
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

        /**
         *  Return the numeric value of this globally unique id 
         *  (alternative syntax).
         *
         *  @return the numeric value of this id.
         */
        operator IdType() const                 throw ()
        {
            return id;
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


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_UniqueId_h

