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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/UniqueId.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <cstdlib>

#include "LiveSupport/Core/Uuid.h"
#include "LiveSupport/Core/Md5.h"
#include "LiveSupport/Core/UniqueId.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Generate a globally unique id.
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
UniqueId :: generateId(void)                        throw ()
{
    Ptr<Uuid>::Ref      uuid = Uuid::generateId();
    // as uuid is 128 bits, but we have only 64 bits, create an md5 hash
    // (which is still 128 bits), and use its values to create a 64 value
    // hopefully this is unique enough
    Md5                 md5((std::string)*uuid);
    Ptr<UniqueId>::Ref  id(new UniqueId(md5.high64bits() + md5.low64bits()));

    return id;
}

