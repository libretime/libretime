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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storage/include/LiveSupport/Storage/Attic/SearchCriteria.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Storage_SearchCriteria_h
#define LiveSupport_Storage_SearchCriteria_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <cctype>
#include <XmlRpcValue.h>

namespace LiveSupport {
namespace Storage {

class TestStorageClient;        // forward declaration of friend class

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An object which contains a collection of search conditions.
 * 
 *  Its fields are:
 *  <ul>
 *    <li>type     - values in (audioClip | playlist | all); the default is
 *                          <i>audioClip</i></li>
 *    <li>operator - values in (and | or); the default is <i>and</i></li>
 *    <li>condition1 : { key : string, comparison: string, value : string }
 *                          - a search condition, where <i>key</i> is one of the
 *                            fields in the metadata, and <i>comparison</i> is
 *                            one of ("=" | "partial" | "prefix" 
 *                                        | "<" | "<=" | ">" | ">=")</li>
 *    <li>...</li>
 *    <li>conditionN</li>
 *    <li>limit  : int - the maximum number of results to be returned;
 *                       the default is 0, which means unlimited</li>
 *    <li>offset : int - ignore the first <i>offset</i> matches;
 *                       the default is 0.</li>
 *  </ul>
 *
 *  Usage: construct a SearchCriteria object either directly using the 
 *  constructor with 4 string arguments, or in several steps using 
 *  addCondition() and the setter methods; 
 *  then pass this object to StorageClientInterface::search() 
 *  to search the local storage.
 *
 *  The <i>key</i> and <i>value</i> fields are case-sensitive, all the other
 *  strings (type, operator names) are case-insensitive.
 */
class SearchCriteria
{
    private:

        /**
         *  The kind of object we are searching for.
         */
        std::string                         type;

        /**
         *  The logical operator joining the conditions: "and" or "or".
         */
        std::string                         logicalOperator;

        /**
         *  A type to hold a single search condition.
         */
        struct SearchConditionType
        {
            std::string key;
            std::string comparisonOperator;
            std::string value;
        };
        
        /**
         *  A type to hold the list of search conditions.
         */
        typedef std::vector<SearchConditionType>
                                            SearchConditionListType;
        /**
         *  The vector of search conditions.
         */
        SearchConditionListType             searchConditions;

        /**
         *  The maximum number of conditions to be returned.
         */
        int                                 limit;

        /**
         *  The index of the first matching condition to be returned.
         */
        int                                 offset;

        /**
         *  Lowercase a string.
         */
        std::string
        lowerCase(const std::string & s)        throw()
        {
            std::string                     returnValue;
            std::string::const_iterator     it = s.begin();
            while (it != s.end()) {
                returnValue += std::tolower(*it);
                ++it;
            }
            return returnValue;
        }

        /**
         *  Give access of private members to the TestStorageClient.
         */
        friend class Storage::TestStorageClient;


    public:

        /**
         *  Construct an empty SearchCriteria object.
         *  This also works as a default constructor.
         *
         *  @param type one of "audioClip" (default), "playlist" or "all"
         *  @param logicalOperator either "and" (default) or "or"
         */
        SearchCriteria(const std::string & type = "audioClip", 
                       const std::string & logicalOperator = "and")
                                                throw(std::invalid_argument)
                       : limit(0), offset(0)
        {
            setType(type);
            setLogicalOperator(logicalOperator);
        }

        /**
         *  Construct a SearchCriteria object with a single condition.
         *
         *  @param type one of "audioClip", "playlist" or "all"
         *  @param key  the metadata field to search in
         *  @param comparisonOperator one of "=", "partial", "prefix",
         *                            "<", "<=", ">" or ">="
         *  @param value the value to compare to
         */
        SearchCriteria(const std::string & type, 
                       const std::string & key,
                       const std::string & comparisonOperator,
                       const std::string & value)
                                                throw(std::invalid_argument);

        /**
         *  Set the type field.
         *
         *  @param type one of "audioClip", "playlist" or "all"
         */
        void
        setType(const std::string & type)
                                                throw(std::invalid_argument)
        {
            std::string  lowerCaseType = lowerCase(type);
            if (lowerCaseType == "audioclip" 
                    || lowerCaseType == "playlist"
                    || lowerCaseType == "all") {
                this->type = lowerCaseType;
            } else {
                throw std::invalid_argument("bad type argument");
            }

        }

        /**
         *  Set the logical operator field.
         *
         *  @param logicalOperator either "and" or "or"
         */
        void
        setLogicalOperator(const std::string & logicalOperator)
                                                throw(std::invalid_argument)
        {
            std::string  lowerCaseOp = lowerCase(logicalOperator);
            if (lowerCaseOp == "and" || lowerCaseOp == "or") {
                this->logicalOperator = lowerCaseOp;
            } else {
                throw std::invalid_argument("bad logical operator argument");
            }
        }

        /**
         *  Add a search condition.
         *
         *  @param key  the metadata field to search in
         *  @param comparisonOperator one of "=", "partial", "prefix",
         *                            "<", "<=", ">" or ">="
         *  @param value the value to compare to
         */
        void
        addCondition(const std::string & key,
                     const std::string & comparisonOperator,
                     const std::string & value)
                                                throw(std::invalid_argument);

        /**
         *  Set the limit field.
         *
         *  @param limit the maximum number of search results to be returned
         */
        void
        setLimit(const int limit)
                                                throw(std::invalid_argument)
        {
            if (limit >= 0) {
                this->limit = limit;
            } else {
                throw std::invalid_argument("bad argument: less than zero");
            }
        }

        /**
         *  Set the offset field.
         *
         *  @param offset   the index of the first matching condition 
         *                  to be returned (first = 0)
         */
        void
        setOffset(const int offset)
                                                throw(std::invalid_argument)
        {
            if (offset >= 0) {
                this->offset = offset;
            } else {
                throw std::invalid_argument("bad argument: less than zero");
            }
        }

        /**
         *  Convert to an XmlRpc::XmlRpcValue.
         */
        operator XmlRpc::XmlRpcValue() const     throw();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Storage
} // namespace LiveSupport

#endif // LiveSupport_Storage_SearchCriteria_h

