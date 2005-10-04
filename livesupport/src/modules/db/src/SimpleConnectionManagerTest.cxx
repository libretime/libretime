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
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif


#include <string>
#include <iostream>
#include <odbc++/resultset.h>
#include <odbc++/preparedstatement.h>

#include "SimpleConnectionManager.h"
#include "SimpleConnectionManagerTest.h"


using namespace odbc;
using namespace LiveSupport::Db;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SimpleConnectionManagerTest);

/**
 *  The name of the configuration file for the connection manager.
 */
static const std::string configFileName = "simpleConnectionManager.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
SimpleConnectionManagerTest :: setUp(void)                  throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SimpleConnectionManagerTest :: tearDown(void)               throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
SimpleConnectionManagerTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        xmlpp::DomParser        parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                             configFileName);
        const xmlpp::Element  * root     = document->get_root_node();
        Ptr<SimpleConnectionManager>::Ref   scm(new SimpleConnectionManager());

        scm->configure(*root);

        Ptr<Connection>::Ref  connection = scm->getConnection();
        CPPUNIT_ASSERT(connection);

        // so far, so good. now simply execute "SELECT 1", and see if
        // it works
        Ptr<Statement>::Ref stmt(connection->createStatement());
        Ptr<ResultSet>::Ref rs(stmt->executeQuery("SELECT 1"));
        CPPUNIT_ASSERT(rs->next());
        CPPUNIT_ASSERT(rs->getInt(1) == 1);

        rs.reset();
        stmt->close();
        stmt.reset();
        scm->returnConnection(connection);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (std::runtime_error &e) {
        CPPUNIT_FAIL(e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Test to handle large integers.
 *----------------------------------------------------------------------------*/
void
SimpleConnectionManagerTest :: bigIntTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    long long   testValue = 0x7fffffffffffffffLL;
    std::string createStmt = "CREATE TABLE testTable\n"
                             "(\n"
                             "  id    BIGINT    NOT NULL\n"
                             ");";
    bool        b;

    try {
        xmlpp::DomParser        parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                             configFileName);
        const xmlpp::Element  * root     = document->get_root_node();
        Ptr<SimpleConnectionManager>::Ref   scm(new SimpleConnectionManager());

        scm->configure(*root);

        Ptr<Connection>::Ref  connection = scm->getConnection();
        CPPUNIT_ASSERT(connection);

        // simply see if selecting the highest 63 bit number works...
        Ptr<PreparedStatement>::Ref   pstmt(connection->prepareStatement(
                                                                "SELECT ?"));
        pstmt->setLong(1, testValue);
        Ptr<ResultSet>::Ref rs(pstmt->executeQuery());
        CPPUNIT_ASSERT(rs->next());
        CPPUNIT_ASSERT(rs->getLong(1) == testValue);
        rs.reset();
        pstmt->close();
        pstmt.reset();

        // so far, so good. now create a table with a BIGINT column
        // and try the same
        Ptr<Statement>::Ref     stmt(connection->createStatement());
        stmt->execute(createStmt);
        stmt->close();
        stmt.reset();

        pstmt.reset(connection->prepareStatement("INSERT INTO testTable "
                                                 " VALUES(?)"));
        pstmt->setLong(1, testValue);
        CPPUNIT_ASSERT(pstmt->executeUpdate() == 1);
        pstmt->close();
        pstmt.reset();

        stmt.reset(connection->createStatement());
        rs.reset(stmt->executeQuery("SELECT * FROM testTable"));
        CPPUNIT_ASSERT(rs->next());
//std::cerr << std::endl;
//std::cerr << "rs->getLong: " << rs->getLong(1) << std::endl;
//std::cerr << "testValue:   " << testValue << std::endl;
        b = rs->getLong(1) == testValue;
        CPPUNIT_ASSERT(b);
        rs.reset();
        stmt->close();
        stmt.reset();

        stmt.reset(connection->createStatement());
        stmt->executeUpdate("DROP TABLE testTable");
        stmt->close();
        stmt.reset();

        scm->returnConnection(connection);
        
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (std::runtime_error &e) {
        CPPUNIT_FAIL(e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(e.what());
    } catch (SQLException &e) {
        CPPUNIT_FAIL(e.what());
    }
}

