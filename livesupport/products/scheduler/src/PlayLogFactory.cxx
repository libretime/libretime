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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/src/PlayLogFactory.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "LiveSupport/Db/ConnectionManagerFactory.h"
#include "PostgresqlPlayLog.h"
#include "PlayLogFactory.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Db;
using namespace LiveSupport::Scheduler;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string PlayLogFactory::configElementNameStr =
                                                        "playLogFactory";

/*------------------------------------------------------------------------------
 *  The singleton instance of PlayLogFactory
 *----------------------------------------------------------------------------*/
Ptr<PlayLogFactory>::Ref PlayLogFactory::singleton;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to PlayLogFactory
 *----------------------------------------------------------------------------*/
Ptr<PlayLogFactory>::Ref
PlayLogFactory :: getInstance(void)             throw ()
{
    if (!singleton.get()) {
        singleton.reset(new PlayLogFactory());
    }

    return singleton;
}


/*------------------------------------------------------------------------------
 *  Configure the play log factory.
 *----------------------------------------------------------------------------*/
void
PlayLogFactory :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    playLog.reset();

    Ptr<ConnectionManagerFactory>::Ref   cmf =
                                    ConnectionManagerFactory::getInstance();
    Ptr<ConnectionManagerInterface>::Ref cm = cmf->getConnectionManager();

    // try to look for a PostgresqlPlayLog configuration element
    xmlpp::Node::NodeList   nodes =
               element.get_children(PostgresqlPlayLog::getConfigElementName());
    if (nodes.size() >= 1) {
        const xmlpp::Element  * configElement =
                         dynamic_cast<const xmlpp::Element*> (*(nodes.begin()));
        Ptr<PostgresqlPlayLog>::Ref     dbs(new PostgresqlPlayLog(cm));
        dbs->configure(*configElement);
        playLog = dbs;
    }

    if (!playLog) {
        throw std::invalid_argument("no play log factories to configure");
    }
}


/*------------------------------------------------------------------------------
 *  Install the play log factory.
 *----------------------------------------------------------------------------*/
void
PlayLogFactory :: install(void)                 throw (std::exception)
{
    if (!playLog) {
        throw std::logic_error("PlayLogFactory not yet configured");
    }

    playLog->install();
}


/*------------------------------------------------------------------------------
 *  Check to see if the factory has already been installed.
 *----------------------------------------------------------------------------*/
bool
PlayLogFactory :: isInstalled(void)             throw (std::exception)
{
    if (!playLog) {
        throw std::logic_error("PlayLogFactory not yet configured");
    }

    return playLog->isInstalled();
}


/*------------------------------------------------------------------------------
 *  Uninstall the play log factory.
 *----------------------------------------------------------------------------*/
void
PlayLogFactory :: uninstall(void)               throw (std::exception)
{
    if (!playLog) {
        throw std::logic_error("PlayLogFactory not yet configured");
    }

    playLog->uninstall();
}

