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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/db/src/SimpleConnectionManager.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <odbc++/drivermanager.h>

#include "SimpleConnectionManager.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Db;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string SimpleConnectionManager::configElementNameStr =
                                                    "simpleConnectionManager";

/**
 *  The name of the attribute to get the dsn for the connection.
 */
static const std::string    dsnAttrName = "dsn";

/**
 *  The name of the attribute to get the userName for the connection.
 */
static const std::string    userNameAttrName = "userName";

/**
 *  The name of the attribute to get the password for the connection.
 */
static const std::string    passwordAttrName = "password";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the connection manager factory.
 *----------------------------------------------------------------------------*/
void
SimpleConnectionManager :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(dsnAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += dsnAttrName;
        throw std::invalid_argument(eMsg);
    }
    dsn = attribute->get_value();

    if (!(attribute = element.get_attribute(userNameAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += userNameAttrName;
        throw std::invalid_argument(eMsg);
    }
    userName = attribute->get_value();

    if (!(attribute = element.get_attribute(passwordAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += passwordAttrName;
        throw std::invalid_argument(eMsg);
    }
    password = attribute->get_value();
}


/*------------------------------------------------------------------------------
 *  Give out a connection.
 *----------------------------------------------------------------------------*/
Ptr<odbc::Connection>::Ref
SimpleConnectionManager :: getConnection(void)
                                                throw (std::runtime_error)
{
    odbc::Connection  * conn;
    try {
        conn = odbc::DriverManager::getConnection(dsn, userName, password);
    } catch (std::exception &e) {
        throw std::runtime_error(e.what());
    }

    if (!conn) {
        std::string eMsg = "unable to open ODBC connection for DSN ";
        eMsg += dsn;
        throw std::runtime_error(eMsg);
    }

    Ptr<odbc::Connection>::Ref  connection(conn);
    return connection;
}


/*------------------------------------------------------------------------------
 *  Receive a connection back.
 *----------------------------------------------------------------------------*/
void
SimpleConnectionManager :: returnConnection(
                            Ptr<odbc::Connection>::Ref connection)
                                                throw (std::runtime_error)
{
    // nothing to do here...
    // we could save the outgoing connections to a set in getConnection()
    // and check here to see if the returned one is contained there
}

