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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/authentication/include/LiveSupport/Authentication/AuthenticationClientInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Authentication_AuthenticationClientInterface_h
#define LiveSupport_Authentication_AuthenticationClientInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Authentication/SessionId.h"

namespace LiveSupport {
namespace Authentication {

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  An interface for authentication clients.
 *
 *  @author  $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class AuthenticationClientInterface
{
    public:
        /**
         *  Login to the authentication server.
         *  Returns a new session ID; in case of an error, returns a
         *  null pointer.
         *
         *  @return the new session ID
         */
        virtual Ptr<SessionId>::Ref
        login(const std::string &login, const std::string &password)
                                                throw ()
                                                                        = 0;

        /**
         *  Logout from the authentication server.
         *
         *  @param  sessionId the ID of the session to end
         *  @return true if logged out successfully, false if not
         */
        virtual const bool
        logout(Ptr<SessionId>::Ref sessionId)
                                                throw ()
                                                                        = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Authentication
} // namespace LiveSupport

#endif // LiveSupport_Authentication_AuthenticationClientInterface_h

