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

#ifdef HAVE_SYS_TYPES_H
#include <sys/types.h>
#else
#error need sys/types.h
#endif

#ifdef HAVE_PWD_H
#include <pwd.h>
#else
#error need pwd.h
#endif

#ifdef HAVE_ERRNO_H
#include <errno.h>
#else
#error need errno.h
#endif

#include <fstream>

#include "LiveSupport/Core/BaseTestMethod.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the current working directory
 *----------------------------------------------------------------------------*/
std::string
BaseTestMethod :: getCwd(void)                      throw ()
{
    size_t  size = 100;
    char  * buffer;

    while (true) {
        buffer = new char[size];
        if (getcwd(buffer, size)) {
            break;
        }
        delete[] buffer;
        if (errno != ERANGE) {
            return "";
        }
        size *= 2;
    }

    std::string     cwd(buffer);
    delete[] buffer;
    return cwd;
}


/*------------------------------------------------------------------------------
 *  Return the full path for a configuration file.
 *----------------------------------------------------------------------------*/
std::string
BaseTestMethod :: getConfigFile(const std::string   configFileName)
                                                throw (std::invalid_argument)
{
    std::string     fileName;
    std::ifstream   file;

    // first, try with ~/.livesupport/configFileName
    struct passwd  * pwd = getpwnam(getlogin());
    if (pwd) {
        fileName += pwd->pw_dir;
        fileName += "/.livesupport/" + configFileName;
        file.open(fileName.c_str());
        if (file.good()) {
            file.close();
            return fileName;
        }
        file.close();
        file.clear();
    }

    // second, try with ./etc/configFileName
    fileName = getCwd() + "/etc/" + configFileName;
    file.open(fileName.c_str());
    if (file.good()) {
        file.close();
        return fileName;
    }
    file.close();

    throw std::invalid_argument("can't find config file " + configFileName);
}


/*------------------------------------------------------------------------------
 *  Return a configuration document
 *----------------------------------------------------------------------------*/
const xmlpp::Document *
BaseTestMethod :: getConfigDocument(xmlpp::DomParser      & parser,
                                    const std::string       configFileName)
                                                throw (std::invalid_argument,
                                                       std::exception)
{
    std::string         realFileName = getConfigFile(configFileName);
    parser.set_validate();
    parser.parse_file(realFileName);
    return parser.get_document();
}

