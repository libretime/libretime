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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/db/include/LiveSupport/Db/Conversion.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Db_Conversion_h
#define LiveSupport_Db_Conversion_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/date_time/posix_time/posix_time.hpp>
#include <odbc++/types.h>

#include "LiveSupport/Core/Ptr.h"


namespace LiveSupport {
namespace Db {

using namespace boost;

using namespace LiveSupport;
using namespace LiveSupport::Core;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A helper object holding static conversion functions, that are
 *  helpful when accessing databases.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class Conversion
{
    private:
        /**
         *  The default constructor.
         */
        Conversion(void)                            throw()
        {
        }


    public:
        /**
         *  Convert a boost::ptime to a odbc::Timestamp.
         *
         *  @param ptime the boost ptime to convert.
         *  @return an odbc::Timestamp, holding the same time.
         */
        static Ptr<odbc::Timestamp>::Ref
        ptimeToTimestamp(Ptr<const posix_time::ptime>::Ref   ptime)  throw();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Db
} // namespace LiveSupport

#endif // LiveSupport_Db_Conversion_h

