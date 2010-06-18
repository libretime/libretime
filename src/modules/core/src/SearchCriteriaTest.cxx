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

#include <iostream>

#include "LiveSupport/Core/SearchCriteria.h"
#include "SearchCriteriaTest.h"

using namespace LiveSupport::Core;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SearchCriteriaTest);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
SearchCriteriaTest :: setUp(void)                         throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SearchCriteriaTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if we can do some simple operations
 *----------------------------------------------------------------------------*/
void
SearchCriteriaTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    XmlRpc::XmlRpcValue xmlRpcValue;

    try {
        SearchCriteria      firstCriteria;
        xmlRpcValue = firstCriteria;
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("filetype"));
    CPPUNIT_ASSERT(xmlRpcValue["filetype"] == "all");
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("conditions"));
    CPPUNIT_ASSERT(xmlRpcValue["conditions"].getType() 
                                == XmlRpc::XmlRpcValue::TypeArray);
    CPPUNIT_ASSERT(xmlRpcValue["conditions"].size() == 0);

    try {
        SearchCriteria      secondCriteria("playlist", "Or");
        secondCriteria.setLimit(50);
        secondCriteria.setOffset(100);
        secondCriteria.addCondition("dc:title", "PREFIX", "My ");
        secondCriteria.addCondition("DcTerms:Extent", "<", "180");
        xmlRpcValue = secondCriteria;
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("filetype"));
    CPPUNIT_ASSERT(xmlRpcValue["filetype"] == "playlist");
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("operator"));
    CPPUNIT_ASSERT(xmlRpcValue["operator"] == "or");
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("conditions"));
    XmlRpc::XmlRpcValue     conditions = xmlRpcValue["conditions"];
    CPPUNIT_ASSERT(conditions.getType() == XmlRpc::XmlRpcValue::TypeArray);
    CPPUNIT_ASSERT(conditions.size() == 2);

    XmlRpc::XmlRpcValue     condition0 = conditions[0];
    CPPUNIT_ASSERT(condition0.hasMember("cat"));
    CPPUNIT_ASSERT(condition0["cat"] == "dc:title");
    CPPUNIT_ASSERT(condition0.hasMember("op"));
    CPPUNIT_ASSERT(condition0["op"] == "prefix");
    CPPUNIT_ASSERT(condition0.hasMember("val"));
    CPPUNIT_ASSERT(condition0["val"] == "My ");

    XmlRpc::XmlRpcValue     condition1 = conditions[1];
    CPPUNIT_ASSERT(condition1.hasMember("cat"));
    CPPUNIT_ASSERT(condition1["cat"] == "DcTerms:Extent");
    CPPUNIT_ASSERT(condition1.hasMember("op"));
    CPPUNIT_ASSERT(condition1["op"] == "<");
    CPPUNIT_ASSERT(condition1.hasMember("val"));
    CPPUNIT_ASSERT(condition1["val"] == "180");

    CPPUNIT_ASSERT(xmlRpcValue.hasMember("limit"));
    CPPUNIT_ASSERT(xmlRpcValue["limit"].getType() 
                                            == XmlRpc::XmlRpcValue::TypeInt);
    CPPUNIT_ASSERT(int(xmlRpcValue["limit"]) == 50);
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("offset"));
    CPPUNIT_ASSERT(xmlRpcValue["offset"].getType() 
                                            == XmlRpc::XmlRpcValue::TypeInt);
    CPPUNIT_ASSERT(int(xmlRpcValue["offset"]) == 100);

    try {
        SearchCriteria      thirdCriteria("all", "dc:creator", "partial", "X");
        xmlRpcValue = thirdCriteria;
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    }
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("filetype"));
    CPPUNIT_ASSERT(xmlRpcValue["filetype"] == "all");
    CPPUNIT_ASSERT(xmlRpcValue.hasMember("conditions"));
    conditions = xmlRpcValue["conditions"];
    CPPUNIT_ASSERT(conditions.getType() == XmlRpc::XmlRpcValue::TypeArray);
    CPPUNIT_ASSERT(conditions.size() == 1);

    condition0 = conditions[0];
    CPPUNIT_ASSERT(condition0.hasMember("cat"));
    CPPUNIT_ASSERT(condition0["cat"] == "dc:creator");
    CPPUNIT_ASSERT(condition0.hasMember("op"));
    CPPUNIT_ASSERT(condition0["op"] == "partial");
    CPPUNIT_ASSERT(condition0.hasMember("val"));
    CPPUNIT_ASSERT(condition0["val"] == "X");
}


/*------------------------------------------------------------------------------
 *  Test the conversion to/from an XmlRpcValue.
 *----------------------------------------------------------------------------*/
void
SearchCriteriaTest :: marshalingTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<SearchCriteria>::Ref    criteria;
    CPPUNIT_ASSERT_NO_THROW(
        criteria.reset(new SearchCriteria("playlist", "Or"));
        criteria->setLimit(50);
        criteria->setOffset(100);
        criteria->addCondition("dc:title", "PREFIX", "My ");
        criteria->addCondition("DcTerms:Extent", "<", "180");
    );
    
    XmlRpc::XmlRpcValue         xmlRpcValue;
    CPPUNIT_ASSERT_NO_THROW(
        xmlRpcValue = *criteria;
    );
    
    Ptr<SearchCriteria>::Ref    copyCriteria;
    CPPUNIT_ASSERT_NO_THROW(
        copyCriteria.reset(new SearchCriteria(xmlRpcValue));
    );
    
    CPPUNIT_ASSERT(*criteria == *copyCriteria);
    
    XmlRpc::XmlRpcValue         copyXmlRpcValue;
    CPPUNIT_ASSERT_NO_THROW(
        copyXmlRpcValue = *copyCriteria;
    );
    
    CPPUNIT_ASSERT(xmlRpcValue == copyXmlRpcValue);
}

