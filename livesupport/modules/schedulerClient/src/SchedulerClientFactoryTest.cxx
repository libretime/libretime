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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/schedulerClient/src/SchedulerClientFactoryTest.cxx,v $

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

#include "SchedulerClientFactoryTest.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::SchedulerClient;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(SchedulerClientFactoryTest);

/**
 *  The name of the configuration file for the scheduler client.
 */
static const std::string configFileName = "schedulerClientFactory.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Set up the test environment
 *----------------------------------------------------------------------------*/
void
SchedulerClientFactoryTest :: setUp(void)                         throw ()
{
    schedulerClientFactory = SchedulerClientFactory::getInstance();

    // TODO: only configure, if not configured earlier
    try {
        xmlpp::DomParser        parser;
        const xmlpp::Document * document = getConfigDocument(parser,
                                                             configFileName);
        const xmlpp::Element  * root     = document->get_root_node();

        schedulerClientFactory->configure(*root);

    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    }

    schedulerClient = schedulerClientFactory->getSchedulerClient();
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
SchedulerClientFactoryTest :: tearDown(void)                      throw ()
{
    schedulerClientFactory.reset();
}


/*------------------------------------------------------------------------------
 *  Test to see if we can log on and off
 *----------------------------------------------------------------------------*/
void
SchedulerClientFactoryTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    CPPUNIT_ASSERT(schedulerClient.get());

    Ptr<const std::string>::Ref     version = schedulerClient->getVersion();
    CPPUNIT_ASSERT(version.get());
}

