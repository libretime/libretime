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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/SearchCriteria.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Core/SearchCriteria.h"

using namespace LiveSupport::Core;

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
 *  Convert to an XmlRpc::XmlRpcValue.
 *----------------------------------------------------------------------------*/
SearchCriteria :: operator XmlRpc::XmlRpcValue() const
                                                throw()
{
    XmlRpc::XmlRpcValue     returnValue;
    
    returnValue["filetype"] = type;
    if (searchConditions.size() != 1) {
        returnValue["operator"] = logicalOperator;
    }
    
    XmlRpc::XmlRpcValue     conditionList;
    conditionList.setSize(searchConditions.size());
    SearchConditionListType::const_iterator     it, end;
    it  = searchConditions.begin();
    end = searchConditions.end();
    for (int i = 0; it != end; ++i, ++it) {
        XmlRpc::XmlRpcValue condition;
        condition["cat"]    = it->key;
        condition["op"]     = it->comparisonOperator;
        condition["val"]    = it->value;
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

