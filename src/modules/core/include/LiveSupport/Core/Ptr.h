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
#ifndef LiveSupport_Core_Ptr_h
#define LiveSupport_Core_Ptr_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <boost/shared_ptr.hpp>


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A placeholder class for holidng typedefs to smart pointers.
 *  This is a workaround, as unfortunately typedfs in themselves may
 *  not be templated. For a discussion on the issue, see
 *  http://www.gotw.ca/gotw/079.htm
 *
 *  The smart pointers here are typedefs to the smart pointers in the
 *  boost library. For documentation of the boost smart pointers,
 *  see  http://www.boost.org/libs/smart_ptr/
 *
 *  @author  $Author$
 *  @version $Revision$
 */
template <class T>
class Ptr
{
    private:
        /**
         *  Default constructor.
         */
        Ptr(void)                           throw ()
        {
        }


    public:
        /**
         *  This is actually a typedef to the boost shared_ptr, which
         *  is a reference counting shared smart pointer.
         *  For more on boost::shared_ptr, see
         *  http://www.boost.org/libs/smart_ptr/shared_ptr.htm
         *
         *  To use this pointer, define as follows:
         *  <code>
         *      Ptr<MyType>::Ref    myPointer;
         *  </code>
         */
        typedef boost::shared_ptr<T>        Ref;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_Ptr_h

