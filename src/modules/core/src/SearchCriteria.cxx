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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/SearchCriteria.h"

using namespace LiveSupport::Core;
using namespace boost::posix_time;
using namespace XmlRpc;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct a SearchCriteria object with a single condition.
 *----------------------------------------------------------------------------*/
SearchCriteria :: SearchCriteria(const std::string & type, 
                                 const std::string & key,
                                 const std::string & comparisonOperator,
                                 const std::string & value)
                                                throw(std::invalid_argument)
    : logicalOperator("and"), limit(0), offset(0)
{
    setType(type);
    addCondition(key, comparisonOperator, value);
}


/*------------------------------------------------------------------------------
 *  Construct a SearchCriteria object from an XmlRpcValue.
 *----------------------------------------------------------------------------*/
SearchCriteria :: SearchCriteria(const XmlRpcValue &    xmlRpcValue)
                                                throw(std::invalid_argument)
{
    // make a non-const copy, because XmlRpcValue::operator[](const char *)
    // does not work on const objects
    XmlRpcValue     value(xmlRpcValue);
    
    if (value.hasMember("filetype") 
            && value["filetype"].getType() == XmlRpcValue::TypeString) {
        type = std::string(value["filetype"]);
    } else {
        throw std::invalid_argument("missing file type in search criteria");
    }
    
    if (value.hasMember("operator")
            && value["operator"].getType() == XmlRpcValue::TypeString) {
        logicalOperator = std::string(value["operator"]);
    } else {
        logicalOperator = std::string("and");
    }
    
    if (value.hasMember("limit")
            && value["limit"].getType() == XmlRpcValue::TypeInt) {
        limit = value["limit"];
    }
    
    if (value.hasMember("offset")
            && value["offset"].getType() == XmlRpcValue::TypeInt) {
        offset = value["offset"];
    }
    
    if (!value.hasMember("conditions")
            || value["conditions"].getType() != XmlRpcValue::TypeArray) {
        throw std::invalid_argument("missing conditions in search criteria");
    }
    
    for (int i = 0; i < value["conditions"].size(); ++i) {
        addCondition(value["conditions"][i]);
    }
}


/*------------------------------------------------------------------------------
 *  Add a search condition.
 *----------------------------------------------------------------------------*/
void
SearchCriteria :: addCondition(const std::string & key,
                               const std::string & comparisonOperator,
                               const std::string & value)
                                                throw(std::invalid_argument)
{
    std::string     lowerCaseOp = lowerCase(comparisonOperator);

    if (lowerCaseOp == "=" 
            || lowerCaseOp == "partial" || lowerCaseOp == "prefix"
            || lowerCaseOp == "<" || lowerCaseOp == "<="
            || lowerCaseOp == ">" || lowerCaseOp == ">=") {
        SearchConditionType  condition(key, lowerCaseOp, value);
        searchConditions.push_back(condition);
    } else {
        throw std::invalid_argument("bad comparison operator argument");
    }
}


/*------------------------------------------------------------------------------
 *  Add a search condition.
 *----------------------------------------------------------------------------*/
void
SearchCriteria :: addCondition(const XmlRpcValue &      xmlRpcValue)
                                                throw(std::invalid_argument)
{
    // make a non-const copy, because XmlRpcValue::operator[](const char *)
    // does not work on const objects
    XmlRpcValue     value(xmlRpcValue);
    
    if (!value.hasMember("cat")
            || value["cat"].getType() != XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing metadata name in search criteria");
    }
    
    if (!value.hasMember("op")
            || value["op"].getType() != XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing operator name in search criteria");
    }
    
    if (!value.hasMember("val")
            || value["val"].getType() != XmlRpcValue::TypeString) {
        throw std::invalid_argument("missing value in search criteria");
    }
    
    addCondition(std::string(value["cat"]),
                 std::string(value["op"]),
                 std::string(value["val"]));
}


/*------------------------------------------------------------------------------
 *  Add a search condition specifying the mtime (modified-at time).
 *----------------------------------------------------------------------------*/
void
SearchCriteria :: addMtimeCondition(const std::string &     comparisonOperator,
                                    Ptr<const ptime>::Ref   value)
                                                throw(std::invalid_argument)
{
    std::string     lowerCaseOp = lowerCase(comparisonOperator);

    if (lowerCaseOp == "=" 
            || lowerCaseOp == "partial" || lowerCaseOp == "prefix"
            || lowerCaseOp == "<" || lowerCaseOp == "<="
            || lowerCaseOp == ">" || lowerCaseOp == ">=") {
        mtimeComparisonOperator = lowerCaseOp;
        mtimeValue              = value;
    } else {
        throw std::invalid_argument("bad comparison operator argument");
    }
}


/*------------------------------------------------------------------------------
 *  Convert to an XmlRpcValue.
 *----------------------------------------------------------------------------*/
SearchCriteria :: operator XmlRpcValue() const
                                                throw()
{
    XmlRpcValue         returnValue;
    
    returnValue["filetype"] = type;
    if (searchConditions.size() != 1) {
        returnValue["operator"] = logicalOperator;
    }
    
    XmlRpcValue         conditionList;
    conditionList.setSize(searchConditions.size());
    SearchConditionListType::const_iterator     it, end;
    it  = searchConditions.begin();
    end = searchConditions.end();
    for (int i = 0; it != end; ++i, ++it) {
        XmlRpcValue     condition;
        condition["cat"]    = it->key;
        condition["op"]     = it->comparisonOperator;
        condition["val"]    = it->value;
        conditionList[i]    = condition;
    }
    
    if (mtimeValue) {
        int             i   = conditionList.size();
        struct tm       mtimeStructTm;
        TimeConversion::ptimeToTm(mtimeValue, mtimeStructTm);
                
        XmlRpcValue     condition;
        condition["cat"]    = "ls:mtime";
        condition["op"]     = mtimeComparisonOperator;
        condition["val"]    = XmlRpcValue(&mtimeStructTm);
        conditionList[i]    = condition;
    }
        
    returnValue["conditions"] = conditionList;

    if (limit) {
        returnValue["limit"]    = limit;
    }
    
    if (offset) {
        returnValue["offset"]   = offset;
    }
    
    return returnValue;
}


/*------------------------------------------------------------------------------
 *  Check two SearchCriteria objects for equality.
 *----------------------------------------------------------------------------*/
bool
SearchCriteria :: operator ==(const SearchCriteria &    other) const
                                                throw()
{
    if (type != other.type
            || limit  != other.limit
            || offset != other.offset
            || searchConditions.size() != other.searchConditions.size()) {
        return false;
    }
    
    if (searchConditions.size() != 1
            && logicalOperator != other.logicalOperator) {
        return false;
    }
    
    SearchConditionListType::const_iterator     it, otherIt;
    it      = searchConditions.begin();
    otherIt = other.searchConditions.begin();
    
    for ( ; it != searchConditions.end(); ++it, ++otherIt) {
        if (it->key != otherIt->key
                || it->comparisonOperator != otherIt->comparisonOperator
                || it->value != otherIt->value) {
            return false;
        }
    }
    
    return true;
}

