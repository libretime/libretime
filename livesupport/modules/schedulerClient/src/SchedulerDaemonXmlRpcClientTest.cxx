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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/src/SchedulerDaemonXmlRpcClientTest.cxx,v $

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
#include <fstream>
#include <iostream>

#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "SchedulerDaemonXmlRpcClientTest.h"


using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::SchedulerClient;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SchedulerDaemonXmlRpcClientTest);

/**
 *  The name of the configuration file for the scheduler client.
 */
static const std::string configFileName = "etc/schedulerDaemonXmlRpcClient.xml";

/**
 *  The name of the configuration file for the authentication client factory.
 */
static const std::string authenticationClientConfigFileName =
                                          "etc/authenticationClient.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------ *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/void
SchedulerDaemonXmlRpcClientTest :: configure(
            Ptr<Configurable>::Ref      configurable,
            const std::string         & fileName)
                                                throw (std::invalid_argument,
                                                       xmlpp::exception)
{
    Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser(fileName, true));
    const xmlpp::Document * document = parser->get_document();
    const xmlpp::Element  * root     = document->get_root_node();

    configurable->configure(*root);
}


/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: setUp(void)                         throw ()
{
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                                    new xmlpp::DomParser(configFileName, true));
        const xmlpp::Document * document = parser->get_document();
        const xmlpp::Element  * root     = document->get_root_node();

        schedulerClient.reset(new SchedulerDaemonXmlRpcClient());
        schedulerClient->configure(*root);
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }

    try {
        Ptr<AuthenticationClientFactory>::Ref acf;
        acf = AuthenticationClientFactory::getInstance();
        configure(acf, authenticationClientConfigFileName);
        authentication = acf->getAuthenticationClient();
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("semantic error in authentication configuration file");
    } catch (xmlpp::exception &e) {
        std::cerr << e.what() << std::endl;
        CPPUNIT_FAIL("error parsing authentication configuration file");
    }

    if (!(sessionId = authentication->login("root", "q"))) {
        CPPUNIT_FAIL("could not log in to authentication server");
    }

}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: tearDown(void)                      throw ()
{
    schedulerClient.reset();

    authentication->logout(sessionId);
    sessionId.reset();
    authentication.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if we can get the version string of the scheduler.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: getVersionTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const std::string>::Ref     version = schedulerClient->getVersion();

    CPPUNIT_ASSERT(version.get());
}


/*------------------------------------------------------------------------------
 *  Test to see if we can get the time of the scheduler.
 *----------------------------------------------------------------------------*/
void
SchedulerDaemonXmlRpcClientTest :: getSchedulerTimeTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<const ptime>::Ref   time = schedulerClient->getSchedulerTime();
    Ptr<const ptime>::Ref   now  = TimeConversion::now();

    CPPUNIT_ASSERT(time.get());
    // assume that the scheduler and the client is in the same year
    // this can break at new year's eve - so don't run the test then :)
    CPPUNIT_ASSERT(time->date().year() == now->date().year());
}

