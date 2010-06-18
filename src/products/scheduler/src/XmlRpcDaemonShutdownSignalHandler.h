/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef XmlRpcDaemonShutdownSignalHandler_h
#define XmlRpcDaemonShutdownSignalHandler_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "SignalHandler.h"
#include "XmlRpcDaemon.h"


namespace LiveSupport {
namespace Scheduler {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Signal handler to shut down an XmlRpcDaemon.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see XmlRpcDaemon
 */
class XmlRpcDaemonShutdownSignalHandler : public SignalHandler
{
    private:
        /**
         *  The XmlRpcDaemon to shut down.
         */
        XmlRpcDaemon      * xmlRpcDaemon;

    public:
        /**
         *  Constructor.
         *
         *  @param xmlRpcDaemon the XmlRpcDaemon to shut down when the
         *         handler is invoked.
         */
        XmlRpcDaemonShutdownSignalHandler(
                            XmlRpcDaemon  * xmlRpcDaemon)
                                                        throw ()
        {
            this->xmlRpcDaemon = xmlRpcDaemon;
        }

        /**
         *  Handle the signal. Shuts down the XmlRpcDaemon used to
         *  construct this handler.
         *
         *  @param signal the actual signal received.
         */
        virtual void
        handleSignal(int signal)                        throw ()
        {
            xmlRpcDaemon->shutdown();
        }

        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~XmlRpcDaemonShutdownSignalHandler(void)        throw ()
        {
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Scheduler
} // namespace LiveSupport

#endif // XmlRpcDaemonShutdownSignalHandler_h

