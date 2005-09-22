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
#ifndef LiveSupport_Foo_Bar_h
#define LiveSupport_Foo_Bar_H

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* =============================================== include files & namespaces */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>


namespace LiveSupport {
namespace Foo {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  Bar class.
 *  This does nothing.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class Bar
{
    private:
        /**
         *  A static member variable.
         */
        static const std::string        barStr;

        /**
         *  A member variable.
         */
        int                             barInt;

    public:
        /**
         *  Default constructor.
         */
        Bar (void)                                      throw ()
        {
        }

        /**
         *  Say something.
         *
         *  @param parameter a parameter we don't care about.
         *  @return the bar string.
         *  @exception std::exception on some problems.
         */
        const std::string
        sayBar (void)                           throw (std::exception)
        {
            return barStr;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */

} // namespace Foo
} // namespace LiveSupport


#endif // LiveSupport_Foo_Bar_H
