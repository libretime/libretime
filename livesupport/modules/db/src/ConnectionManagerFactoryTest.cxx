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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/db/src/ConnectionManagerFactoryTest.cxx,v $

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

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "ConnectionManagerFactoryTest.h"


using namespace odbc;
using namespace LiveSupport::Db;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(ConnectionManagerFactoryTest);

/**
 *  The name of the configuration file for the connection manager factory.
 */
static const std::string configFileName = "connectionManagerFactory.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
ConnectionManagerFactoryTest :: setUp(void)                  throw ()
{
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
ConnectionManagerFactoryTest :: tearDown(void)               throw ()
{
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
ConnectionManagerFactoryTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    try {
        xmlpp::DomParser        parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                             configFileName);
        const xmlpp::Element  * root     = document->get_root_node();
        Ptr<ConnectionManagerFactory>::Ref   cmf =
                                        ConnectionManagerFactory::getInstance();

        cmf->configure(*root);

        Ptr<ConnectionManagerInterface>::Ref cm = cmf->getConnectionManager();
        CPPUNIT_ASSERT(cm);

        Ptr<Connection>::Ref  connection = cm->getConnection();
        CPPUNIT_ASSERT(connection);

        // so far, so good. now simply execute "SELECT 1", and see if
        // it works
        Ptr<Statement>::Ref stmt(connection->createStatement());
        Ptr<ResultSet>::Ref rs(stmt->executeQuery("SELECT 1"));
        CPPUNIT_ASSERT(rs->next());
        CPPUNIT_ASSERT(rs->getInt(1) == 1);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL(e.what());
    } catch (std::runtime_error &e) {
        CPPUNIT_FAIL(e.what());
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
}

