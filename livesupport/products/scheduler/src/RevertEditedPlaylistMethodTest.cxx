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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/RevertEditedPlaylistMethodTest.cxx,v $

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
#include "OpenPlaylistForEditingMethod.h"
#include "RemoveAudioClipFromPlaylistMethod.h"
#include "SavePlaylistMethod.h"


#include "RevertEditedPlaylistMethod.h"
#include "RevertEditedPlaylistMethodTest.h"

using namespace std;
using namespace LiveSupport::Db;
using namespace LiveSupport::Storage;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

CPPUNIT_TEST_SUITE_REGISTRATION(RevertEditedPlaylistMethodTest);

/**
 *  The name of the configuration file for the storage client factory.
 */
const std::string RevertEditedPlaylistMethodTest::storageClientConfig =
                                                    "etc/storageClient.xml";

/**
 *  The name of the configuration file for the connection manager factory.
 */
const std::string RevertEditedPlaylistMethodTest::connectionManagerConfig =
                                          "etc/connectionManagerFactory.xml";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure a Configurable with an XML file.
 *----------------------------------------------------------------------------*/
void
RevertEditedPlaylistMethodTest :: configure(
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
RevertEditedPlaylistMethodTest :: setUp(void)                         throw ()
{
    try {
        Ptr<StorageClientFactory>::Ref scf
                                        = StorageClientFactory::getInstance();
        configure(scf, storageClientConfig);

        Ptr<ConnectionManagerFactory>::Ref cmf
                                    = ConnectionManagerFactory::getInstance();
        configure(cmf, connectionManagerConfig);

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
RevertEditedPlaylistMethodTest :: tearDown(void)                      throw ()
{
}


/*------------------------------------------------------------------------------
 *  Just a very simple smoke test
 *----------------------------------------------------------------------------*/
void
RevertEditedPlaylistMethodTest :: firstTest(void)
                                                throw (CPPUNIT_NS::Exception)
{
    Ptr<OpenPlaylistForEditingMethod>::Ref
                            openMethod(new OpenPlaylistForEditingMethod);
    Ptr<RemoveAudioClipFromPlaylistMethod>::Ref
                            removeMethod(new RemoveAudioClipFromPlaylistMethod);
    Ptr<SavePlaylistMethod>::Ref
                            saveMethod(new SavePlaylistMethod);
    Ptr<RevertEditedPlaylistMethod>::Ref 
                            revertMethod(new RevertEditedPlaylistMethod);
    XmlRpc::XmlRpcValue             parameters;
    XmlRpc::XmlRpcValue             rootParameter;
    rootParameter.setSize(1);
    XmlRpc::XmlRpcValue             result;

    parameters["playlistId"]     = 1;
    parameters["relativeOffset"] = 0;
    rootParameter[0]        = parameters;

    result.clear();
    revertMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(result.hasMember("errorCode"));
    CPPUNIT_ASSERT(int(result["errorCode"]) == 804);    // no saved copy yet

    result.clear();
    openMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(!result.hasMember("errorCode"));
    result.clear();
    removeMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(!result.hasMember("errorCode"));
    result.clear();
    removeMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(result.hasMember("errorCode"));      // can't remove it twice

    result.clear();
    revertMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(!result.hasMember("errorCode"));
    result.clear();
    removeMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(!result.hasMember("errorCode"));     // but now we can again

    result.clear();
    saveMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(!result.hasMember("errorCode"));
    result.clear();
    revertMethod->execute(rootParameter, result);
    CPPUNIT_ASSERT(result.hasMember("errorCode"));      // saved copy has been
    CPPUNIT_ASSERT(int(result["errorCode"]) == 804);    //   discarded
}
