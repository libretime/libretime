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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/UploadPlaylistMethodTest.cxx,v $

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
#include <XmlRpcValue.h>

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "ScheduleFactory.h"
#include "UploadPlaylistMethod.h"
#include "UploadPlaylistMethodTest.h"


using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(UploadPlaylistMethodTest);

/**
 *  The name of the configuration file for the storage client factory.
 */
const std::string UploadPlaylistMethodTest::storageClientConfig =
                                                    "etc/storageClient.xml";

/**
 *  The name of the configuration file for the connection manager factory.
 */
const std::string UploadPlaylistMethodTest::connectionManagerConfig =
                                          "etc/connectionManagerFactory.xml";

/**
 *  The name of the configuration file for the schedule factory.
 */
const std::string UploadPlaylistMethodTest::scheduleConfig =
                                            "etc/scheduleFactory.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethodTest :: configure(
            Ptr<Configurable>::Ref      configurable,
            const std::string           fileName)
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
UploadPlaylistMethodTest :: setUp(void)                         throw ()
{
    try {
        Ptr<StorageClientFactory>::Ref scf
                                        = StorageClientFactory::getInstance();
        configure(scf, storageClientConfig);

        Ptr<ConnectionManagerFactory>::Ref cmf
                                    = ConnectionManagerFactory::getInstance();
        configure(cmf, connectionManagerConfig);

        Ptr<ScheduleFactory>::Ref   sf = ScheduleFactory::getInstance();
        configure(sf, scheduleConfig);

        schedule = sf->getSchedule();
        schedule->install();
    } catch (std::invalid_argument &e) {
        CPPUNIT_FAIL("semantic error in configuration file");
    } catch (xmlpp::exception &e) {
        CPPUNIT_FAIL("error parsing configuration file");
    } catch (std::exception &e) {
        CPPUNIT_FAIL(e.what());
    }
}


/*------------------------------------------------------------------------------
 *  Clean up the test environment
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethodTest :: tearDown(void)                      throw ()
{
    schedule->uninstall();
}


/*------------------------------------------------------------------------------
 *  Test to see if the singleton Hello object is accessible
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  method(new UploadPlaylistMethod());
    XmlRpc::XmlRpcValue             rootParameter;
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    // set up a structure for the parameters
    parameters["playlistId"] = 1;
    strptime("2001-11-12 18:31:01", "%Y-%m-%d %H:%M:%S", &time);
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    method->execute(rootParameter, result);
    CPPUNIT_ASSERT(result);
}


/*------------------------------------------------------------------------------
 *  Try to upload overlapping playlists, and see them fail.
 *----------------------------------------------------------------------------*/
void
UploadPlaylistMethodTest :: overlappingPlaylists(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<UploadPlaylistMethod>::Ref  method(new UploadPlaylistMethod());
    XmlRpc::XmlRpcValue             rootParameter;
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             result;
    struct tm                       time;

    // load the first playlist, this will succeed
    parameters["playlistId"] = 1;
    strptime("2001-11-12 10:00:00", "%Y-%m-%d %H:%M:%S", &time);
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    method->execute(rootParameter, result);
    CPPUNIT_ASSERT(result);

    // try to load the same one, but in an overlapping time region
    // (we know that playlist with id 1 in 1 hour long)
    parameters["playlistId"] = 1;
    strptime("2001-11-12 10:30:00", "%Y-%m-%d %H:%M:%S", &time);
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    method->execute(rootParameter, result);
    CPPUNIT_ASSERT(!result);

    // try to load the same one, but now in good timing
    parameters["playlistId"] = 1;
    strptime("2001-11-12 11:30:00", "%Y-%m-%d %H:%M:%S", &time);
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    method->execute(rootParameter, result);
    CPPUNIT_ASSERT(result);

    // try to load the same one, this time overlapping both previos instnaces
    parameters["playlistId"] = 1;
    strptime("2001-11-12 10:45:00", "%Y-%m-%d %H:%M:%S", &time);
    parameters["playtime"] = &time;
    rootParameter[0] = parameters;

    method->execute(rootParameter, result);
    CPPUNIT_ASSERT(!result);
}

