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
#ifndef LiveSupport_Core_Installable_h
#define LiveSupport_Core_Installable_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <stdexcept>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface for having objects that can install themselves.
 *  The concept of installation means setting up for long-term use.
 *  When something installs, it creates databases, writes configuration
 *  files, etc. It sets up the object to be run. This is not to be
 *  confused with instance initialization, e.g. when a server is started
 *  or stopped.
 *
 *  The following life cycle is expected from systems impelementing this
 *  interface:
 *
 *  <ul>
 *      <li>install</li>
 *      <li><ul>
 *          <li>start</li>
 *          <li>stop</li>
 *          <li>start</li>
 *          <li>stop</li>
 *      </ul></li>
 *      <li>uninstall</li>
 *  </ul>
 *
 *  Later more stages will be added, and load/save (externalization)
 *  facilities.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class Installable
{
    public:
        /**
         *  Install the component.
         *  This step involves creating the environment in which the component
         *  will run. This may be creation of coniguration files,
         *  database tables, etc.
         *
         *  @exception std::exception on installation problems.
         */
        virtual void
        install(void)                           throw (std::exception)
                                                                        = 0;

        /**
         *  Check to see if the component has already been installed.
         *
         *  @return true if the component is properly installed,
         *          false otherwise
         *  @exception std::exception on generic problems
         */
        virtual bool
        isInstalled(void)                       throw (std::exception)
                                                                        = 0;

        /**
         *  Uninstall the component.
         *  Removes all the resources created in the install step.
         *
         *  @exception std::exception on unistallation problems.
         */
        virtual void
        uninstall(void)                         throw (std::exception)
                                                                        = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Installable_h

